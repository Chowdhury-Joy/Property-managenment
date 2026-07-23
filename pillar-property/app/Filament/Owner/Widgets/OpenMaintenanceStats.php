<?php

namespace App\Filament\Owner\Widgets;

use App\Models\MaintenanceRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OpenMaintenanceStats extends BaseWidget
{
    protected function getStats(): array
    {
        $owner = auth()->guard('owner')->user();
        if (! $owner) {
            return [Stat::make('Open Maintenance Requests', 0)];
        }

        $count = MaintenanceRequest::whereHas('unit.property', fn ($q) => $q->where('owner_id', $owner->id))
            ->whereIn('status', ['submitted', 'assigned', 'in_progress'])
            ->count();

        return [
            Stat::make('Open Maintenance Requests', $count)
                ->description('Requires attention')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color($count > 0 ? 'warning' : 'success'),
        ];
    }
}
