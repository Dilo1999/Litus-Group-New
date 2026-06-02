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

class TeamSales extends Page implements HasForms
{
    use BlocksHrAccess;
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Team Sales Page';

    protected static ?string $slug = 'sales/team';

    protected static string $view = 'filament.pages.sales.team-sales';

    public array $data = [];

    public function mount(): void
    {
        $this->abortIfHr();

        $this->form->fill([
            'hero_image_path' => SiteSetting::getValue('team.hero.image_path'),
        ]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            Pages\Dashboard::getUrl() => 'Dashboard',
            PageCustomization::getUrl() => 'Page Customization',
            static::getUrl() => 'Team',
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
                ->description('Upload, replace, or remove the hero image shown on the Team page.')
                ->schema([
                    Forms\Components\FileUpload::make('hero_image_path')
                        ->label('Hero image')
                        ->disk('public')
                        ->directory('site/team/hero')
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

        $previousHero = SiteSetting::getValue('team.hero.image_path');
        $nextHero = $state['hero_image_path'] ?? null;
        if ($previousHero && $previousHero !== $nextHero) {
            Storage::disk('public')->delete($previousHero);
        }
        SiteSetting::setValue('team.hero.image_path', $nextHero);
        $this->notify('success', 'Team hero image updated.');
    }
}

