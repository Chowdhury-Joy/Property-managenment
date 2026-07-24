<?php

namespace App\Filament\Owner\Widgets;

use App\Models\RentPayment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OwnerLatestRentPayments extends BaseWidget
{
    protected static ?int $sort = 10;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $ownerId = auth()->guard('owner')->id();

        return $table
            ->query(
                RentPayment::query()
                    ->whereHas('lease.unit.property', function ($query) use ($ownerId) {
                        $query->where('owner_id', $ownerId);
                    })
                    ->latest('due_date')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('lease.unit.property.name')
                    ->label('Property')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lease.unit.name')
                    ->label('Unit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending', 'upcoming' => 'warning',
                        'paid' => 'success',
                        'late' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_date')
                    ->date(),
            ])
            ->paginated(false);
    }
}
