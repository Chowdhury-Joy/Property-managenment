<x-filament-panels::page>
    {{-- Header stat widgets from ViewProperty::getHeaderWidgets() are rendered
         automatically by <x-filament-panels::page> itself — echoing
         $this->headerWidgets here isn't a real property and used to 500. --}}
    {{ $this->infolist }}
</x-filament-panels::page>
