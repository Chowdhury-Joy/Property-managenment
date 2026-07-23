<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\PaymentResource\Pages;
use App\Models\RentPayment;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = RentPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Payment History';

    // Scope to the logged-in tenant's active lease
    public static function getEloquentQuery(): Builder
    {
        $tenant = auth()->guard('tenant')->user();
        $activeLeaseId = $tenant?->activeLease?->id;

        return parent::getEloquentQuery()
            ->where('lease_id', $activeLeaseId)
            ->orderByDesc('due_date');
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('due_date')->date()->label('Due Date'),
            Tables\Columns\TextColumn::make('amount')->money('USD'),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'paid' => 'success',
                    'late' => 'danger',
                    default => 'warning',
                }),
            Tables\Columns\TextColumn::make('paid_date')->date()->label('Paid On')->placeholder('Not paid'),
            Tables\Columns\TextColumn::make('method_note')->label('Method')->placeholder('-'),
        ])->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
