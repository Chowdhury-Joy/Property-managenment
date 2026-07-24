<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\MaintenanceRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantMaintenanceStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $tenant = auth()->guard('tenant')->user();

        $openRequests = MaintenanceRequest::where('tenant_id', $tenant->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        return [
            Stat::make('Open Maintenance Requests', $openRequests)
                ->description($openRequests > 0 ? 'We are working on it.' : 'Everything looks good!')
                ->color($openRequests > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-wrench'),
        ];
    }
}
