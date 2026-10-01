<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'author',
        'read_time',
        'image',
        'excerpt',
        'content',
        'content_blocks',
        'body',
        'published_at',
        'is_active',
        'meta_title',
        'meta_description',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'canonical_url',
        'robots',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'content_blocks' => 'array',
    ];

    /**
     * Article HTML for the public page (written in the admin HTML editor).
     */
    public function bodyHtml(): string
    {
        return trim((string) $this->body);
    }
}

