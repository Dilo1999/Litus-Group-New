<?php

namespace App\Filament\Resources\MediaAssetResource\Pages;

use App\Filament\Resources\MediaAssetResource;
use App\Models\MediaAsset;
use App\Support\MediaAssetLibrary;
use Filament\Forms;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\TemporaryUploadedFile;

class CreateMediaAsset extends CreateRecord
{
    protected static string $resource = MediaAssetResource::class;

    protected function getTitle(): string
    {
        return 'Upload assets';
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Upload images')
                ->description('Select one or more images at once. File names are used as asset names (you can rename them later).')
                ->schema([
                    Forms\Components\FileUpload::make('paths')
                        ->label('Images')
                        ->image()
                        ->multiple()
                        ->disk('public')
                        ->directory(MediaAssetLibrary::UPLOAD_DIRECTORY)
                        ->visibility('public')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp', 'image/avif', 'image/svg+xml'])
                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                            $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                            return (Str::slug($original) ?: 'image').'-'.Str::lower(Str::random(6)).'.'.Str::lower($file->getClientOriginalExtension() ?: 'jpg');
                        })
                        ->storeFileNamesIn('original_names')
                        ->maxSize(MediaAssetLibrary::MAX_UPLOAD_KB)
                        ->required()
                        ->helperText('JPEG, PNG, GIF, WebP, BMP, AVIF or SVG. Max 4 MB each. For company and gallery images keep files under 500 KB.')
                        ->columnSpanFull(),
                ]),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $paths = array_values(array_filter((array) ($data['paths'] ?? []), fn ($path): bool => is_string($path) && $path !== ''));
        // Written by storeFileNamesIn(); read from the raw form state (it is not a field of its own).
        $names = (array) ($data['original_names'] ?? $this->data['original_names'] ?? []);

        if ($paths === []) {
            throw ValidationException::withMessages(['data.paths' => 'Please select at least one image to upload.']);
        }

        $first = null;

        foreach ($paths as $path) {
            $disk = Storage::disk('public');
            $original = isset($names[$path]) ? pathinfo((string) $names[$path], PATHINFO_FILENAME) : '';

            $asset = MediaAsset::query()->create([
                'name' => $original !== '' ? $original : MediaAssetLibrary::labelFromPath($path),
                'path' => $path,
                'disk' => 'public',
                'mime_type' => rescue(fn () => $disk->mimeType($path) ?: null, null, false),
                'size' => rescue(fn () => $disk->size($path) ?: null, null, false),
            ]);

            $first ??= $asset;
        }

        return $first;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Assets uploaded';
    }
}
