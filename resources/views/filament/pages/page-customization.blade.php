<x-filament::page>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <a
            href="{{ \App\Filament\Pages\Sales\HomeSales::getUrl() }}"
            class="group block rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500"
        >
            <x-filament::card class="transition group-hover:shadow-md">
                <div class="space-y-1">
                    <div class="flex items-center justify-between gap-3">
                        <div class="text-lg font-semibold">Home</div>
                        <span class="text-sm text-primary-600 dark:text-primary-400">Open</span>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Customize the Home page content and sections.</div>
                </div>
            </x-filament::card>
        </a>

        <a
            href="{{ \App\Filament\Pages\Sales\AboutUsSales::getUrl() }}"
            class="group block rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500"
        >
            <x-filament::card class="transition group-hover:shadow-md">
                <div class="space-y-1">
                    <div class="flex items-center justify-between gap-3">
                        <div class="text-lg font-semibold">About Us</div>
                        <span class="text-sm text-primary-600 dark:text-primary-400">Open</span>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Customize the About Us page content and layout.</div>
                </div>
            </x-filament::card>
        </a>

        <a
            href="{{ \App\Filament\Pages\Sales\TeamSales::getUrl() }}"
            class="group block rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500"
        >
            <x-filament::card class="transition group-hover:shadow-md">
                <div class="space-y-1">
                    <div class="flex items-center justify-between gap-3">
                        <div class="text-lg font-semibold">Team</div>
                        <span class="text-sm text-primary-600 dark:text-primary-400">Open</span>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Manage team members and the Team page sections.</div>
                </div>
            </x-filament::card>
        </a>

        <a
            href="{{ \App\Filament\Pages\Sales\CareersSales::getUrl() }}"
            class="group block rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500"
        >
            <x-filament::card class="transition group-hover:shadow-md">
                <div class="space-y-1">
                    <div class="flex items-center justify-between gap-3">
                        <div class="text-lg font-semibold">Careers</div>
                        <span class="text-sm text-primary-600 dark:text-primary-400">Open</span>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Customize Careers page details and job listings sections.</div>
                </div>
            </x-filament::card>
        </a>
    </div>
</x-filament::page>

