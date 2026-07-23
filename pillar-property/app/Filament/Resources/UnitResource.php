<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use App\Filament\Resources\UnitResource\RelationManagers\LeasesRelationManager;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Property Management';

    protected static ?int $navigationSort = 2;

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
            Forms\Components\Select::make('property_id')
                ->relationship('property', 'name')
                ->required()->searchable()->preload(),
            Forms\Components\TextInput::make('name')->label('Unit Name (e.g. Unit A, or leave blank for Single Family)'),
            Forms\Components\TextInput::make('bedrooms')->numeric()->default(0),
            Forms\Components\TextInput::make('bathrooms')->numeric()->default(0),
            Forms\Components\TextInput::make('sqft')->numeric(),
            Forms\Components\Select::make('status')->options([
                'vacant' => 'Vacant', 'occupied' => 'Occupied', 'maintenance' => 'Under Maintenance',
            ])->default('vacant')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('property.name')->searchable(),
            Tables\Columns\TextColumn::make('name')->label('Unit')->searchable(),
            Tables\Columns\TextColumn::make('bedrooms')->suffix(' bed'),
            Tables\Columns\TextColumn::make('bathrooms')->suffix(' bath'),
            Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                'vacant' => 'warning', 'occupied' => 'success', 'maintenance' => 'danger', default => 'gray',
            }),
        ])->filters([Tables\Filters\TrashedFilter::make()])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make(), Tables\Actions\RestoreAction::make(), Tables\Actions\ForceDeleteAction::make()])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make(), Tables\Actions\RestoreBulkAction::make(), Tables\Actions\ForceDeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            LeasesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
