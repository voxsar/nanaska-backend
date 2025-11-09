# Mock Exam API Documentation

## Overview
This API provides endpoints for managing Mock Exams, separate from the Past Papers system. Mock exams can be linked to Pre-Seen documents and support student attempts with AI-powered marking.

## API Endpoints

All API endpoints are prefixed with `/api` and CSRF protection is disabled for external access.

### List Mock Exams
```
GET /api/mock-exams
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Test Mock Exam",
      "description": "Test Description",
      "pre_seen_document_id": 1,
      "duration_minutes": 120,
      "is_active": true,
      "questions": [...],
      "pre_seen_document": {...}
    }
  ]
}
```

### Get Mock Exam Details
```
GET /api/mock-exams/{id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Test Mock Exam",
    "description": "Test Description",
    "pre_seen_document_id": 1,
    "duration_minutes": 120,
    "is_active": true,
    "questions": [...],
    "pre_seen_document": {...},
    "marking_prompts": [...]
  }
}
```

### Get Questions for Mock Exam
```
GET /api/mock-exams/{id}/questions
```

**Response:**
```json
{
  "success": true,
  "data": {
    "mock_exam": {
      "id": 1,
      "name": "Test Mock Exam"
    },
    "questions": [
      {
        "id": 1,
        "question_number": "1",
        "question_text": "What is accounting?",
        "marks": 10,
        "order": 1
      }
    ]
  }
}
```

### Submit Answer
```
POST /api/mock-exams/submit-answer
```

**Request Body:**
```json
{
  "student_email": "student@example.com",
  "student_password": "plain-text-password",
  "mock_exam_id": 1,
  "mock_exam_question_id": 1,
  "mock_exam_attempt_id": 1, // optional, will create new attempt if not provided
  "answer_text": "The answer is..."
}
```

**Response:**
```json
{
  "success": true,
  "message": "Answer submitted successfully",
  "data": {
    "answer_id": 1,
    "attempt_id": 1,
    "status": "submitted"
  }
}
```

**Notes:**
- Students use plain text passwords stored in the database (separate from User authentication)
- Upon successful submission, an observer triggers a background job to send the answer to N8N for AI marking
- The answer status changes from "pending" → "submitted" → "marking" → "marked"

### Get Student Attempts
```
GET /api/mock-exams/attempts/{studentId}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "student_id": 1,
      "mock_exam_id": 1,
      "started_at": "2024-11-08T12:00:00.000000Z",
      "completed_at": null,
      "total_marks_obtained": 15.50,
      "total_marks_available": 25.00,
      "percentage": 62.00,
      "status": "in_progress",
      "mock_exam": {...},
      "answers": [...]
    }
  ]
}
```

## Filament Admin Panel

Access the admin panel at `/admin`

### Mock Exams Navigation Group

1. **Mock Exams** (Mock Exams > Mock Exams)
   - Create and manage mock exams
   - Link to Pre-Seen documents
   - Set duration and active status
   - Add questions via relation manager
   
2. **Marking Prompts** (Mock Exams > Marking Prompts)
   - Create marking prompts specific to mock exams
   - Track version history
   - Active/inactive toggle
   
3. **Student Attempts** (Mock Exams > Student Attempts)
   - View all student attempts
   - See individual answers and marks
   - Track completion status and results

## Database Models

### MockExam
- name
- description
- pre_seen_document_id (FK to PreSeenDocument)
- duration_minutes
- is_active

### MockExamQuestion
- mock_exam_id (FK)
- question_number
- question_text
- marks
- order

### MockExamMarkingPrompt
- mock_exam_id (FK, nullable - can be general)
- name
- prompt_text
- is_active
- version

### MockExamMarkingPromptHistory
- mock_exam_marking_prompt_id (FK)
- prompt_text
- version
- changed_by
- change_reason

### MockExamAttempt
- student_id (FK)
- mock_exam_id (FK)
- started_at
- completed_at
- total_marks_obtained
- total_marks_available
- percentage
- status (in_progress, submitted, marked)

### MockExamAnswer
- mock_exam_attempt_id (FK)
- mock_exam_question_id (FK)
- student_id (FK)
- answer_text
- file_path (optional)
- marks_obtained
- feedback
- ai_response (JSON)
- status (pending, submitted, marking, marked)
- submitted_at

## Observer Pattern

The `MockExamAnswerObserver` automatically triggers marking when:
- A new answer is created with status "submitted"
- An existing answer's status is changed to "submitted"

This triggers the `TriggerMockExamMarkingJob` which:
1. Updates answer status to "marking"
2. Gets the active marking prompt for the mock exam
3. Sends answer data to N8N marking webhook
4. Logs success/failure

## N8N Integration

### Marking Workflow
1. Student submits answer via API
2. `MockExamAnswerObserver` detects submission
3. `TriggerMockExamMarkingJob` sends to N8N with payload:
   ```json
   {
     "mock_exam_answer_id": 1,
     "student_id": 1,
     "question_id": 1,
     "answer_text": "...",
     "question": "...",
     "marks": 10,
     "marking_prompt": "...",
     "type": "mock_exam"
   }
   ```
4. N8N processes with AI
5. N8N posts results back (uses same webhook as Past Papers)
6. Results stored in `MockExamAnswer`

## Relationships

```
MockExam
├── belongsTo PreSeenDocument
├── hasMany MockExamQuestion
├── hasMany MockExamAttempt
└── hasMany MockExamMarkingPrompt

MockExamQuestion
├── belongsTo MockExam
└── hasMany MockExamAnswer

MockExamMarkingPrompt
├── belongsTo MockExam (nullable)
└── hasMany MockExamMarkingPromptHistory

MockExamAttempt
├── belongsTo Student
├── belongsTo MockExam
└── hasMany MockExamAnswer

MockExamAnswer
├── belongsTo MockExamAttempt
├── belongsTo MockExamQuestion
└── belongsTo Student
```

## Security Notes

⚠️ **Important:**
- Student passwords are stored as plain text (not recommended for production)
- CSRF protection is disabled for `/api/*` routes
- Configure CORS appropriately for your frontend domain
- Consider adding authentication tokens for production use

## Setup

1. Run migrations:
   ```bash
   php artisan migrate
   ```

2. Configure N8N URL in `.env`:
   ```
   N8N_MARKING_URL=https://your-n8n.com/webhook/marking
   ```

3. Start queue worker for background jobs:
   ```bash
   php artisan queue:work
   ```

4. Access admin panel at `/admin` to create mock exams

## Example Usage

### Create a Mock Exam Attempt
```bash
# 1. Student submits first answer (creates attempt)
curl -X POST http://localhost:8000/api/mock-exams/submit-answer \
  -H "Content-Type: application/json" \
  -d '{
    "student_email": "student@example.com",
    "student_password": "password",
    "mock_exam_id": 1,
    "mock_exam_question_id": 1,
    "answer_text": "My answer to question 1"
  }'

# Response includes attempt_id
# {"success":true,"data":{"answer_id":1,"attempt_id":1,"status":"submitted"}}

# 2. Student submits subsequent answers to same attempt
curl -X POST http://localhost:8000/api/mock-exams/submit-answer \
  -H "Content-Type: application/json" \
  -d '{
    "student_email": "student@example.com",
    "student_password": "password",
    "mock_exam_id": 1,
    "mock_exam_question_id": 2,
    "mock_exam_attempt_id": 1,
    "answer_text": "My answer to question 2"
  }'
```

### View Student's Attempts
```bash
curl http://localhost:8000/api/mock-exams/attempts/1
```
