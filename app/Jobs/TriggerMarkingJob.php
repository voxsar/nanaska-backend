<?php

namespace App\Jobs;

use App\Models\MarkingPrompt;
use App\Models\StudentAnswer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TriggerMarkingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $studentAnswer;

    /**
     * Create a new job instance.
     */
    public function __construct(StudentAnswer $studentAnswer)
    {
        $this->studentAnswer = $studentAnswer;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->studentAnswer->update(['status' => 'marking']);

        $markingPrompt = MarkingPrompt::where('is_active', true)->first();

        $n8nUrl = config('services.n8n.marking_url');

        if (!$n8nUrl) {
            Log::error('N8N Marking URL not configured');
            $this->studentAnswer->update(['status' => 'submitted']);
            return;
        }

        try {
            $response = Http::post($n8nUrl, [
                'student_answer_id' => $this->studentAnswer->id,
                'student_id' => $this->studentAnswer->student_id,
                'question_id' => $this->studentAnswer->question_id,
                'answer_text' => $this->studentAnswer->answer_text,
                'question' => $this->studentAnswer->question->question_text ?? null,
                'marks' => $this->studentAnswer->question->marks ?? 0,
                'marking_prompt' => $markingPrompt->prompt_text ?? null,
            ]);

            if ($response->successful()) {
                Log::info('Marking triggered successfully for answer: ' . $this->studentAnswer->id);
            } else {
                Log::error('Failed to trigger marking for answer: ' . $this->studentAnswer->id);
                $this->studentAnswer->update(['status' => 'submitted']);
            }
        } catch (\Exception $e) {
            Log::error('Error triggering marking: ' . $e->getMessage());
            $this->studentAnswer->update(['status' => 'submitted']);
        }
    }
}
