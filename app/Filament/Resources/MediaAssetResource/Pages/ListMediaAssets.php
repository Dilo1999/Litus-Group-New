<?php

namespace App\Filament\Resources\MediaAssetResource\Pages;

use App\Filament\Resources\MediaAssetResource;
use App\Support\MediaAssetLibrary;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMediaAssets extends ListRecords
{
    protected static string $resource = MediaAssetResource::class;

    protected function getTitle(): string
    {
        return 'Assets';
    }

    protected function getSubheading(): ?string
    {
        return 'Shared image library for blog posts, gallery events, companies and team members. Upload once, reuse anywhere.';
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [48, 96, 24, 12];
    }

    protected function getActions(): array
    {
        return [
            Actions\Action::make('sync')
                ->label('Scan storage')
                ->icon('heroicon-o-refresh')
                ->color('secondary')
                ->tooltip('Add images that were uploaded elsewhere and remove entries whose file no longer exists')
                ->action(function (): void {
                    $result = MediaAssetLibrary::sync();

                    Notification::make()
                        ->title('Storage scanned')
                        ->body("Added {$result['added']}, removed {$result['removed']} missing.")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make()
                ->label('Upload assets')
                ->icon('heroicon-o-upload'),
        ];
    }
}
