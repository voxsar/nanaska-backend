<?php

namespace Tests\Feature;

use App\Jobs\ProcessMarkingResultJob;
use App\Models\MarkingResult;
use App\Models\PastPaper;
use App\Models\Question;
use App\Models\Student;
use App\Models\StudentAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MarkingResultTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_receive_marking_results_from_n8n_webhook()
    {
        // Arrange: Create a student answer
        $student = Student::factory()->create();
        $pastPaper = PastPaper::factory()->create();
        $question = Question::factory()->create([
            'past_paper_id' => $pastPaper->id,
            'marks' => 10,
        ]);
        
        $studentAnswer = StudentAnswer::factory()->create([
            'student_id' => $student->id,
            'question_id' => $question->id,
            'past_paper_id' => $pastPaper->id,
            'answer_text' => 'This is my answer to the question.',
            'status' => 'marking',
        ]);

        Queue::fake();

        // Act: Simulate N8N webhook callback
        $response = $this->postJson('/api/marking-results', [
            'student_answer_id' => $studentAnswer->id,
            'marks_obtained' => 8.5,
            'total_marks' => 10,
            'feedback' => 'Good answer, but missed some key points about...',
            'ai_response' => [
                'raw_response' => 'Full AI response text here',
                'confidence' => 0.85,
            ],
        ]);

        // Assert
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Marking result received and will be processed',
                 ]);

        Queue::assertPushed(ProcessMarkingResultJob::class, function ($job) use ($studentAnswer) {
            return $job->data['student_answer_id'] === $studentAnswer->id;
        });
    }

    /** @test */
    public function it_validates_required_fields_for_marking_results()
    {
        $response = $this->postJson('/api/marking-results', [
            'marks_obtained' => 8.5,
            // Missing student_answer_id and total_marks
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['student_answer_id', 'total_marks']);
    }

    /** @test */
    public function it_validates_student_answer_exists()
    {
        $response = $this->postJson('/api/marking-results', [
            'student_answer_id' => 99999, // Non-existent
            'marks_obtained' => 8.5,
            'total_marks' => 10,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['student_answer_id']);
    }

    /** @test */
    public function it_processes_marking_result_and_creates_record()
    {
        // Arrange
        $student = Student::factory()->create();
        $pastPaper = PastPaper::factory()->create();
        $question = Question::factory()->create([
            'past_paper_id' => $pastPaper->id,
            'marks' => 10,
        ]);
        
        $studentAnswer = StudentAnswer::factory()->create([
            'student_id' => $student->id,
            'question_id' => $question->id,
            'past_paper_id' => $pastPaper->id,
            'status' => 'marking',
        ]);

        $data = [
            'student_answer_id' => $studentAnswer->id,
            'marks_obtained' => 7.5,
            'total_marks' => 10,
            'feedback' => 'Well structured answer with good examples.',
            'ai_response' => ['confidence' => 0.9],
        ];

        // Act: Manually process the job (simulating queue processing)
        $job = new ProcessMarkingResultJob($data);
        $job->handle();

        // Assert: Check marking result was created
        $this->assertDatabaseHas('marking_results', [
            'student_answer_id' => $studentAnswer->id,
            'student_id' => $student->id,
            'question_id' => $question->id,
            'marks_obtained' => 7.5,
            'total_marks' => 10,
            'feedback' => 'Well structured answer with good examples.',
        ]);

        // Assert: Check answer status was updated
        $studentAnswer->refresh();
        $this->assertEquals('marked', $studentAnswer->status);
    }

    /** @test */
    public function it_handles_marking_result_with_optional_fields()
    {
        $student = Student::factory()->create();
        $pastPaper = PastPaper::factory()->create();
        $question = Question::factory()->create([
            'past_paper_id' => $pastPaper->id,
        ]);
        
        $studentAnswer = StudentAnswer::factory()->create([
            'student_id' => $student->id,
            'question_id' => $question->id,
            'past_paper_id' => $pastPaper->id,
            'status' => 'marking',
        ]);

        Queue::fake();

        // Minimal payload without optional fields
        $response = $this->postJson('/api/marking-results', [
            'student_answer_id' => $studentAnswer->id,
            'marks_obtained' => 5,
            'total_marks' => 10,
            // No feedback or ai_response
        ]);

        $response->assertStatus(200);

        Queue::assertPushed(ProcessMarkingResultJob::class);
    }

    /** @test */
    public function it_can_retrieve_marking_results_for_a_student_answer()
    {
        // Arrange
        $student = Student::factory()->create();
        $pastPaper = PastPaper::factory()->create();
        $question = Question::factory()->create([
            'past_paper_id' => $pastPaper->id,
        ]);
        
        $studentAnswer = StudentAnswer::factory()->create([
            'student_id' => $student->id,
            'question_id' => $question->id,
            'past_paper_id' => $pastPaper->id,
        ]);

        $markingResult = MarkingResult::create([
            'student_answer_id' => $studentAnswer->id,
            'student_id' => $student->id,
            'question_id' => $question->id,
            'marks_obtained' => 8.5,
            'total_marks' => 10,
            'feedback' => 'Excellent work!',
            'ai_response' => ['confidence' => 0.95],
        ]);

        // Act
        $retrieved = MarkingResult::where('student_answer_id', $studentAnswer->id)->first();

        // Assert
        $this->assertNotNull($retrieved);
        $this->assertEquals(8.5, $retrieved->marks_obtained);
        $this->assertEquals('Excellent work!', $retrieved->feedback);
        $this->assertEquals(['confidence' => 0.95], $retrieved->ai_response);
    }

    /** @test */
    public function it_stores_ai_response_as_json()
    {
        $student = Student::factory()->create();
        $pastPaper = PastPaper::factory()->create();
        $question = Question::factory()->create([
            'past_paper_id' => $pastPaper->id,
        ]);
        
        $studentAnswer = StudentAnswer::factory()->create([
            'student_id' => $student->id,
            'question_id' => $question->id,
            'past_paper_id' => $pastPaper->id,
        ]);

        $aiResponse = [
            'raw_response' => 'This is the full AI analysis...',
            'confidence' => 0.88,
            'strengths' => ['Good structure', 'Clear examples'],
            'weaknesses' => ['Missing conclusion'],
        ];

        $markingResult = MarkingResult::create([
            'student_answer_id' => $studentAnswer->id,
            'student_id' => $student->id,
            'question_id' => $question->id,
            'marks_obtained' => 7,
            'total_marks' => 10,
            'feedback' => 'Good overall',
            'ai_response' => $aiResponse,
        ]);

        $this->assertIsArray($markingResult->fresh()->ai_response);
        $this->assertEquals($aiResponse, $markingResult->fresh()->ai_response);
    }

    /** @test */
    public function multiple_marking_results_can_exist_for_different_answers()
    {
        $student = Student::factory()->create();
        $pastPaper = PastPaper::factory()->create();
        
        $question1 = Question::factory()->create(['past_paper_id' => $pastPaper->id]);
        $question2 = Question::factory()->create(['past_paper_id' => $pastPaper->id]);

        $answer1 = StudentAnswer::factory()->create([
            'student_id' => $student->id,
            'question_id' => $question1->id,
            'past_paper_id' => $pastPaper->id,
        ]);

        $answer2 = StudentAnswer::factory()->create([
            'student_id' => $student->id,
            'question_id' => $question2->id,
            'past_paper_id' => $pastPaper->id,
        ]);

        MarkingResult::create([
            'student_answer_id' => $answer1->id,
            'student_id' => $student->id,
            'question_id' => $question1->id,
            'marks_obtained' => 8,
            'total_marks' => 10,
            'feedback' => 'Answer 1 feedback',
        ]);

        MarkingResult::create([
            'student_answer_id' => $answer2->id,
            'student_id' => $student->id,
            'question_id' => $question2->id,
            'marks_obtained' => 6,
            'total_marks' => 10,
            'feedback' => 'Answer 2 feedback',
        ]);

        $this->assertCount(2, MarkingResult::where('student_id', $student->id)->get());
    }

    /** @test */
    public function it_handles_decimal_marks_correctly()
    {
        $student = Student::factory()->create();
        $pastPaper = PastPaper::factory()->create();
        $question = Question::factory()->create(['past_paper_id' => $pastPaper->id]);
        
        $studentAnswer = StudentAnswer::factory()->create([
            'student_id' => $student->id,
            'question_id' => $question->id,
            'past_paper_id' => $pastPaper->id,
        ]);

        $markingResult = MarkingResult::create([
            'student_answer_id' => $studentAnswer->id,
            'student_id' => $student->id,
            'question_id' => $question->id,
            'marks_obtained' => 8.75,
            'total_marks' => 10.00,
            'feedback' => 'Very good',
        ]);

        $this->assertEquals('8.75', $markingResult->fresh()->marks_obtained);
        $this->assertEquals('10.00', $markingResult->fresh()->total_marks);
    }
}
