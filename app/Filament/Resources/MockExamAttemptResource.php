<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MockExamAttemptResource\Pages;
use App\Filament\Resources\MockExamAttemptResource\RelationManagers;
use App\Models\MockExamAttempt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MockExamAttemptResource extends Resource
{
    protected static ?string $model = MockExamAttempt::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Mock Exams';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Student Attempts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('mock_exam_id')
                    ->relationship('mockExam', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\DateTimePicker::make('started_at'),
                Forms\Components\DateTimePicker::make('completed_at'),
                Forms\Components\TextInput::make('total_marks_obtained')
                    ->numeric()
                    ->step(0.01),
                Forms\Components\TextInput::make('total_marks_available')
                    ->numeric()
                    ->step(0.01),
                Forms\Components\TextInput::make('percentage')
                    ->numeric()
                    ->step(0.01)
                    ->suffix('%'),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'in_progress' => 'In Progress',
                        'submitted' => 'Submitted',
                        'marked' => 'Marked',
                    ])
                    ->default('in_progress'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mockExam.name')
                    ->label('Mock Exam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_marks_obtained')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_marks_available')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('percentage')
                    ->numeric()
                    ->sortable()
                    ->suffix('%'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'in_progress',
                        'primary' => 'submitted',
                        'success' => 'marked',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'in_progress' => 'In Progress',
                        'submitted' => 'Submitted',
                        'marked' => 'Marked',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AnswersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMockExamAttempts::route('/'),
            'create' => Pages\CreateMockExamAttempt::route('/create'),
            'view' => Pages\ViewMockExamAttempt::route('/{record}'),
            'edit' => Pages\EditMockExamAttempt::route('/{record}/edit'),
        ];
    }
}
