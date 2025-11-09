<?php

namespace Tests\Feature;

use App\Jobs\ProcessMarkingResultJob;
use App\Jobs\TriggerMarkingJob;
use App\Models\MarkingPrompt;
use App\Models\PastPaper;
use App\Models\Question;
use App\Models\Student;
use App\Models\StudentAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EndToEndMarkingFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function complete_marking_flow_from_submission_to_result()
    {
        // Setup: Configure N8N URL
        config(['services.n8n.marking_url' => 'https://n8n-test.com/webhook/marking']);

        // Arrange: Create test data
        $student = Student::factory()->create([
            'email' => 'student@test.com',
            'password' => 'password123',
        ]);

        $pastPaper = PastPaper::factory()->create();
        $question = Question::factory()->create([
            'past_paper_id' => $pastPaper->id,
            'question_text' => 'Explain the concept of double-entry bookkeeping.',
            'marks' => 10,
        ]);

        MarkingPrompt::factory()->create([
            'name' => 'Test Marking Prompt',
            'prompt_text' => 'Please mark this answer based on accuracy and clarity.',
            'is_active' => true,
        ]);

        Http::fake([
            'n8n-test.com/*' => Http::response(['success' => true], 200),
        ]);

        Queue::fake();

        // Step 1: Student submits answer
        $submitResponse = $this->postJson('/api/students/submit-answer', [
            'student_email' => 'student@test.com',
            'student_password' => 'password123',
            'question_id' => $question->id,
            'answer_text' => 'Double-entry bookkeeping is an accounting method where every transaction affects at least two accounts...',
        ]);

        $submitResponse->assertStatus(200)
                      ->assertJsonStructure([
                          'success',
                          'message',
                          'data' => ['id', 'status'],
                      ]);

        $studentAnswerId = $submitResponse->json('data.id');

        // Assert: Answer was created
        $this->assertDatabaseHas('student_answers', [
            'id' => $studentAnswerId,
            'student_id' => $student->id,
            'question_id' => $question->id,
            'status' => 'submitted',
        ]);

        // Step 2: Marking job should be dispatched
        Queue::assertPushed(TriggerMarkingJob::class, function ($job) use ($studentAnswerId) {
            return $job->studentAnswer->id === $studentAnswerId;
        });

        // Step 3: Simulate the marking job execution
        $studentAnswer = StudentAnswer::find($studentAnswerId);
        $markingJob = new TriggerMarkingJob($studentAnswer);
        $markingJob->handle();

        // Assert: Status updated to 'marking'
        $studentAnswer->refresh();
        $this->assertEquals('marking', $studentAnswer->status);

        // Assert: HTTP request was sent to N8N
        Http::assertSent(function ($request) use ($studentAnswerId, $question) {
            return $request->url() === 'https://n8n-test.com/webhook/marking' &&
                   $request['student_answer_id'] === $studentAnswerId &&
                   $request['question_id'] === $question->id &&
                   isset($request['answer_text']) &&
                   isset($request['marking_prompt']);
        });

        // Step 4: N8N processes and sends results back
        Queue::fake(); // Reset queue for this step

        $markingResultResponse = $this->postJson('/api/marking-results', [
            'student_answer_id' => $studentAnswerId,
            'marks_obtained' => 8.5,
            'total_marks' => 10,
            'feedback' => 'Good explanation of double-entry bookkeeping. Could be improved by adding more examples.',
            'ai_response' => [
                'raw_response' => 'Detailed AI analysis...',
                'confidence' => 0.87,
            ],
        ]);

        $markingResultResponse->assertStatus(200)
                             ->assertJson([
                                 'success' => true,
                                 'message' => 'Marking result received and will be processed',
                             ]);

        // Step 5: Process marking result
        Queue::assertPushed(ProcessMarkingResultJob::class);

        $processingJob = new ProcessMarkingResultJob([
            'student_answer_id' => $studentAnswerId,
            'marks_obtained' => 8.5,
            'total_marks' => 10,
            'feedback' => 'Good explanation of double-entry bookkeeping. Could be improved by adding more examples.',
            'ai_response' => [
                'raw_response' => 'Detailed AI analysis...',
                'confidence' => 0.87,
            ],
        ]);
        $processingJob->handle();

        // Step 6: Final assertions
        // Check marking result was created
        $this->assertDatabaseHas('marking_results', [
            'student_answer_id' => $studentAnswerId,
            'student_id' => $student->id,
            'question_id' => $question->id,
            'marks_obtained' => 8.5,
            'total_marks' => 10,
        ]);

        // Check answer status is now 'marked'
        $studentAnswer->refresh();
        $this->assertEquals('marked', $studentAnswer->status);

        // Verify the student can retrieve their result
        $markingResult = $studentAnswer->markingResult;
        $this->assertNotNull($markingResult);
        $this->assertEquals(8.5, $markingResult->marks_obtained);
        $this->assertStringContainsString('Good explanation', $markingResult->feedback);
        $this->assertEquals(0.87, $markingResult->ai_response['confidence']);
    }

    /** @test */
    public function marking_flow_handles_n8n_failure_gracefully()
    {
        // Setup
        config(['services.n8n.marking_url' => 'https://n8n-test.com/webhook/marking']);

        $student = Student::factory()->create([
            'email' => 'student@test.com',
            'password' => 'password123',
        ]);

        $pastPaper = PastPaper::factory()->create();
        $question = Question::factory()->create([
            'past_paper_id' => $pastPaper->id,
        ]);

        MarkingPrompt::factory()->create(['is_active' => true]);

        // Simulate N8N failure
        Http::fake([
            'n8n-test.com/*' => Http::response(['error' => 'Server error'], 500),
        ]);

        Queue::fake();

        // Submit answer
        $submitResponse = $this->postJson('/api/students/submit-answer', [
            'student_email' => 'student@test.com',
            'student_password' => 'password123',
            'question_id' => $question->id,
            'answer_text' => 'Test answer',
        ]);

        $studentAnswerId = $submitResponse->json('data.id');
        $studentAnswer = StudentAnswer::find($studentAnswerId);

        // Execute marking job
        $markingJob = new TriggerMarkingJob($studentAnswer);
        $markingJob->handle();

        // Assert: Answer status should revert to 'submitted' on failure
        $studentAnswer->refresh();
        $this->assertEquals('submitted', $studentAnswer->status);
    }

    /** @test */
    public function marking_flow_works_without_marking_prompt()
    {
        // Setup: No active marking prompt
        config(['services.n8n.marking_url' => 'https://n8n-test.com/webhook/marking']);

        $student = Student::factory()->create([
            'email' => 'student@test.com',
            'password' => 'password123',
        ]);

        $pastPaper = PastPaper::factory()->create();
        $question = Question::factory()->create([
            'past_paper_id' => $pastPaper->id,
        ]);

        Http::fake([
            'n8n-test.com/*' => Http::response(['success' => true], 200),
        ]);

        Queue::fake();

        // Submit answer
        $submitResponse = $this->postJson('/api/students/submit-answer', [
            'student_email' => 'student@test.com',
            'student_password' => 'password123',
            'question_id' => $question->id,
            'answer_text' => 'Test answer',
        ]);

        $studentAnswerId = $submitResponse->json('data.id');
        $studentAnswer = StudentAnswer::find($studentAnswerId);

        // Execute marking job
        $markingJob = new TriggerMarkingJob($studentAnswer);
        $markingJob->handle();

        // Assert: Request sent with null marking_prompt
        Http::assertSent(function ($request) {
            return $request['marking_prompt'] === null;
        });
    }

    /** @test */
    public function marking_url_not_configured_prevents_marking()
    {
        // Setup: No N8N URL configured
        config(['services.n8n.marking_url' => null]);

        $student = Student::factory()->create([
            'email' => 'student@test.com',
            'password' => 'password123',
        ]);

        $pastPaper = PastPaper::factory()->create();
        $question = Question::factory()->create([
            'past_paper_id' => $pastPaper->id,
        ]);

        Http::fake();
        Queue::fake();

        // Submit answer
        $submitResponse = $this->postJson('/api/students/submit-answer', [
            'student_email' => 'student@test.com',
            'student_password' => 'password123',
            'question_id' => $question->id,
            'answer_text' => 'Test answer',
        ]);

        $studentAnswerId = $submitResponse->json('data.id');
        $studentAnswer = StudentAnswer::find($studentAnswerId);

        // Execute marking job
        $markingJob = new TriggerMarkingJob($studentAnswer);
        $markingJob->handle();

        // Assert: No HTTP request was made
        Http::assertNothingSent();

        // Assert: Status reverted to submitted
        $studentAnswer->refresh();
        $this->assertEquals('submitted', $studentAnswer->status);
    }
}
