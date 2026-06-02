<?php

namespace App\Filament\Pages\Sales;

use App\Filament\Concerns\BlocksHrAccess;
use App\Filament\Pages\PageCustomization;
use App\Filament\Support\HeroImageForm;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class OurCompaniesSales extends Page implements HasForms
{
    use BlocksHrAccess;
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Our Companies Sales Page';

    protected static ?string $slug = 'sales/our-companies';

    protected static string $view = 'filament.pages.sales.our-companies-sales';

    public array $data = [];

    public function mount(): void
    {
        $this->abortIfHr();

        $this->form->fill([
            'hero_image_path' => SiteSetting::getValue('our_companies.hero.image_path'),
            'hero_image_position_y' => (int) SiteSetting::getValue('our_companies.hero.position_y', 50),
        ]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            Pages\Dashboard::getUrl() => 'Dashboard',
            PageCustomization::getUrl() => 'Page Customization',
            static::getUrl() => 'Our Companies',
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
                'site/our-companies/hero',
                'Upload, replace, or remove the hero image shown on the Our Companies page.'
            ),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $previousPath = SiteSetting::getValue('our_companies.hero.image_path');
        $nextPath = $state['hero_image_path'] ?? null;

        if ($previousPath && $previousPath !== $nextPath) {
            Storage::disk('public')->delete($previousPath);
        }

        SiteSetting::setValue('our_companies.hero.image_path', $nextPath);
        SiteSetting::setValue('our_companies.hero.position_y', (int) ($state['hero_image_position_y'] ?? 50));

        $this->notify('success', 'Our Companies hero image updated.');
    }
}
