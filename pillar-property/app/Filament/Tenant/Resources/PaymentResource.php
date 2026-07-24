<?php

namespace App\Filament\Tenant\Resources;

use Filament\Tables\Columns\TextColumn;
use App\Filament\Tenant\Resources\PaymentResource\Pages\ListPayments;
use App\Filament\Tenant\Resources\PaymentResource\Pages;
use App\Models\RentPayment;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = RentPayment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

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
            TextColumn::make('due_date')->date()->label('Due Date'),
            TextColumn::make('amount')->money('USD'),
            TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'paid' => 'success',
                    'late' => 'danger',
                    default => 'warning',
                }),
            TextColumn::make('paid_date')->date()->label('Paid On')->placeholder('Not paid'),
            TextColumn::make('method_note')->label('Method')->placeholder('-'),
        ])->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
