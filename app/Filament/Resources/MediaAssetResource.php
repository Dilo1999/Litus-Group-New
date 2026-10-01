<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaAssetResource\Pages;
use App\Models\MediaAsset;
use App\Models\User;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class MediaAssetResource extends Resource
{
    protected static ?string $model = MediaAsset::class;

    protected static ?string $navigationIcon = 'heroicon-o-photograph';
    protected static ?string $navigationLabel = 'Assets';
    protected static ?string $modelLabel = 'Asset';
    protected static ?string $pluralModelLabel = 'Assets';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $slug = 'assets';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 87;

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

    public static function canEdit(Model $record): bool
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
            ->contentGrid([
                'default' => 1,
                'sm' => 2,
                'md' => 3,
                'xl' => 4,
            ])
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ImageColumn::make('path')
                        ->label('Preview')
                        // Full URL: ImageColumn passes anything else through Storage::url() again.
                        ->getStateUsing(fn (MediaAsset $record): string => $record->absoluteUrl())
                        ->height(160)
                        ->extraImgAttributes([
                            'loading' => 'lazy',
                            'style' => 'height:10rem;width:100%;object-fit:cover;border-radius:.6rem;background:#f1f3f8;',
                        ]),
                    Tables\Columns\TextColumn::make('name')
                        ->label('Name')
                        ->weight('bold')
                        ->limit(30)
                        ->getStateUsing(fn (MediaAsset $record): string => $record->displayName())
                        ->tooltip(fn (MediaAsset $record): string => $record->displayName())
                        ->searchable(['name', 'path'])
                        ->sortable(),
                    Tables\Columns\TextColumn::make('size')
                        ->label('Size')
                        ->getStateUsing(fn (MediaAsset $record): string => $record->sizeLabel().' · '.$record->path)
                        ->limit(48)
                        ->tooltip(fn (MediaAsset $record): string => $record->path)
                        ->color('secondary')
                        ->size('sm'),
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Uploaded')
                        ->dateTime()
                        ->color('secondary')
                        ->size('sm')
                        ->sortable(),
                ])->space(2),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\Action::make('copyUrl')
                    ->label('Copy URL')
                    ->icon('heroicon-o-clipboard-copy')
                    ->color('secondary')
                    ->action(function (MediaAsset $record, $livewire): void {
                        $url = $record->absoluteUrl();

                        $livewire->dispatchBrowserEvent('litus-copy-to-clipboard', ['text' => $url]);

                        Notification::make()->title('URL copied')->body($url)->success()->send();
                    }),
                Tables\Actions\Action::make('rename')
                    ->label('Rename')
                    ->icon('heroicon-o-pencil')
                    ->color('secondary')
                    ->mountUsing(fn (Forms\ComponentContainer $form, MediaAsset $record) => $form->fill(['name' => $record->displayName()]))
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Shown in the Assets library and used as the default alt text when the image is inserted into a blog post.'),
                    ])
                    ->action(function (MediaAsset $record, array $data): void {
                        $record->update(['name' => $data['name']]);

                        Notification::make()->title('Asset renamed')->success()->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->modalSubheading(function (MediaAsset $record): HtmlString {
                        $usages = $record->usages();

                        if ($usages === []) {
                            return new HtmlString('The image file is deleted permanently. It is not used anywhere on the website.');
                        }

                        return new HtmlString(
                            '<strong>This image is still used on the website</strong> and cannot be deleted until it is replaced there:<br>'
                            .collect($usages)->map(fn (string $usage): string => '• '.e($usage))->implode('<br>')
                        );
                    })
                    ->before(function (MediaAsset $record, Tables\Actions\DeleteAction $action): void {
                        if ($record->usages() !== []) {
                            Notification::make()
                                ->title('Image is in use')
                                ->body('Replace it on the pages listed first, then delete it.')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    })
                    ->after(fn (MediaAsset $record) => static::deleteFile($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('delete')
                    ->label('Delete selected')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalSubheading('Images still used on the website are skipped.')
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records): void {
                        $deleted = 0;
                        $skipped = 0;

                        foreach ($records as $record) {
                            if ($record->usages() !== []) {
                                $skipped++;

                                continue;
                            }

                            static::deleteFile($record);
                            $record->delete();
                            $deleted++;
                        }

                        Notification::make()
                            ->title($deleted.' '.str('asset')->plural($deleted).' deleted')
                            ->body($skipped ? $skipped.' skipped because they are still used on the website.' : null)
                            ->success()
                            ->send();
                    }),
            ]);
    }

    protected static function deleteFile(MediaAsset $record): void
    {
        if (filled($record->path)) {
            Storage::disk($record->disk ?: 'public')->delete($record->path);
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMediaAssets::route('/'),
            'create' => Pages\CreateMediaAsset::route('/upload'),
        ];
    }
}
