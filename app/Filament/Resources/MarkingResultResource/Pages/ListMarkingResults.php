<?php

namespace App\Filament\Resources\MarkingResultResource\Pages;

use App\Filament\Resources\MarkingResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarkingResults extends ListRecords
{
    protected static string $resource = MarkingResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
