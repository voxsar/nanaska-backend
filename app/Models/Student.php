<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'student_id',
        'phone',
    ];

    public function answers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    public function markingResults()
    {
        return $this->hasMany(MarkingResult::class);
    }
}
