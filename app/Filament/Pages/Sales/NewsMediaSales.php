<?php

namespace App\Filament\Pages\Sales;

use App\Filament\Concerns\AuthorizesSuperAdminSettings;
use App\Filament\Pages\PageCustomization;
use App\Filament\Support\HeroImageForm;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class NewsMediaSales extends Page implements HasForms
{
    use AuthorizesSuperAdminSettings;
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'News & Media Sales Page';

    protected static ?string $slug = 'sales/news-media';

    protected static string $view = 'filament.pages.sales.news-media-sales';

    public array $data = [];

    public function mount(): void
    {
        $this->authorizeSuperAdminSettings();

        $this->form->fill([
            'hero_image_path' => SiteSetting::getValue('blogs.hero.image_path'),
            'hero_image_position_y' => (int) SiteSetting::getValue('blogs.hero.position_y', 50),
        ]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            Pages\Dashboard::getUrl() => 'Dashboard',
            PageCustomization::getUrl() => 'Page Customization',
            static::getUrl() => 'News & Media',
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return [
            HeroImageForm::section(
                'site/blogs/hero',
                'Upload, replace, or remove the hero image shown on the News & Media page.'
            ),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $previousPath = SiteSetting::getValue('blogs.hero.image_path');
        $nextPath = $state['hero_image_path'] ?? null;

        if ($previousPath && $previousPath !== $nextPath) {
            Storage::disk('public')->delete($previousPath);
        }

        SiteSetting::setValue('blogs.hero.image_path', $nextPath);
        SiteSetting::setValue('blogs.hero.position_y', (int) ($state['hero_image_position_y'] ?? 50));

        $this->notify('success', 'News & Media hero image updated.');
    }
}

