<?php

namespace App\Filament\Pages\Sales;

use App\Filament\Concerns\BlocksHrAccess;
use App\Filament\Pages\PageCustomization;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class CareersSales extends Page implements HasForms
{
    use BlocksHrAccess;
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Careers Sales Page';

    protected static ?string $slug = 'sales/careers';

    protected static string $view = 'filament.pages.sales.careers-sales';

    public array $data = [];

    public function mount(): void
    {
        $this->abortIfHr();

        $this->form->fill([
            'hero_image_path' => SiteSetting::getValue('careers.hero.image_path'),
        ]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            Pages\Dashboard::getUrl() => 'Dashboard',
            PageCustomization::getUrl() => 'Page Customization',
            static::getUrl() => 'Careers',
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Hero image')
                ->description('Upload, replace, or remove the hero image shown on the Careers page.')
                ->schema([
                    Forms\Components\FileUpload::make('hero_image_path')
                        ->label('Hero image')
                        ->disk('public')
                        ->directory('site/careers/hero')
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

    public function save(): void
    {
        $state = $this->form->getState();

        $previousPath = SiteSetting::getValue('careers.hero.image_path');
        $nextPath = $state['hero_image_path'] ?? null;

        if ($previousPath && $previousPath !== $nextPath) {
            Storage::disk('public')->delete($previousPath);
        }

        SiteSetting::setValue('careers.hero.image_path', $nextPath);

        $this->notify('success', 'Careers hero image updated.');
    }
}

