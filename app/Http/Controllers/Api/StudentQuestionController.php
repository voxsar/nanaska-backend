<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PreSeenDocument;
use App\Models\StudentQuestion;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentQuestionController extends Controller
{
    /**
     * List all questions (optionally filter by student_id).
     */
    public function index(Request $request)
    {
        $query = StudentQuestion::with('preSeenDocument')
            ->orderBy('created_at', 'desc');

        // Filter by student if provided
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $questions = $query->get();

        return response()->json([
            'success' => true,
            'data' => $questions,
        ]);
    }

    /**
     * Ask a question - creates record and dispatches to external webhooks.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'pre_seen_document_id' => 'nullable|exists:pre_seen_documents,id',
            'question_text' => 'required|string|min:5',
        ]);

        $question = StudentQuestion::create([
            'student_id' => $data['student_id'] ?? null,
            'pre_seen_document_id' => $data['pre_seen_document_id'] ?? null,
            'question_text' => $data['question_text'],
            'status' => 'pending',
        ]);

        $payload = [
            'student_question_id' => $question->id,
            'student_id' => $question->student_id,
            'question_text' => $question->question_text,
            'pre_seen_document' => $question->pre_seen_document_id ? PreSeenDocument::find($question->pre_seen_document_id) : null,
            'callback_url' => url('/api/student-questions/'.$question->id.'/response'),
        ];

        $client = new Client(['timeout' => 30, 'http_errors' => false]);
		
        $endpoints = [
            config('services.n8n.ask_preseen_test_url'),
            config('services.n8n.ask_preseen_url'),
        ];

        foreach ($endpoints as $url) {
            try {
                $client->post($url, [
                    'json' => $payload,
                ]);
            } catch (\Throwable $e) {
                Log::error('StudentQuestionController: failed sending question', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Question submitted and awaiting AI response',
            'data' => $question,
        ], 201);
    }

    /**
     * Poll a question status.
     */
    public function show($id)
    {
        $question = StudentQuestion::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $question,
        ]);
    }

    /**
     * Webhook callback to attach AI response.
     * Expected JSON: { bullet_point_answers: [...], quoted_snippets: [...] }
     */
    public function response(Request $request, $id)
    {
        $question = StudentQuestion::findOrFail($id);

        $data = $request->validate([
            'bullet_point_answers' => 'required|array|min:1',
            'bullet_point_answers.*' => 'string',
            'quoted_snippets' => 'required|array|min:1',
            'quoted_snippets.*' => 'string',
        ]);

        $question->update([
            'bullet_point_answers' => $data['bullet_point_answers'],
            'quoted_snippets' => $data['quoted_snippets'],
            'status' => 'answered',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'AI response stored',
            'data' => $question,
        ]);
    }
}
