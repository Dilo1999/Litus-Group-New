<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobOpeningResource\Pages;
use App\Models\JobOpening;
use Filament\Forms;
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

class JobOpeningResource extends Resource
{
    protected static ?string $model = JobOpening::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Job Openings';

    protected static ?string $modelLabel = 'Job Opening';

    protected static ?string $pluralModelLabel = 'Job Openings';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $slug = 'job-openings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 87;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Job details')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('company')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('location')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('type')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('department')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description')
                        ->label('Job Description')
                        ->rows(6)
                        ->columnSpanFull(),
                    Toggle::make('is_active')
                        ->label('Active')
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
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('location')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('type')
                    ->toggleable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at')
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
            'index' => Pages\ListJobOpenings::route('/'),
            'create' => Pages\CreateJobOpening::route('/create'),
            'edit' => Pages\EditJobOpening::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('created_at')->orderBy('id');
    }
}

