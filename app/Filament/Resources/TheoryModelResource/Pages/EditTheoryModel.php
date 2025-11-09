<?php

namespace App\Filament\Resources\TheoryModelResource\Pages;

use App\Filament\Resources\TheoryModelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTheoryModel extends EditRecord
{
    protected static string $resource = TheoryModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
