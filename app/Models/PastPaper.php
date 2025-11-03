<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PastPaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'year',
        'type',
        'description',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function questionPaper()
    {
        return $this->hasOne(QuestionPaper::class);
    }

    public function answerGuide()
    {
        return $this->hasOne(AnswerGuide::class);
    }

    public function markingGuide()
    {
        return $this->hasOne(MarkingGuide::class);
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }
}
