# Mock Exams vs Practice Questions - Separation Implementation

## Overview

This document explains how Mock Exams and Practice Questions are now **fundamentally separate** features in the Nanaska backend system, addressing the confusion that was causing them to mix.

## Problem Statement

**Before this implementation:**
- Practice questions were using the `MockExam` model
- `PracticeExamController` was reusing mock exam data
- Same n8n URLs were being used for both features
- No separate database tables for practice questions
- Practice questions were incorrectly tied to mock exam papers
- Frontend couldn't distinguish between mock exams and practice questions

## Solution: Complete Separation

### 1. Database Separation

**New Tables Created:**

#### `practice_questions`
```sql
- id
- pre_seen_document_id (FK to pre_seen_documents)
- question_number
- question_text
- context (optional)
- reference_material (optional)
- marks (string, e.g., "15" or "2a")
- order
- is_active
- created_at
- updated_at
```

#### `practice_question_attempts`
```sql
- id
- student_id (FK to students)
- practice_question_id (FK to practice_questions)
- answer_text
- status (submitted, marking, marked)
- marks_obtained
- total_marks
- feedback
- ai_response (JSON)
- submitted_at
- marked_at
- created_at
- updated_at
```

**Existing Mock Exam Tables (unchanged):**
- `mock_exams`
- `mock_exam_questions`
- `mock_exam_sub_questions`
- `mock_exam_attempts`
- `mock_exam_answers`

### 2. Model Separation

**New Models:**
- `PracticeQuestion` - Standalone questions linked to pre-seen documents
- `PracticeQuestionAttempt` - Student attempts at practice questions

**Existing Models (unchanged):**
- `MockExam`
- `MockExamQuestion`
- `MockExamSubQuestion`
- `MockExamAttempt`
- `MockExamAnswer`

**Key Model Differences:**

| Aspect | MockExam | PracticeQuestion |
|--------|----------|------------------|
| Parent | MockExam (full paper) | PreSeenDocument (document only) |
| Structure | Exam → Questions → SubQuestions | Direct question |
| Purpose | Full mock exam paper | Individual practice question |
| Context | Exam-level duration/description | Question-level context |

### 3. API Endpoint Separation

**Mock Exam Endpoints:**
```
GET  /api/mock-exams
GET  /api/mock-exams/{id}
GET  /api/mock-exams/{id}/questions
POST /api/mock-exams/submit-answer
GET  /api/mock-exams/attempts/{studentId}
POST /api/mock-exams/upload
```

**Practice Question Endpoints (NEW):**
```
GET  /api/practice-questions
GET  /api/practice-questions/{id}
POST /api/practice-questions/submit-answer
GET  /api/practice-questions/attempts/{studentId}
```

**No Shared Endpoints!**

### 4. Controller Separation

**MockExamController:**
- Handles full mock exam papers
- Returns exam structure with questions and sub-questions
- Creates `MockExamAttempt` records
- Creates `MockExamAnswer` records

**PracticeExamController (completely rewritten):**
- Handles individual practice questions
- Returns question list immediately (no exam wrapper)
- Creates `PracticeQuestionAttempt` records directly
- No concept of "exam attempt"

### 5. n8n URL Separation

**Mock Exam n8n URLs:**
```env
N8N_QUESTION_URL=.../webhook/mock-exams
N8N_QUESTION_TEST_URL=.../webhook-test/mock-exams
N8N_MARKING_URL=.../webhook/marking
N8N_MARKING_TEST_URL=.../webhook-test/marking
```

**Practice Question n8n URLs (NEW):**
```env
N8N_PRACTICE_QUESTION_URL=.../webhook/practice-questions
N8N_PRACTICE_QUESTION_TEST_URL=.../webhook-test/practice-questions
N8N_PRACTICE_MARKING_URL=.../webhook/practice-marking
N8N_PRACTICE_MARKING_TEST_URL=.../webhook-test/practice-marking
```

**Why Separate URLs?**
- Different processing logic in n8n
- Different marking criteria
- Prevents data mixing
- Easier to debug and monitor
- Can scale independently

### 6. Filament Admin Separation

**Mock Exams Section:**
- Mock Exams resource
- Mock Exam Questions resource
- Mock Exam Sub Questions resource
- Mock Exam Attempts resource
- Mock Exam Marking Prompts resource

**Practice Questions Section (NEW):**
- Practice Questions resource (new navigation group)
- Manages individual questions
- Links to pre-seen documents (not mock exams)
- Tracks student attempts
- Separate from mock exam workflow

### 7. Frontend Integration Changes

**Mock Exam Flow:**
```javascript
// 1. List mock exams
GET /api/mock-exams

// 2. Show exam details (overview page)
GET /api/mock-exams/{id}

// 3. Show questions with sub-questions
GET /api/mock-exams/{id}/questions

// 4. Submit answers as part of exam attempt
POST /api/mock-exams/submit-answer
```

