<?php

namespace App\Filament\Pages\Sales;

use App\Filament\Pages\PageCustomization;
use Filament\Pages;
use Filament\Pages\Page;

class AboutUsSales extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'About Us Sales Page';

    protected static ?string $slug = 'sales/about-us';

    protected static string $view = 'filament.pages.sales.about-us-sales';

    public function getBreadcrumbs(): array
    {
        return [
            Pages\Dashboard::getUrl() => 'Dashboard',
            PageCustomization::getUrl() => 'Page Customization',
            static::getUrl() => 'About Us',
        ];
    }
}

