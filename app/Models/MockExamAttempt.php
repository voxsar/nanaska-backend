<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'mock_exam_id',
        'started_at',
        'completed_at',
        'total_marks_obtained',
        'total_marks_available',
        'percentage',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_marks_obtained' => 'decimal:2',
        'total_marks_available' => 'decimal:2',
        'percentage' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function mockExam()
    {
        return $this->belongsTo(MockExam::class);
    }

    public function answers()
    {
        return $this->hasMany(MockExamAnswer::class);
    }
}
