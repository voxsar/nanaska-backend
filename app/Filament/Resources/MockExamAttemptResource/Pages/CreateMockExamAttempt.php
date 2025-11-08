<?php

namespace App\Filament\Resources\MockExamAttemptResource\Pages;

use App\Filament\Resources\MockExamAttemptResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMockExamAttempt extends CreateRecord
{
    protected static string $resource = MockExamAttemptResource::class;
}
