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

class AboutUsSales extends Page implements HasForms
{
    use BlocksHrAccess;
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'About Us Sales Page';

    protected static ?string $slug = 'sales/about-us';

    protected static string $view = 'filament.pages.sales.about-us-sales';

    public array $data = [];

    public function mount(): void
    {
        $this->abortIfHr();

        $this->form->fill([
            'business_partnership_image_path' => SiteSetting::getValue('about.business_partnership.image_path'),
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
            Forms\Components\Section::make('Business partnership image')
                ->description('Image shown beside “About LITUS Group” on the public About Us page.')
                ->schema([
                    Forms\Components\FileUpload::make('business_partnership_image_path')
                        ->label('Section image')
                        ->disk('public')
                        ->directory('site/about/business-partnership')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->image()
                        ->imagePreviewHeight('180')
                        ->maxSize(4096)
                        ->helperText('PNG/JPG/WebP. Recommended: 1200×900 or similar landscape.'),
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

        $previousPath = SiteSetting::getValue('about.business_partnership.image_path');
        $nextPath = $state['business_partnership_image_path'] ?? null;

        if ($previousPath && $previousPath !== $nextPath) {
            Storage::disk('public')->delete($previousPath);
        }

        SiteSetting::setValue('about.business_partnership.image_path', $nextPath);

        $this->notify('success', 'About Us image updated.');
    }

    public function removeBusinessPartnershipImage(): void
    {
        $previousPath = SiteSetting::getValue('about.business_partnership.image_path');

        if ($previousPath) {
            Storage::disk('public')->delete($previousPath);
        }

        SiteSetting::setValue('about.business_partnership.image_path', null);
        $this->form->fill(['business_partnership_image_path' => null]);

        $this->notify('success', 'Image removed.');
    }
}
