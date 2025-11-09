# Marking Results Data Flow

## System Architecture

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         MARKING RESULTS FLOW                            │
└─────────────────────────────────────────────────────────────────────────┘

1. STUDENT SUBMISSION
   ┌──────────────────┐
   │   Student submits│
   │   exam answer    │
   └────────┬─────────┘
            │
            ▼
   ┌──────────────────────┐
   │ TriggerMarkingJob    │
   │ (Laravel Queue)      │
   └──────────┬───────────┘
              │
              │ POST /n8n/webhook/marking
              ▼
   ┌──────────────────────┐
   │   N8N Workflow       │
   │   - Fetches documents│
   │   - Calls AI service │
   │   - Generates results│
   └──────────┬───────────┘
              │
              │ Webhook callback
              ▼

2. RESULT RECEPTION
   ┌─────────────────────────────────────────┐
   │ POST /api/marking-results               │
   │ MarkingResultController::store()        │
   │                                         │
   │ Receives:                               │
   │ [{                                      │
   │   "output": {                           │
   │     "level": "OCS",                     │
   │     "band_level": 2,                    │
   │     "band_explanation": "...",          │
   │     "answered_specific_question": true, │
   │     "assumptions": [...],               │
   │     "points_summary": [...],            │
   │     "citations": [...],                 │
   │     "strengths_extracts": [...],        │
   │     "weaknesses_extracts": [...],       │
   │     "improvement_plan": [...],          │
   │     "structure_ok": true                │
   │   },                                    │
   │   "body": {                             │
   │     "marks_obtained": 12,               │
   │     "total_marks": 20,                  │
   │     "feedback": "..."                   │
   │   }                                     │
   │ }]                                      │
   └────────────┬────────────────────────────┘
                │
                │ Extract & merge data
                │ Dispatch job
                ▼
   ┌─────────────────────────────────────────┐
   │ ProcessMarkingResultJob                 │
   │ (Laravel Queue)                         │
   │                                         │
   │ - Extracts output object                │
   │ - Maps 12 detailed fields               │
   │ - Stores in marking_results table       │
   │ - Updates MockExamAnswer                │
   └────────────┬────────────────────────────┘
                │
                │ Store in database
                ▼

3. DATABASE STORAGE
   ┌─────────────────────────────────────────┐
   │ marking_results table                   │
   ├─────────────────────────────────────────┤
   │ • id                                    │
   │ • mock_exam_answer_id (FK)             │
   │ • marks_obtained                        │
   │ • total_marks                           │
   │ • feedback                              │
   │ • level (OCS/MCS/SCS)                  │
   │ • band_level (1-3)                     │
   │ • band_explanation (TEXT)              │
   │ • answered_specific_question (BOOL)    │
   │ • assumptions (JSON)                    │
   │ • points_summary (JSON)                 │
   │ • genericity_comment (TEXT)            │
   │ • improvement_plan (JSON)               │
   │ • citations (JSON)                      │
   │ • strengths_extracts (JSON)            │
   │ • weaknesses_extracts (JSON)           │
   │ • structure_ok (BOOL)                  │
   │ • created_at                            │
   │ • updated_at                            │
   └────────────┬────────────────────────────┘
                │
                │ Relationships
                ▼
   ┌─────────────────────────────────────────┐
   │ mock_exam_answers table                 │
   │ hasOne → marking_result                 │
   └─────────────────────────────────────────┘

