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

        $aboutRaw = $this->record->about_image ?? null;
        $aboutUrl = null;
        if (filled($aboutRaw) && (str_starts_with($aboutRaw, 'http://') || str_starts_with($aboutRaw, 'https://'))) {
            $aboutUrl = $aboutRaw;
        } elseif (filled($aboutRaw) && str_starts_with($aboutRaw, 'companies/') && Storage::disk('public')->exists($aboutRaw)) {
            // Already on disk; FileUpload will hydrate it.
            $aboutUrl = null;
        } elseif (filled($aboutRaw)) {
            // If it isn't a URL and doesn't exist on disk, don't attempt to preview.
            $aboutUrl = null;
        }

        if ($aboutUrl) {
            $this->data['about_image'] = [(string) Str::uuid() => $aboutUrl];
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
