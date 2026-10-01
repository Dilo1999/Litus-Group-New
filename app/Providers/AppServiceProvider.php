<?php

namespace App\Providers;

use App\Models\BlogPost;
use App\Models\Company;
use App\Models\GalleryEvent;
use App\Models\JobOpening;
use App\Models\MediaAsset;
use App\Models\PageSeo;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\User;
use App\Observers\AuditableModelObserver;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // One instance per request so the controller and the layout share SEO state
        // (otherwise site-wide JSON-LD is emitted twice).
        $this->app->singleton(\App\Services\SeoService::class);
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
        MediaAsset::observe($observer);
        PageSeo::observe($observer);
        SiteSetting::observe($observer);
        TeamMember::observe($observer);
        User::observe($observer);

        // LITUS brand theme for the admin panel (navy primary + dashboard styles).
        Filament::serving(function () {
            Filament::registerStyles(collect(['admin-primary', 'admin', 'admin-assets'])
                ->mapWithKeys(fn (string $file) => [
                    "litus-{$file}" => '<link rel="stylesheet" href="'.asset("css/{$file}.css").'?v='.@filemtime(public_path("css/{$file}.css")).'" />',
                ])
                ->all());

            // Asset picker fields: must be registered before Alpine starts (core scripts).
            Filament::registerScripts([
                'litus-asset-picker' => asset('js/filament/asset-picker.js').'?v='.@filemtime(public_path('js/filament/asset-picker.js')),
            ], true);

            // Blog post HTML editor (live editable preview) + unsaved-changes warning.
            Filament::registerScripts(collect(['blog-post-html-preview', 'unsaved-changes-guard'])
                ->mapWithKeys(fn (string $file) => [
                    "litus-{$file}" => asset("js/filament/{$file}.js").'?v='.@filemtime(public_path("js/filament/{$file}.js")),
                ])
                ->all());
        });
    }
}
