<?php

namespace App\Filament\Widgets;

use App\Models\Lease;
use App\Models\Property;
use App\Models\RentPayment;
use App\Models\Tenant;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $monthlyRevenue = RentPayment::where('status', 'paid')
            ->whereMonth('paid_date', Carbon::now()->month)
            ->whereYear('paid_date', Carbon::now()->year)
            ->sum('amount');

        return [
            Stat::make('Total Properties', Property::count())
                ->icon('heroicon-o-home-modern'),
            Stat::make('Total Units', Unit::count())
                ->icon('heroicon-o-building-office'),
            Stat::make('Total Tenants', Tenant::count())
                ->icon('heroicon-o-users'),
            Stat::make('Active Leases', Lease::where('status', 'active')->count())
                ->icon('heroicon-o-document-text'),
            Stat::make('Monthly Revenue', '$' . number_format($monthlyRevenue, 2))
                ->icon('heroicon-o-currency-dollar')
                ->description('For ' . Carbon::now()->format('F')),
        ];
    }
}
