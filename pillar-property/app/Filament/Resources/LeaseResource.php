<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaseResource\Pages;
use App\Models\Lease;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaseResource extends Resource
{
    protected static ?string $model = Lease::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('unit_id')->relationship('unit', 'name')->required()->searchable()->preload(),
            Forms\Components\Select::make('tenant_id')->relationship('tenant', 'name')->required()->searchable()->preload(),
            Forms\Components\DatePicker::make('start_date')->required(),
            Forms\Components\DatePicker::make('end_date')->required(),
            Forms\Components\TextInput::make('rent_amount')->numeric()->prefix('$')->required(),
            Forms\Components\TextInput::make('due_day')->numeric()->minValue(1)->maxValue(28)->default(1)->label('Due Day of Month'),
            Forms\Components\TextInput::make('security_deposit')->numeric()->prefix('$')->default(0),
            Forms\Components\Select::make('status')->options([
                'draft' => 'Draft', 'active' => 'Active', 'ending_soon' => 'Ending Soon', 'ended' => 'Ended', 'terminated' => 'Terminated',
            ])->default('active')->required(),
            Forms\Components\FileUpload::make('document_path')->label('Lease Document')->directory('leases'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('unit.property.name')->label('Property')->searchable(),
            Tables\Columns\TextColumn::make('unit.name')->label('Unit'),
            Tables\Columns\TextColumn::make('tenant.name')->searchable(),
            Tables\Columns\TextColumn::make('rent_amount')->money('USD'),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('end_date')->date(),
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
            'index' => Pages\ListLeases::route('/'),
            'create' => Pages\CreateLease::route('/create'),
            'edit' => Pages\EditLease::route('/{record}/edit'),
        ];
    }
}
