<?php

namespace App\Jobs;

use App\Models\MarkingResult;
use App\Models\MockExamAnswer;
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
            $studentAnswer = MockExamAnswer::findOrFail($this->data['student_answer_id']);
            
            // Extract output data (detailed grading info)
            $output = $this->data['output'] ?? [];
            
            // Calculate marks if not provided directly
            $marksObtained = $this->data['marks_obtained'] ?? 0;
            $totalMarks = $this->data['total_marks'] ?? $this->data['marks'] ?? 0;
            
            // Create marking result with all detailed fields
            MarkingResult::create([
                'student_answer_id' => $studentAnswer->id,
                'student_id' => $studentAnswer->student_id,
                'question_id' => $studentAnswer->mock_exam_question_id,
                'marks_obtained' => $marksObtained,
                'total_marks' => $totalMarks,
                'feedback' => $this->data['feedback'] ?? null,
                'ai_response' => $this->data['ai_response'] ?? $this->data,
                
                // New detailed grading fields from output
                'level' => $output['level'] ?? null,
                'band_level' => $output['band_level'] ?? null,
                'band_explanation' => $output['band_explanation'] ?? null,
                'answered_specific_question' => $output['answered_specific_question'] ?? null,
                'assumptions' => $output['assumptions'] ?? null,
                'points_summary' => $output['points_summary'] ?? null,
                'genericity_comment' => $output['genericity_comment'] ?? null,
                'improvement_plan' => $output['improvement_plan'] ?? null,
                'citations' => $output['citations'] ?? null,
                'strengths_extracts' => $output['strengths_extracts'] ?? null,
                'weaknesses_extracts' => $output['weaknesses_extracts'] ?? null,
                'structure_ok' => $output['structure_ok'] ?? null,
            ]);

            $studentAnswer->update(['status' => 'marked']);

            Log::info('Marking result processed for answer: '.$studentAnswer->id);
        } catch (\Exception $e) {
            Log::error('Error processing marking result: '.$e->getMessage());
            throw $e;
        }
    }
}
