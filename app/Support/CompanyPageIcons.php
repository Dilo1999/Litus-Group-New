<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class CompanyPageIcons
{
    /**
     * @return array{label: string, icon_url: ?string}
     */
    public static function resolveLabeledItem(mixed $item): array
    {
        $label = is_array($item)
            ? trim((string) ($item['label'] ?? ''))
            : trim((string) $item);

        $iconPath = is_array($item) ? ($item['icon_path'] ?? null) : null;

        return [
            'label' => $label,
            'icon_url' => filled($iconPath) ? self::storedIconUrl((string) $iconPath) : null,
        ];
    }

    public static function storedIconUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        try {
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->url($path);
            }
        } catch (\Throwable) {
        }

        return null;
    }

    /**
     * @param  list<mixed>  $items
     * @return list<string>
     */
    public static function iconPathsFromItems(array $items): array
    {
        return collect($items)
            ->map(function ($item) {
                if (! is_array($item)) {
                    return null;
                }

                $path = $item['icon_path'] ?? null;

                return is_string($path) && $path !== '' ? $path : null;
            })
            ->filter()
            ->values()
            ->all();
    }
}
