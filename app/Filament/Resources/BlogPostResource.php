<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\AssetPicker;
use App\Filament\Forms\Components\SeoFields;
use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
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

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Blog Posts';
    protected static ?string $modelLabel = 'Blog Post';
    protected static ?string $pluralModelLabel = 'Blog Posts';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $slug = 'blog-posts';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 86;

    protected static function canAccessForUser(?User $user): bool
    {
        return $user?->hasAdminAccess() || $user?->isManagement();
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
        return $form->schema([
            Forms\Components\Section::make('Post')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    TextInput::make('category')
                        ->maxLength(255)
                        ->helperText('Example: Logistics, Company News, Hospitality.'),

                    Toggle::make('is_active')
                        ->label('Published')
                        ->inline(false)
                        ->default(true),

                    DateTimePicker::make('published_at')
                        ->label('Publish date/time')
                        ->withoutSeconds()
                        ->helperText('Optional. Used for display.'),

                    TextInput::make('author')
                        ->maxLength(255),

                    TextInput::make('read_time')
                        ->label('Read time')
                        ->maxLength(50)
                        ->helperText('Example: 4 min read'),

                    AssetPicker::make('image')
                        ->label('Cover image')
                        ->imagePreviewHeight(180)
                        ->columnSpanFull(),

                    Textarea::make('excerpt')
                        ->rows(3)
                        ->maxLength(2000)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Article content')
                ->description('Write or paste the article HTML, or edit it directly in the preview: click any text to type, click an image to resize, align or replace it.')
                ->schema([
                    Textarea::make('body')
                        ->label('HTML')
                        ->rows(18)
                        ->helperText('Example: <h2>…</h2><p>…</p><img src="…" alt="…">')
                        ->extraAttributes([
                            'class' => 'font-mono text-sm',
                            'data-blog-post-body' => '1',
                        ])
                        ->columnSpanFull(),
                    Forms\Components\ViewField::make('body_html_preview')
                        ->disableLabel()
                        ->view('filament.forms.blog-post-html-preview')
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ]),
            SeoFields::section(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('category')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('created_at')->orderByDesc('id');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function assignSlug(array $data, ?BlogPost $existing = null): array
    {
        $title = trim((string) ($data['title'] ?? ''));
        if ($title !== '') {
            $data['slug'] = static::uniqueSlugForTitle($title, $existing?->getKey());
        }

        return $data;
    }

    public static function uniqueSlugForTitle(string $title, ?int $exceptId = null): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'post';
        }

        $slug = $base;
        $suffix = 2;

        while (
            BlogPost::query()
                ->when($exceptId !== null, fn (Builder $query) => $query->whereKeyNot($exceptId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}

