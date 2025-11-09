<?php

namespace App\Filament\Resources\MarkingResultResource\Pages;

use App\Filament\Resources\MarkingResultResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMarkingResult extends EditRecord
{
    protected static string $resource = MarkingResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
