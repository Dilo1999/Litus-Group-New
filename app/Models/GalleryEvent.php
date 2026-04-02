<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryEvent extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'date_display',
        'description',
        'cover_image',
        'image_alt',
        'gallery_images',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'is_active' => 'boolean',
    ];
}
