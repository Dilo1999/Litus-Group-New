<?php

namespace App\Filament\Resources\GalleryEventResource\Pages;

use App\Filament\Resources\GalleryEventResource;
use App\Models\GalleryEvent;
use Filament\Resources\Pages\CreateRecord;

class CreateGalleryEvent extends CreateRecord
{
    protected static string $resource = GalleryEventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure `sort_order` always reflects the order entries are created in.
        $data['sort_order'] = GalleryEvent::query()->max('sort_order') + 1;

        return $data;
    }
}
