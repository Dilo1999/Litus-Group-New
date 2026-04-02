<?php

namespace App\Providers;

use App\Models\BlogPost;
use App\Models\GalleryEvent;
use App\Models\PageSeo;
use App\Models\User;
use App\Policies\BlogPostPolicy;
use App\Policies\GalleryEventPolicy;
use App\Policies\PageSeoPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        BlogPost::class => BlogPostPolicy::class,
        GalleryEvent::class => GalleryEventPolicy::class,
        PageSeo::class => PageSeoPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
