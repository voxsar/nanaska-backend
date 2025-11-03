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
    ];

    protected $casts = [
        'ai_response' => 'array',
        'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2',
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
