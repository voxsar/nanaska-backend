<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * SECURITY NOTE: Plain text passwords are used by design per requirements.
     * Students are a separate entity from Users and do not use Laravel auth.
     * This is NOT recommended for production systems handling sensitive data.
     */
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
