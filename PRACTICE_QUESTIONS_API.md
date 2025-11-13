# Practice Questions API Documentation

## Overview

Practice Questions are **fundamentally separate** from Mock Exams. They are standalone questions based on pre-seen documents that students can practice with individually, without the context of a full mock exam paper.

### Key Differences from Mock Exams

| Feature | Mock Exams | Practice Questions |
|---------|-----------|-------------------|
| Structure | Full exam paper with multiple questions | Individual questions |
| Context | Complete mock exam with duration and paper | Questions linked to pre-seen documents |
| n8n URLs | `N8N_QUESTION_URL` and `N8N_MARKING_URL` | `N8N_PRACTICE_QUESTION_URL` and `N8N_PRACTICE_MARKING_URL` |
| Attempts | Tracked via `MockExamAttempt` | Tracked via `PracticeQuestionAttempt` |
| Display | Shows exam overview, then questions | Shows questions immediately on first screen |
| Filament | Managed in "Mock Exams" section | Managed in "Practice Questions" section |
| API Endpoints | `/api/mock-exams/*` | `/api/practice-questions/*` |

## Environment Configuration

Add the following to your `.env` file:

```env
# N8N Practice Question URL - Webhook URL for extracting practice questions (separate from mock exams)
N8N_PRACTICE_QUESTION_URL=https://automation.artslabcreatives.com/webhook/practice-questions
N8N_PRACTICE_QUESTION_TEST_URL=https://automation.artslabcreatives.com/webhook-test/practice-questions

# N8N Practice Marking URL - Webhook URL for marking practice question answers (separate from mock exam marking)
N8N_PRACTICE_MARKING_URL=https://automation.artslabcreatives.com/webhook/practice-marking
N8N_PRACTICE_MARKING_TEST_URL=https://automation.artslabcreatives.com/webhook-test/practice-marking
```

## API Endpoints

All API endpoints are prefixed with `/api` and have CSRF protection disabled.

### List Practice Questions

```
GET /api/practice-questions
```

Returns all active practice questions, ordered by display order.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "pre_seen_document_id": 1,
      "question_number": "1",
      "question_text": "Analyze the financial performance of the company...",
      "context": "The company is facing declining profits...",
      "reference_material": "Refer to Exhibit 1 - Financial Statements",
      "marks": "15",
      "order": 1,
      "is_active": true,
      "pre_seen_document": {
        "id": 1,
        "name": "CIMA MCS Pre-Seen 2024",
        "file_path": "documents/preseen-2024.pdf",
        "company_name": "Example Corp"
      }
    }
  ]
}
```

### Get Practice Question Details

```
GET /api/practice-questions/{id}
```

Returns detailed information for a specific practice question.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "pre_seen_document_id": 1,
    "question_number": "1",
    "question_text": "Analyze the financial performance of the company...",
    "context": "The company is facing declining profits...",
    "reference_material": "Refer to Exhibit 1 - Financial Statements",
    "marks": "15",
    "order": 1,
    "is_active": true,
    "pre_seen_document": {
      "id": 1,
      "name": "CIMA MCS Pre-Seen 2024",
      "file_path": "documents/preseen-2024.pdf",
      "company_name": "Example Corp"
    }
  }
}
```

### Submit Answer to Practice Question

```
POST /api/practice-questions/submit-answer
```

Submits a student's answer to a practice question for marking.

**Request Body:**
```json
{
  "student_id": 1,
  "practice_question_id": 1,
  "answer_text": "The company's financial performance shows..."
}
```

**Response:**
```json
{
  "success": true,
  "message": "Practice question answer submitted successfully",
  "data": {
    "id": 1,
    "student_id": 1,
    "practice_question_id": 1,
    "answer_text": "The company's financial performance shows...",
    "status": "submitted",
    "marks_obtained": null,
    "total_marks": null,
    "feedback": null,
    "ai_response": null,
    "submitted_at": "2024-11-09T18:00:00.000000Z",
    "marked_at": null,
    "practice_question": {
      "id": 1,
      "question_number": "1",
      "question_text": "Analyze the financial performance...",
      "marks": "15"
    },
    "student": {
      "id": 1,
      "name": "John Doe",
      "email": "student@test.com"
    }
  }
}
```

