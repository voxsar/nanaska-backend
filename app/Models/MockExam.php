<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'pre_seen_document_id',
        'duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function preSeenDocument()
    {
        return $this->belongsTo(PreSeenDocument::class);
    }

    public function questions()
    {
        return $this->hasMany(MockExamQuestion::class);
    }

    public function attempts()
    {
        return $this->hasMany(MockExamAttempt::class);
    }

    public function markingPrompts()
    {
        return $this->hasMany(MockExamMarkingPrompt::class);
    }
}
