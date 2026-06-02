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

class ContactSales extends Page implements HasForms
{
    use BlocksHrAccess;
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Contact Sales Page';

    protected static ?string $slug = 'sales/contact';

    protected static string $view = 'filament.pages.sales.contact-sales';

    public array $data = [];

    public function mount(): void
    {
        $this->abortIfHr();

        $this->form->fill([
            'hero_image_path' => SiteSetting::getValue('contact.hero.image_path'),
            'hero_image_position_y' => (int) SiteSetting::getValue('contact.hero.position_y', 50),
        ]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            Pages\Dashboard::getUrl() => 'Dashboard',
            PageCustomization::getUrl() => 'Page Customization',
            static::getUrl() => 'Contact Us',
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
                'site/contact/hero',
                'Upload, replace, or remove the hero image shown on the Contact Us page.'
            ),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $previousPath = SiteSetting::getValue('contact.hero.image_path');
        $nextPath = $state['hero_image_path'] ?? null;

        if ($previousPath && $previousPath !== $nextPath) {
            Storage::disk('public')->delete($previousPath);
        }

        SiteSetting::setValue('contact.hero.image_path', $nextPath);
        SiteSetting::setValue('contact.hero.position_y', (int) ($state['hero_image_position_y'] ?? 50));

        $this->notify('success', 'Contact hero image updated.');
    }
}

