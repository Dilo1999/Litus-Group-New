<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\AssetPicker;
use App\Filament\Resources\TeamMemberResource\Pages;
use App\Models\TeamMember;
use App\Models\User;
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
use Illuminate\Support\Facades\Storage;

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Team Members';

    protected static ?string $modelLabel = 'Team Member';

    protected static ?string $pluralModelLabel = 'Team Members';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $slug = 'team-members';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 86;

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
        return $form->schema([
            Forms\Components\Section::make('Profile')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('role')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('bio')
                        ->rows(6)
                        ->columnSpanFull(),
                    TextInput::make('expertise')
                        ->label('Expertise (comma separated)')
                        ->maxLength(500)
                        ->columnSpanFull(),
                    TextInput::make('linkedin_url')
                        ->label('LinkedIn URL')
                        ->url()
                        ->maxLength(500)
                        ->columnSpanFull(),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    AssetPicker::make('photo')
                        ->label('Photo')
                        ->imagePreviewHeight(200)
                        ->extraAttributes(['class' => 'max-w-md'])
                        ->helperText('Shown on the public Team page. Remove to clear.'),
                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->required(),
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
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
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
            'index' => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit' => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('sort_order')->orderBy('id');
    }
}

