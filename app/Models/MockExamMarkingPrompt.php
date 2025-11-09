<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockExamMarkingPrompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'mock_exam_id',
        'name',
        'prompt_text',
        'is_active',
        'version',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function mockExam()
    {
        return $this->belongsTo(MockExam::class);
    }

    public function histories()
    {
        return $this->hasMany(MockExamMarkingPromptHistory::class);
    }
}
