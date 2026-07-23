<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentPaymentResource\Pages;
use App\Models\RentPayment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentPaymentResource extends Resource
{
    protected static ?string $model = RentPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Financials';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Rent Payments';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('lease_id')
                ->relationship('lease', 'id')
                ->getOptionLabelFromRecordUsing(fn ($record) => "Lease #{$record->id} - {$record->unit?->property?->name} ({$record->unit?->name}) - {$record->tenant?->name}")
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('amount')->numeric()->prefix('$')->required(),
            Forms\Components\DatePicker::make('due_date')->required(),
            Forms\Components\DatePicker::make('paid_date'),
            Forms\Components\Select::make('status')->options([
                'upcoming' => 'Upcoming',
                'paid' => 'Paid',
                'late' => 'Late',
            ])->default('upcoming')->required(),
            Forms\Components\TextInput::make('method_note')->label('Payment Method / Note')->placeholder('e.g., Zelle, Check #102'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('lease.unit.property.name')->label('Property')->searchable(),
            Tables\Columns\TextColumn::make('lease.unit.name')->label('Unit'),
            Tables\Columns\TextColumn::make('lease.tenant.name')->label('Tenant')->searchable(),
            Tables\Columns\TextColumn::make('due_date')->date()->sortable(),
            Tables\Columns\TextColumn::make('amount')->money('USD'),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'paid' => 'success',
                    'late' => 'danger',
                    default => 'warning',
                }),
            Tables\Columns\TextColumn::make('paid_date')->date()->placeholder('Unpaid'),
            Tables\Columns\TextColumn::make('method_note')->label('Method')->placeholder('-'),
        ])->filters([Tables\Filters\TrashedFilter::make()])->actions([
            Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make(), Tables\Actions\RestoreAction::make(), Tables\Actions\ForceDeleteAction::make(),
        ])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make(), Tables\Actions\RestoreBulkAction::make(), Tables\Actions\ForceDeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentPayments::route('/'),
            'create' => Pages\CreateRentPayment::route('/create'),
            'edit' => Pages\EditRentPayment::route('/{record}/edit'),
        ];
    }
}
