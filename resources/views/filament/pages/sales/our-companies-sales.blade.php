<x-filament::page>
    <div class="space-y-6">
        <form wire:submit.prevent="save" class="space-y-6">
            <x-filament::card>
                <div class="space-y-2">
                    <div class="text-2xl font-bold">Our Companies</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">
                        Customize the Our Companies page hero image, and manage company records from the Companies manager.
                    </div>
                </div>
            </x-filament::card>

            {{ $this->form }}

            <div class="flex flex-wrap items-center gap-3">
                <x-filament::button type="submit">
                    Save
                </x-filament::button>
                <x-filament::button
                    tag="a"
                    href="{{ \App\Filament\Resources\CompanyResource::getUrl() }}"
                    color="gray"
                >
                    Manage companies
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament::page>
