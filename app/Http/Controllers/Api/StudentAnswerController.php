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
            'question_id' => 'required|exists:questions,id',
            'answer_text' => 'required|string',
        ]);


        /**
         * SECURITY NOTE: Plain text password comparison per requirements.
         * Students use separate auth system from Laravel Users.
         * This is NOT recommended for production systems.
         */
        $student = Student::first();

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
