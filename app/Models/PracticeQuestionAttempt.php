<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticeQuestionAttempt extends Model
{
    use HasFactory;

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

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'ai_response' => 'array',
        'submitted_at' => 'datetime',
        'marked_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function practiceQuestion()
    {
        return $this->belongsTo(PracticeQuestion::class);
    }
}
