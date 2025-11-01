<?php

namespace App\Filament\Resources\PreSeenDocumentResource\Pages;

use App\Filament\Resources\PreSeenDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPreSeenDocument extends EditRecord
{
    protected static string $resource = PreSeenDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
