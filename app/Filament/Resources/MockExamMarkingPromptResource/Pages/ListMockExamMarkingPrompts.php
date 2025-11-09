<?php

namespace App\Filament\Resources\MockExamMarkingPromptResource\Pages;

use App\Filament\Resources\MockExamMarkingPromptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMockExamMarkingPrompts extends ListRecords
{
    protected static string $resource = MockExamMarkingPromptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
