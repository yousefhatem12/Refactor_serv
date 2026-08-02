<x-filament-panels::page>
    <div class="max-w-5xl mx-auto w-full">
        <form wire:submit.prevent="generate" class="space-y-6">
            {{ $this->form }}

            <x-filament::actions
                :actions="$this->getFormActions()"
            />
        </form>
    </div>
</x-filament-panels::page>
