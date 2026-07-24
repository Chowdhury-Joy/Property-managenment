<?php

namespace App\Filament\Owner\Widgets;

use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\RentPayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Per-record header widget for the Owner "Property Deep-Dive" view.
 *
 * Header widgets must be class-based (`getStats()` override) rather than
 * `StatsOverviewWidget::make([Stat::make(...), ...])`, because `Widget::make()`
 * builds a WidgetConfiguration whose array is hydrated as Livewire component
 * properties, not as ready-made Stat objects — passing Stats directly there
 * is what caused this page's 500 ("Property type not supported in Livewire").
 * `$record` below is populated via `PropertyOverviewStats::make(['record' => ...])`.
 */
class PropertyOverviewStats extends BaseWidget
{
    public ?Property $record = null;

    protected function getStats(): array
    {
        $property = $this->record;
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $totalUnits = $property->units()->count();
        $occupiedUnits = $property->units()->where('status', 'occupied')->count();
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0;

        $rentOwed = RentPayment::whereHas('lease.unit', fn ($q) => $q->where('property_id', $property->id))
            ->whereMonth('due_date', $currentMonth)->whereYear('due_date', $currentYear)->sum('amount');
        $rentCollected = RentPayment::whereHas('lease.unit', fn ($q) => $q->where('property_id', $property->id))
            ->where('status', 'paid')->whereMonth('due_date', $currentMonth)->whereYear('due_date', $currentYear)->sum('amount');

        $openMaint = MaintenanceRequest::whereHas('unit', fn ($q) => $q->where('property_id', $property->id))
            ->whereIn('status', ['submitted', 'assigned', 'in_progress'])->count();

        return [
            Stat::make('Occupancy', "{$occupancyRate}%")
                ->description("{$occupiedUnits} of {$totalUnits} units")
                ->color($occupancyRate >= 90 ? 'success' : 'warning')
                ->icon('heroicon-o-user-group'),

            Stat::make('Rent Collected (MTD)', '$'.number_format($rentCollected, 2))
                ->description('Out of $'.number_format($rentOwed, 2).' owed')
                ->color($rentOwed > 0 && $rentCollected < $rentOwed ? 'warning' : 'success')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Open Maintenance', $openMaint)
                ->description($openMaint > 0 ? 'Needs attention' : 'All clear')
                ->color($openMaint > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-wrench-screwdriver'),
        ];
    }
}
