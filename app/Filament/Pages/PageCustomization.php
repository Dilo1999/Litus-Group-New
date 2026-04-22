<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\BlocksHrAccess;
use Filament\Pages;
use Filament\Pages\Page;

class PageCustomization extends Page
{
    use BlocksHrAccess;

    protected static ?string $navigationIcon = 'heroicon-o-template';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 90;

    protected static ?string $title = 'Page Customization';

    protected static ?string $navigationLabel = 'Page Customization';

    protected static ?string $slug = 'page-customization';

    protected static string $view = 'filament.pages.page-customization';

    public function mount(): void
    {
        $this->abortIfHr();
    }

    protected static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()?->isHr()) {
            return false;
        }

        return parent::shouldRegisterNavigation();
    }

    public function getBreadcrumbs(): array
    {
        return [
            Pages\Dashboard::getUrl() => 'Dashboard',
            static::getUrl() => 'Page Customization',
        ];
    }
}