4. DATA RETRIEVAL & DISPLAY

   A. ADMIN VIEW (Filament)
   ┌─────────────────────────────────────────┐
   │ /admin/marking-results                  │
   │ MarkingResultResource                   │
   │                                         │
   │ Features:                               │
   │ • Color-coded band badges               │
   │ • Collapsible sections                  │
   │ • Repeater fields for arrays            │
   │ • Filters (level, band, flags)          │
   │ • Percentage calculations               │
   └─────────────────────────────────────────┘

   B. STUDENT VIEW - INDIVIDUAL ANSWER
   ┌─────────────────────────────────────────┐
   │ GET /api/mock-exams/attempts/{id}      │
   │ MockExamController::studentAttempts()   │
   │                                         │
   │ Eager loads:                            │
   │ • answers.markingResult                 │
   │ • answers.question                      │
   │ • answers.subQuestion                   │
   └────────────┬────────────────────────────┘
                │
                │ JSON response
                ▼
   ┌─────────────────────────────────────────┐
   │ /mock-exams/attempt/:id                │
   │ MockExamAttemptView.vue                 │
   │                                         │
   │ Displays per answer:                    │
   │ ┌─────────────────────────────────────┐ │
   │ │ MarkingResultDisplay.vue            │ │
   │ │                                     │ │
   │ │ • CIMA Level badge                  │ │
   │ │ • Band Level badge (color-coded)    │ │
   │ │ • Marks obtained card               │ │
   │ │ • Quality indicators                │ │
   │ │ • Band explanation                  │ │
   │ │ • Strengths (green section)         │ │
   │ │ • Weaknesses (red section)          │ │
   │ │ • Points summary table              │ │
   │ │ • Improvement plan list             │ │
   │ │ • Citations list                    │ │
   │ │ • Assumptions list                  │ │
   │ └─────────────────────────────────────┘ │
   └─────────────────────────────────────────┘

   C. STUDENT VIEW - EXAM SUMMARY
   ┌─────────────────────────────────────────┐
   │ /mock-exams/attempts/:id/summary       │
   │ MockExamAttemptSummary.vue              │
   │                                         │
   │ Aggregated Display:                     │
   │ • Total score across all questions      │
   │ • Average band level                    │
   │ • Band distribution chart               │
   │ • Quality metrics (% relevant, etc)     │
   │ • Top 5 common strengths                │
   │ • Top 5 priority weaknesses             │
   │ • Overall improvement plan (top 8)      │
   │ • Question-by-question summary list     │
   └─────────────────────────────────────────┘
```

## Color Coding System

### Band Levels
```
Band 1: RED (Identification only)
   └─ bg-red-50, text-red-800, border-red-200

Band 2: AMBER (Partial application)
   └─ bg-amber-50, text-amber-800, border-amber-200

Band 3: GREEN (Justified & practical)
   └─ bg-green-50, text-green-800, border-green-200
```

### Quality Indicators
```
✅ Answered Specific Question: GREEN
❌ Did Not Answer Question: RED

✅ Good Structure: BLUE
❌ Poor Structure: RED
```

### Practicality Ratings (Points Summary)
```
Low Practicality: RED badge
Medium Practicality: AMBER badge
High Practicality: GREEN badge
```

## Data Transformations

### Input Format (from N8N)
```json
[{
  "output": { ...12 detailed fields... },
  "body": { ...basic marks & feedback... }
}]
```

### Processing Steps
1. **Extract array**: `data = data[0]` if array
2. **Merge objects**: `{ ...data.output, ...data.body }`
3. **Map fields**: Schema → Database columns
4. **Calculate marks**: If not provided, derive from total
5. **Store relationships**: Link to MockExamAnswer

### Output Format (to Frontend)
```json
{
  "marking_result": {
    "id": 123,
    "level": "OCS",
    "band_level": 2,
    "marks_obtained": 12,
    "total_marks": 20,
    "answered_specific_question": true,
    "structure_ok": true,
    "strengths_extracts": ["...", "..."],
    "weaknesses_extracts": ["...", "..."],
    "improvement_plan": ["...", "..."],
    "points_summary": [{...}, {...}],
    "citations": [{...}, {...}]
  }
}
```

## Key Relationships

```
Student (1) ──────┐
                  │
                  ▼
MockExamAttempt (1) ───────┐
                           │
                           ▼
            MockExamAnswer (*) ────── (1) MarkingResult
                  │
                  ├──── (1) MockExamQuestion
                  │
                  └──── (1) MockExamSubQuestion
```

## Performance Considerations

1. **Eager Loading**: Use `with(['answers.markingResult'])` to avoid N+1 queries
2. **JSON Indexing**: Consider indexing JSON fields if querying frequently
3. **Caching**: Cache aggregated statistics for exam summaries
4. **Pagination**: Paginate marking results in admin panel for large datasets
5. **Queue Processing**: ProcessMarkingResultJob runs async to avoid blocking
