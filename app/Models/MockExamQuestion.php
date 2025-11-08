<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockExamQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'mock_exam_id',
        'question_number',
        'question_text',
        'marks',
        'order',
    ];

    public function mockExam()
    {
        return $this->belongsTo(MockExam::class);
    }

    public function answers()
    {
        return $this->hasMany(MockExamAnswer::class);
    }
}
