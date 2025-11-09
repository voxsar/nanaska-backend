<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MockExam;
use Illuminate\Http\Request;

class PracticeExamController extends Controller
{
    /**
     * List all practice exams
     * Practice exams are mock exams marked for practice
     */
    public function index()
    {
        // For now, we'll use mock exams as practice exams
        // You can add a 'is_practice' flag to mock_exams table if you want to separate them
        $practiceExams = MockExam::with(['questions', 'preSeenDocument', 'questions.subQuestions'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $practiceExams,
        ]);
    }

    /**
     * Get questions for a practice exam
     */
    public function questions($id)
    {
        $exam = MockExam::findOrFail($id);
        $questions = $exam->questions()
            ->with('subQuestions')
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'exam' => $exam,
                'questions' => $questions,
            ],
        ]);
    }
}
