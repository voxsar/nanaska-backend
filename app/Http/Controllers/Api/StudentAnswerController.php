<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\TriggerMarkingJob;
use App\Models\Question;
use App\Models\Student;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentAnswerController extends Controller
{
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_email' => 'required|email',
            'student_password' => 'required',
            'question_id' => 'required|exists:questions,id',
            'answer_text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $student = Student::where('email', $request->student_email)
            ->where('password', $request->student_password)
            ->first();

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid student credentials',
            ], 401);
        }

        $question = Question::findOrFail($request->question_id);

        $answer = StudentAnswer::create([
            'student_id' => $student->id,
            'question_id' => $request->question_id,
            'past_paper_id' => $question->past_paper_id,
            'answer_text' => $request->answer_text,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        TriggerMarkingJob::dispatch($answer);

        return response()->json([
            'success' => true,
            'message' => 'Answer submitted successfully',
            'data' => $answer,
        ], 201);
    }
}
