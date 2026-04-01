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
        'published_at',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'content_blocks' => 'array',
    ];
}