**Notes:**
- Creates a new `PracticeQuestionAttempt` record
- Triggers background job to send answer to N8N practice marking endpoint
- Separate from mock exam marking workflow

### Get Student's Practice Question Attempts

```
GET /api/practice-questions/attempts/{studentId}
```

Returns all practice question attempts for a specific student.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "student_id": 1,
      "practice_question_id": 1,
      "answer_text": "The company's financial performance shows...",
      "status": "marked",
      "marks_obtained": 12.5,
      "total_marks": 15.0,
      "feedback": "Good analysis, but could improve on...",
      "ai_response": {
        "raw_response": "...",
        "confidence": 0.85
      },
      "submitted_at": "2024-11-09T18:00:00.000000Z",
      "marked_at": "2024-11-09T18:05:00.000000Z",
      "practice_question": {
        "id": 1,
        "question_number": "1",
        "question_text": "Analyze the financial performance...",
        "marks": "15",
        "pre_seen_document": {
          "id": 1,
          "name": "CIMA MCS Pre-Seen 2024"
        }
      }
    }
  ]
}
```

## Database Models

### PracticeQuestion

```php
protected $fillable = [
    'pre_seen_document_id',
    'question_number',
    'question_text',
    'context',
    'reference_material',
    'marks',
    'order',
    'is_active',
];
```

**Relationships:**
- `belongsTo(PreSeenDocument::class)` - The pre-seen document this question is based on
- `hasMany(PracticeQuestionAttempt::class)` - Student attempts at this question

### PracticeQuestionAttempt

```php
protected $fillable = [
    'student_id',
    'practice_question_id',
    'answer_text',
    'status',
    'marks_obtained',
    'total_marks',
    'feedback',
    'ai_response',
    'submitted_at',
    'marked_at',
];
```

**Statuses:**
- `submitted` - Answer submitted, awaiting marking
- `marking` - Currently being marked by AI
- `marked` - Marking completed

**Relationships:**
- `belongsTo(Student::class)` - The student who made this attempt
- `belongsTo(PracticeQuestion::class)` - The question being answered

## Filament Admin Panel

Access the admin panel at `/admin`

### Practice Questions Resource

Located in: **Practice Questions > Practice Questions**

**Features:**
1. Create/Edit/Delete practice questions
2. Link to pre-seen documents
3. Add context and reference material
4. Set question order and marks
5. Activate/Deactivate questions
6. View student attempts (relation manager)

**Form Fields:**
- Pre-Seen Document (dropdown)
- Question Number
- Question Text
- Context (optional)
- Reference Material (optional)
- Marks
- Display Order
- Active toggle

## Frontend Integration

### Display Flow

When displaying practice questions to students:

1. **List all questions immediately** (no mock paper overview)
   ```javascript
   const response = await fetch('/api/practice-questions');
   const { data } = await response.json();
   
   // Display questions in a list/grid
   data.forEach(question => {
     renderQuestion(question);
   });
   ```

2. **Show question details** when clicked
   ```javascript
   // Show pre-seen document reference
   renderPreSeenDocument(question.pre_seen_document);
   
   // Show context if available
   if (question.context) {
     renderContext(question.context);
   }
   
   // Show reference material if available
   if (question.reference_material) {
     renderReferences(question.reference_material);
   }
   
   // Show question text and answer input
   renderQuestionText(question.question_text);
   renderAnswerInput(question.id);
   ```

3. **Submit answer**
   ```javascript
   const submitAnswer = async (questionId, answerText) => {
     const response = await fetch('/api/practice-questions/submit-answer', {
       method: 'POST',
       headers: { 'Content-Type': 'application/json' },
       body: JSON.stringify({
         student_id: currentStudent.id,
         practice_question_id: questionId,
         answer_text: answerText
       })
     });
     
     return response.json();
   };
   ```

4. **View attempts and results**
   ```javascript
   const response = await fetch(`/api/practice-questions/attempts/${studentId}`);
   const { data } = await response.json();
   
   // Display attempt history with marks and feedback
   data.forEach(attempt => {
     renderAttempt({
       question: attempt.practice_question.question_text,
       marks: `${attempt.marks_obtained}/${attempt.total_marks}`,
       feedback: attempt.feedback,
       submittedAt: attempt.submitted_at
     });
   });
   ```

## n8n Integration

### Practice Question Extraction Workflow

**Endpoint:** `N8N_PRACTICE_QUESTION_URL`

When uploading practice questions via Filament admin:
- Sends practice question metadata to n8n
- Payload includes: question_text, pre_seen_document_id, context, etc.
- n8n can process and enhance the question data

### Practice Question Marking Workflow

**Endpoint:** `N8N_PRACTICE_MARKING_URL`

1. Student submits answer via API
2. Background job sends to `N8N_PRACTICE_MARKING_URL` with:
   - practice_question_attempt_id
   - question_text
   - answer_text
   - marks
   - marking_prompt (specific to practice questions)
3. n8n processes with AI
4. n8n posts results back to `/api/practice-marking-results` (TODO: create this endpoint)
5. Background job stores results in `practice_question_attempts` table

**Important:** This workflow is **completely separate** from mock exam marking to prevent confusion.

## Comparison with Mock Exams

### Mock Exam Flow
1. Student selects a mock exam
2. Views exam overview (duration, description, pre-seen)
3. Starts exam attempt
4. Answers questions in sequence
5. Submits entire exam
6. Marking via `N8N_MARKING_URL`

### Practice Question Flow
1. Student sees all practice questions immediately
2. Selects individual question to practice
3. Views question with context and references
4. Submits answer for that question only
5. Marking via `N8N_PRACTICE_MARKING_URL`
6. Can attempt same question multiple times
7. No concept of "exam attempt" - each question is independent

## Testing

### API Testing

```bash
# List practice questions
curl http://localhost:8000/api/practice-questions

