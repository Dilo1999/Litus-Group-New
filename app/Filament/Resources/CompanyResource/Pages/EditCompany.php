<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return CompanyResource::hydrateRepeaterFields($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return CompanyResource::normalizeFormDataForSave($data, $this->getRecord());
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
