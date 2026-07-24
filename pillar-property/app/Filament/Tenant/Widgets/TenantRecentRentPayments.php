<?php

namespace App\Filament\Tenant\Widgets;

use Filament\Tables\Columns\TextColumn;
use App\Models\RentPayment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TenantRecentRentPayments extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $tenantId = auth()->guard('tenant')->id();

        return $table
            ->query(
                RentPayment::query()
                    ->whereHas('lease', function ($query) use ($tenantId) {
                        $query->where('tenant_id', $tenantId);
                    })
                    ->latest('due_date')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('lease.unit.name')
                    ->label('Unit')
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending', 'upcoming' => 'warning',
                        'paid' => 'success',
                        'late' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('paid_date')
                    ->date(),
            ])
            ->paginated(false);
    }
}
