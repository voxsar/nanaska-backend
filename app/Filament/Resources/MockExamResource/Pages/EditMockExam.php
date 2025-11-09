<?php

namespace App\Filament\Resources\MockExamResource\Pages;

use App\Filament\Resources\MockExamResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditMockExam extends EditRecord
{
    protected static string $resource = MockExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
			Action::make('uploadPdf')
				->label('Upload PDF')
				->action(function () {
					// upload record to the upload PDF route not redirect
					$mockExamId = $this->record->id;
					$response = app('App\Http\Controllers\Api\MockExamController')->sendMockExamRecord($this->record);
					if ($response->getStatusCode() == 200) {
						$this->notify('success', 'Mock Exam PDF uploaded successfully.');
					} else {
						$this->notify('danger', 'Failed to upload Mock Exam PDF.');
					}
				})
				->color('secondary'),
        ];
    }
}
