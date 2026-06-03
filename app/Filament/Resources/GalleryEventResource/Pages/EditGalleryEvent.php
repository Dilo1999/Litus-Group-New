<?php

namespace App\Filament\Resources\GalleryEventResource\Pages;

use App\Filament\Resources\GalleryEventResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGalleryEvent extends EditRecord
{
    protected static string $resource = GalleryEventResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return GalleryEventResource::assignSlug($data, $this->getRecord());
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
