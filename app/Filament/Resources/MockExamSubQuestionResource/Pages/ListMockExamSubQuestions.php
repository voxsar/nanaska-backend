<?php

namespace App\Filament\Resources\MockExamSubQuestionResource\Pages;

use App\Filament\Resources\MockExamSubQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMockExamSubQuestions extends ListRecords
{
    protected static string $resource = MockExamSubQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
