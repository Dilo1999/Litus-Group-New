<?php

namespace App\Providers;

use App\Models\BlogPost;
use App\Models\Company;
use App\Models\GalleryEvent;
use App\Models\JobOpening;
use App\Models\PageSeo;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\User;
use App\Observers\AuditableModelObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // MySQL (e.g. MariaDB / older MySQL) has a 1000-byte index limit with utf8mb4.
        // Default string length 191 keeps unique indexes under that limit.
        Schema::defaultStringLength(191);

        // Use current request origin for storage URLs so Filament file previews
        // work without CORS (e.g. when using 127.0.0.1:8000 vs localhost).
        if (! $this->app->runningInConsole() && $this->app->request?->getHost()) {
            $url = $this->app->request->getSchemeAndHttpHost();
            config(['filesystems.disks.public.url' => $url.'/storage']);
        }

        $observer = AuditableModelObserver::class;
        BlogPost::observe($observer);
        Company::observe($observer);
        GalleryEvent::observe($observer);
        JobOpening::observe($observer);
        PageSeo::observe($observer);
        SiteSetting::observe($observer);
        TeamMember::observe($observer);
        User::observe($observer);
    }
}
