<?php

namespace Tests\Feature;

use App\Models\PracticeQuestion;
use App\Models\PracticeQuestionAttempt;
use App\Models\PreSeenDocument;
use App\Models\Student;
use App\Models\MockExam;
use App\Models\MockExamQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PracticeQuestionSeparationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that practice questions use separate tables from mock exams
     */
    public function test_practice_questions_use_separate_table()
    {
        $preSeenDoc = PreSeenDocument::create([
            'name' => 'Test Pre-Seen Document',
            'file_path' => 'documents/test.pdf',
        ]);

        $practiceQuestion = PracticeQuestion::create([
            'pre_seen_document_id' => $preSeenDoc->id,
            'question_number' => '1',
            'question_text' => 'Test practice question',
            'marks' => '15',
            'order' => 1,
            'is_active' => true,
        ]);

        // Verify it's stored in practice_questions table, not mock_exam_questions
        $this->assertDatabaseHas('practice_questions', [
            'id' => $practiceQuestion->id,
            'question_text' => 'Test practice question',
        ]);

        $this->assertDatabaseMissing('mock_exam_questions', [
            'question_number' => '1',
        ]);
    }

    /**
     * Test that practice questions and mock exam questions don't mix
     */
    public function test_practice_and_mock_questions_are_separate()
    {
        $preSeenDoc = PreSeenDocument::create([
            'name' => 'Test Pre-Seen Document',
            'file_path' => 'documents/test.pdf',
        ]);

        // Create a practice question
        $practiceQuestion = PracticeQuestion::create([
            'pre_seen_document_id' => $preSeenDoc->id,
            'question_number' => '1',
            'question_text' => 'Practice question text',
            'marks' => '15',
            'order' => 1,
            'is_active' => true,
        ]);

        // Create a mock exam with a question
        $mockExam = MockExam::create([
            'name' => 'Test Mock Exam',
            'is_active' => true,
        ]);

        $mockQuestion = MockExamQuestion::create([
            'mock_exam_id' => $mockExam->id,
            'question_number' => '1',
            'context' => 'Mock exam context',
            'marks' => '20',
            'order' => 1,
        ]);

        // Verify they exist in separate tables
        $this->assertDatabaseHas('practice_questions', ['id' => $practiceQuestion->id]);
        $this->assertDatabaseHas('mock_exam_questions', ['id' => $mockQuestion->id]);

        // Verify counts are correct
        $this->assertEquals(1, PracticeQuestion::count());
        $this->assertEquals(1, MockExamQuestion::count());
    }

    /**
     * Test practice questions API endpoint is separate from mock exams
     */
    public function test_practice_questions_api_endpoint_is_separate()
    {
        $preSeenDoc = PreSeenDocument::create([
            'name' => 'Test Pre-Seen Document',
            'file_path' => 'documents/test.pdf',
        ]);

        $practiceQuestion = PracticeQuestion::create([
            'pre_seen_document_id' => $preSeenDoc->id,
            'question_number' => '1',
            'question_text' => 'Test practice question',
            'marks' => '15',
            'order' => 1,
            'is_active' => true,
        ]);

        // Call practice questions endpoint
        $response = $this->getJson('/api/practice-questions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'pre_seen_document_id',
                        'question_number',
                        'question_text',
                        'marks',
                        'pre_seen_document',
                    ]
                ]
            ]);

        // Verify response contains practice question
        $responseData = $response->json('data');
        $this->assertCount(1, $responseData);
        $this->assertEquals('Test practice question', $responseData[0]['question_text']);
    }

    /**
     * Test that practice question attempts use separate table
     */
    public function test_practice_question_attempts_use_separate_table()
    {
        $student = Student::create([
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'password' => 'password123',
            'student_id' => 'STU001',
        ]);

        $preSeenDoc = PreSeenDocument::create([
            'name' => 'Test Pre-Seen Document',
            'file_path' => 'documents/test.pdf',
        ]);

        $practiceQuestion = PracticeQuestion::create([
            'pre_seen_document_id' => $preSeenDoc->id,
            'question_number' => '1',
            'question_text' => 'Test practice question',
            'marks' => '15',
            'order' => 1,
            'is_active' => true,
        ]);

        $attempt = PracticeQuestionAttempt::create([
            'student_id' => $student->id,
            'practice_question_id' => $practiceQuestion->id,
            'answer_text' => 'My answer to the practice question',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Verify it's stored in practice_question_attempts table
        $this->assertDatabaseHas('practice_question_attempts', [
            'id' => $attempt->id,
            'practice_question_id' => $practiceQuestion->id,
        ]);

        // Verify it's NOT in mock_exam_answers or mock_exam_attempts
        $this->assertDatabaseMissing('mock_exam_attempts', [
            'student_id' => $student->id,
        ]);
    }

    /**
     * Test practice question show endpoint returns correct structure
     */
    public function test_practice_question_show_endpoint()
    {
        $preSeenDoc = PreSeenDocument::create([
            'name' => 'Test Pre-Seen Document',
            'file_path' => 'documents/test.pdf',
        ]);

        $practiceQuestion = PracticeQuestion::create([
            'pre_seen_document_id' => $preSeenDoc->id,
            'question_number' => '1',
            'question_text' => 'Test practice question',
            'context' => 'Some context for the question',
            'reference_material' => 'Refer to Exhibit A',
            'marks' => '15',
            'order' => 1,
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/practice-questions/{$practiceQuestion->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'pre_seen_document_id',
                    'question_number',
                    'question_text',
                    'context',
                    'reference_material',
                    'marks',
                    'pre_seen_document',
                ]
            ]);

        // Verify the response contains correct data
        $data = $response->json('data');
        $this->assertEquals('Test practice question', $data['question_text']);
        $this->assertEquals('Some context for the question', $data['context']);
        $this->assertEquals('15', $data['marks']);
    }

    /**
     * Test that practice questions are linked to pre-seen documents, not mock exams
     */
    public function test_practice_questions_linked_to_preseen_not_mock_exam()
    {
        $preSeenDoc = PreSeenDocument::create([
            'name' => 'Test Pre-Seen Document',
            'file_path' => 'documents/test.pdf',
        ]);

        $practiceQuestion = PracticeQuestion::create([
            'pre_seen_document_id' => $preSeenDoc->id,
            'question_number' => '1',
            'question_text' => 'Test practice question',
            'marks' => '15',
            'order' => 1,
            'is_active' => true,
        ]);

        // Verify relationship exists to pre-seen document
        $this->assertNotNull($practiceQuestion->preSeenDocument);
        $this->assertEquals('Test Pre-Seen Document', $practiceQuestion->preSeenDocument->name);

        // Verify practice question does NOT have mock_exam_id
        $this->assertNull($practiceQuestion->mock_exam_id ?? null);
    }
}
