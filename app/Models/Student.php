<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Notifications\StudentResetPasswordNotification;

class Student extends Authenticatable implements CanResetPasswordContract
{
     use HasApiTokens, HasFactory, Notifiable, CanResetPasswordTrait;

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

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new StudentResetPasswordNotification($token));
    }
}
