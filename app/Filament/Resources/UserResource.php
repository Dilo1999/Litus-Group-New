<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $slug = 'users';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 100;

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
                Forms\Components\Section::make('User details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn ($context) => $context === 'create')
                            ->maxLength(255)
                            ->helperText('Leave blank on edit to keep current password.'),
                        Select::make('role')
                            ->options(fn () => User::assignableRoleOptions(auth()->user()))
                            ->required()
                            ->helperText(fn (): string => auth()->user()?->isSuperadmin()
                                ? 'Superadmin: full access including Super admin settings. Admin: all sections except Super admin settings. Management: Gallery Events and Blog Posts only. HR: Job Openings only.'
                                : 'Admin: full access to Settings, Management, and HR sections. Management: Gallery Events and Blog Posts only. HR: Job Openings only.'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->formatStateUsing(fn (string $state): string => User::roleOptions()[$state] ?? $state)
                    ->sortable(),
            ])
            ->defaultSort('name')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $actor = auth()->user();

        return $actor instanceof User
            ? $query->visibleTo($actor)
            : $query->whereRaw('1 = 0');
    }
}
