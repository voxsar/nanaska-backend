<?php

namespace App\Providers;

use App\Models\MockExamAnswer;
use App\Models\PreSeenDocument;
use App\Observers\MockExamAnswerObserver;
use App\Observers\PreSeenDocumentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    MockExamAnswer::observe(MockExamAnswerObserver::class);
    PreSeenDocument::observe(PreSeenDocumentObserver::class);
    }
}
