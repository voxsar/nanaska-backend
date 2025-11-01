# Nanaska Backend API Documentation

## Overview
This API provides endpoints for managing CIMA exam materials, past papers, student submissions, and AI-powered marking.

## Environment Configuration

Add the following to your `.env` file:

```env
# N8N Upload URL - Webhook URL for uploading files to N8N workflow
N8N_UPLOAD_URL=https://your-n8n-instance.com/webhook/upload

# N8N Marking URL - Webhook URL for triggering AI marking workflow
N8N_MARKING_URL=https://your-n8n-instance.com/webhook/marking
```

## API Endpoints

All API endpoints are prefixed with `/api` and have CSRF protection disabled.

### Past Papers

#### List Past Papers
```
GET /api/past-papers
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "CIMA P1 2024",
      "year": 2024,
      "type": "exam",
      "description": "...",
      "questions": [...],
      "question_paper": {...},
      "answer_guide": {...},
      "marking_guide": {...}
    }
  ]
}
```

#### Get Past Paper Details
```
GET /api/past-papers/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "CIMA P1 2024",
    "year": 2024,
    "type": "exam",
    "description": "...",
    "questions": [...],
    "question_paper": {...},
    "answer_guide": {...},
    "marking_guide": {...}
  }
}
```

#### Get Questions for Past Paper
```
GET /api/past-papers/{id}/questions
```

**Response:**
```json
{
  "success": true,
  "data": {
    "past_paper": {
      "id": 1,
      "name": "CIMA P1 2024",
      "year": 2024,
      "type": "exam"
    },
    "questions": [
      {
        "id": 1,
        "question_number": "1",
        "question_text": "What is the purpose of...",
        "marks": 10,
        "order": 1
      }
    ]
  }
}
```

### Student Submissions

#### Submit Answer
```
POST /api/students/submit-answer
```

**Request Body:**
```json
{
  "student_email": "student@example.com",
  "student_password": "plain-text-password",
  "question_id": 1,
  "answer_text": "The answer is..."
}
```

**Response:**
```json
{
  "success": true,
  "message": "Answer submitted successfully",
  "data": {
    "id": 1,
    "student_id": 1,
    "question_id": 1,
    "past_paper_id": 1,
    "answer_text": "The answer is...",
    "status": "submitted",
    "submitted_at": "2024-11-01T12:00:00.000000Z"
  }
}
```

**Notes:**
- Students use plain text passwords stored in the database
- Authentication is separate from Laravel's User authentication
- Upon successful submission, a background job is triggered to send the answer to N8N for AI marking

### Marking Results Webhook

#### Receive Marking Results (from N8N)
```
POST /api/marking-results
```

**Request Body:**
```json
{
  "student_answer_id": 1,
  "marks_obtained": 8.5,
  "total_marks": 10,
  "feedback": "Good answer, but missed some key points...",
  "ai_response": {
    "raw_response": "...",
    "confidence": 0.85
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Marking result received and will be processed"
}
```

## Filament Admin Panel

Access the admin panel at `/admin`

### Resources Available:

1. **Pre-Seen Documents** (Documents > Pre Seen Documents)
   - Upload and manage CIMA pre-seen documents
   - Upload to N8N button available
   
2. **Past Papers** (Documents > Past Papers)
   - Manage past paper metadata
   - Add questions via relation manager
   - Upload question papers, answer guides, and marking guides
   - Upload to N8N button available

3. **Marking Prompts** (Configuration > Marking Prompts)
   - Manage AI marking prompts
   - Version history tracking

4. **Students** (Users > Students)
   - Manage student accounts
   - Plain text passwords (no encryption)

## Database Models

### PreSeenDocument
- name
- file_path
- description
- company_name
- page_count

### PastPaper
- name
- year
- type (pre_seen, case_study, exam, other)
- description

### Question
- past_paper_id (FK)
- question_number
- question_text
- marks
- order

### QuestionPaper / AnswerGuide / MarkingGuide
- past_paper_id (FK)
- file_path
- description

### Student
- name
- email (unique)
- password (plain text)
- student_id (unique)
- phone

### StudentAnswer
- student_id (FK)
- question_id (FK)
- past_paper_id (FK)
- answer_text
- file_path
- status (pending, submitted, marking, marked)
- submitted_at

### MarkingResult
- student_answer_id (FK)
- student_id (FK)
- question_id (FK)
- marks_obtained
- total_marks
- feedback
- ai_response (JSON)

### MarkingPrompt
- name
- prompt_text
- is_active
- version
- parent_id (FK - for version history)

## Background Jobs

### TriggerMarkingJob
Triggered when a student submits an answer. Sends the answer to N8N for AI marking.

### ProcessMarkingResultJob
Triggered when N8N sends back marking results. Stores the results in the database.

## N8N Integration

### Upload Workflow
When clicking "Upload to N8N" button in Filament:
- Sends document metadata to N8N_UPLOAD_URL
- Payload includes: type, id, name, and file_path

### Marking Workflow
1. Student submits answer via API
2. TriggerMarkingJob sends to N8N_MARKING_URL with:
   - student_answer_id
   - question_text
   - answer_text
   - marks
   - marking_prompt
3. N8N processes with AI
4. N8N posts results to /api/marking-results
5. ProcessMarkingResultJob stores results

## Security Notes

⚠️ **Important Security Considerations:**

1. **Student Passwords**: Stored as plain text as requested. This is NOT recommended for production systems. Consider implementing proper authentication in the future.

2. **CSRF Protection**: Disabled for all `/api/*` routes to allow external access.

3. **CORS**: Configure appropriately for your frontend domain.

4. **N8N Webhooks**: Consider adding authentication tokens to webhook URLs.

## Setup Instructions

1. Run migrations:
   ```bash
   php artisan migrate
   ```

2. Configure N8N URLs in `.env`

3. Create admin user for Filament:
   ```bash
   php artisan make:filament-user
   ```

4. Set up storage link:
   ```bash
   php artisan storage:link
   ```

5. Configure queue worker for background jobs:
   ```bash
   php artisan queue:work
   ```
