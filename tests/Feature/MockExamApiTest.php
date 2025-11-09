<?php

namespace Tests\Feature;

use App\Models\MockExam;
use App\Models\MockExamQuestion;
use App\Models\MockExamSubQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MockExamApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_mock_exam_questions_endpoint_returns_sub_questions()
    {
        // Create a mock exam with questions and sub-questions
        $exam = MockExam::create([
            'name' => 'Test Mock Exam',
            'description' => 'Test Description',
            'duration_minutes' => 120,
            'is_active' => true,
        ]);

        $question = $exam->questions()->create([
            'question_number' => '1',
            'context' => 'This is the context for question 1',
            'reference_material' => 'Reference material here',
            'duration_minutes' => 30,
            'marks' => '20',
            'order' => 1,
        ]);

        $subQuestion1 = $question->subQuestions()->create([
            'sub_question_number' => 'a',
            'sub_question_text' => 'What is accounting?',
            'marks' => '5',
            'order' => 1,
        ]);

        $subQuestion2 = $question->subQuestions()->create([
            'sub_question_number' => 'b',
            'sub_question_text' => 'What is finance?',
            'marks' => '8',
            'order' => 2,
        ]);

        // Call the API
        $response = $this->getJson("/api/mock-exams/{$exam->id}/questions");

        // Assert response structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'mock_exam',
                    'questions' => [
                        '*' => [
                            'id',
                            'question_number',
                            'context',
                            'reference_material',
                            'marks',
                            'sub_questions' => [
                                '*' => [
                                    'id',
                                    'sub_question_number',
                                    'sub_question_text',
                                    'marks',
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

        // Assert specific values
        $responseData = $response->json('data');
        $this->assertEquals('20', $responseData['questions'][0]['marks']);
        $this->assertCount(2, $responseData['questions'][0]['sub_questions']);
        $this->assertEquals('5', $responseData['questions'][0]['sub_questions'][0]['marks']);
        $this->assertEquals('8', $responseData['questions'][0]['sub_questions'][1]['marks']);
    }

    public function test_mock_exam_show_endpoint_includes_sub_questions()
    {
        // Create a mock exam with questions and sub-questions
        $exam = MockExam::create([
            'name' => 'Test Mock Exam',
            'description' => 'Test Description',
            'duration_minutes' => 120,
            'is_active' => true,
        ]);

        $question = $exam->questions()->create([
            'question_number' => '1',
            'context' => 'Context text',
            'reference_material' => 'Reference text',
            'duration_minutes' => 30,
            'marks' => '15',
            'order' => 1,
        ]);

        $question->subQuestions()->create([
            'sub_question_number' => 'a',
            'sub_question_text' => 'Sub-question a',
            'marks' => '10',
            'order' => 1,
        ]);

        // Call the API
        $response = $this->getJson("/api/mock-exams/{$exam->id}");

        // Assert response includes sub_questions
        $response->assertStatus(200)
            ->assertJsonPath('data.questions.0.sub_questions.0.marks', '10');
    }

    public function test_marks_can_be_stored_as_string()
    {
        $exam = MockExam::create([
            'name' => 'Test Mock Exam',
            'is_active' => true,
        ]);

        $question = $exam->questions()->create([
            'question_number' => '2',
            'context' => 'Context',
            'marks' => '2a',  // String mark
            'order' => 1,
        ]);

        $subQuestion = $question->subQuestions()->create([
            'sub_question_number' => 'i',
            'sub_question_text' => 'Sub-question',
            'marks' => '2b',  // String mark
            'order' => 1,
        ]);

        // Verify marks are stored as strings
        $this->assertEquals('2a', $question->marks);
        $this->assertEquals('2b', $subQuestion->marks);
        $this->assertIsString($question->marks);
        $this->assertIsString($subQuestion->marks);
    }
}
