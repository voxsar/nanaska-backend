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
		"context",
        'question_number',
        'question_text',
		'duration_minutes',
        'marks',
        'order',
		'full_json',
    ];

	protected $casts = [
		'question_text' => 'array',
		'full_json' => 'array',
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
