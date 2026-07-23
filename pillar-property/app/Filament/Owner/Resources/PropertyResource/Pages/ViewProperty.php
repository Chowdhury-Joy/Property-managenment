<?php

namespace App\Filament\Owner\Resources\PropertyResource\Pages;

use App\Filament\Owner\Resources\PropertyResource;
use App\Models\MaintenanceRequest;
use App\Models\RentPayment;
use Filament\Resources\Pages\ViewRecord;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ViewProperty extends ViewRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderWidgets(): array
    {
        $property = $this->record;
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // 1. Occupancy
        $totalUnits = $property->units()->count();
        $occupiedUnits = $property->units()->where('status', 'occupied')->count();
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0;

        // 2. Rent Collected this month
        $rentOwed = RentPayment::whereHas('lease.unit', fn ($q) => $q->where('property_id', $property->id))
            ->whereMonth('due_date', $currentMonth)->whereYear('due_date', $currentYear)->sum('amount');
        $rentCollected = RentPayment::whereHas('lease.unit', fn ($q) => $q->where('property_id', $property->id))
            ->where('status', 'paid')->whereMonth('due_date', $currentMonth)->whereYear('due_date', $currentYear)->sum('amount');

        // 3. Open Maintenance
        $openMaint = MaintenanceRequest::whereHas('unit', fn ($q) => $q->where('property_id', $property->id))
            ->whereIn('status', ['submitted', 'assigned', 'in_progress'])->count();

        return [
            StatsOverviewWidget::make([
                Stat::make('Occupancy', "{$occupancyRate}%")
                    ->description("{$occupiedUnits} of {$totalUnits} units")
                    ->color($occupancyRate >= 90 ? 'success' : 'warning')
                    ->icon('heroicon-o-user-group'),

                Stat::make('Rent Collected (MTD)', '$'.number_format($rentCollected, 2))
                    ->description('Out of $'.number_format($rentOwed, 2).' owed')
                    ->color('success')
                    ->icon('heroicon-o-banknotes'),

                Stat::make('Open Maintenance', $openMaint)
                    ->description($openMaint > 0 ? 'Needs attention' : 'All clear')
                    ->color($openMaint > 0 ? 'danger' : 'success')
                    ->icon('heroicon-o-wrench-screwdriver'),
            ]),
        ];
    }
}
