<?php

namespace App\Jobs;

use App\Models\MockExamAnswer;
use App\Models\MockExamMarkingPrompt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
// unique
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TriggerMockExamMarkingJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->mockExamAnswer->id;
    }

    public $mockExamAnswer;

    // max attempts
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(MockExamAnswer $mockExamAnswer)
    {
        $this->mockExamAnswer = $mockExamAnswer;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->mockExamAnswer->update(['status' => 'marking']);

        // Get the active marking prompt for this mock exam
        $markingPrompt = MockExamMarkingPrompt::where('is_active', true)
            ->where('mock_exam_id', $this->mockExamAnswer->question->mockExam->id)
            ->first();

        // Fallback to any active marking prompt for mock exams
        if (! $markingPrompt) {
            $markingPrompt = MockExamMarkingPrompt::where('is_active', true)->first();
        }

        $n8nMarkingUrl = config('services.n8n.marking_url');
		$n8nMarkingTestUrl = config('services.n8n.marking_test_url');

        Log::info([
            'Using N8N Marking URL' => $n8nMarkingUrl,
            'Mock Exam Answer ID' => $this->mockExamAnswer->id,
        ]);

        if (! $n8nMarkingUrl) {
            Log::error('Mock Exam Marking URL not configured');
            $this->mockExamAnswer->update(['status' => 'submitted']);

            return;
        }

        try {
            $response = Http::post($n8nMarkingTestUrl, [
                'mock_exam_answer_id' => $this->mockExamAnswer->id,
                'preseen_document_id' => $this->mockExamAnswer->question->mockExam->pre_seen_document_id,
                'student_id' => $this->mockExamAnswer->student_id,
                'question_id' => $this->mockExamAnswer->mock_exam_question_id,
                'answer_text' => $this->mockExamAnswer->answer_text,
                'question' => $this->mockExamAnswer->question->question_text ?? null,
                'subQuestion' => $this->mockExamAnswer->subQuestion->sub_question_text ?? null,
                'marks' => $this->mockExamAnswer->question->marks ?? 0,
                'marking_prompt' => $markingPrompt->prompt_text ?? null,
                'type' => 'mock_exam',
            ]);

            if ($response->successful()) {
                Log::info('Mock exam marking triggered successfully for answer: '.$this->mockExamAnswer->id);
            } else {
                Log::error('Failed to trigger mock exam marking for answer: '.$this->mockExamAnswer->id);
                $this->mockExamAnswer->update(['status' => 'submitted']);
            }
        } catch (\Exception $e) {
            $response = Http::post($n8nMarkingUrl, [
                'mock_exam_answer_id' => $this->mockExamAnswer->id,
                'preseen_document_id' => $this->mockExamAnswer->question->mockExam->pre_seen_document_id,
                'student_id' => $this->mockExamAnswer->student_id,
                'question_id' => $this->mockExamAnswer->mock_exam_question_id,
                'answer_text' => $this->mockExamAnswer->answer_text,
                'question' => $this->mockExamAnswer->question->question_text ?? null,
                'subQuestion' => $this->mockExamAnswer->subQuestion->sub_question_text ?? null,
                'marks' => $this->mockExamAnswer->question->marks ?? 0,
                'marking_prompt' => $markingPrompt->prompt_text ?? null,
                'type' => 'mock_exam',
            ]);

            if ($response->successful()) {
                Log::info('Mock exam marking triggered successfully for answer: '.$this->mockExamAnswer->id);
            } else {
                Log::error('Failed to trigger mock exam marking for answer: '.$this->mockExamAnswer->id);
                $this->mockExamAnswer->update(['status' => 'submitted']);
            }
            $this->mockExamAnswer->update(['status' => 'submitted']);
        }
    }
}
