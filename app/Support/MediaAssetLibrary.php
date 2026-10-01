<?php

namespace App\Support;

use App\Models\MediaAsset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Shared image library ("Assets" in the admin): registering files, URLs and usage lookups.
 */
class MediaAssetLibrary
{
    /** Folder on the public disk that new library uploads go to. */
    public const UPLOAD_DIRECTORY = 'media-assets';

    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'avif', 'svg'];

    public const MAX_UPLOAD_KB = 4096;

    /**
     * Folders scanned into the library. "site/" is left out on purpose: the page settings
     * screens delete their old image when it is replaced, so those files must not be shared.
     *
     * @return list<string>
     */
    public static function syncDirectories(): array
    {
        return [self::UPLOAD_DIRECTORY, 'blog-content', 'blog', 'blogs', 'companies', 'gallery', 'team'];
    }

    public static function isImagePath(?string $path): bool
    {
        return in_array(Str::lower(pathinfo((string) $path, PATHINFO_EXTENSION)), self::IMAGE_EXTENSIONS, true);
    }

    /** "/storage/a b/c.jpg" -> "/storage/a%20b/c.jpg" (root-relative, works on any host). */
    public static function publicUrl(?string $path): string
    {
        $path = static::normalizePath($path);

        if ($path === '') {
            return '';
        }

        return '/storage/'.implode('/', array_map('rawurlencode', explode('/', $path)));
    }

    public static function normalizePath(?string $path): string
    {
        $path = ltrim(str_replace('\\', '/', trim((string) $path)), '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        return str_replace('../', '', $path);
    }

    /**
     * Readable label for Livewire upload names, e.g. "hash-metaYmFzZS53ZWJw-.webp" -> "base".
     */
    public static function labelFromPath(string $path): string
    {
        $name = pathinfo($path, PATHINFO_FILENAME);

        if (preg_match('/-meta([A-Za-z0-9+\/=]+)-$/', $name, $matches)) {
            $decoded = base64_decode($matches[1], true);

            if (is_string($decoded) && preg_match('/^[\x20-\x7E]{1,255}$/', $decoded)) {
                return pathinfo($decoded, PATHINFO_FILENAME) ?: $name;
            }
        }

        return $name !== '' ? $name : 'Untitled asset';
    }

    public static function storeUpload(UploadedFile $file, ?string $name = null): MediaAsset
    {
        $original = pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = Str::lower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = (Str::slug($original) ?: 'image').'-'.Str::lower(Str::random(6)).'.'.$extension;
        $path = $file->storeAs(self::UPLOAD_DIRECTORY, $filename, 'public');

        if (! is_string($path) || $path === '') {
            throw new \RuntimeException('Could not store the image. Please try again.');
        }

        return MediaAsset::query()->create([
            'name' => trim((string) $name) ?: ($original !== '' ? $original : null),
            'path' => $path,
            'disk' => 'public',
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize() ?: null,
        ]);
    }

    /**
     * Register an existing public-disk file (no-op if it is already in the library).
     */
    public static function register(string $path): ?MediaAsset
    {
        $path = static::normalizePath($path);
        $disk = Storage::disk('public');

        if ($path === '' || ! static::isImagePath($path) || ! $disk->exists($path)) {
            return null;
        }

        $existing = MediaAsset::query()->where('path', $path)->first();

        if ($existing) {
            return $existing;
        }

        return MediaAsset::query()->create([
            'name' => static::labelFromPath($path),
            'path' => $path,
            'disk' => 'public',
            'mime_type' => rescue(fn () => $disk->mimeType($path) ?: null, null, false),
            'size' => rescue(fn () => $disk->size($path) ?: null, null, false),
        ]);
    }

    /**
     * Delete a file that a record no longer uses — unless it belongs to the Assets library,
     * where other pages may use it too (library files are only deleted from the Assets page).
     */
    public static function deleteUnlessShared(?string $path): bool
    {
        $path = static::normalizePath($path);

        if ($path === '' || (Schema::hasTable('media_assets') && MediaAsset::query()->where('path', $path)->exists())) {
            return false;
        }

        return Storage::disk('public')->delete($path);
    }

    /**
     * Add images found on disk to the library and drop entries whose file is gone.
     *
     * @return array{added: int, removed: int}
     */
    public static function sync(): array
    {
        $disk = Storage::disk('public');
        $known = MediaAsset::query()->pluck('path')->flip();
        $added = 0;

        foreach (static::syncDirectories() as $directory) {
            if (! $disk->exists($directory)) {
                continue;
            }

            foreach ($disk->allFiles($directory) as $path) {
                if (! isset($known[$path]) && static::isImagePath($path) && static::register($path)) {
                    $known[$path] = true;
                    $added++;
                }
            }
        }

        $removed = 0;
        MediaAsset::query()->orderBy('id')->each(function (MediaAsset $asset) use (&$removed): void {
            if (! $asset->fileExists()) {
                $asset->delete();
                $removed++;
            }
        });

        return compact('added', 'removed');
    }

    /**
     * Records that reference this file, so deleting it can warn first.
     *
     * @return list<string>
     */
    public static function usagesOf(?string $path): array
    {
        $path = static::normalizePath($path);

        if ($path === '') {
            return [];
        }

        // JSON columns store "/" as "\/"; HTML bodies use the encoded /storage/ URL.
        $json = str_replace('/', '\\/', $path);
        $like = fn (string $value): string => '%'.addcslashes($value, '%_\\').'%';
        $usages = [];

        $find = function (string $table, string $label, array $exact, array $contains = []) use ($path, $json, $like, &$usages): void {
            if (! Schema::hasTable($table)) {
                return;
            }

            $rows = DB::table($table)
                ->where(function ($query) use ($exact, $contains, $path, $json, $like): void {
                    foreach ($exact as $column) {
                        $query->orWhere($column, $path);
                    }
                    foreach ($contains as $column) {
                        $query->orWhere($column, 'like', $like($path))->orWhere($column, 'like', $like($json));
                    }
                })
                ->get(['id', $label]);

            foreach ($rows as $row) {
                $usages[] = Str::headline(Str::singular($table)).': '.($row->{$label} ?: '#'.$row->id);
            }
        };

        $find('blog_posts', 'title', ['image', 'og_image', 'twitter_image'], []);
        $find('companies', 'name', ['logo', 'hero_image', 'about_image', 'og_image', 'twitter_image'], ['services', 'strengths']);
        $find('gallery_events', 'title', ['cover_image', 'og_image', 'twitter_image'], ['gallery_images']);
        $find('team_members', 'name', ['photo']);

        if (Schema::hasTable('blog_posts')) {
            $url = static::publicUrl($path);
            foreach (DB::table('blog_posts')->where('body', 'like', $like($url))->orWhere('body', 'like', $like('/storage/'.$path))->get(['id', 'title']) as $row) {
                $usages[] = 'Blog post text: '.($row->title ?: '#'.$row->id);
            }
        }

        return array_values(array_unique($usages));
    }
}
