<?php

namespace App\Filament\Owner\Resources\PropertyResource\Pages;

use App\Filament\Owner\Resources\PropertyResource;
use App\Filament\Owner\Widgets\PropertyOverviewStats;
use Filament\Resources\Pages\ViewRecord;

class ViewProperty extends ViewRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            PropertyOverviewStats::make(['record' => $this->record]),
        ];
    }
}
