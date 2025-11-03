<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'past_paper_id',
        'question_number',
        'question_text',
        'marks',
        'order',
    ];

    public function pastPaper()
    {
        return $this->belongsTo(PastPaper::class);
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    public function markingResults()
    {
        return $this->hasMany(MarkingResult::class);
    }
}
