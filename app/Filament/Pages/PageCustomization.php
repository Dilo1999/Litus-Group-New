<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\BlocksHrAccess;
use App\Models\User;
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
        $user = auth()->user();
        if (! $user instanceof User || ! $user->isAdmin()) {
            abort(403);
        }
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return auth()->user() instanceof User && auth()->user()->isAdmin();
    }

    public function getBreadcrumbs(): array
    {
        return [
            Pages\Dashboard::getUrl() => 'Dashboard',
            static::getUrl() => 'Page Customization',
        ];
    }
}

