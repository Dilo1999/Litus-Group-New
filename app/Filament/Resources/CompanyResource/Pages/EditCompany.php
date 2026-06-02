<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use App\Support\SiteData;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    /**
     * FileUpload drops non-storage paths on hydrate; restore legacy/URL logos so preview works in the uploader.
     */
    protected function afterFill(): void
    {
        $this->restoreNonDiskFileUploadPreview(field: 'logo', url: SiteData::companyLogoUrl($this->record->logo ?? null));

        $this->restoreExternalFileUploadPreview('hero_image');
        $this->restoreExternalFileUploadPreview('about_image');
    }

    protected function restoreExternalFileUploadPreview(string $field): void
    {
        $raw = $this->record->getAttribute($field);
        if (blank($raw)) {
            return;
        }

        if (str_starts_with((string) $raw, 'companies/') && Storage::disk('public')->exists((string) $raw)) {
            return;
        }

        if (str_starts_with((string) $raw, 'http://') || str_starts_with((string) $raw, 'https://')) {
            $this->data[$field] = [(string) Str::uuid() => (string) $raw];
        }
    }

    protected function restoreNonDiskFileUploadPreview(string $field, ?string $url): void
    {
        $raw = $this->record->getAttribute($field);
        if (blank($raw) || blank($url)) {
            return;
        }

        $onPublicDisk = str_starts_with((string) $raw, 'companies/')
            && Storage::disk('public')->exists((string) $raw);
        if ($onPublicDisk) {
            return;
        }

        $this->data[$field] = [(string) Str::uuid() => (string) $raw];
    }

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
