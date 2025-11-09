<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkingResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_answer_id',
        'student_id',
        'question_id',
        'marks_obtained',
        'total_marks',
        'feedback',
        'ai_response',
        // New detailed grading fields
        'level',
        'band_level',
        'band_explanation',
        'answered_specific_question',
        'assumptions',
        'points_summary',
        'genericity_comment',
        'improvement_plan',
        'citations',
        'strengths_extracts',
        'weaknesses_extracts',
        'structure_ok',
    ];

    protected $casts = [
        'ai_response' => 'array',
        'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'answered_specific_question' => 'boolean',
        'structure_ok' => 'boolean',
        'band_level' => 'integer',
        'assumptions' => 'array',
        'points_summary' => 'array',
        'improvement_plan' => 'array',
        'citations' => 'array',
        'strengths_extracts' => 'array',
        'weaknesses_extracts' => 'array',
    ];

    public function studentAnswer()
    {
        return $this->belongsTo(StudentAnswer::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
