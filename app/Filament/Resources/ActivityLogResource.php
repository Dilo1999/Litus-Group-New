<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use App\Models\User;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\Modal\Actions\Action as ModalAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-list';

    protected static ?string $navigationLabel = 'Activity log';

    protected static ?string $modelLabel = 'Activity log entry';

    protected static ?string $pluralModelLabel = 'Activity log';

    protected static ?string $slug = 'activity-logs';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 95;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date & time')
                    ->dateTime('M j, Y H:i:s')
                    ->sortable(),
                TextColumn::make('actor_label')
                    ->label('User (at time of action)')
                    ->getStateUsing(fn ($record): string => $record instanceof ActivityLog ? $record->actor_label : '—')
                    ->searchable(true, function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $q) use ($search): void {
                            $q->where('actor_name', 'like', "%{$search}%")
                                ->orWhere('actor_email', 'like', "%{$search}%")
                                ->orWhereHas('user', function (Builder $uq) use ($search): void {
                                    $uq->where('name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%");
                                });
                        });
                    })
                    ->sortable(true, function (Builder $query, string $direction): Builder {
                        return $query->orderBy('actor_name', $direction)
                            ->orderBy('actor_email', $direction);
                    }),
                TextColumn::make('event')
                    ->label('Event')
                    ->sortable(),
                TextColumn::make('description')
                    ->label('What was done')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('subject_type')
                    ->label('Subject type')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—')
                    ->toggleable(),
                TextColumn::make('subject_id')
                    ->label('Subject ID')
                    ->toggleable(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(true, true),
                TextColumn::make('properties')
                    ->label('Details')
                    ->getStateUsing(function (?ActivityLog $record): string {
                        if (! $record instanceof ActivityLog) {
                            return '—';
                        }

                        $props = $record->properties;
                        $summary = is_array($props) && array_key_exists('description', $props)
                            ? (string) $props['description']
                            : (string) ($record->description ?? '');

                        if (! is_array($props) || $props === []) {
                            return $summary !== '' ? $summary : '—';
                        }

                        $rest = $props;
                        unset($rest['description']);

                        if ($rest === []) {
                            return $summary !== '' ? $summary : '—';
                        }

                        $json = json_encode($rest, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                        if ($json === false) {
                            return $summary !== '' ? $summary : '—';
                        }

                        if ($summary === '') {
                            return $json;
                        }

                        return $summary."\n\n".$json;
                    })
                    ->wrap()
                    ->toggleable(true, true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all()),
                Tables\Filters\SelectFilter::make('event')
                    ->label('Event type')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'login' => 'Login',
                    ]),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-s-eye')
                    ->color('secondary')
                    ->tooltip('View full entry')
                    ->modalHeading(fn (ActivityLog $record): string => 'Activity log entry')
                    ->modalSubheading(fn (ActivityLog $record): ?string => $record->created_at?->format('M j, Y H:i:s'))
                    ->modalWidth('4xl')
                    ->modalContent(fn (ActivityLog $record) => view('filament.activity-log-view', ['record' => $record]))
                    ->modalActions(fn (): array => [
                        ModalAction::make('close')
                            ->label(__('filament-support::actions/view.single.modal.actions.close.label'))
                            ->cancel(),
                    ])
                    ->authorize(fn (ActivityLog $record): bool => static::canView($record))
                    ->action(fn () => null),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }
}
