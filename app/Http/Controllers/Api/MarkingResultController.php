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
        $validator = Validator::make($request->all(), [
            'student_answer_id' => 'required|exists:student_answers,id',
            'marks_obtained' => 'required|numeric',
            'total_marks' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        ProcessMarkingResultJob::dispatch($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Marking result received and will be processed',
        ]);
    }
}
