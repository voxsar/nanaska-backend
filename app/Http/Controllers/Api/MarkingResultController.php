<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessMarkingResultJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MarkingResultController extends Controller
{
    public function receive(Request $request)
    {
        // Handle array format where data comes wrapped in an array
        $data = $request->all();
        
        // If data is an array with output and body, extract them
        if (is_array($data) && isset($data[0])) {
            $data = $data[0];
        }
        
        // Extract the body (contains IDs) and output (contains grading)
        $body = $data['body'] ?? [];
        $output = $data['output'] ?? [];
        
        // Determine which ID field is present
        $answerIdField = $body['mock_exam_answer_id'] ?? $body['student_answer_id'] ?? null;
        
        if (!$answerIdField) {
            return response()->json([
                'success' => false,
                'errors' => ['answer_id' => ['No valid answer ID provided']],
            ], 422);
        }
        
        // Merge body and output for processing
        $processData = array_merge($body, [
            'student_answer_id' => $answerIdField,
            'output' => $output,
        ]);
        
        ProcessMarkingResultJob::dispatch($processData);

        return response()->json([
            'success' => true,
            'message' => 'Marking result received and will be processed',
        ]);
    }
}
