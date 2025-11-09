<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockExamQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'mock_exam_id',
        'reference_material',
        'context',
        'question_number',
        'duration_minutes',
        'marks',
        'order',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'marks' => 'string',
        'order' => 'integer',
    ];

    public function mockExam()
    {
        return $this->belongsTo(MockExam::class);
    }

    public function subQuestions()
    {
        return $this->hasMany(MockExamSubQuestion::class, 'mock_exam_question_id');
    }

    public function answers()
    {
        return $this->hasMany(MockExamAnswer::class);
    }
}
