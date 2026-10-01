<?php

namespace App\Models;

use App\Support\MediaAssetLibrary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * One image in the shared Assets library (a file on the public disk).
 */
class MediaAsset extends Model
{
    protected $fillable = [
        'name',
        'path',
        'disk',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    /**
     * Root-relative URL (/storage/...). Used inside saved HTML so it works on any host.
     */
    public function publicUrl(): string
    {
        return MediaAssetLibrary::publicUrl($this->path);
    }

    /**
     * Full URL on the current host, for copying / sharing.
     */
    public function absoluteUrl(): string
    {
        $relative = $this->publicUrl();

        if ($relative === '') {
            return '';
        }

        return rtrim((string) (request()?->getSchemeAndHttpHost() ?: config('app.url')), '/').$relative;
    }

    public function fileExists(): bool
    {
        try {
            return Storage::disk($this->disk ?: 'public')->exists($this->path);
        } catch (\Throwable) {
            return false;
        }
    }

    public function displayName(): string
    {
        $name = trim((string) $this->name);

        return $name !== '' ? $name : (pathinfo((string) $this->path, PATHINFO_FILENAME) ?: 'Untitled asset');
    }

    public function sizeLabel(): string
    {
        if (! $this->size) {
            return '—';
        }

        return $this->size >= 1048576
            ? number_format($this->size / 1048576, 1).' MB'
            : max(1, (int) round($this->size / 1024)).' KB';
    }

    /**
     * Where this image is used on the website (e.g. "Blog post: Title").
     *
     * @return list<string>
     */
    public function usages(): array
    {
        return MediaAssetLibrary::usagesOf($this->path);
    }
}
