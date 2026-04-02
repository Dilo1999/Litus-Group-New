<?php

namespace App\Filament\Pages;

use Filament\Pages;
use Filament\Pages\Page;

class PageCustomization extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-template';

    protected static ?string $navigationGroup = 'Management';

    protected static ?int $navigationSort = 90;

    protected static ?string $title = 'Page Customization';

    protected static ?string $navigationLabel = 'Page Customization';

    protected static ?string $slug = 'page-customization';

    protected static string $view = 'filament.pages.page-customization';

    public function getBreadcrumbs(): array
    {
        return [
            Pages\Dashboard::getUrl() => 'Dashboard',
            static::getUrl() => 'Page Customization',
        ];
    }
}

