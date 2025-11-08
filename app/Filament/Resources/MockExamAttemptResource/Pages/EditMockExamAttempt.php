<?php

namespace App\Filament\Resources\MockExamAttemptResource\Pages;

use App\Filament\Resources\MockExamAttemptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMockExamAttempt extends EditRecord
{
    protected static string $resource = MockExamAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
