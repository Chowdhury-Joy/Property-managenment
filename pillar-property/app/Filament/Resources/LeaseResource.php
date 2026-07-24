<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
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
use App\Filament\Resources\LeaseResource\Pages\ListLeases;
use App\Filament\Resources\LeaseResource\Pages\CreateLease;
use App\Filament\Resources\LeaseResource\Pages\EditLease;
use App\Filament\Resources\LeaseResource\Pages;
use App\Models\Lease;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaseResource extends Resource
{
    protected static ?string $model = Lease::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static string | \UnitEnum | null $navigationGroup = 'Leasing';

    protected static ?int $navigationSort = 1;

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
            Select::make('unit_id')->relationship('unit', 'name')->required()->searchable()->preload(),
            Select::make('tenant_id')->relationship('tenant', 'name')->required()->searchable()->preload(),
            DatePicker::make('start_date')->required(),
            DatePicker::make('end_date')->required(),
            TextInput::make('rent_amount')->numeric()->prefix('$')->required(),
            TextInput::make('due_day')->numeric()->minValue(1)->maxValue(28)->default(1)->label('Due Day of Month'),
            TextInput::make('security_deposit')->numeric()->prefix('$')->default(0),
            Select::make('status')->options([
                'draft' => 'Draft', 'active' => 'Active', 'ending_soon' => 'Ending Soon', 'ended' => 'Ended', 'terminated' => 'Terminated',
            ])->default('active')->required(),
            FileUpload::make('document_path')->label('Lease Document')->directory('leases'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('unit.property.name')->label('Property')->searchable(),
            TextColumn::make('unit.name')->label('Unit'),
            TextColumn::make('tenant.name')->searchable(),
            TextColumn::make('rent_amount')->money('USD'),
            TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                'draft' => 'gray', 'active' => 'success', 'ending_soon' => 'warning', 'ended' => 'info', 'terminated' => 'danger', default => 'gray',
            }),
            TextColumn::make('end_date')->date(),
        ])->filters([TrashedFilter::make()])->recordActions([EditAction::make(), DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make(), RestoreBulkAction::make(), ForceDeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeases::route('/'),
            'create' => CreateLease::route('/create'),
            'edit' => EditLease::route('/{record}/edit'),
        ];
    }
}
