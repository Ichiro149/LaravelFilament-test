<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Delivery Logs
        </x-slot>
        <x-slot name="description">
            View all webhook delivery attempts for {{ $this->record->name }}
        </x-slot>

        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
