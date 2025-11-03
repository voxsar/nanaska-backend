<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionPaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'past_paper_id',
        'file_path',
        'description',
    ];

    public function pastPaper()
    {
        return $this->belongsTo(PastPaper::class);
    }
}
