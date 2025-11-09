<?php

namespace App\Filament\Resources\MockExamResource\Pages;

use App\Filament\Resources\MockExamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMockExams extends ListRecords
{
    protected static string $resource = MockExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
