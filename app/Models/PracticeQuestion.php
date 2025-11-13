<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticeQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'pre_seen_document_id',
        'question_number',
        'question_text',
        'context',
        'reference_material',
        'marks',
        'order',
        'is_active',
    ];

    protected $casts = [
        'marks' => 'string',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function preSeenDocument()
    {
        return $this->belongsTo(PreSeenDocument::class);
    }

    public function attempts()
    {
        return $this->hasMany(PracticeQuestionAttempt::class);
    }
}
