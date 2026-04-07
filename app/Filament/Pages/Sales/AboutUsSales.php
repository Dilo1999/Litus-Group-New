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
            'intro_paragraph_1' => SiteSetting::getValue(
                'about.intro.paragraph_1',
                'LITUS Group is a diversified business conglomerate with a strong presence across multiple sectors including hospitality, construction, automotive, technology, and trading. Our commitment to excellence drives everything we do.'
            ),
            'intro_paragraph_2' => SiteSetting::getValue(
                'about.intro.paragraph_2',
                'With a portfolio spanning from luxury hotels and resorts to cutting-edge technology solutions, we deliver comprehensive services that meet the evolving needs of our clients. Our diverse businesses work in synergy to create value and drive sustainable growth.'
            ),
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
            Forms\Components\Section::make('About LITUS Group — intro text')
                ->description('Two paragraphs shown beside the image under “About LITUS Group” on the public About Us page.')
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

        SiteSetting::setValue('about.intro.paragraph_1', $state['intro_paragraph_1'] ?? '');
        SiteSetting::setValue('about.intro.paragraph_2', $state['intro_paragraph_2'] ?? '');
        SiteSetting::setValue('about.business_partnership.image_path', $nextPath);

        $this->notify('success', 'About Us page updated.');
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
