<?php

namespace App\Providers;

use App\Models\BlogPost;
use App\Models\Company;
use App\Models\GalleryEvent;
use App\Models\JobOpening;
use App\Models\TeamMember;
use App\Models\User;
use App\Policies\BlogPostPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\GalleryEventPolicy;
use App\Policies\JobOpeningPolicy;
use App\Policies\TeamMemberPolicy;
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
        Company::class => CompanyPolicy::class,
        GalleryEvent::class => GalleryEventPolicy::class,
        JobOpening::class => JobOpeningPolicy::class,
        TeamMember::class => TeamMemberPolicy::class,
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
