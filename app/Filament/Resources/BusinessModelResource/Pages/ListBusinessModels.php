<?php

namespace App\Filament\Resources\BusinessModelResource\Pages;

use App\Filament\Resources\BusinessModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBusinessModels extends ListRecords
{
    protected static string $resource = BusinessModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
