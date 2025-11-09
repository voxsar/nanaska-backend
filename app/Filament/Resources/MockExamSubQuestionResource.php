<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MockExamSubQuestionResource\Pages;
use App\Models\MockExamSubQuestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MockExamSubQuestionResource extends Resource
{
    protected static ?string $model = MockExamSubQuestion::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Mock Exams';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('mock_exam_question_id')
                    ->relationship('question', 'question_number')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Mock Exam Question'),
                Forms\Components\TextInput::make('sub_question_number')
                    ->maxLength(255)
                    ->label('Sub-Question Number (e.g., a, b, c)'),
                Forms\Components\Textarea::make('sub_question_text')
                    ->required()
                    ->rows(4)
                    ->label('Sub-Question Text'),
                Forms\Components\TextInput::make('marks')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Marks'),
                Forms\Components\TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Order'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question.question_number')
                    ->label('Question')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('sub_question_number')
                    ->label('Sub-Question')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('sub_question_text')
                    ->label('Text')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('marks')
                    ->label('Marks')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMockExamSubQuestions::route('/'),
            'create' => Pages\CreateMockExamSubQuestion::route('/create'),
            'edit' => Pages\EditMockExamSubQuestion::route('/{record}/edit'),
        ];
    }
}
