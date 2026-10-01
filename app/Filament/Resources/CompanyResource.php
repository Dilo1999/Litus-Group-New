<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use App\Models\User;
use App\Support\CompanyPageIcons;
use App\Support\MediaAssetLibrary;
use App\Support\SiteData;
use Filament\Forms;
use App\Filament\Forms\Components\AssetPicker;
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

    /** Maximum upload size for company images (kilobytes). */
    public const MAX_IMAGE_SIZE_KB = 500;

    protected static function canAccessForUser(?User $user): bool
    {
        return $user?->hasAdminAccess() ?? false;
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
                Forms\Components\Tabs::make('Company page')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Details')
                            ->icon('heroicon-o-adjustments')
                            ->schema([
                                Forms\Components\Section::make('General information')
                                    ->description('Basic details used across the site navigation, listings, and company profile.')
                                    ->schema(static::identityFields())
                                    ->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Overview')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make('Page content')
                                    ->description('Headline and about copy shown on the public company page.')
                                    ->schema(static::contentFields()),
                            ]),
                        Forms\Components\Tabs\Tab::make('Contact')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Section::make('Contact details')
                                    ->description('Phone and email shown in the company hero and contact section.')
                                    ->schema(static::contactFields())
                                    ->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Media')
                            ->icon('heroicon-o-photograph')
                            ->schema([
                                Forms\Components\Section::make('Logo')
                                    ->description('Company logo used in the hero, navigation dropdown, and listings. Maximum file size: 500 KB.')
                                    ->schema([
                                        static::logoUpload(),
                                    ]),
                                Forms\Components\Section::make('Hero banner')
                                    ->description('Full-width background image behind the company page hero. Maximum file size: 500 KB. A blue overlay keeps text readable.')
                                    ->schema([
                                        static::heroImageUpload(),
                                    ]),
                                Forms\Components\Section::make('About section image')
                                    ->description('Image beside the about text on the company page. Maximum file size: 500 KB.')
                                    ->schema([
                                        static::aboutImageUpload(),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Services')
                            ->icon('heroicon-o-view-grid')
                            ->schema([
                                Forms\Components\Section::make('Services')
                                    ->description('Add each service with a title and optional icon. Icons appear on the “Our Services” cards.')
                                    ->schema([
                                        static::labeledItemsRepeater(
                                            name: 'service_items',
                                            itemLabel: 'Service',
                                            addButtonLabel: 'Add service',
                                            iconDirectory: 'companies/service-icons',
                                            descriptionLabel: 'Description',
                                            descriptionHelp: 'Optional. Short sentence shown under the title on pages that support it (e.g. Zaha Travels).',
                                        ),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Strengths')
                            ->icon('heroicon-o-star')
                            ->schema([
                                Forms\Components\Section::make('Why choose us')
                                    ->description('Highlight what sets this company apart. Each item can include an optional icon.')
                                    ->schema([
                                        static::labeledItemsRepeater(
                                            name: 'strength_items',
                                            itemLabel: 'Strength',
                                            addButtonLabel: 'Add strength',
                                            iconDirectory: 'companies/strength-icons',
                                            descriptionLabel: 'Caption',
                                            descriptionHelp: 'Optional. Small line under the value on pages with a highlights strip (e.g. Zaha Travels: “100+” → “Resort & hotel partners”).',
                                        ),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    protected static function identityFields(): array
    {
        return [
            TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255)
                ->columnSpan(1),
            TextInput::make('category')
                ->label('Category')
                ->maxLength(255)
                ->placeholder('e.g. Travel & Tourism')
                ->columnSpan(1),
            Select::make('division')
                ->label('Division')
                ->options(SiteData::divisionOptions())
                ->searchable()
                ->required()
                ->columnSpan(1),
            TextInput::make('sort_order')
                ->label('Sort order')
                ->numeric()
                ->default(0)
                ->required()
                ->helperText('Lower numbers appear first in listings.')
                ->columnSpan(1),
            Toggle::make('featured')
                ->label('Featured on homepage')
                ->inline(false)
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    protected static function contentFields(): array
    {
        return [
            TextInput::make('tagline')
                ->label('Tagline')
                ->maxLength(500)
                ->placeholder('Short headline under the company name')
                ->columnSpanFull(),
            Textarea::make('description')
                ->label('About — paragraph 1')
                ->rows(5)
                ->columnSpanFull()
                ->helperText('Opening paragraph describing what the company does.'),
            Textarea::make('description_secondary')
                ->label('About — paragraph 2')
                ->rows(5)
                ->columnSpanFull()
                ->helperText('Second paragraph about expertise, value, or the LITUS Group family.'),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    protected static function contactFields(): array
    {
        return [
            TextInput::make('hotline')
                ->label('Phone')
                ->tel()
                ->maxLength(50)
                ->placeholder('+960 332 2289'),
            TextInput::make('email')
                ->label('Email')
                ->email()
                ->maxLength(255)
                ->placeholder('info@example.com'),
        ];
    }

    protected static function labeledItemsRepeater(
        string $name,
        string $itemLabel,
        string $addButtonLabel,
        string $iconDirectory,
        ?string $descriptionLabel = null,
        ?string $descriptionHelp = null,
    ): Forms\Components\Repeater {
        return Forms\Components\Repeater::make($name)
            ->label($itemLabel.' items')
            ->schema([
                TextInput::make('label')
                    ->label('Title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(1),
                static::labeledItemIconUpload($iconDirectory)
                    ->columnSpan(1),
                Forms\Components\Textarea::make('description')
                    ->label($descriptionLabel ?? 'Description')
                    ->helperText($descriptionHelp)
                    ->rows(2)
                    ->maxLength(500)
                    ->columnSpanFull()
                    ->visible($descriptionLabel !== null),
            ])
            ->columns(2)
            ->itemLabel(fn (array $state): ?string => filled($state['label'] ?? null) ? (string) $state['label'] : null)
            ->createItemButtonLabel($addButtonLabel)
            ->defaultItems(0)
            ->collapsible()
            ->orderable();
    }

    protected static function logoUpload(): AssetPicker
    {
        return AssetPicker::make('logo')
            ->label('Logo')
            ->imagePreviewHeight(140)
            ->maxSize(self::MAX_IMAGE_SIZE_KB)
            // Default logos are bare file names shipped in public/assets/logo.
            ->fallbackBaseUrl(asset('assets/logo'))
            ->extraAttributes(['class' => 'max-w-2xl']);
    }

    protected static function heroImageUpload(): AssetPicker
    {
        return AssetPicker::make('hero_image')
            ->label('Hero image')
            ->imagePreviewHeight(200)
            ->maxSize(self::MAX_IMAGE_SIZE_KB)
            ->extraAttributes(['class' => 'max-w-3xl']);
    }

    protected static function aboutImageUpload(): AssetPicker
    {
        return AssetPicker::make('about_image')
            ->label('About image')
            ->imagePreviewHeight(180)
            ->maxSize(self::MAX_IMAGE_SIZE_KB)
            ->extraAttributes(['class' => 'max-w-3xl']);
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
     * @return list<string|array{label: string, icon_path?: string, description?: string}>
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
                $description = trim((string) ($item['description'] ?? ''));

                if ($iconPath === null && $description === '') {
                    return $label;
                }

                return array_filter([
                    'label' => $label,
                    'icon_path' => $iconPath,
                    'description' => $description !== '' ? $description : null,
                ], fn ($value) => $value !== null);
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
                MediaAssetLibrary::deleteUnlessShared($path);
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
                MediaAssetLibrary::deleteUnlessShared((string) $prev);
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
                        'description' => null,
                    ];
                }

                if (! is_array($item)) {
                    return null;
                }

                return [
                    'label' => $item['label'] ?? '',
                    'icon_path' => $item['icon_path'] ?? null,
                    'description' => $item['description'] ?? null,
                ];
            })
            ->filter(fn ($item) => filled($item['label'] ?? null))
            ->values()
            ->all();
    }

    protected static function labeledItemIconUpload(string $directory): AssetPicker
    {
        return AssetPicker::make('icon_path')
            ->label('Icon')
            ->imagePreviewHeight(96)
            ->maxSize(self::MAX_IMAGE_SIZE_KB)
            ->helperText('Optional SVG/PNG/WebP. Maximum file size: 500 KB.')
            ->extraAttributes(['class' => 'max-w-xs']);
    }
}
