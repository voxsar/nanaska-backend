<?php

namespace App\Filament\Resources\MarkingPromptResource\Pages;

use App\Filament\Resources\MarkingPromptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarkingPrompts extends ListRecords
{
    protected static string $resource = MarkingPromptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
