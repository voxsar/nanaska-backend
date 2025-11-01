<?php

namespace App\Jobs;

use App\Models\MarkingResult;
use App\Models\StudentAnswer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessMarkingResultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $studentAnswer = StudentAnswer::findOrFail($this->data['student_answer_id']);

            MarkingResult::create([
                'student_answer_id' => $studentAnswer->id,
                'student_id' => $studentAnswer->student_id,
                'question_id' => $studentAnswer->question_id,
                'marks_obtained' => $this->data['marks_obtained'] ?? 0,
                'total_marks' => $this->data['total_marks'] ?? 0,
                'feedback' => $this->data['feedback'] ?? null,
                'ai_response' => $this->data['ai_response'] ?? null,
            ]);

            $studentAnswer->update(['status' => 'marked']);

            Log::info('Marking result processed for answer: '.$studentAnswer->id);
        } catch (\Exception $e) {
            Log::error('Error processing marking result: '.$e->getMessage());
            throw $e;
        }
    }
}
