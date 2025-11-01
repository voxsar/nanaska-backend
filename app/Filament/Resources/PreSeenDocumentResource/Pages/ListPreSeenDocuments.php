<?php

namespace App\Filament\Resources\PreSeenDocumentResource\Pages;

use App\Filament\Resources\PreSeenDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPreSeenDocuments extends ListRecords
{
    protected static string $resource = PreSeenDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
