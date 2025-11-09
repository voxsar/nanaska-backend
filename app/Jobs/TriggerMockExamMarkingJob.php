<?php

namespace App\Jobs;

use App\Models\MockExamAnswer;
use App\Models\MockExamMarkingPrompt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TriggerMockExamMarkingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mockExamAnswer;

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
        if (!$markingPrompt) {
            $markingPrompt = MockExamMarkingPrompt::where('is_active', true)->first();
        }

        $n8nUrl = config('services.n8n.marking_url');

        if (!$n8nUrl) {
            Log::error('Mock Exam Marking URL not configured');
            $this->mockExamAnswer->update(['status' => 'submitted']);
            return;
        }

        try {
            $response = Http::post($n8nUrl, [
                'mock_exam_answer_id' => $this->mockExamAnswer->id,
                'student_id' => $this->mockExamAnswer->student_id,
                'question_id' => $this->mockExamAnswer->mock_exam_question_id,
                'answer_text' => $this->mockExamAnswer->answer_text,
                'question' => $this->mockExamAnswer->question->question_text ?? null,
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
            Log::error('Error triggering mock exam marking: '.$e->getMessage());
            $this->mockExamAnswer->update(['status' => 'submitted']);
        }
    }
}
