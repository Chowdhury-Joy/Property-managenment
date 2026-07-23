<?php

namespace App\Filament\Owner\Widgets;

use App\Models\RentPayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RentSummaryStats extends BaseWidget
{
    protected function getStats(): array
    {
        $owner = auth()->guard('owner')->user();
        if (! $owner) {
            return [Stat::make('Rent Collected (This Month)', '$0.00')];
        }

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $query = RentPayment::whereHas('lease.unit.property', fn ($q) => $q->where('owner_id', $owner->id))
            ->whereMonth('due_date', $currentMonth)
            ->whereYear('due_date', $currentYear);

        $owed = (clone $query)->sum('amount');
        $collected = (clone $query)->where('status', 'paid')->sum('amount');

        return [
            Stat::make('Rent Collected (This Month)', '$'.number_format($collected, 2))
                ->description('Out of $'.number_format($owed, 2).' owed')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
        ];
    }
}
