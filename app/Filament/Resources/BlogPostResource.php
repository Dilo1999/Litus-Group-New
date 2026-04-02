<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Components\Builder as ContentBuilder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\DateTimePicker;
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

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Post')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set, ?string $state): mixed => $set('slug', Str::slug((string) $state)))
                        ->columnSpanFull(),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->disabled()
                        ->dehydrated()
                        ->helperText('Auto-generated from the title.'),

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

                    FileUpload::make('image')
                        ->label('Cover image')
                        ->disk('public')
                        ->directory('blogs')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->image()
                        ->imagePreviewHeight('180')
                        ->maxSize(4096)
                        ->columnSpanFull(),

                    Textarea::make('excerpt')
                        ->rows(3)
                        ->maxLength(2000)
                        ->columnSpanFull(),

                    Textarea::make('content')
                        ->label('Content (plain text)')
                        ->rows(6)
                        ->columnSpanFull()
                        ->helperText('Optional. If you don’t use sections below, this will be shown on the article page.'),

                    ContentBuilder::make('content_blocks')
                        ->label('Content')
                        ->columnSpanFull()
                        ->blocks([
                            Block::make('paragraph')
                                ->label('Normal content')
                                ->schema([
                                    Textarea::make('text')
                                        ->label('Text')
                                        ->rows(8)
                                        ->required()
                                        ->columnSpanFull(),
                                ]),
                            Block::make('quote')
                                ->label('Quote section')
                                ->schema([
                                    Textarea::make('text')
                                        ->rows(3)
                                        ->required()
                                        ->columnSpanFull(),
                                    TextInput::make('attribution')
                                        ->label('Attribution (optional)')
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->collapsible()
                        ->cloneable()
                        ->createItemButtonLabel('Add content section')
                        ->helperText('Add normal content and/or quote sections. Cover image is used for images.'),
                ])
                ->columns(2),
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
}

