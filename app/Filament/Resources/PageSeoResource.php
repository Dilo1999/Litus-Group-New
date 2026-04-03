<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\PageSeoFields;
use App\Filament\Resources\PageSeoResource\Pages;
use App\Models\PageSeo;
use App\Support\SiteSeoRoutes;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class PageSeoResource extends Resource
{
    protected static ?string $model = PageSeo::class;

    protected static ?string $navigationIcon = 'heroicon-o-search-circle';

    protected static ?string $navigationLabel = 'Page SEO';

    protected static ?string $modelLabel = 'Page SEO';

    protected static ?string $pluralModelLabel = 'Page SEO';

    // Only admins should manage SEO, so surface this under Settings
    // alongside Users rather than Management content modules.
    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 95;

    protected static ?string $slug = 'page-seo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Page')
                    ->description('Choose which public URL this record configures. Each route can only have one SEO record.')
                    ->schema([
                        Select::make('route_name')
                            ->label('Page')
                            ->options(SiteSeoRoutes::options())
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (?PageSeo $record) => $record !== null)
                            ->helperText('Route cannot be changed after creation. Delete and re-add if needed.'),
                    ]),
                PageSeoFields::section(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('route_name')
                    ->label('Route')
                    ->formatStateUsing(fn (string $state): string => SiteSeoRoutes::options()[$state] ?? $state)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('meta_title')
                    ->label('Meta title')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('route_name')
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
            'index' => Pages\ListPageSeos::route('/'),
            'create' => Pages\CreatePageSeo::route('/create'),
            'edit' => Pages\EditPageSeo::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('route_name');
    }
}
