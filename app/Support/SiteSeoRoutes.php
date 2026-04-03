<?php

namespace App\Support;

/**
 * Public site routes managed under Filament “Page SEO” (static URLs only).
 */
class SiteSeoRoutes
{
    /**
     * Laravel route name => admin label
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            'site.home' => 'Home',
            'site.our-companies' => 'Our Companies',
            'site.about' => 'About',
            'site.team' => 'Team',
            'site.careers' => 'Careers',
            'site.blogs' => 'News & Media (listing)',
            'site.contact' => 'Contact',
        ];
    }
}
