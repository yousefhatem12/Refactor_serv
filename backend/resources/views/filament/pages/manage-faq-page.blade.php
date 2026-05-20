<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6">
        {{ $this->form }}

        <x-filament::actions
            :actions="$this->getFormActions()"
        />
    </form>

    <div class="space-y-4 pt-6 border-t border-gray-200 dark:border-gray-800">
        <h2 class="text-xl font-bold tracking-tight">{{ __('Frequently Asked Questions List') }}</h2>
        {{ $this->table }}
    </div>
</x-filament-panels::page>
