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

class AboutUsSales extends Page implements HasForms
{
    use AuthorizesSuperAdminSettings;
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'About Us Sales Page';

    protected static ?string $slug = 'sales/about-us';

    protected static string $view = 'filament.pages.sales.about-us-sales';

    public array $data = [];

    public function mount(): void
    {
        $this->authorizeSuperAdminSettings();

        $this->form->fill([
            'hero_image_path' => SiteSetting::getValue('about.hero.image_path'),
            'hero_image_position_y' => (int) SiteSetting::getValue('about.hero.position_y', 50),
            'intro_paragraph_1' => SiteSetting::getValue(
                'about.intro.paragraph_1',
                'LITUS Group brings together businesses in automotive, building materials, logistics, engineering, currency exchange, travel, hospitality, construction and technology. Based in the Maldives, we serve individuals, businesses, resorts and project teams through companies specialising in their respective industries.'
            ),
            'intro_paragraph_2' => SiteSetting::getValue(
                'about.intro.paragraph_2',
                'From motorcycle spare parts and home improvements to freight movements and resort engineering support, our businesses provide practical products and services that help customers keep everyday life and business moving.'
            ),
        ]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            Pages\Dashboard::getUrl() => 'Dashboard',
            PageCustomization::getUrl() => 'Page Customization',
            static::getUrl() => 'About Us',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            HeroImageForm::section(
                'site/about/hero',
                'Upload, replace, or remove the hero image shown at the top of the public About Us page.'
            ),
            Forms\Components\Section::make('Who we are — intro text')
                ->description('Two paragraphs shown beside “A diversified business group in the Maldives.” on the public About Us page.')
                ->schema([
                    Forms\Components\Textarea::make('intro_paragraph_1')
                        ->label('First paragraph')
                        ->rows(4)
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('intro_paragraph_2')
                        ->label('Second paragraph')
                        ->rows(4)
                        ->required()
                        ->columnSpanFull(),
                ])
                ->columns(1),
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $previousHero = SiteSetting::getValue('about.hero.image_path');
        $nextHero = $state['hero_image_path'] ?? null;
        if ($previousHero && $previousHero !== $nextHero) {
            Storage::disk('public')->delete($previousHero);
        }
        SiteSetting::setValue('about.hero.image_path', $nextHero);
        SiteSetting::setValue('about.hero.position_y', (int) ($state['hero_image_position_y'] ?? 50));

        SiteSetting::setValue('about.intro.paragraph_1', $state['intro_paragraph_1'] ?? '');
        SiteSetting::setValue('about.intro.paragraph_2', $state['intro_paragraph_2'] ?? '');

        $this->notify('success', 'About Us page updated.');
    }

    public function removeHeroImage(): void
    {
        $previousPath = SiteSetting::getValue('about.hero.image_path');

        if ($previousPath) {
            Storage::disk('public')->delete($previousPath);
        }

        SiteSetting::setValue('about.hero.image_path', null);
        $this->form->fill(['hero_image_path' => null]);

        $this->notify('success', 'Hero image removed.');
    }
}
