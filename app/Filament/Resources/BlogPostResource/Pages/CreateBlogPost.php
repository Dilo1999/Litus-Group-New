<?php

namespace App\Filament\Resources\BlogPostResource\Pages;

use App\Filament\Resources\BlogPostResource;
use App\Models\BlogPost;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateBlogPost extends CreateRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $base = Str::slug((string) ($data['title'] ?? ''));
        $base = $base !== '' ? $base : 'post';

        $slug = $base;
        $i = 2;
        while (BlogPost::query()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        $data['slug'] = $slug;

        return $data;
    }
}

