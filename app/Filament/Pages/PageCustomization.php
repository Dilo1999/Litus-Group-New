<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesSuperAdminSettings;
use App\Filament\NavigationGroups;
use App\Models\User;
use Filament\Pages;
use Filament\Pages\Page;

class PageCustomization extends Page
{
    use AuthorizesSuperAdminSettings;

    protected static ?string $navigationIcon = 'heroicon-o-template';

    protected static ?string $navigationGroup = NavigationGroups::CUSTOMIZATION;

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Page Customization';

    protected static ?string $navigationLabel = 'Page Customization';

    protected static ?string $slug = 'page-customization';

    protected static string $view = 'filament.pages.page-customization';

    public function mount(): void
    {
        $this->authorizeSuperAdminSettings();
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return auth()->user() instanceof User && auth()->user()->canAccessSuperAdminSettings();
    }

    public function getBreadcrumbs(): array
    {
        return [
            Pages\Dashboard::getUrl() => 'Dashboard',
            static::getUrl() => 'Page Customization',
        ];
    }
}

