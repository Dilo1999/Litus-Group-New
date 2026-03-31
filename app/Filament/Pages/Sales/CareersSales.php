<?php

namespace App\Filament\Pages\Sales;

use App\Filament\Pages\PageCustomization;
use App\Models\JobOpening;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages;
use Filament\Pages\Page;

class CareersSales extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Careers Sales Page';

    protected static ?string $slug = 'sales/careers';

    protected static string $view = 'filament.pages.sales.careers-sales';

    public array $data = [];

    public function mount(): void
    {
        $openings = JobOpening::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (JobOpening $j) => [
                'id' => $j->id,
                'title' => $j->title,
                'company' => $j->company,
                'location' => $j->location,
                'type' => $j->type,
                'department' => $j->department,
                'description' => $j->description,
                'is_active' => (bool) $j->is_active,
                'sort_order' => (int) $j->sort_order,
            ])
            ->all();

        $this->form->fill([
            'openings' => $openings,
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
            Forms\Components\Section::make('Job openings')
                ->description('Add, edit, reorder, activate/deactivate, or delete job vacancies shown on the public Careers page.')
                ->schema([
                    Forms\Components\Repeater::make('openings')
                        ->label('Vacancies')
                        ->defaultItems(0)
                        ->collapsible()
                        ->orderable()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                        ->schema([
                            Forms\Components\Hidden::make('id'),
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('company')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('location')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('type')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('department')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Textarea::make('description')
                                ->rows(4)
                                ->columnSpanFull()
                                ->helperText('Shown when the vacancy row is expanded on the Careers page.'),
                            Forms\Components\Toggle::make('is_active')
                                ->label('Active')
                                ->default(true)
                                ->inline(false),
                        ])
                        ->columns(2),
                ]),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $items = $state['openings'] ?? [];

        $keptIds = [];

        foreach (array_values($items) as $index => $item) {
            $id = $item['id'] ?? null;

            $payload = [
                'title' => $item['title'] ?? '',
                'company' => $item['company'] ?? '',
                'location' => $item['location'] ?? '',
                'type' => $item['type'] ?? '',
                'department' => $item['department'] ?? '',
                'description' => $item['description'] ?? null,
                'is_active' => (bool)($item['is_active'] ?? true),
                'sort_order' => $index + 1,
            ];

            if ($id) {
                JobOpening::query()->whereKey($id)->update($payload);
                $keptIds[] = (int) $id;
            } else {
                $created = JobOpening::query()->create($payload);
                $keptIds[] = (int) $created->id;
            }
        }

        JobOpening::query()
            ->when(count($keptIds) > 0, fn ($q) => $q->whereNotIn('id', $keptIds))
            ->delete();

        $this->notify('success', 'Job openings updated.');

        $this->mount();
    }
}

