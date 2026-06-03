<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\SeoFields;
use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use App\Models\User;
use App\Support\CompanyPageIcons;
use App\Support\SiteData;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-office-building';

    protected static ?string $navigationLabel = 'Companies';

    protected static ?string $modelLabel = 'Company';

    protected static ?string $pluralModelLabel = 'Companies';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $slug = 'companies';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 88;

    protected static function canAccessForUser(?User $user): bool
    {
        return $user?->isAdmin() ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccessForUser(auth()->user());
    }

    public static function canViewAny(): bool
    {
        return static::canAccessForUser(auth()->user());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identity')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Select::make('division')
                            ->options(SiteData::divisionOptions())
                            ->searchable()
                            ->required(),
                        TextInput::make('category')
                            ->maxLength(255),
                        Toggle::make('featured')
                            ->inline(false),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Content')
                    ->schema([
                        TextInput::make('tagline')
                            ->maxLength(500),
                        Textarea::make('description')
                            ->label('Description — part 1')
                            ->rows(5)
                            ->columnSpanFull()
                            ->helperText('Opening paragraph (e.g. what the company is and sectors).'),
                        Textarea::make('description_secondary')
                            ->label('Description — part 2')
                            ->rows(5)
                            ->columnSpanFull()
                            ->helperText('Second paragraph (e.g. LITUS Group family, expertise, value to clients).'),
                    ]),
                Forms\Components\Section::make('Contact')
                    ->schema([
                        TextInput::make('hotline')
                            ->tel()
                            ->maxLength(50),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Branding')
                    ->schema([
                        FileUpload::make('hero_image')
                            ->label('Hero section image')
                            ->disk('public')
                            ->directory('companies/hero')
                            ->visibility('public')
                            ->image()
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'image/svg+xml',
                            ])
                            ->rules([
                                'mimetypes:image/jpeg,image/png,image/webp,image/svg+xml',
                            ])
                            ->getUploadedFileNameForStorageUsing(function (\Illuminate\Http\UploadedFile $file): string {
                                $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                                $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');

                                return Str::slug($name).'-'.Str::lower(Str::random(10)).'.'.$ext;
                            })
                            ->panelLayout('integrated')
                            ->panelAspectRatio('21:9')
                            ->removeUploadedFileButtonPosition('left')
                            ->uploadButtonPosition('center bottom')
                            ->loadingIndicatorPosition('center bottom')
                            ->uploadProgressIndicatorPosition('center bottom')
                            ->maxSize(8192)
                            ->nullable()
                            ->placeholder('Drag & drop your image or browse')
                            ->helperText('Background image for the company page hero banner. A blue overlay keeps text readable.')
                            ->extraAttributes(['class' => 'max-w-3xl'])
                            ->getUploadedFileUrlUsing(function (FileUpload $component, string $file): ?string {
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
                            }),
                        FileUpload::make('logo')
                            ->label('Logo')
                            ->disk('public')
                            ->directory('companies/logos')
                            ->visibility('public')
                            ->image()
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'image/svg+xml',
                            ])
                            ->rules([
                                'mimetypes:image/jpeg,image/png,image/webp,image/svg+xml',
                            ])
                            ->getUploadedFileNameForStorageUsing(function (\Illuminate\Http\UploadedFile $file): string {
                                $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                                $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');

                                return Str::slug($name).'-'.Str::lower(Str::random(10)).'.'.$ext;
                            })
                            ->panelLayout('integrated')
                            ->panelAspectRatio('13:8')
                            ->removeUploadedFileButtonPosition('left')
                            ->uploadButtonPosition('center bottom')
                            ->loadingIndicatorPosition('center bottom')
                            ->uploadProgressIndicatorPosition('center bottom')
                            ->maxSize(4096)
                            ->nullable()
                            ->placeholder('Drag & drop your image or browse')
                            ->helperText('Large preview with filename and size; use ✕ to remove. Saving applies changes.')
                            ->extraAttributes(['class' => 'max-w-3xl'])
                            ->getUploadedFileUrlUsing(function (FileUpload $component, string $file): ?string {
                                $disk = $component->getDisk();

                                try {
                                    if ($disk->exists($file)) {
                                        return $disk->url($file);
                                    }
                                } catch (\Throwable) {
                                }

                                return SiteData::companyLogoUrl($file);
                            }),
                        FileUpload::make('about_image')
                            ->label('About section image')
                            ->disk('public')
                            ->directory('companies/about')
                            ->visibility('public')
                            ->image()
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'image/svg+xml',
                            ])
                            ->rules([
                                'mimetypes:image/jpeg,image/png,image/webp,image/svg+xml',
                            ])
                            ->getUploadedFileNameForStorageUsing(function (\Illuminate\Http\UploadedFile $file): string {
                                $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                                $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');

                                return Str::slug($name).'-'.Str::lower(Str::random(10)).'.'.$ext;
                            })
                            ->panelLayout('integrated')
                            ->panelAspectRatio('16:10')
                            ->removeUploadedFileButtonPosition('left')
                            ->uploadButtonPosition('center bottom')
                            ->loadingIndicatorPosition('center bottom')
                            ->uploadProgressIndicatorPosition('center bottom')
                            ->maxSize(6144)
                            ->nullable()
                            ->placeholder('Drag & drop your image or browse')
                            ->helperText('Shown on the public Company page “About” section (right side).')
                            ->extraAttributes(['class' => 'max-w-3xl'])
                            ->getUploadedFileUrlUsing(function (FileUpload $component, string $file): ?string {
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
                            }),
                    ])
                    ->columns(1),
                Forms\Components\Section::make('Services')
                    ->schema([
                        Forms\Components\Repeater::make('service_items')
                            ->schema([
                                TextInput::make('label')
                                    ->label('Service')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                static::labeledItemIconUpload('companies/service-icons'),
                            ])
                            ->columns(1)
                            ->defaultItems(0)
                            ->collapsible(),
                    ]),
                Forms\Components\Section::make('Strengths')
                    ->schema([
                        Forms\Components\Repeater::make('strength_items')
                            ->schema([
                                TextInput::make('label')
                                    ->label('Strength')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                static::labeledItemIconUpload('companies/strength-icons'),
                            ])
                            ->columns(1)
                            ->defaultItems(0)
                            ->collapsible(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->toggleable(),
                TextColumn::make('division')
                    ->formatStateUsing(function (?string $state): string {
                        if ($state === null || $state === '') {
                            return '';
                        }
                        $div = SiteData::divisions()[$state] ?? null;

                        return $div['title'] ?? $state;
                    })
                    ->sortable(),
                IconColumn::make('featured')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeFormDataForSave(array $data, ?Company $existing): array
    {
        $previousServiceIconPaths = CompanyPageIcons::iconPathsFromItems($existing?->services ?? []);
        $previousStrengthIconPaths = CompanyPageIcons::iconPathsFromItems($existing?->strengths ?? []);

        $data['services'] = static::normalizeLabeledItems($data['service_items'] ?? []);
        $data['strengths'] = static::normalizeLabeledItems($data['strength_items'] ?? []);

        static::deleteRemovedIconPaths(
            $previousServiceIconPaths,
            CompanyPageIcons::iconPathsFromItems($data['services']),
            'companies/service-icons/'
        );
        static::deleteRemovedIconPaths(
            $previousStrengthIconPaths,
            CompanyPageIcons::iconPathsFromItems($data['strengths']),
            'companies/strength-icons/'
        );

        unset($data['service_items'], $data['strength_items']);

        $data = static::normalizeFileFieldForSave(
            data: $data,
            field: 'hero_image',
            existing: $existing,
            deleteWhenReplacedPrefix: 'companies/',
        );

        $data = static::normalizeFileFieldForSave(
            data: $data,
            field: 'logo',
            existing: $existing,
            deleteWhenReplacedPrefix: 'companies/',
        );

        $data = static::normalizeFileFieldForSave(
            data: $data,
            field: 'about_image',
            existing: $existing,
            deleteWhenReplacedPrefix: 'companies/',
        );

        $name = trim((string) ($data['name'] ?? ''));
        if ($name !== '') {
            $data['slug'] = static::uniqueSlugForName($name, $existing?->getKey());
        }

        return $data;
    }

    /**
     * @param  list<array<string, mixed>>  $items
     * @return list<string|array{label: string, icon_path: string}>
     */
    protected static function normalizeLabeledItems(array $items): array
    {
        return collect($items)
            ->map(function ($item) {
                $label = trim((string) ($item['label'] ?? ''));
                if ($label === '') {
                    return null;
                }

                $iconPath = $item['icon_path'] ?? null;
                if (is_array($iconPath)) {
                    $iconPath = reset($iconPath) ?: null;
                }
                $iconPath = is_string($iconPath) && $iconPath !== '' ? $iconPath : null;

                if ($iconPath === null) {
                    return $label;
                }

                return [
                    'label' => $label,
                    'icon_path' => $iconPath,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $previousPaths
     * @param  list<string>  $nextPaths
     */
    protected static function deleteRemovedIconPaths(array $previousPaths, array $nextPaths, string $prefix): void
    {
        foreach ($previousPaths as $path) {
            if (! in_array($path, $nextPaths, true) && str_starts_with($path, $prefix)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    public static function uniqueSlugForName(string $name, ?int $exceptId = null): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'company';
        }

        $slug = $base;
        $suffix = 2;

        while (
            Company::query()
                ->when($exceptId !== null, fn (Builder $query) => $query->whereKeyNot($exceptId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected static function normalizeFileFieldForSave(
        array $data,
        string $field,
        ?Company $existing,
        string $deleteWhenReplacedPrefix
    ): array {
        $next = $data[$field] ?? null;
        if (is_array($next)) {
            $next = reset($next) ?: null;
        }
        if ($next === '') {
            $next = null;
        }

        if ($existing && $existing->getKey()) {
            $prev = $existing->getAttribute($field);
            if ($prev !== $next && $prev && str_starts_with((string) $prev, $deleteWhenReplacedPrefix)) {
                Storage::disk('public')->delete((string) $prev);
            }
        }

        $data[$field] = $next;

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function hydrateRepeaterFields(array $data): array
    {
        $data['service_items'] = static::hydrateLabeledItems($data['services'] ?? []);
        $data['strength_items'] = static::hydrateLabeledItems($data['strengths'] ?? []);

        return $data;
    }

    /**
     * @param  list<mixed>  $items
     * @return list<array{label: string, icon_path: ?string}>
     */
    protected static function hydrateLabeledItems(array $items): array
    {
        return collect($items)
            ->map(function ($item) {
                if (is_string($item)) {
                    return [
                        'label' => $item,
                        'icon_path' => null,
                    ];
                }

                if (! is_array($item)) {
                    return null;
                }

                return [
                    'label' => $item['label'] ?? '',
                    'icon_path' => $item['icon_path'] ?? null,
                ];
            })
            ->filter(fn ($item) => filled($item['label'] ?? null))
            ->values()
            ->all();
    }

    protected static function labeledItemIconUpload(string $directory): FileUpload
    {
        return FileUpload::make('icon_path')
            ->label('Icon')
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->image()
            ->acceptedFileTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/svg+xml',
            ])
            ->rules([
                'mimetypes:image/jpeg,image/png,image/webp,image/svg+xml',
            ])
            ->getUploadedFileNameForStorageUsing(function (\Illuminate\Http\UploadedFile $file): string {
                $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');

                return Str::slug($name).'-'.Str::lower(Str::random(10)).'.'.$ext;
            })
            ->imagePreviewHeight('80')
            ->maxSize(2048)
            ->nullable()
            ->columnSpanFull()
            ->helperText('Optional SVG/PNG/WebP uploaded from storage.')
            ->getUploadedFileUrlUsing(function (FileUpload $component, string $file): ?string {
                $disk = $component->getDisk();

                try {
                    if ($disk->exists($file)) {
                        return $disk->url($file);
                    }
                } catch (\Throwable) {
                }

                return CompanyPageIcons::storedIconUrl($file);
            });
    }
}
