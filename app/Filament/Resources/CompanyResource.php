<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identity')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rule('regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'),
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
                        FileUpload::make('logo')
                            ->label('Logo')
                            ->disk('public')
                            ->directory('companies/logos')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->image()
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
                            ->preserveFilenames()
                            ->image()
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
                                    ->maxLength(255),
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
                                    ->maxLength(255),
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
        $data['services'] = collect($data['service_items'] ?? [])
            ->pluck('label')
            ->map(fn ($s) => trim((string) $s))
            ->filter()
            ->values()
            ->all();

        $data['strengths'] = collect($data['strength_items'] ?? [])
            ->pluck('label')
            ->map(fn ($s) => trim((string) $s))
            ->filter()
            ->values()
            ->all();

        unset($data['service_items'], $data['strength_items']);

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

        return $data;
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
        $data['service_items'] = array_map(
            fn ($s) => ['label' => $s],
            $data['services'] ?? []
        );
        $data['strength_items'] = array_map(
            fn ($s) => ['label' => $s],
            $data['strengths'] ?? []
        );

        return $data;
    }
}
