<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Support\MediaAssetLibrary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON API behind the image pickers (Asset picker fields and the blog post HTML editor).
 */
class MediaAssetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q', ''));
        $perPage = max(1, min(100, (int) $request->query('per_page', 24)));
        $page = max(1, (int) $request->query('page', 1));

        $query = MediaAsset::query()
            ->when($search !== '', fn ($query) => $query->where(fn ($inner) => $inner
                ->where('name', 'like', '%'.$search.'%')
                ->orWhere('path', 'like', '%'.$search.'%')))
            ->orderByDesc('id');

        $total = (clone $query)->count();

        return response()->json([
            'data' => $query->forPage($page, $perPage)->get()->map(fn (MediaAsset $asset): array => $this->toArray($asset))->values(),
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'has_more' => ($page * $perPage) < $total,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // A field may ask for a smaller limit (e.g. 500 KB company images), never a larger one.
        $maxKb = min(MediaAssetLibrary::MAX_UPLOAD_KB, max(1, (int) $request->input('max_kb', MediaAssetLibrary::MAX_UPLOAD_KB)));

        $request->validate([
            'image' => ['required', 'file', 'max:'.$maxKb, 'mimes:'.implode(',', MediaAssetLibrary::IMAGE_EXTENSIONS)],
            'name' => ['nullable', 'string', 'max:255'],
        ], [
            'image.max' => 'The image must not be larger than '.($maxKb >= 1024 ? round($maxKb / 1024, 1).' MB' : $maxKb.' KB').'.',
        ]);

        try {
            $asset = MediaAssetLibrary::storeUpload($request->file('image'), $request->input('name'));
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json($this->toArray($asset));
    }

    /**
     * @return array<string, mixed>
     */
    private function toArray(MediaAsset $asset): array
    {
        return [
            'id' => $asset->id,
            'name' => $asset->displayName(),
            'path' => $asset->path,
            'url' => $asset->publicUrl(),
            'size' => $asset->size,
            'size_label' => $asset->sizeLabel(),
        ];
    }
}
