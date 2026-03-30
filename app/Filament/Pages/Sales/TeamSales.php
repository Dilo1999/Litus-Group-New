<?php

namespace App\Filament\Pages\Sales;

use App\Filament\Pages\PageCustomization;
use Filament\Pages;
use Filament\Pages\Page;

class TeamSales extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Team Sales Page';

    protected static ?string $slug = 'sales/team';

    protected static string $view = 'filament.pages.sales.team-sales';

    public function getBreadcrumbs(): array
    {
        return [
            Pages\Dashboard::getUrl() => 'Dashboard',
            PageCustomization::getUrl() => 'Page Customization',
            static::getUrl() => 'Team',
        ];
    }
}

