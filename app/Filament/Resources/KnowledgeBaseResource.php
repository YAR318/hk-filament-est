<?php

namespace App\Filament\Resources;

use App\Models\KnowledgeDocument;
use App\Services\DocumentParserService;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KnowledgeBaseResource extends Resource
{
    protected static ?string $model = KnowledgeDocument::class;

    protected static ?string $navigationLabel = 'Base de Conocimiento';

    protected static ?string $modelLabel = 'Documento';

    protected static ?string $pluralModelLabel = 'Documentos';

    public static function getNavigationGroup(): ?string
    {
        return 'Bot';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return 'heroicon-o-book-open';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Documento')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Información de la empresa, Precios, FAQ...'),

                        FileUpload::make('file_path')
                            ->label('Archivo')
                            ->required()
                            ->disk('public')
                            ->directory('knowledge-base')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'text/plain',
                            ])
                            ->maxSize(10240)
                            ->helperText('Formatos aceptados: PDF, Word (.docx), TXT. Máximo 10MB.'),

                        Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true)
                            ->helperText('Solo los documentos activos se usan como contexto del bot.'),

                        Hidden::make('file_type'),
                        Hidden::make('file_size'),
                        Hidden::make('uploaded_by')
                            ->default(fn () => Auth::id()),
                    ]),

                Section::make('Texto extraído')
                    ->schema([
                        Textarea::make('extracted_text')
                            ->label('Contenido extraído del documento')
                            ->rows(15)
                            ->helperText('Este texto se extrae automáticamente al subir el archivo. Puedes editarlo manualmente si es necesario.'),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('file_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'pdf' => 'danger',
                        'docx' => 'primary',
                        'txt' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('formatted_size')
                    ->label('Tamaño'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Subido')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\KnowledgeBaseResource\Pages\ListKnowledgeDocuments::route('/'),
            'create' => \App\Filament\Resources\KnowledgeBaseResource\Pages\CreateKnowledgeDocument::route('/create'),
            'edit' => \App\Filament\Resources\KnowledgeBaseResource\Pages\EditKnowledgeDocument::route('/{record}/edit'),
        ];
    }
}
