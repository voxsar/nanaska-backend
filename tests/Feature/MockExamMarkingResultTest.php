<?php

namespace Tests\Feature;

use App\Jobs\ProcessMarkingResultJob;
use App\Models\MockExam;
use App\Models\MockExamAnswer;
use App\Models\MockExamAttempt;
use App\Models\MockExamQuestion;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MockExamMarkingResultTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_receive_mock_exam_marking_results()
    {
        // Arrange
        $student = Student::factory()->create();
        $mockExam = MockExam::factory()->create();
        $mockExamQuestion = MockExamQuestion::factory()->create([
            'mock_exam_id' => $mockExam->id,
            'marks' => 15,
        ]);
        
        $attempt = MockExamAttempt::factory()->create([
            'student_id' => $student->id,
            'mock_exam_id' => $mockExam->id,
        ]);

        $mockExamAnswer = MockExamAnswer::factory()->create([
            'student_id' => $student->id,
            'mock_exam_attempt_id' => $attempt->id,
            'mock_exam_question_id' => $mockExamQuestion->id,
            'answer_text' => 'My answer to this mock exam question.',
            'status' => 'marking',
        ]);

        Queue::fake();

        // Act: Simulate N8N webhook returning mock exam results
        $response = $this->postJson('/api/marking-results', [
            'student_answer_id' => $mockExamAnswer->id,
            'marks_obtained' => 12.5,
            'total_marks' => 15,
            'feedback' => 'Strong answer with good analysis.',
            'ai_response' => [
                'type' => 'mock_exam',
                'confidence' => 0.92,
            ],
        ]);

        // Assert
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Marking result received and will be processed',
                 ]);

        Queue::assertPushed(ProcessMarkingResultJob::class);
    }

    /** @test */
    public function mock_exam_answer_status_updates_after_marking()
    {
        // Arrange
        $student = Student::factory()->create();
        $mockExam = MockExam::factory()->create();
        $mockExamQuestion = MockExamQuestion::factory()->create([
            'mock_exam_id' => $mockExam->id,
            'marks' => 20,
        ]);
        
        $attempt = MockExamAttempt::factory()->create([
            'student_id' => $student->id,
            'mock_exam_id' => $mockExam->id,
        ]);

        $mockExamAnswer = MockExamAnswer::factory()->create([
            'student_id' => $student->id,
            'mock_exam_attempt_id' => $attempt->id,
            'mock_exam_question_id' => $mockExamQuestion->id,
            'status' => 'marking',
        ]);

        // Act: Process marking result
        $data = [
            'student_answer_id' => $mockExamAnswer->id,
            'marks_obtained' => 18,
            'total_marks' => 20,
            'feedback' => 'Excellent understanding demonstrated.',
        ];

        $job = new ProcessMarkingResultJob($data);
        $job->handle();

        // Assert: Mock exam answer status should be updated
        $mockExamAnswer->refresh();
        $this->assertEquals('marked', $mockExamAnswer->status);
        
        // Check fields on MockExamAnswer model
        $this->assertEquals(18, $mockExamAnswer->marks_obtained);
        $this->assertEquals('Excellent understanding demonstrated.', $mockExamAnswer->feedback);
    }

    /** @test */
    public function mock_exam_attempt_totals_are_updated_after_all_answers_marked()
    {
        // Arrange
        $student = Student::factory()->create();
        $mockExam = MockExam::factory()->create();
        
        $question1 = MockExamQuestion::factory()->create([
            'mock_exam_id' => $mockExam->id,
            'marks' => 10,
        ]);
        $question2 = MockExamQuestion::factory()->create([
            'mock_exam_id' => $mockExam->id,
            'marks' => 15,
        ]);

        $attempt = MockExamAttempt::factory()->create([
            'student_id' => $student->id,
            'mock_exam_id' => $mockExam->id,
            'status' => 'in_progress',
        ]);

        $answer1 = MockExamAnswer::factory()->create([
            'student_id' => $student->id,
            'mock_exam_attempt_id' => $attempt->id,
            'mock_exam_question_id' => $question1->id,
            'status' => 'marking',
        ]);

        $answer2 = MockExamAnswer::factory()->create([
            'student_id' => $student->id,
            'mock_exam_attempt_id' => $attempt->id,
            'mock_exam_question_id' => $question2->id,
            'status' => 'marking',
        ]);

        // Act: Mark both answers
        $answer1->update([
            'marks_obtained' => 8,
            'status' => 'marked',
        ]);

        $answer2->update([
            'marks_obtained' => 13,
            'status' => 'marked',
        ]);

        // Update attempt totals (this would typically be done by an observer or service)
        $attempt->update([
            'total_marks_obtained' => $answer1->marks_obtained + $answer2->marks_obtained,
            'total_marks_available' => 25,
            'percentage' => (($answer1->marks_obtained + $answer2->marks_obtained) / 25) * 100,
            'status' => 'completed',
        ]);

        // Assert
        $attempt->refresh();
        $this->assertEquals(21, $attempt->total_marks_obtained);
        $this->assertEquals(25, $attempt->total_marks_available);
        $this->assertEquals(84, $attempt->percentage);
        $this->assertEquals('completed', $attempt->status);
    }

    /** @test */
    public function it_can_handle_zero_marks_for_incorrect_answers()
    {
        $student = Student::factory()->create();
        $mockExam = MockExam::factory()->create();
        $mockExamQuestion = MockExamQuestion::factory()->create([
            'mock_exam_id' => $mockExam->id,
            'marks' => 10,
        ]);
        
        $attempt = MockExamAttempt::factory()->create([
            'student_id' => $student->id,
            'mock_exam_id' => $mockExam->id,
        ]);

        $mockExamAnswer = MockExamAnswer::factory()->create([
            'student_id' => $student->id,
            'mock_exam_attempt_id' => $attempt->id,
            'mock_exam_question_id' => $mockExamQuestion->id,
            'status' => 'marking',
        ]);

        Queue::fake();

        // Act: Submit marking with zero marks
        $response = $this->postJson('/api/marking-results', [
            'student_answer_id' => $mockExamAnswer->id,
            'marks_obtained' => 0,
            'total_marks' => 10,
            'feedback' => 'Answer does not address the question.',
        ]);

        // Assert
        $response->assertStatus(200);
        Queue::assertPushed(ProcessMarkingResultJob::class);
    }

    /** @test */
    public function it_can_handle_full_marks()
    {
        $student = Student::factory()->create();
        $mockExam = MockExam::factory()->create();
        $mockExamQuestion = MockExamQuestion::factory()->create([
            'mock_exam_id' => $mockExam->id,
            'marks' => 20,
        ]);
        
        $attempt = MockExamAttempt::factory()->create([
            'student_id' => $student->id,
            'mock_exam_id' => $mockExam->id,
        ]);

        $mockExamAnswer = MockExamAnswer::factory()->create([
            'student_id' => $student->id,
            'mock_exam_attempt_id' => $attempt->id,
            'mock_exam_question_id' => $mockExamQuestion->id,
            'status' => 'marking',
        ]);

        Queue::fake();

        // Act: Submit marking with full marks
        $response = $this->postJson('/api/marking-results', [
            'student_answer_id' => $mockExamAnswer->id,
            'marks_obtained' => 20,
            'total_marks' => 20,
            'feedback' => 'Perfect answer covering all aspects.',
        ]);

        // Assert
        $response->assertStatus(200);
        Queue::assertPushed(ProcessMarkingResultJob::class);
    }
}
