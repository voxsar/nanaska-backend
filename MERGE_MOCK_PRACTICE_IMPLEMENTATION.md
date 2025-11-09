# Mock Interface + Practice Questions Feature Merge Implementation

## Overview

This document describes the implementation of merging features between Mock Exams and Practice Questions to enable a unified interface for displaying questions with pre-seen documents, context, references, and sub-questions.

## Issue Requirements

The original issue requested:
1. Mock exam list display (already existed)
2. Questions displayed similarly to practice interface but based on exam question parts
3. Display pre-seen, context, references, then loop through sub-questions
4. Backend support for string marks to support sub-question marking

## Implementation Details

### 1. Database Schema Changes

#### Migration: `change_marks_to_string_in_mock_exam_questions_table`
- Changed `marks` column from `INTEGER` to `VARCHAR(50)` in `mock_exam_questions` table
- Allows storing flexible marks like "20", "2a", "2b", "10-15", etc.
- Default value: '0'

#### Migration: `change_marks_to_string_in_mock_exam_sub_questions_table`
- Changed `marks` column from `INTEGER` to `VARCHAR(50)` in `mock_exam_sub_questions` table
- Enables sub-questions to have alphanumeric marks
- Default value: '0'

### 2. Model Updates

#### MockExamQuestion Model
**Changed:**
```php
protected $casts = [
    'duration_minutes' => 'integer',
    'marks' => 'string',  // Changed from 'integer'
    'order' => 'integer',
];
```

#### MockExamSubQuestion Model
**Changed:**
```php
protected $casts = [
    'marks' => 'string',  // Changed from 'integer'
    'order' => 'integer',
];
```

### 3. Admin Panel (Filament) Updates

#### QuestionsRelationManager
**Changed:**
```php
Forms\Components\TextInput::make('marks')
    ->required()
    ->maxLength(50)    // Changed from ->numeric()
    ->default('0'),     // Changed from default(0)
```

#### MockExamSubQuestionResource
**Changed:**
```php
Forms\Components\TextInput::make('marks')
    ->required()
    ->maxLength(50)    // Changed from ->numeric()
    ->default('0')     // Changed from default(0)
    ->label('Marks'),
```

### 4. API Enhancements

#### MockExamController::show()
**Changed:**
```php
$mockExam = MockExam::with([
    'questions.subQuestions',  // Added subQuestions
    'preSeenDocument',
    'markingPrompts' => function ($query) {
        $query->where('is_active', true);
    }
])->findOrFail($id);
```

#### MockExamController::questions()
**Changed:**
```php
$questions = $mockExam->questions()
    ->with('subQuestions')  // Added eager loading of subQuestions
    ->orderBy('order')
    ->get();
```

### 5. API Response Structure

#### GET `/api/mock-exams/{id}/questions`
Returns questions with complete structure:
```json
{
  "success": true,
  "data": {
    "mock_exam": {
      "id": 1,
      "name": "CIMA Management Case Study - Mock 1",
      "pre_seen_document_id": 1,
      "duration_minutes": 180
    },
    "questions": [
      {
        "id": 1,
        "question_number": "1",
        "context": "The company is facing declining profits...",
        "reference_material": "Refer to Exhibit 1 - Financial Statements",
        "duration_minutes": 45,
        "marks": "25",
        "order": 1,
        "sub_questions": [
          {
            "id": 1,
            "sub_question_number": "a",
            "sub_question_text": "Calculate the gross profit margin...",
            "marks": "8",
            "order": 1
          },
          {
            "id": 2,
            "sub_question_number": "b",
            "sub_question_text": "Analyze the trend in operating expenses...",
            "marks": "10",
            "order": 2
          }
        ]
      }
    ]
  }
}
```

### 6. Test Coverage

Created `tests/Feature/MockExamApiTest.php` with 3 comprehensive tests:

1. **test_mock_exam_questions_endpoint_returns_sub_questions**
   - Verifies questions endpoint includes sub-questions
   - Checks response structure
   - Validates marks are returned as strings

2. **test_mock_exam_show_endpoint_includes_sub_questions**
   - Ensures show endpoint loads sub-questions
   - Validates nested relationships

3. **test_marks_can_be_stored_as_string**
   - Tests string marks storage (including alphanumeric)
   - Verifies data type is string
   - Tests values like "2a", "2b"

**Test Results:** ✅ All 3 tests pass (31 assertions)

## Frontend Integration Guide

### Display Flow

When displaying a mock exam question to a student:

1. **Show Pre-Seen Document** (if linked)
   - Access via `mock_exam.pre_seen_document.name` and `file_path`

2. **Display Question Header**
   - Question Number: `question.question_number`
   - Total Marks: `question.marks`
   - Duration: `question.duration_minutes` minutes

3. **Show Context Section**
   - Display: `question.context`

