<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\RentPayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NextPaymentStats extends BaseWidget
{
    protected function getStats(): array
    {
        $tenant = auth()->guard('tenant')->user();
        if (!$tenant || !$tenant->activeLease) {
            return [
                Stat::make('Next Payment', 'No Active Lease')
                    ->description('No active lease found.')
                    ->color('gray')
                    ->icon('heroicon-o-exclamation-circle'),
            ];
        }

        $nextPayment = RentPayment::where('lease_id', $tenant->activeLease->id)
            ->whereIn('status', ['upcoming', 'late'])
            ->orderBy('due_date')
            ->first();

        if (!$nextPayment) {
            return [
                Stat::make('Next Payment', 'All Caught Up!')
                    ->description('No upcoming payments due.')
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),
            ];
        }

        $statusColor = $nextPayment->status === 'late' ? 'danger' : 'warning';
        $statusText = $nextPayment->status === 'late' ? 'OVERDUE' : 'Upcoming';

        return [
            Stat::make('Next Payment Due', '$' . number_format($nextPayment->amount, 2))
                ->description("{$statusText} on " . $nextPayment->due_date->format('M j, Y'))
                ->color($statusColor)
                ->icon('heroicon-o-calendar-days'),
        ];
    }
}
