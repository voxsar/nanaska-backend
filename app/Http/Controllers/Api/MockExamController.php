<?php

namespace App\Http\Controllers\Api;

use Log;
use App\Http\Controllers\Controller;
use App\Models\MockExam;
use App\Models\MockExamAnswer;
use App\Models\MockExamAttempt;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MockExamController extends Controller
{
    // send mock exam record including pdf to https://automation.artslabcreatives.com/webhook-test/mock-exams
    public function sendMockExamRecord(MockExam $mockExam)
    {
        Log::info('Sending mock exam record', ['mockExamId' => $mockExam->id]);
        $client = new \GuzzleHttp\Client;
		
        $questionUrl = config('services.n8n.question_url');
		$questionTestUrl = config('services.n8n.question_test_url');

		try{
			Log::info('Sending to test URL', ['testUrl' => $questionTestUrl, 'mockExamId' => $mockExam->id]);
			$response = $client->post($questionTestUrl, [
				'multipart' => [
					[
						'name' => 'file',
						'contents' => fopen(storage_path('app/public/'.$mockExam->file_path), 'r'),
						'filename' => Str::snake($mockExam->name),
					],
					[
						'name' => 'mockExamData',
						'contents' => json_encode($mockExam),
					],
				],
			]);
		}catch(\Exception $e){
			Log::error('Error sending mock exam to test URL', [
				'error' => $e->getMessage(),
				'mockExamId' => $mockExam->id,
			]);
			$response = $client->post($questionUrl, [
				'multipart' => [
					[
						'name' => 'file',
						'contents' => fopen(storage_path('app/public/'.$mockExam->file_path), 'r'),
						'filename' => Str::snake($mockExam->name),
					],
					[
						'name' => 'mockExamData',
						'contents' => json_encode($mockExam),
					],
				],
			]);
		}

        return $response;
    }

    /**
     * List all active mock exams
     */
    public function index()
    {
        $mockExams = MockExam::with(['questions', 'preSeenDocument', 'questions.subQuestions'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $mockExams,
        ]);
    }

    /**
     * Get specific mock exam details
     */
    public function show($id)
    {
        $mockExam = MockExam::with(['questions.subQuestions', 'preSeenDocument', 'markingPrompts' => function ($query) {
            $query->where('is_active', true);
        }])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $mockExam,
        ]);
    }

    /**
     * Get questions for a mock exam
     */
    public function questions($id)
    {
        $mockExam = MockExam::findOrFail($id);
        $questions = $mockExam->questions()
            ->with('subQuestions')
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'mock_exam' => $mockExam,
                'questions' => $questions,
            ],
        ]);
    }

    /**
     * Submit answer to a mock exam question
     * Supports both creating new answers and updating existing ones
     * Note: CSRF protection is disabled for API routes
     */
    public function submitAnswer(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'mock_exam_id' => 'required|exists:mock_exams,id',
            'mock_exam_question_id' => 'required|exists:mock_exam_questions,id',
            'mock_exam_sub_question_id' => 'nullable|exists:mock_exam_sub_questions,id',
            'mock_exam_attempt_id' => 'nullable|exists:mock_exam_attempts,id',
            'answer_id' => 'nullable|exists:mock_exam_answers,id',
            'answer_text' => 'required|string',
        ]);

        // Create or get existing attempt
        if (isset($validated['mock_exam_attempt_id'])) {
            $attempt = MockExamAttempt::findOrFail($validated['mock_exam_attempt_id']);
        } else {
            $attempt = MockExamAttempt::create([
                'student_id' => $validated['student_id'],
                'mock_exam_id' => $validated['mock_exam_id'],
                'started_at' => now(),
                'status' => 'in_progress',
            ]);
        }

        // Create or update answer
        if (isset($validated['answer_id'])) {
            // Update existing answer
            $answer = MockExamAnswer::findOrFail($validated['answer_id']);
            $answer->update([
                'answer_text' => $validated['answer_text'],
                'submitted_at' => now(),
            ]);
            $message = 'Answer updated successfully';
        } else {
            // Create new answer
            $answer = MockExamAnswer::create([
                'mock_exam_attempt_id' => $attempt->id,
                'mock_exam_question_id' => $validated['mock_exam_question_id'],
                'mock_exam_sub_question_id' => $validated['mock_exam_sub_question_id'] ?? null,
                'student_id' => $validated['student_id'],
                'answer_text' => $validated['answer_text'],
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);
            $message = 'Answer submitted successfully';
        }

        // Observer will automatically trigger marking job on create/update

        // Load relationships for complete answer object
        $answer->load(['attempt', 'question', 'subQuestion', 'student']);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $answer,
        ]);
    }

    /**
     * Get student attempts for mock exams
     */
    public function studentAttempts($studentId)
    {
        $attempts = MockExamAttempt::with([
            'mockExam',
            'answers.question',
            'answers.subQuestion',
            'answers.markingResult',
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
