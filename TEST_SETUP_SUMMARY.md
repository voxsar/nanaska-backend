# Test Setup Summary

## Marking Results Flow

### How Results Are Returned:

1. **Submission**: Student submits answer → Creates `StudentAnswer` with status "submitted"
   
2. **Trigger Marking**: Observer dispatches `TriggerMarkingJob` or `TriggerMockExamMarkingJob`

3. **Send to N8N**: Job POSTs to `N8N_MARKING_URL` with:
   - `student_answer_id` (or `mock_exam_answer_id`)
   - `student_id`
   - `question_id` 
   - `answer_text`
   - `question`
   - `marks`
   - `marking_prompt`
   - `type` (for mock exams)

4. **N8N Processes**: AI marks the answer

5. **Return Results**: N8N POSTs back to Laravel:
   ```
   POST /api/marking-results
   ```
   
   **Payload:**
   ```json
   {
     "student_answer_id": 1,
     "marks_obtained": 8.5,
     "total_marks": 10,
     "feedback": "Good answer...",
     "ai_response": { ... }
   }
   ```

6. **Process Results**: `MarkingResultController` receives webhook, dispatches `ProcessMarkingResultJob`

7. **Store**: Job creates `MarkingResult` record and updates answer status to "marked"

### Controller Endpoint:
- **File**: `app/Http/Controllers/Api/MarkingResultController.php`
- **Route**: `POST /api/marking-results` (no auth required - webhook)
- **Validation**: Requires `student_answer_id`, `marks_obtained`, `total_marks`

## Running Tests

### Created Test Files:
1. `/var/www/backend/tests/Feature/MarkingResultTest.php` - Tests marking result webhook and processing
2. `/var/www/backend/tests/Feature/MockExamMarkingResultTest.php` - Tests mock exam marking
3. `/var/www/backend/tests/Feature/EndToEndMarkingFlowTest.php` - Full flow from submission to result

### Run Tests:
```bash
# Run all marking tests
php artisan test --filter=MarkingResult

# Run specific test file
php artisan test tests/Feature/MarkingResultTest.php

# Run end-to-end flow test
php artisan test tests/Feature/EndToEndMarkingFlowTest.php
```

### Test Coverage:
- ✓ Webhook receives results from N8N
- ✓ Validation of required fields
- ✓ Processing and storing results
- ✓ Status updates (marking → marked)
- ✓ Decimal marks handling
- ✓ JSON ai_response storage
- ✓ Mock exam marking flow
- ✓ Error handling and N8N failures
- ✓ Missing configuration handling

## Database Factories Created:
All model factories have been created in `database/factories/`:
- StudentFactory
- PastPaperFactory  
- QuestionFactory
- StudentAnswerFactory
- MockExamFactory
- MockExamQuestionFactory
- MockExamAttemptFactory
- MockExamAnswerFactory
- MarkingPromptFactory

**Note**: Factories need to be populated with proper definitions for tests to pass.
