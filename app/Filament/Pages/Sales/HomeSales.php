<?php

namespace App\Filament\Pages\Sales;

use App\Filament\Pages\PageCustomization;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class HomeSales extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Home Sales Page';

    protected static ?string $slug = 'sales/home';

    protected static string $view = 'filament.pages.sales.home-sales';

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'hero_image_path' => SiteSetting::getValue('home.hero.image_path'),
        ]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            Pages\Dashboard::getUrl() => 'Dashboard',
            PageCustomization::getUrl() => 'Page Customization',
            static::getUrl() => 'Home',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Hero image')
                ->description('Upload, replace, or remove the hero image shown on the Home page.')
                ->schema([
                    Forms\Components\FileUpload::make('hero_image_path')
                        ->label('Hero image')
                        ->disk('public')
                        ->directory('site/home/hero')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->image()
                        ->imagePreviewHeight('180')
                        ->maxSize(4096)
                        ->helperText('PNG/JPG/WebP. Recommended: 1920×1080.'),
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

        $previousPath = SiteSetting::getValue('home.hero.image_path');
        $nextPath = $state['hero_image_path'] ?? null;

        if ($previousPath && $previousPath !== $nextPath) {
            Storage::disk('public')->delete($previousPath);
        }

        SiteSetting::setValue('home.hero.image_path', $nextPath);

        $this->notify('success', 'Hero image updated.');
    }

    public function removeHeroImage(): void
    {
        $previousPath = SiteSetting::getValue('home.hero.image_path');

        if ($previousPath) {
            Storage::disk('public')->delete($previousPath);
        }

        SiteSetting::setValue('home.hero.image_path', null);
        $this->form->fill(['hero_image_path' => null]);

        $this->notify('success', 'Hero image removed.');
    }
}

