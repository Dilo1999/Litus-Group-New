<?php

namespace App\Filament\Pages\Sales;

use App\Filament\Pages\PageCustomization;
use Filament\Pages;
use Filament\Pages\Page;

class CareersSales extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Careers Sales Page';

    protected static ?string $slug = 'sales/careers';

    protected static string $view = 'filament.pages.sales.careers-sales';

    public function getBreadcrumbs(): array
    {
        return [
            Pages\Dashboard::getUrl() => 'Dashboard',
            PageCustomization::getUrl() => 'Page Customization',
            static::getUrl() => 'Careers',
        ];
    }
}

