<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers\UnitsRelationManager;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationGroup = 'Property Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('owner_id')
                ->relationship('owner', 'name')
                ->required()->searchable()->preload(),
            Forms\Components\TextInput::make('name')->required()->label('Property Name (e.g. 123 Main St)'),
            Forms\Components\TextInput::make('address')->required(),
            Forms\Components\TextInput::make('city')->required(),
            Forms\Components\TextInput::make('state')->required()->maxLength(2),
            Forms\Components\TextInput::make('zip')->required()->maxLength(10),
            Forms\Components\Select::make('type')->options([
                'single_family' => 'Single Family',
                'multi_unit' => 'Multi-Unit',
                'commercial' => 'Commercial',
            ])->required(),
            Forms\Components\Select::make('status')->options([
                'active' => 'Active', 'inactive' => 'Inactive',
            ])->default('active')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('owner.name')->searchable(),
            Tables\Columns\TextColumn::make('type')->badge(),
            Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray'),
            Tables\Columns\TextColumn::make('units_count')->counts('units')->label('Units'),
        ])->filters([])->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            UnitsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
