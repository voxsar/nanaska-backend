<?php

namespace App\Filament\Resources\MarkingPromptResource\Pages;

use App\Filament\Resources\MarkingPromptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMarkingPrompt extends EditRecord
{
    protected static string $resource = MarkingPromptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
