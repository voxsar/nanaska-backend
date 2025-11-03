<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PastPaperResource\Pages;
use App\Filament\Resources\PastPaperResource\RelationManagers;
use App\Models\PastPaper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PastPaperResource extends Resource
{
    protected static ?string $model = PastPaper::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Documents';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2100),
                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'pre_seen' => 'Pre-Seen',
                        'case_study' => 'Case Study',
                        'exam' => 'Exam',
                        'other' => 'Other',
                    ])
                    ->default('exam'),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pre_seen' => 'info',
                        'case_study' => 'warning',
                        'exam' => 'success',
                        'other' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('questions_count')
                    ->counts('questions')
                    ->label('Questions'),
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
                //
            ])
            ->actions([
                Tables\Actions\Action::make('upload_to_n8n')
                    ->label('Upload to AI')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->action(function (PastPaper $record) {
                        $n8nUrl = config('services.n8n.upload_url');
                        if ($n8nUrl) {
                            \Illuminate\Support\Facades\Http::post($n8nUrl, [
                                'type' => 'past_paper',
                                'id' => $record->id,
                                'name' => $record->name,
                                'year' => $record->year,
                                'paper_type' => $record->type,
                            ]);
                        }
                    })
                    ->requiresConfirmation(),
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
            RelationManagers\QuestionsRelationManager::class,
            RelationManagers\QuestionPaperRelationManager::class,
            RelationManagers\AnswerGuideRelationManager::class,
            RelationManagers\MarkingGuideRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPastPapers::route('/'),
            'create' => Pages\CreatePastPaper::route('/create'),
            'edit' => Pages\EditPastPaper::route('/{record}/edit'),
        ];
    }
}
