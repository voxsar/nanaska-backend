<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockExamMarkingPromptHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'mock_exam_marking_prompt_id',
        'prompt_text',
        'version',
        'changed_by',
        'change_reason',
    ];

    public function markingPrompt()
    {
        return $this->belongsTo(MockExamMarkingPrompt::class, 'mock_exam_marking_prompt_id');
    }
}
