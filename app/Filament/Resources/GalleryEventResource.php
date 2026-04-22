<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\SeoFields;
use App\Filament\Resources\GalleryEventResource\Pages;
use App\Models\User;
use App\Models\GalleryEvent;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class GalleryEventResource extends Resource
{
    protected static ?string $model = GalleryEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-photograph';

    protected static ?string $navigationLabel = 'Gallery Events';

    protected static ?string $modelLabel = 'Gallery Event';

    protected static ?string $pluralModelLabel = 'Gallery Events';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $slug = 'gallery-events';

    protected static ?string $navigationGroup = 'Management';

    protected static ?int $navigationSort = 85;

    protected static function canAccessForUser(?User $user): bool
    {
        return $user?->isAdmin() || $user?->isManagement();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccessForUser(auth()->user());
    }

    public static function canViewAny(): bool
    {
        return static::canAccessForUser(auth()->user());
    }

    protected static function uploadUrlResolver(): callable
    {
        return function (FileUpload $component, string $file): ?string {
            $disk = $component->getDisk();

            try {
                if ($disk->exists($file)) {
                    return $disk->url($file);
                }
            } catch (\Throwable) {
            }

            if (str_starts_with($file, 'http://') || str_starts_with($file, 'https://')) {
                return $file;
            }

            return null;
        };
    }

    public static function form(Form $form): Form
    {
        $resolveUrl = self::uploadUrlResolver();

        return $form->schema([
            Forms\Components\Section::make('Event')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set, ?string $state): mixed => $set('slug', Str::slug((string) $state)))
                        ->columnSpanFull(),

                    TextInput::make('slug')
                        ->maxLength(255)
                        ->disabled()
                        ->dehydrated()
                        ->default(fn (callable $get): string => Str::slug((string) $get('title') ?: ''))
                        ->unique(ignoreRecord: true)
                        ->helperText('Used in the public URL (e.g. /events/your-slug).'),

                    DatePicker::make('date_display')
                        ->label('Display date')
                        ->required()
                        ->format('Y-m-d')
                        ->displayFormat('F j, Y')
                        ->helperText('Shown on the site (e.g. March 15, 2026).'),

                    Textarea::make('description')
                        ->rows(4)
                        ->columnSpanFull(),

                    FileUpload::make('cover_image')
                        ->label('Cover image')
                        ->disk('public')
                        ->directory('gallery/covers')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->image()
                        ->imagePreviewHeight('180')
                        ->maxSize(8192)
                        ->nullable()
                        ->columnSpanFull()
                        ->helperText('Thumbnail on News & Media. You can use an uploaded file or keep an external URL stored from a previous import.')
                        ->getUploadedFileUrlUsing($resolveUrl),

                    FileUpload::make('gallery_images')
                        ->label('Gallery photos (detail page)')
                        ->disk('public')
                        ->directory('gallery/events')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->image()
                        ->multiple()
                        ->enableReordering()
                        ->maxSize(8192)
                        ->nullable()
                        ->columnSpanFull()
                        ->helperText('Photos on the event gallery page. If empty, the cover image is repeated as a placeholder grid.')
                        ->getUploadedFileUrlUsing($resolveUrl),

                    Toggle::make('is_active')
                        ->label('Published')
                        ->inline(false)
                        ->default(true),

                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('date_display')
                    ->label('Date')
                    ->toggleable()
                    ->formatStateUsing(static fn ($state): string => blank($state) ? '' : \Illuminate\Support\Carbon::parse((string) $state)->format('F j, Y'))
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleryEvents::route('/'),
            'create' => Pages\CreateGalleryEvent::route('/create'),
            'edit' => Pages\EditGalleryEvent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('sort_order')->orderBy('id');
    }
}
