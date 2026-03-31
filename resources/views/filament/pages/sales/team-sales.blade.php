<x-filament::page>
    <form wire:submit.prevent="save" class="space-y-6">
        <x-filament::card>
            <div class="space-y-2">
                <div class="text-2xl font-bold">Team</div>
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    Manage the leadership team cards shown on the public Team page.
                </div>
            </div>
        </x-filament::card>

        {{ $this->form }}

        <div class="flex flex-wrap items-center gap-3">
            <x-filament::button type="submit">
                Save
            </x-filament::button>
        </div>
    </form>
</x-filament::page>

