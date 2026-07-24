<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';

    // Mirrors UnitResource::form() minus property_id, which this relation
    // manager already scopes to the parent Property automatically.
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Unit Name (e.g. Unit A, or leave blank for Single Family)'),
                TextInput::make('bedrooms')->numeric()->default(0),
                TextInput::make('bathrooms')->numeric()->default(0),
                TextInput::make('sqft')->numeric(),
                Select::make('status')->options([
                    'vacant' => 'Vacant', 'occupied' => 'Occupied', 'maintenance' => 'Under Maintenance',
                ])->default('vacant')->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Unit')->searchable(),
                TextColumn::make('bedrooms')->suffix(' bed'),
                TextColumn::make('bathrooms')->suffix(' bath'),
                TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'vacant' => 'warning', 'occupied' => 'success', 'maintenance' => 'danger', default => 'gray',
                }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
