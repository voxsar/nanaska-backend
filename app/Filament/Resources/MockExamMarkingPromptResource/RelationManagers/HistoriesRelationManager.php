<?php

namespace App\Filament\Resources\MockExamMarkingPromptResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class HistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'histories';

    protected static ?string $title = 'History';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('prompt_text')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->disabled(),
                Forms\Components\TextInput::make('version')
                    ->required()
                    ->numeric()
                    ->disabled(),
                Forms\Components\TextInput::make('changed_by')
                    ->maxLength(255)
                    ->disabled(),
                Forms\Components\Textarea::make('change_reason')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('version')
            ->columns([
                Tables\Columns\TextColumn::make('version')
                    ->sortable(),
                Tables\Columns\TextColumn::make('prompt_text')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('changed_by')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // History is automatically created, no manual creation
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for history
            ])
            ->defaultSort('version', 'desc');
    }
}
