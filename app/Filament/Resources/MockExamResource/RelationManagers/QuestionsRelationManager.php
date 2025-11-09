<?php

namespace App\Filament\Resources\MockExamResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Novadaemon\FilamentPrettyJson\Form\PrettyJsonField;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('question_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('reference_material')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('context')
                    ->required()
                    ->columnSpanFull(),
                PrettyJsonField::make('question_text')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('duration_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('marks')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question_number')
            ->columns([
                Tables\Columns\TextColumn::make('question_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference_material')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('question_text')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('marks')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
    }
}
