<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PracticeQuestion;
use App\Models\PracticeQuestionAttempt;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PracticeExamController extends Controller
{
    /**
     * List all active practice questions
     * Practice questions are standalone questions based on pre-seen documents
     */
    public function index()
    {
        $practiceQuestions = PracticeQuestion::with(['preSeenDocument'])
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $practiceQuestions,
        ]);
    }

    /**
     * Get specific practice question details
     */
    public function show($id)
    {
        $practiceQuestion = PracticeQuestion::with(['preSeenDocument'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $practiceQuestion,
        ]);
    }

    /**
     * Submit answer to a practice question
     */
    public function submitAnswer(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'practice_question_id' => 'required|exists:practice_questions,id',
            'answer_text' => 'required|string',
        ]);

        // Create practice question attempt
        $attempt = PracticeQuestionAttempt::create([
            'student_id' => $validated['student_id'],
            'practice_question_id' => $validated['practice_question_id'],
            'answer_text' => $validated['answer_text'],
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Load relationships for complete response
        $attempt->load(['practiceQuestion', 'student']);

        // TODO: Trigger marking job to send to n8n practice marking endpoint
        // This will be handled by an observer or job similar to mock exam marking

        return response()->json([
            'success' => true,
            'message' => 'Practice question answer submitted successfully',
            'data' => $attempt,
        ]);
    }

    /**
     * Get student attempts for practice questions
     */
    public function studentAttempts($studentId)
    {
        $attempts = PracticeQuestionAttempt::with([
            'practiceQuestion',
            'practiceQuestion.preSeenDocument',
        ])
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attempts,
        ]);
    }
}
