<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PreSeenDocumentResource\Pages;
use App\Models\PreSeenDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PreSeenDocumentResource extends Resource
{
    protected static ?string $model = PreSeenDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Documents';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('file_path')
                    ->label('Document File')
                    ->required()
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ->directory('pre-seen-documents'),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('company_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('page_count')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('page_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_path')
                    ->numeric()
                    // storage_path('app/' . $record->file_path)
                    ->url(fn ($record) => asset('storage/'.$record->file_path))
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
                //
            ])
            ->actions([
                Tables\Actions\Action::make('upload_to_n8n')
                    ->label('Upload to AI')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->action(function (PreSeenDocument $record) {
                        $n8nUrl = config('services.n8n.upload_url');
                        if ($n8nUrl) {
                            $filePath = asset('storage/'.$record->file_path);
                            // if (file_exists($filePath)) {
                            \Illuminate\Support\Facades\Http::attach(
                                'file_data', file_get_contents($filePath), basename($record->file_path)
                            )->post($n8nUrl, [
                                'type' => 'pre_seen_document',
                                'id' => $record->id,
                                'name' => $record->name,
                            ]);
                            // }
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPreSeenDocuments::route('/'),
            'create' => Pages\CreatePreSeenDocument::route('/create'),
            'edit' => Pages\EditPreSeenDocument::route('/{record}/edit'),
        ];
    }
}
