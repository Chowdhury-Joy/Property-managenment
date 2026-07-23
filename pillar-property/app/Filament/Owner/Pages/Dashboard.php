<?php

namespace App\Filament\Owner\Pages;

use App\Filament\Owner\Widgets\OccupancyStats;
use App\Filament\Owner\Widgets\OpenMaintenanceStats;
use App\Filament\Owner\Widgets\RentSummaryStats;
use App\Filament\Owner\Widgets\TotalPropertiesStats;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            TotalPropertiesStats::class,
            OccupancyStats::class,
            RentSummaryStats::class,
            OpenMaintenanceStats::class,
        ];
    }
}
