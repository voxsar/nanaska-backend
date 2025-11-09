<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarkingResultResource\Pages;
use App\Models\MarkingResult;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MarkingResultResource extends Resource
{
    protected static ?string $model = MarkingResult::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Marking';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('student.name')
                            ->label('Student')
                            ->disabled(),
                        Forms\Components\TextInput::make('question.question_text')
                            ->label('Question')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Grading')
                    ->schema([
                        Forms\Components\TextInput::make('marks_obtained')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('total_marks')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\Select::make('level')
                            ->options([
                                'OCS' => 'Operational (OCS)',
                                'MCS' => 'Management (MCS)',
                                'SCS' => 'Strategic (SCS)',
                            ])
                            ->disabled(),
                        Forms\Components\Select::make('band_level')
                            ->options([
                                1 => 'Band 1 - Identification Only',
                                2 => 'Band 2 - Partial Application',
                                3 => 'Band 3 - Justified & Practical',
                            ])
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Feedback')
                    ->schema([
                        Forms\Components\Textarea::make('band_explanation')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('feedback')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('genericity_comment')
                            ->label('Generic Comment')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Quality Checks')
                    ->schema([
                        Forms\Components\Toggle::make('answered_specific_question')
                            ->label('Answered Specific Question')
                            ->disabled(),
                        Forms\Components\Toggle::make('structure_ok')
                            ->label('Structure OK')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Strengths')
                    ->schema([
                        Forms\Components\Repeater::make('strengths_extracts')
                            ->label('Strength Examples')
                            ->schema([
                                Forms\Components\Textarea::make('extract')
                                    ->label(false)
                                    ->disabled(),
                            ])
                            ->disabled()
                            ->columnSpanFull(),
                    ])->collapsible(),

                Forms\Components\Section::make('Weaknesses')
                    ->schema([
                        Forms\Components\Repeater::make('weaknesses_extracts')
                            ->label('Weakness Examples')
                            ->schema([
                                Forms\Components\Textarea::make('extract')
                                    ->label(false)
                                    ->disabled(),
                            ])
                            ->disabled()
                            ->columnSpanFull(),
                    ])->collapsible(),

                Forms\Components\Section::make('Improvement Plan')
                    ->schema([
                        Forms\Components\Repeater::make('improvement_plan')
                            ->schema([
                                Forms\Components\Textarea::make('action')
                                    ->label(false)
                                    ->disabled(),
                            ])
                            ->disabled()
                            ->columnSpanFull(),
                    ])->collapsible(),

                Forms\Components\Section::make('Points Summary')
                    ->schema([
                        Forms\Components\Repeater::make('points_summary')
                            ->schema([
                                Forms\Components\Fieldset::make('Point Row')
                                    ->schema([
                                        Forms\Components\TextArea::make('point')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('justified_with')
                                            ->disabled(),
                                        Forms\Components\Select::make('practicality')
                                            ->options([
                                                'low' => 'Low',
                                                'medium' => 'Medium',
                                                'high' => 'High',
                                            ])
                                            ->disabled(),
                                    ])
                                    ->columns(1)
                                    ->columnSpanFull(),
                            ])
                            ->disabled()
                            ->columns(5)
                            ->columnSpanFull(),
                    ])->collapsible(),
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
                Tables\Columns\TextColumn::make('question.question_text')
                    ->label('Question')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('marks_obtained')
                    ->label('Marks')
                    ->formatStateUsing(fn ($record) => "{$record->marks_obtained}/{$record->total_marks}")
                    ->badge()
                    ->color(function ($record) {
                        if (! $record->total_marks) {
                            return 'gray';
                        }
                        $percentage = 0; // ($record->marks_obtained / $record->total_marks) * 100;
                        if ($percentage >= 70) {
                            return 'success';
                        }
                        if ($percentage >= 50) {
                            return 'warning';
                        }

                        return 'danger';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'OCS' => 'info',
                        'MCS' => 'warning',
                        'SCS' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('band_level')
                    ->label('Band')
                    ->badge()
                    ->color(fn (?int $state): string => match ($state) {
                        1 => 'danger',
                        2 => 'warning',
                        3 => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state ? "Band {$state}" : 'N/A')
                    ->sortable(),
                Tables\Columns\IconColumn::make('answered_specific_question')
                    ->label('On Topic')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\IconColumn::make('structure_ok')
                    ->label('Structure')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Marked At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('level')
                    ->options([
                        'OCS' => 'Operational (OCS)',
                        'MCS' => 'Management (MCS)',
                        'SCS' => 'Strategic (SCS)',
                    ]),
                Tables\Filters\SelectFilter::make('band_level')
                    ->options([
                        1 => 'Band 1',
                        2 => 'Band 2',
                        3 => 'Band 3',
                    ]),
                Tables\Filters\TernaryFilter::make('answered_specific_question')
                    ->label('Answered Specific Question'),
                Tables\Filters\TernaryFilter::make('structure_ok')
                    ->label('Good Structure'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListMarkingResults::route('/'),
            'view' => Pages\ViewMarkingResult::route('/{record}'),
        ];
    }
}
