<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $rows = require app_path('Support/data/blog_posts.php');

        foreach ($rows as $row) {
            $slug = (string) ($row['slug'] ?? '');
            if ($slug === '') {
                continue;
            }

            $publishedAt = null;
            $date = (string) ($row['date'] ?? '');
            if ($date !== '') {
                $publishedAt = Carbon::createFromFormat('F j, Y', $date)->startOfDay();
            }

            $post = BlogPost::query()->firstOrNew(['slug' => $slug]);
            $post->fill([
                'title' => (string) ($row['title'] ?? ''),
                'category' => (string) ($row['category'] ?? ''),
                'author' => (string) ($row['author'] ?? ''),
                'read_time' => (string) ($row['readTime'] ?? ''),
                'image' => (string) ($row['image'] ?? ''),
                'excerpt' => (string) ($row['excerpt'] ?? ''),
                'content' => null,
                'published_at' => $publishedAt,
                'is_active' => true,
            ]);

            if ($publishedAt) {
                $post->created_at = $publishedAt;
                $post->updated_at = $publishedAt;
            }

            $post->save();
        }
    }
}