# Get specific question
curl http://localhost:8000/api/practice-questions/1

# Submit answer
curl -X POST http://localhost:8000/api/practice-questions/submit-answer \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": 1,
    "practice_question_id": 1,
    "answer_text": "My answer..."
  }'

# Get student attempts
curl http://localhost:8000/api/practice-questions/attempts/1
```

### Manual Testing Checklist

- [ ] Create practice question in Filament admin
- [ ] Link to pre-seen document
- [ ] Add context and reference material
- [ ] Set marks and order
- [ ] Test API endpoints return correct structure
- [ ] Submit answer via API
- [ ] Verify separate n8n URL is called (not mock exam URL)
- [ ] Check attempts are tracked correctly
- [ ] Verify no mixing with mock exam data

## Migration Guide

### Running Migrations

```bash
php artisan migrate
```

This will create:
- `practice_questions` table
- `practice_question_attempts` table

### For Existing Systems

If you have been using mock exams for practice:
1. Run migrations to create new tables
2. Create practice questions in Filament admin
3. Update frontend to use new `/api/practice-questions` endpoints
4. Configure new n8n webhooks for practice questions
5. Test marking workflow separately from mock exams

## Security Notes

⚠️ **Important:**
- Practice question marking uses **separate n8n URLs** from mock exams
- Student attempts are tracked separately in `practice_question_attempts` table
- No data sharing between mock exams and practice questions
- Prevents confusion and mixing of different question types

## Future Enhancements

Potential improvements:
1. Question difficulty levels
2. Topic/category tagging
3. Prerequisites (complete question X before Y)
4. Leaderboards for practice questions
5. Hints system
6. Detailed analytics on common mistakes
7. Recommended practice questions based on performance
