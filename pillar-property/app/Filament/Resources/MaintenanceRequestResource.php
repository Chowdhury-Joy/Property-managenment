<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
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
use App\Filament\Resources\MaintenanceRequestResource\Pages\ListMaintenanceRequests;
use App\Filament\Resources\MaintenanceRequestResource\Pages\CreateMaintenanceRequest;
use App\Filament\Resources\MaintenanceRequestResource\Pages\EditMaintenanceRequest;
use App\Filament\Resources\MaintenanceRequestResource\Pages;
use App\Models\MaintenanceRequest;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaintenanceRequestResource extends Resource
{
    protected static ?string $model = MaintenanceRequest::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string | \UnitEnum | null $navigationGroup = 'Maintenance';

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
            Select::make('tenant_id')->relationship('tenant', 'name')->searchable()->preload(),
            Select::make('vendor_id')->relationship('vendor', 'name')->searchable()->preload(),
            Select::make('category')->options([
                'plumbing' => 'Plumbing', 'electrical' => 'Electrical', 'appliance' => 'Appliance', 'hvac' => 'HVAC', 'other' => 'Other',
            ])->required(),
            Textarea::make('description')->required()->columnSpanFull(),
            FileUpload::make('photo_path')->image()->directory('maintenance-photos'),
            Select::make('urgency')->options([
                'routine' => 'Routine', 'urgent' => 'Urgent', 'emergency' => 'Emergency',
            ])->default('routine')->required(),
            Select::make('status')->options([
                'submitted' => 'Submitted', 'assigned' => 'Assigned', 'in_progress' => 'In Progress', 'resolved' => 'Resolved',
            ])->default('submitted')->required(),
            TextInput::make('cost')->numeric()->prefix('$'),
            DateTimePicker::make('resolved_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('unit.property.name')->label('Property')->searchable(),
            TextColumn::make('category')->badge(),
            TextColumn::make('urgency')->badge()->color(fn (string $state): string => match ($state) {
                'emergency' => 'danger', 'urgent' => 'warning', default => 'gray',
            }),
            TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                'submitted' => 'gray', 'assigned' => 'warning', 'in_progress' => 'info', 'resolved' => 'success', default => 'gray',
            }),
            TextColumn::make('vendor.name')->label('Assigned To'),
            TextColumn::make('cost')->money('USD'),
            TextColumn::make('created_at')->dateTime()->sortable(),
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
            'index' => ListMaintenanceRequests::route('/'),
            'create' => CreateMaintenanceRequest::route('/create'),
            'edit' => EditMaintenanceRequest::route('/{record}/edit'),
        ];
    }
}
