<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    protected static function booted(): void
    {
        static::deleting(function (Company $company): void {
            if ($company->logo && str_starts_with($company->logo, 'companies/')) {
                Storage::disk('public')->delete($company->logo);
            }
        });
    }

    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'description',
        'description_secondary',
        'category',
        'division',
        'logo',
        'about_image',
        'icon',
        'hotline',
        'email',
        'services',
        'strengths',
        'featured',
        'sort_order',
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
        'services' => 'array',
        'strengths' => 'array',
        'featured' => 'boolean',
    ];

    /**
     * Shape expected by existing Blade / SiteData consumers.
     *
     * @return array<string, mixed>
     */
    public function toSitePayload(): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'description_secondary' => $this->description_secondary,
            'category' => $this->category,
            'division' => $this->division,
            'logo' => $this->logo,
            'about_image' => $this->about_image,
            'icon' => $this->icon,
            'hotline' => $this->hotline,
            'email' => $this->email,
            'services' => $this->services ?? [],
            'strengths' => $this->strengths ?? [],
            'featured' => (bool) $this->featured,
        ];
    }
}
