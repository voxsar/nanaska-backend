<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockExamSubQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'mock_exam_question_id',
        'sub_question_number',
        'sub_question_text',
        'marks',
        'order',
    ];

    protected $casts = [
        'marks' => 'integer',
        'order' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(MockExamQuestion::class, 'mock_exam_question_id');
    }

    public function answers()
    {
        return $this->hasMany(MockExamAnswer::class, 'mock_exam_sub_question_id');
    }
}