**Practice Question Flow (NEW):**
```javascript
// 1. List practice questions immediately (no exam overview)
GET /api/practice-questions

// 2. Show individual question details
GET /api/practice-questions/{id}

// 3. Submit answer (no exam attempt context)
POST /api/practice-questions/submit-answer

// 4. View attempts for this question only
GET /api/practice-questions/attempts/{studentId}
```

## Key Architectural Decisions

### 1. No Reuse of Mock Exam Infrastructure

**Decision:** Create separate tables and models instead of reusing mock exam structure.

**Rationale:**
- Cleaner separation of concerns
- Prevents accidental data mixing
- Different data requirements
- Easier to maintain and evolve independently
- Better performance (no complex queries to filter)

### 2. Direct Question-to-PreSeen Link

**Decision:** Practice questions link directly to pre-seen documents, not through mock exams.

**Rationale:**
- Practice questions are about understanding pre-seen material
- No need for exam paper context
- Simpler data model
- Matches user mental model

### 3. Independent Attempt Tracking

**Decision:** `PracticeQuestionAttempt` is separate from `MockExamAttempt`.

**Rationale:**
- Different attempt lifecycles
- Mock exams have multiple questions per attempt
- Practice questions have one answer per attempt
- Can attempt same practice question multiple times
- Different marking workflows

### 4. Separate n8n Endpoints

**Decision:** Use different n8n webhook URLs for practice vs mock.

**Rationale:**
- Different processing requirements
- Different marking prompts
- Easier to troubleshoot
- Can deploy updates independently
- Clear separation in n8n workflows

## Migration Path

### For New Implementations

1. Run migrations: `php artisan migrate`
2. Add n8n URLs to `.env`
3. Create practice questions in Filament admin
4. Update frontend to use new endpoints
5. Set up n8n workflows for practice questions

### For Existing Systems Using Mock Exams for Practice

1. **Database:**
   - Run migrations to create new tables
   - Data remains in mock exam tables (no migration needed)

2. **Admin Panel:**
   - Create new practice questions in Practice Questions section
   - Keep existing mock exams unchanged
   - Can keep both features running simultaneously

3. **Frontend:**
   - Update to call `/api/practice-questions` for practice
   - Continue calling `/api/mock-exams` for mock exams
   - No breaking changes if both features needed

4. **n8n:**
   - Set up new practice question workflows
   - Keep existing mock exam workflows
   - Both can run in parallel

## Benefits of Separation

### 1. Clarity
- No confusion about what type of content students are viewing
- Clear distinction in admin panel
- Easier for developers to understand codebase

### 2. Maintainability
- Changes to mock exams don't affect practice questions
- Can evolve features independently
- Easier to add new features to either system

### 3. Performance
- No complex queries to filter mock vs practice
- Separate tables are more efficient
- Can optimize each feature independently

### 4. Scalability
- Can scale practice questions differently from mock exams
- Different caching strategies
- Different rate limits if needed

### 5. Data Integrity
- No risk of practice data contaminating mock exam data
- Clearer audit trails
- Easier to debug issues

## Testing Verification

To verify the separation is working:

```bash
# Test mock exam endpoints still work
curl http://localhost:8000/api/mock-exams
curl http://localhost:8000/api/mock-exams/1/questions

# Test practice question endpoints are separate
curl http://localhost:8000/api/practice-questions
curl http://localhost:8000/api/practice-questions/1

# Verify no data mixing in responses
# - Mock exams should have exam_id, questions, sub_questions
# - Practice questions should have pre_seen_document_id, no exam context

# Check database tables are separate
SELECT * FROM mock_exams;
SELECT * FROM practice_questions;

# Verify different n8n URLs are configured
php artisan tinker
>>> config('services.n8n.question_url')  // Mock exam URL
>>> config('services.n8n.practice_question_url')  // Practice URL
```

## Documentation

Comprehensive documentation created:

1. **PRACTICE_QUESTIONS_API.md** - Complete API reference for practice questions
2. **This document** - Architectural decisions and separation strategy
3. **Updated .env.example** - All n8n URLs documented

## Future Considerations

### Potential Enhancements

**Practice Questions:**
- Difficulty levels
- Topic tagging
- Progress tracking
- Recommended questions based on performance

**Mock Exams:**
- Multiple attempts comparison
- Time tracking per question
- Pause/resume functionality

Both can be enhanced independently without affecting the other.

## Summary

The separation is now complete:

✅ Separate database tables  
✅ Separate models  
✅ Separate controllers  
✅ Separate API endpoints  
✅ Separate n8n URLs  
✅ Separate Filament resources  
✅ Separate marking workflows  
✅ Comprehensive documentation  

**No mixing, no confusion, clean architecture.**
