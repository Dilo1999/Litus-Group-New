<?php

namespace App\Filament\Forms\Components;

use App\Support\MediaAssetLibrary;
use Closure;
use Filament\Forms\Components\Field;

/**
 * Image field backed by the shared Assets library: "Choose from Assets" or "Upload new".
 *
 * Stores the same value as a FileUpload on the public disk — a path string, or an array of
 * paths with ->multiple() — so it can replace an existing FileUpload without a data migration.
 * Picked files are shared: removing an image here never deletes the file.
 */
class AssetPicker extends Field
{
    protected string $view = 'filament.forms.components.asset-picker';

    protected bool | Closure $isMultiple = false;

    protected int | string | Closure | null $imagePreviewHeight = 160;

    protected int | Closure $maxSizeKb = MediaAssetLibrary::MAX_UPLOAD_KB;

    protected string | Closure | null $fallbackBaseUrl = null;

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function imagePreviewHeight(int | string | Closure | null $height): static
    {
        $this->imagePreviewHeight = $height;

        return $this;
    }

    public function getImagePreviewHeight(): string
    {
        $height = $this->evaluate($this->imagePreviewHeight) ?: 160;

        return is_numeric($height) ? $height.'px' : (string) $height;
    }

    /** Largest image (KB) that may be uploaded or picked for this field. */
    public function maxSize(int | Closure $kilobytes): static
    {
        $this->maxSizeKb = $kilobytes;

        return $this;
    }

    public function getMaxSizeKb(): int
    {
        return min(MediaAssetLibrary::MAX_UPLOAD_KB, max(1, (int) $this->evaluate($this->maxSizeKb)));
    }

    /**
     * Base URL for older values that are a bare file name instead of a storage path
     * (e.g. company logos shipped in public/assets/logo).
     */
    public function fallbackBaseUrl(string | Closure | null $url): static
    {
        $this->fallbackBaseUrl = $url;

        return $this;
    }

    public function getFallbackBaseUrl(): ?string
    {
        $url = $this->evaluate($this->fallbackBaseUrl);

        return filled($url) ? rtrim((string) $url, '/').'/' : null;
    }

    public function getAssetsUrl(): string
    {
        return route('admin.media-assets.index');
    }

    public function getUploadUrl(): string
    {
        return route('admin.media-assets.store');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $clean = fn ($paths): array => array_values(array_filter(
            is_array($paths) ? $paths : (filled($paths) ? [$paths] : []),
            fn ($path): bool => is_string($path) && $path !== ''
        ));

        $this->afterStateHydrated(function (AssetPicker $component, $state) use ($clean): void {
            $paths = $clean($state);
            $component->state($component->isMultiple() ? $paths : ($paths[0] ?? null));
        });

        $this->dehydrateStateUsing(function (AssetPicker $component, $state) use ($clean) {
            $paths = $clean($state);

            return $component->isMultiple() ? $paths : ($paths[0] ?? null);
        });
    }
}
