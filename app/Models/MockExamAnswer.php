<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'mock_exam_attempt_id',
        'mock_exam_question_id',
        'student_id',
        'answer_text',
        'file_path',
        'marks_obtained',
        'feedback',
        'ai_response',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'marks_obtained' => 'decimal:2',
        'ai_response' => 'array',
    ];

    public function attempt()
    {
        return $this->belongsTo(MockExamAttempt::class, 'mock_exam_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(MockExamQuestion::class, 'mock_exam_question_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
