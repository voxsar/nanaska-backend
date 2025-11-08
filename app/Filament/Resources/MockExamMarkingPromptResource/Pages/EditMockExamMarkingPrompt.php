<?php

namespace App\Filament\Resources\MockExamMarkingPromptResource\Pages;

use App\Filament\Resources\MockExamMarkingPromptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMockExamMarkingPrompt extends EditRecord
{
    protected static string $resource = MockExamMarkingPromptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
