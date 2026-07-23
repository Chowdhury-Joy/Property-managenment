<?php

namespace App\Filament\Owner\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OccupancyStats extends BaseWidget
{
    protected function getStats(): array
    {
        $owner = auth()->guard('owner')->user();
        if (!$owner) {
            return [Stat::make('Portfolio Occupancy', '0%')];
        }

        $totalUnits = $owner->properties()->withCount('units')->get()->sum('units_count');
        $occupiedUnits = Unit::whereHas('property', fn($q) => $q->where('owner_id', $owner->id))
                                        ->where('status', 'occupied')->count();
        
        $rate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0;

        return [
            Stat::make('Portfolio Occupancy', "{$rate}%")
                ->description("{$occupiedUnits} of {$totalUnits} units occupied")
                ->color($rate >= 90 ? 'success' : ($rate >= 70 ? 'warning' : 'danger'))
                ->icon('heroicon-o-user-group'),
        ];
    }
}
