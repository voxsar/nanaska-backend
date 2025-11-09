<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MockExamMarkingPromptResource\Pages;
use App\Filament\Resources\MockExamMarkingPromptResource\RelationManagers;
use App\Models\MockExam;
use App\Models\MockExamMarkingPrompt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MockExamMarkingPromptResource extends Resource
{
    protected static ?string $model = MockExamMarkingPrompt::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Mock Exams';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Marking Prompts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('mock_exam_id')
                    ->label('Mock Exam (Optional)')
                    ->options(MockExam::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Leave empty for a general marking prompt'),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('prompt_text')
                    ->label('Prompt Text')
                    ->required()
                    ->maxLength(65535)
                    ->rows(10)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->required(),
                Forms\Components\TextInput::make('version')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mockExam.name')
                    ->label('Mock Exam')
                    ->searchable()
                    ->sortable()
                    ->default('General'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('version')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('histories_count')
                    ->counts('histories')
                    ->label('History Count')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\HistoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMockExamMarkingPrompts::route('/'),
            'create' => Pages\CreateMockExamMarkingPrompt::route('/create'),
            'edit' => Pages\EditMockExamMarkingPrompt::route('/{record}/edit'),
        ];
    }
}
