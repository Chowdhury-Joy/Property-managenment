<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use App\Models\RentPayment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestRentPayments extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                RentPayment::query()->latest('due_date')->limit(5)
            )
            ->columns([
                TextColumn::make('lease.unit.name')
                    ->label('Unit')
                    ->sortable(),
                TextColumn::make('lease.tenant.first_name')
                    ->label('Tenant')
                    ->formatStateUsing(fn ($record) => $record->lease?->tenant?->first_name . ' ' . $record->lease?->tenant?->last_name),
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
