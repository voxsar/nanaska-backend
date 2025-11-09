<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MockExam;
use App\Models\MockExamQuestion;
use App\Models\MockExamSubQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MockExamQuestionController extends Controller
{
    // create from post
    public function store(MockExam $mockExam, Request $request)
    {
        DB::beginTransaction();
        Log::info($request->all());
        $output = $request->input('output');

        $mockExam->name = $output['exam_structure']['title'] ?? $mockExam->name;
        $mockExam->save();

        // Get reference materials (note: API sends "referance_materials" with typo)
        $referenceMaterials = $output['referance_materials'] ?? $output['reference_materials'] ?? [];

        // Store all reference materials as a single string for easy access across all questions
        $allReferenceMaterials = ! empty($referenceMaterials)
            ? implode("\n\n---\n\n", $referenceMaterials)
            : null;

        $questions = $output['questions'];
        foreach ($questions as $key => $questionData) {
            $mockExamQuestion = new MockExamQuestion;
            $mockExamQuestion->question_number = $key + 1;
            $mockExamQuestion->order = $questionData['order'] ?? ($key + 1);

            // Store all reference materials for each question
            // This allows all questions to have access to all reference materials
            $mockExamQuestion->reference_material = $allReferenceMaterials;

            $mockExamQuestion->context = $questionData['context'] ?? null;
            $mockExamQuestion->duration_minutes = $questionData['duration_minutes'] ?? 0;

            // Calculate total marks from subtasks
            $totalMarks = 0;

            $mockExamQuestion->marks = $totalMarks;

            $mockExamQuestion->mock_exam_id = $mockExam->id;
            $mockExamQuestion->save();

            // Create sub-questions from subtasks
            if (isset($questionData['subtasks']) && is_array($questionData['subtasks'])) {
                foreach ($questionData['subtasks'] as $subKey => $subtask) {
                    $subQuestion = new MockExamSubQuestion;
                    $subQuestion->mock_exam_question_id = $mockExamQuestion->id;
                    $subQuestion->sub_question_number = $subtask['sub_question_number'] ?? chr(97 + $subKey); // a, b, c, etc.
                    $subQuestion->sub_question_text = $subtask['question'] ?? '';
                    $subQuestion->marks = 0;
                    $subQuestion->order = $subKey + 1;
                    $subQuestion->save();
                }
            }
        }

        DB::commit();

        return response()->json([
            'message' => 'Mock exam questions created successfully',
            'mock_exam_id' => $mockExam->id,
        ], 201);

    }
}
