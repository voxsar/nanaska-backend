<?php

namespace App\Filament\Resources\MockExamAttemptResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'answers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('mock_exam_question_id')
                    ->relationship('question', 'question_number')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Textarea::make('answer_text')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('file_path')
                    ->directory('mock-exam-answers')
                    ->nullable(),
                Forms\Components\TextInput::make('marks_obtained')
                    ->numeric()
                    ->step(0.01)
                    ->nullable(),
                Forms\Components\Textarea::make('feedback')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->nullable(),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'pending' => 'Pending',
                        'submitted' => 'Submitted',
                        'marking' => 'Marking',
                        'marked' => 'Marked',
                    ])
                    ->default('pending'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('question.question_number')
                    ->label('Question')
                    ->sortable(),
                Tables\Columns\TextColumn::make('answer_text')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('marks_obtained')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('question.marks')
                    ->label('Total Marks')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'pending',
                        'warning' => 'submitted',
                        'primary' => 'marking',
                        'success' => 'marked',
                    ]),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
