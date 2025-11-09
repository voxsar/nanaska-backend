<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'pre_seen_document_id',
        'question_text',
        'status',
        'bullet_point_answers',
        'quoted_snippets',
    ];

    protected $casts = [
        'bullet_point_answers' => 'array',
        'quoted_snippets' => 'array',
    ];

    public function preSeenDocument()
    {
        return $this->belongsTo(PreSeenDocument::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
