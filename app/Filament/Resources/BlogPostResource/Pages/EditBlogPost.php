<?php

namespace App\Filament\Resources\BlogPostResource\Pages;

use App\Filament\Resources\BlogPostResource;
use App\Models\BlogPost;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $base = Str::slug((string) ($data['title'] ?? ''));
        $base = $base !== '' ? $base : 'post';

        $slug = $base;
        $i = 2;
        while (
            BlogPost::query()
                ->where('slug', $slug)
                ->whereKeyNot($this->record->getKey())
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        $data['slug'] = $slug;

        return $data;
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

