@php
    $companiesGrid = \App\Support\SiteData::companiesForOurCompaniesGrid();
@endphp

<x-filament::page>
    <div class="space-y-6">
        <x-filament::card>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-2">
                    <div class="text-2xl font-bold">Our Companies</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">
                        Edit every company field (content, services, strengths, logo, division, contact) from the Companies manager. The list below mirrors the public site.
                    </div>
                </div>
                <x-filament::button
                    tag="a"
                    href="{{ \App\Filament\Resources\CompanyResource::getUrl() }}"
                    color="primary"
                >
                    Manage companies
                </x-filament::button>
            </div>
        </x-filament::card>

        <x-filament::card class="shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Company list</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Two-column preview: logo and company name for all {{ count($companiesGrid) }} brands.
                </p>
            </div>

            <div
                class="grid grid-cols-1 gap-x-12 gap-y-1 sm:grid-cols-2"
                role="list"
            >
                @foreach($companiesGrid as $company)
                    @php
                        $logoUrl = \App\Support\SiteData::companyLogoUrl($company['logo'] ?? null);
                    @endphp
                    <div
                        class="flex items-center gap-3 py-2.5"
                        role="listitem"
                    >
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center">
                            @if($logoUrl)
                                <img
                                    src="{{ $logoUrl }}"
                                    alt="{{ $company['name'] ?? 'Company logo' }}"
                                    class="max-h-11 max-w-11 object-contain"
                                />
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </div>
                        <p class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800 dark:text-gray-100">
                            {{ $company['name'] ?? '' }}
                        </p>
                    </div>
                @endforeach
            </div>
        </x-filament::card>
    </div>
</x-filament::page>
