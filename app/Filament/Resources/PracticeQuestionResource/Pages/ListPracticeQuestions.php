<?php

namespace App\Filament\Resources\PracticeQuestionResource\Pages;

use App\Filament\Resources\PracticeQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPracticeQuestions extends ListRecords
{
    protected static string $resource = PracticeQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
