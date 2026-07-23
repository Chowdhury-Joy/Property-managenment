<?php

namespace App\Filament\Owner\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalPropertiesStats extends BaseWidget
{
    protected function getStats(): array
    {
        $owner = auth()->guard('owner')->user();
        $count = $owner ? $owner->properties()->count() : 0;

        return [
            Stat::make('Total Properties', $count)
                ->description('Properties under management')
                ->icon('heroicon-o-building-office'),
        ];
    }
}
