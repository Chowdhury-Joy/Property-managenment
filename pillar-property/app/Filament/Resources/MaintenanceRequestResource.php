<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceRequestResource\Pages;
use App\Models\MaintenanceRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MaintenanceRequestResource extends Resource
{
    protected static ?string $model = MaintenanceRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('unit_id')->relationship('unit', 'name')->required()->searchable()->preload(),
            Forms\Components\Select::make('tenant_id')->relationship('tenant', 'name')->searchable()->preload(),
            Forms\Components\Select::make('vendor_id')->relationship('vendor', 'name')->searchable()->preload(),
            Forms\Components\Select::make('category')->options([
                'plumbing' => 'Plumbing', 'electrical' => 'Electrical', 'appliance' => 'Appliance', 'hvac' => 'HVAC', 'other' => 'Other',
            ])->required(),
            Forms\Components\Textarea::make('description')->required()->columnSpanFull(),
            Forms\Components\FileUpload::make('photo_path')->image()->directory('maintenance-photos'),
            Forms\Components\Select::make('urgency')->options([
                'routine' => 'Routine', 'urgent' => 'Urgent', 'emergency' => 'Emergency',
            ])->default('routine')->required(),
            Forms\Components\Select::make('status')->options([
                'submitted' => 'Submitted', 'assigned' => 'Assigned', 'in_progress' => 'In Progress', 'resolved' => 'Resolved',
            ])->default('submitted')->required(),
            Forms\Components\TextInput::make('cost')->numeric()->prefix('$'),
            Forms\Components\DateTimePicker::make('resolved_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('unit.property.name')->label('Property')->searchable(),
            Tables\Columns\TextColumn::make('category')->badge(),
            Tables\Columns\TextColumn::make('urgency')->badge()->color(fn (string $state): string => match($state) {
                'emergency' => 'danger', 'urgent' => 'warning', default => 'gray',
            }),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('vendor.name')->label('Assigned To'),
            Tables\Columns\TextColumn::make('cost')->money('USD'),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])->filters([])->actions([Tables\Actions\EditAction::make()]);
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
            'index' => Pages\ListMaintenanceRequests::route('/'),
            'create' => Pages\CreateMaintenanceRequest::route('/create'),
            'edit' => Pages\EditMaintenanceRequest::route('/{record}/edit'),
        ];
    }
}
