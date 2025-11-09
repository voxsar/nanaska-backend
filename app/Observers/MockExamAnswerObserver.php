<?php

namespace App\Observers;

use App\Jobs\TriggerMockExamMarkingJob;
use App\Models\MockExamAnswer;

class MockExamAnswerObserver
{
    /**
     * Handle the MockExamAnswer "created" event.
     */
    public function created(MockExamAnswer $mockExamAnswer): void
    {
        // Trigger marking job when answer is created and status is submitted
        if ($mockExamAnswer->status === 'submitted') {
            TriggerMockExamMarkingJob::dispatch($mockExamAnswer);
        }
    }

    /**
     * Handle the MockExamAnswer "updated" event.
     */
    public function updated(MockExamAnswer $mockExamAnswer): void
    {
        // Trigger marking job when answer status changes to submitted
        if ($mockExamAnswer->isDirty('status') && $mockExamAnswer->status === 'submitted') {
            TriggerMockExamMarkingJob::dispatch($mockExamAnswer);
        }
    }

    /**
     * Handle the MockExamAnswer "deleted" event.
     */
    public function deleted(MockExamAnswer $mockExamAnswer): void
    {
        //
    }

    /**
     * Handle the MockExamAnswer "restored" event.
     */
    public function restored(MockExamAnswer $mockExamAnswer): void
    {
        //
    }

    /**
     * Handle the MockExamAnswer "force deleted" event.
     */
    public function forceDeleted(MockExamAnswer $mockExamAnswer): void
    {
        //
    }
}
