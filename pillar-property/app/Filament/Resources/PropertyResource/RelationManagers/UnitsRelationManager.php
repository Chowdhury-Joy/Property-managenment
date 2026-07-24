<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';

    // Mirrors UnitResource::form() minus property_id, which this relation
    // manager already scopes to the parent Property automatically.
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Unit Name (e.g. Unit A, or leave blank for Single Family)'),
                Forms\Components\TextInput::make('bedrooms')->numeric()->default(0),
                Forms\Components\TextInput::make('bathrooms')->numeric()->default(0),
                Forms\Components\TextInput::make('sqft')->numeric(),
                Forms\Components\Select::make('status')->options([
                    'vacant' => 'Vacant', 'occupied' => 'Occupied', 'maintenance' => 'Under Maintenance',
                ])->default('vacant')->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Unit')->searchable(),
                Tables\Columns\TextColumn::make('bedrooms')->suffix(' bed'),
                Tables\Columns\TextColumn::make('bathrooms')->suffix(' bath'),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'vacant' => 'warning', 'occupied' => 'success', 'maintenance' => 'danger', default => 'gray',
                }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
