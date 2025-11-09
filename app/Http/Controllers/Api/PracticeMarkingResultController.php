<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PracticeQuestionAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PracticeMarkingResultController extends Controller
{
    /**
     * Receive marking results from n8n for practice questions
     * This endpoint is separate from mock exam marking to prevent confusion
     */
    public function receive(Request $request)
    {
        Log::info('Practice marking result received', ['data' => $request->all()]);

        // Handle array format where data comes wrapped in an array
        $data = $request->all();
        
        // If data is an array with output and body, extract them
        if (is_array($data) && isset($data[0])) {
            $data = $data[0];
        }
        
        // Extract the body (contains IDs) and output (contains grading)
        $body = $data['body'] ?? [];
        $output = $data['output'] ?? [];
        
        $attemptId = $body['practice_question_attempt_id'] ?? null;
        
        if (!$attemptId) {
            Log::error('Practice marking result missing attempt ID', ['data' => $data]);
            return response()->json([
                'success' => false,
                'errors' => ['attempt_id' => ['No valid practice question attempt ID provided']],
            ], 422);
        }
        
        // Find the practice question attempt
        $attempt = PracticeQuestionAttempt::find($attemptId);
        
        if (!$attempt) {
            Log::error('Practice question attempt not found', ['attempt_id' => $attemptId]);
            return response()->json([
                'success' => false,
                'errors' => ['attempt_id' => ['Practice question attempt not found']],
            ], 404);
        }
        
        // Update the attempt with marking results
        $attempt->update([
            'status' => 'marked',
            'marks_obtained' => $output['marks_obtained'] ?? null,
            'total_marks' => $output['total_marks'] ?? $attempt->practiceQuestion->marks,
            'feedback' => $output['feedback'] ?? null,
            'ai_response' => $output,
            'marked_at' => now(),
        ]);
        
        Log::info('Practice question marked successfully', [
            'attempt_id' => $attemptId,
            'marks' => $attempt->marks_obtained,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Practice marking result received and processed',
            'data' => $attempt,
        ]);
    }

    /**
     * Get marking results for a specific practice question attempt
     */
    public function attemptResult($attemptId)
    {
        $attempt = PracticeQuestionAttempt::with([
            'practiceQuestion',
            'student',
        ])->find($attemptId);

        if (!$attempt) {
            return response()->json([
                'success' => false,
                'message' => 'Practice question attempt not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $attempt,
        ]);
    }

    /**
     * Get all marking results for a specific student
     */
    public function studentResults($studentId)
    {
        $attempts = PracticeQuestionAttempt::with([
            'practiceQuestion.preSeenDocument',
        ])
            ->where('student_id', $studentId)
            ->where('status', 'marked')
            ->orderBy('marked_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attempts,
        ]);
    }

    /**
     * Get all marking results for a specific practice question
     */
    public function questionResults($questionId)
    {
        $attempts = PracticeQuestionAttempt::with([
            'student',
        ])
            ->where('practice_question_id', $questionId)
            ->where('status', 'marked')
            ->orderBy('marked_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attempts,
        ]);
    }
}