4. **Show Reference Materials**
   - Display: `question.reference_material`

5. **Loop Through Sub-Questions**
   ```javascript
   question.sub_questions.forEach((subQ) => {
     // Display:
     // - subQ.sub_question_number (e.g., "a", "b", "i", "ii")
     // - subQ.sub_question_text
     // - subQ.marks
     // - Answer input field
     // - Save draft / Submit buttons
   });
   ```

### Example Frontend Code

```javascript
// Fetch mock exam questions
const response = await fetch(`/api/mock-exams/${examId}/questions`);
const { data } = await response.json();

// Display each question
data.questions.forEach(question => {
  // Show pre-seen if available
  if (data.mock_exam.pre_seen_document) {
    renderPreSeenDocument(data.mock_exam.pre_seen_document);
  }
  
  // Show question context
  renderContext(question.context);
  
  // Show reference material
  renderReferences(question.reference_material);
  
  // Loop through sub-questions
  question.sub_questions.forEach(subQuestion => {
    renderSubQuestion({
      number: `${question.question_number}${subQuestion.sub_question_number}`,
      text: subQuestion.sub_question_text,
      marks: subQuestion.marks,
      onSave: (answer) => saveDraft(subQuestion.id, answer),
      onSubmit: (answer) => submitAnswer(subQuestion.id, answer)
    });
  });
});
```

## Benefits

### 1. Unified Interface
- Both Mock Exams and Practice Questions can use the same display components
- Consistent user experience across exam types
- Reusable frontend code

### 2. Flexible Marking
- Supports various marking schemes:
  - Simple integers: "10", "20"
  - Alphanumeric: "2a", "2b"
  - Ranges: "10-15"
  - Complex: "Part A: 5 marks"

### 3. Better Structure
- Questions properly organized with sub-questions
- Each sub-question has its own marks allocation
- Clear hierarchy: Exam → Questions → Sub-Questions

### 4. Complete Context
- Pre-seen documents linked at exam level
- Context and references at question level
- Individual sub-questions for detailed testing

### 5. API Consistency
- All endpoints return sub-questions automatically
- No need for additional API calls
- Clean, predictable response structure

## Database Schema Summary

### mock_exam_questions
```sql
id                  INTEGER PRIMARY KEY
mock_exam_id        INTEGER (FK)
question_number     VARCHAR(255)
context             TEXT
reference_material  TEXT
duration_minutes    INTEGER
marks               VARCHAR(50)  ← Changed from INTEGER
order               INTEGER
created_at          DATETIME
updated_at          DATETIME
```

### mock_exam_sub_questions
```sql
id                      INTEGER PRIMARY KEY
mock_exam_question_id   INTEGER (FK)
sub_question_number     VARCHAR(255)
sub_question_text       TEXT
marks                   VARCHAR(50)  ← Changed from INTEGER
order                   INTEGER
created_at              DATETIME
updated_at              DATETIME
```

## Migration Guide

### For Existing Data

If you have existing mock exams with integer marks:

1. **Run migrations**
   ```bash
   php artisan migrate
   ```

2. **Existing integer marks are automatically converted to strings**
   - "10" becomes "10"
   - "20" becomes "20"
   - No data loss

3. **New marks can be alphanumeric**
   - Can now store "2a", "2b", etc.

### For Filament Admin Users

When creating/editing questions:
- Marks field now accepts any text input
- Can enter: "10", "2a", "Part A: 5", etc.
- No numeric validation

## Testing Recommendations

### Backend Testing
```bash
# Run the mock exam tests
php artisan test tests/Feature/MockExamApiTest.php

# Run all tests
php artisan test
```

### API Testing
```bash
# Test questions endpoint
curl http://localhost:8000/api/mock-exams/1/questions

# Test show endpoint
curl http://localhost:8000/api/mock-exams/1

# Test list endpoint
curl http://localhost:8000/api/mock-exams
```

### Manual Testing Checklist
- [ ] Create mock exam in Filament admin
- [ ] Add questions with context and references
- [ ] Add sub-questions with alphanumeric marks
- [ ] Test API endpoints return correct structure
- [ ] Verify sub-questions appear in responses
- [ ] Test string marks are stored and retrieved correctly

## Future Enhancements

Potential improvements for consideration:
1. Add validation rules for marks format
2. Support for mark ranges (e.g., "5-10")
3. Auto-calculate total marks from sub-questions
4. Mark allocation suggestions in admin panel
5. Analytics on mark distribution

## References

- **API Documentation**: `MOCK_EXAM_API.md`
- **Database Migrations**: `database/migrations/2025_11_09_*`
- **Test Suite**: `tests/Feature/MockExamApiTest.php`
- **Models**: `app/Models/MockExamQuestion.php`, `app/Models/MockExamSubQuestion.php`
- **Controller**: `app/Http/Controllers/Api/MockExamController.php`
