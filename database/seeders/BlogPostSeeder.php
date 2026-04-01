<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        // Seed removed: blog posts are managed via Filament.
        // Keep this seeder as a no-op for older environments.
        if (BlogPost::query()->exists()) {
            return;
        }
    }
}

