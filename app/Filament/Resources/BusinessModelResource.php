<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessModelResource\Pages;
use App\Filament\Resources\BusinessModelResource\RelationManagers;
use App\Models\BusinessModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BusinessModelResource extends Resource
{
    protected static ?string $model = BusinessModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Business Models';

    protected static ?string $modelLabel = 'Business Model';

    protected static ?string $pluralModelLabel = 'Business Models';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('analysis_prompt')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->rows(10)
                    ->helperText('Fill the model based on the pre-seen, information can be used from anywhere in the preseen'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50),
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
            'index' => Pages\ListBusinessModels::route('/'),
            'create' => Pages\CreateBusinessModel::route('/create'),
            'edit' => Pages\EditBusinessModel::route('/{record}/edit'),
        ];
    }
}
