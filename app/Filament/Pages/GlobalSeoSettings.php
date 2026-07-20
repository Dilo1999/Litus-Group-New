<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Models\User;
use App\Support\GlobalSeo;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class GlobalSeoSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-globe';

    protected static ?string $navigationLabel = 'Global SEO';

    protected static ?string $title = 'Global SEO';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 94;

    protected static ?string $slug = 'global-seo';

    protected static string $view = 'filament.pages.global-seo-settings';

    public array $data = [];

    protected static function canAccessForUser(?User $user): bool
    {
        return $user?->hasAdminAccess() ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccessForUser(auth()->user());
    }

    public function mount(): void
    {
        abort_unless(static::canAccessForUser(auth()->user()), 403);

        $seo = GlobalSeo::all();

        $this->form->fill([
            'site_name' => $seo['site_name'] ?? 'LITUS Group',
            'meta_title' => $seo['meta_title'],
            'meta_description' => $seo['meta_description'],
            'keywords' => $seo['keywords'],
            'og_image' => $seo['og_image'],
            'robots' => $seo['robots'],
            'twitter_site' => $seo['twitter_site'],
            'google_verification' => $seo['google_verification'],
            'bing_verification' => $seo['bing_verification'],
            'google_analytics_id' => $seo['google_analytics_id'],
        ]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            Pages\Dashboard::getUrl() => 'Dashboard',
            static::getUrl() => 'Global SEO',
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Site identity')
                ->description('Defaults used across the whole website when a page does not have its own Page SEO record.')
                ->schema([
                    Forms\Components\TextInput::make('site_name')
                        ->label('Site name')
                        ->required()
                        ->maxLength(120)
                        ->helperText('Used in Open Graph site_name and as a suffix on dynamic titles.'),
                    Forms\Components\TextInput::make('meta_title')
                        ->label('Default meta title')
                        ->maxLength(70)
                        ->helperText('Fallback title when a page has no Page SEO title. Recommended: ≤60 characters.'),
                    Forms\Components\Textarea::make('meta_description')
                        ->label('Default meta description')
                        ->rows(3)
                        ->maxLength(320)
                        ->helperText('Fallback search snippet for pages without their own description. Recommended: 150–160 characters.'),
                    Forms\Components\TextInput::make('keywords')
                        ->label('Default keywords')
                        ->maxLength(500)
                        ->helperText('Comma-separated keywords for the whole site (e.g. LITUS Group, Maldives, hospitality).'),
                    Forms\Components\TextInput::make('robots')
                        ->label('Default robots')
                        ->maxLength(120)
                        ->placeholder('index,follow')
                        ->helperText('Examples: index,follow | noindex,nofollow. Leave blank for the framework default.'),
                ])
                ->columns(1),

            Forms\Components\Section::make('Default image')
                ->description('Upload the default social preview image used across the whole site when a page has no image of its own.')
                ->schema([
                    Forms\Components\FileUpload::make('og_image')
                        ->label('Default social / Open Graph image')
                        ->disk('public')
                        ->directory('site/seo/global')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->image()
                        ->imagePreviewHeight('180')
                        ->maxSize(4096)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('Recommended: 1200×630px (JPG, PNG, or WebP). Used for Facebook, LinkedIn, Twitter/X previews, and as the site fallback image.'),
                ])
                ->columns(1),

            Forms\Components\Section::make('Social sharing defaults')
                ->description('Optional Twitter / X settings for the whole site.')
                ->schema([
                    Forms\Components\TextInput::make('twitter_site')
                        ->label('Twitter / X site handle')
                        ->maxLength(50)
                        ->placeholder('@litusgroup')
                        ->helperText('Optional. Include the @ if you use one.'),
                ])
                ->columns(1)
                ->collapsed(),

            Forms\Components\Section::make('Verification & analytics')
                ->description('Site-wide search console verification and analytics. Applied on every public page.')
                ->schema([
                    Forms\Components\TextInput::make('google_verification')
                        ->label('Google Search Console verification')
                        ->maxLength(255)
                        ->helperText('Content value from Google’s meta verification tag (not the full HTML tag).'),
                    Forms\Components\TextInput::make('bing_verification')
                        ->label('Bing Webmaster verification')
                        ->maxLength(255)
                        ->helperText('Content value from Bing’s meta verification tag.'),
                    Forms\Components\TextInput::make('google_analytics_id')
                        ->label('Google Analytics / GTM ID')
                        ->maxLength(40)
                        ->placeholder('G-XXXXXXXXXX or GTM-XXXXXXX')
                        ->helperText('Optional. Measurement ID (G-…) or Google Tag Manager container ID (GTM-…).'),
                ])
                ->columns(1)
                ->collapsed(),
        ];
    }

    public function save(): void
    {
        abort_unless(static::canAccessForUser(auth()->user()), 403);

        $state = $this->form->getState();

        $previousImage = SiteSetting::getValue(GlobalSeo::KEY_OG_IMAGE);
        $nextImage = $state['og_image'] ?? null;
        if (is_array($nextImage)) {
            $nextImage = $nextImage[0] ?? null;
        }
        $nextImage = is_string($nextImage) && $nextImage !== '' ? $nextImage : null;
        if (is_string($previousImage) && $previousImage !== '' && $previousImage !== $nextImage) {
            Storage::disk('public')->delete($previousImage);
        }

        SiteSetting::setValue(GlobalSeo::KEY_SITE_NAME, trim((string) ($state['site_name'] ?? '')) ?: 'LITUS Group');
        SiteSetting::setValue(GlobalSeo::KEY_META_TITLE, $this->nullableString($state['meta_title'] ?? null));
        SiteSetting::setValue(GlobalSeo::KEY_META_DESCRIPTION, $this->nullableString($state['meta_description'] ?? null));
        SiteSetting::setValue(GlobalSeo::KEY_KEYWORDS, $this->nullableString($state['keywords'] ?? null));
        SiteSetting::setValue(GlobalSeo::KEY_OG_IMAGE, $nextImage);
        SiteSetting::setValue(GlobalSeo::KEY_ROBOTS, $this->nullableString($state['robots'] ?? null));
        SiteSetting::setValue(GlobalSeo::KEY_TWITTER_SITE, $this->nullableString($state['twitter_site'] ?? null));
        SiteSetting::setValue(GlobalSeo::KEY_GOOGLE_VERIFICATION, $this->nullableString($state['google_verification'] ?? null));
        SiteSetting::setValue(GlobalSeo::KEY_BING_VERIFICATION, $this->nullableString($state['bing_verification'] ?? null));
        SiteSetting::setValue(GlobalSeo::KEY_GOOGLE_ANALYTICS_ID, $this->nullableString($state['google_analytics_id'] ?? null));

        $this->notify('success', 'Global SEO settings saved.');
    }

    protected function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
