<?php

namespace App\Http\Controllers\Api;

use App\Models\MockExam;
use App\Models\MockExamQuestion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MockExamQuestionController extends Controller
{
    //create from post
	public function store(MockExam $mockExam, Request $request)
	{

		$output = $request->input('output');

		$mockExam->name = $output['exam_structure']['title'] ?? $mockExam->name;
		$mockExam->save();

		$questions = $output['questions'];
		foreach ($questions as $key => $questionData) {
			$mockExamQuestion = new MockExamQuestion();
			$mockExamQuestion->question_number = $key + 1;
			$mockExamQuestion->full_json = $questionData;
			$mockExamQuestion->order = $questionData['order'] ?? null;
			$mockExamQuestion->reference_material = $questionData['reference_material'] ?? null;
			//context field
			$mockExamQuestion->context = $questionData['context'] ?? null;
			$mockExamQuestion->question_text = $questionData['subtasks'];

			$mockExamQuestion->mock_exam_id = $mockExam->id;
			$mockExamQuestion->save();
		}		

		return response()->json($mockExamQuestion, 201);
	}

}
