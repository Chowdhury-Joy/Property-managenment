<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use App\Filament\Resources\RentPaymentResource\Pages\ListRentPayments;
use App\Filament\Resources\RentPaymentResource\Pages\CreateRentPayment;
use App\Filament\Resources\RentPaymentResource\Pages\EditRentPayment;
use App\Filament\Resources\RentPaymentResource\Pages;
use App\Models\RentPayment;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentPaymentResource extends Resource
{
    protected static ?string $model = RentPayment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string | \UnitEnum | null $navigationGroup = 'Financials';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Rent Payments';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('lease_id')
                ->relationship('lease', 'id')
                ->getOptionLabelFromRecordUsing(fn ($record) => "Lease #{$record->id} - {$record->unit?->property?->name} ({$record->unit?->name}) - {$record->tenant?->name}")
                ->required()
                ->searchable()
                ->preload(),
            TextInput::make('amount')->numeric()->prefix('$')->required(),
            DatePicker::make('due_date')->required(),
            DatePicker::make('paid_date'),
            Select::make('status')->options([
                'upcoming' => 'Upcoming',
                'paid' => 'Paid',
                'late' => 'Late',
            ])->default('upcoming')->required(),
            TextInput::make('method_note')->label('Payment Method / Note')->placeholder('e.g., Zelle, Check #102'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('lease.unit.property.name')->label('Property')->searchable(),
            TextColumn::make('lease.unit.name')->label('Unit'),
            TextColumn::make('lease.tenant.name')->label('Tenant')->searchable(),
            TextColumn::make('due_date')->date()->sortable(),
            TextColumn::make('amount')->money('USD'),
            TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'paid' => 'success',
                    'late' => 'danger',
                    default => 'warning',
                }),
            TextColumn::make('paid_date')->date()->placeholder('Unpaid'),
            TextColumn::make('method_note')->label('Method')->placeholder('-'),
        ])->filters([TrashedFilter::make()])->recordActions([
            EditAction::make(), DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make(),
        ])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make(), RestoreBulkAction::make(), ForceDeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRentPayments::route('/'),
            'create' => CreateRentPayment::route('/create'),
            'edit' => EditRentPayment::route('/{record}/edit'),
        ];
    }
}
