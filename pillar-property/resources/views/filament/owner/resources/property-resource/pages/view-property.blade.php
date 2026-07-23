<x-filament-panels::page>
    {{ $this->headerWidgets }}
    
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
    </x-filament-panels::form>
</x-filament-panels::page>
