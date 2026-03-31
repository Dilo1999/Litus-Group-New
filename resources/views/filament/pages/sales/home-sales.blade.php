<x-filament::page>
    <form wire:submit.prevent="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex flex-wrap items-center gap-3">
            <x-filament::button type="submit">
                Save
            </x-filament::button>

            <x-filament::button
                type="button"
                color="danger"
                outlined
                wire:click="removeHeroImage"
            >
                Remove image
            </x-filament::button>
        </div>
    </form>
</x-filament::page>

