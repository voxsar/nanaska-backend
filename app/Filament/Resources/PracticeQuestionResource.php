<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PracticeQuestionResource\Pages;
use App\Filament\Resources\PracticeQuestionResource\RelationManagers;
use App\Models\PracticeQuestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PracticeQuestionResource extends Resource
{
    protected static ?string $model = PracticeQuestion::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Practice Questions';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Practice Questions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pre_seen_document_id')
                    ->relationship('preSeenDocument', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Pre-Seen Document'),
                Forms\Components\TextInput::make('question_number')
                    ->required()
                    ->maxLength(255)
                    ->label('Question Number'),
                Forms\Components\Textarea::make('question_text')
                    ->required()
                    ->rows(4)
                    ->label('Question Text'),
                Forms\Components\Textarea::make('context')
                    ->rows(4)
                    ->label('Context (Optional)'),
                Forms\Components\Textarea::make('reference_material')
                    ->rows(4)
                    ->label('Reference Material (Optional)'),
                Forms\Components\TextInput::make('marks')
                    ->required()
                    ->maxLength(50)
                    ->default('0')
                    ->label('Marks'),
                Forms\Components\TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Display Order'),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true)
                    ->label('Active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question_number')
                    ->label('Question #')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('preSeenDocument.name')
                    ->label('Pre-Seen Document')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('question_text')
                    ->label('Question')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('marks')
                    ->label('Marks')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Order')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All questions')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPracticeQuestions::route('/'),
            'create' => Pages\CreatePracticeQuestion::route('/create'),
            'edit' => Pages\EditPracticeQuestion::route('/{record}/edit'),
        ];
    }
}
