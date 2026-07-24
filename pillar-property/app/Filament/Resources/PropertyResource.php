<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use App\Filament\Resources\PropertyResource\Pages\ListProperties;
use App\Filament\Resources\PropertyResource\Pages\CreateProperty;
use App\Filament\Resources\PropertyResource\Pages\EditProperty;
use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers\UnitsRelationManager;
use App\Models\Property;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home-modern';

    protected static string | \UnitEnum | null $navigationGroup = 'Property Management';

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
            Select::make('owner_id')
                ->relationship('owner', 'name')
                ->required()->searchable()->preload(),
            TextInput::make('name')->required()->label('Property Name (e.g. 123 Main St)'),
            TextInput::make('address')->required(),
            TextInput::make('city')->required(),
            TextInput::make('state')->required()->maxLength(2),
            TextInput::make('zip')->required()->maxLength(10),
            Select::make('type')->options([
                'single_family' => 'Single Family',
                'multi_unit' => 'Multi-Unit',
                'commercial' => 'Commercial',
            ])->required(),
            FileUpload::make('image')
                ->image()
                ->directory('properties'),
            Select::make('status')->options([
                'active' => 'Active', 'inactive' => 'Inactive',
            ])->default('active')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('image'),
            TextColumn::make('name')->searchable(),
            TextColumn::make('owner.name')->searchable(),
            TextColumn::make('type')->badge(),
            TextColumn::make('status')->badge()->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray'),
            TextColumn::make('units_count')->counts('units')->label('Units'),
        ])->filters([TrashedFilter::make()])->recordActions([
            EditAction::make(), DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make(),
        ])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make(), RestoreBulkAction::make(), ForceDeleteBulkAction::make()])]);
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
            'index' => ListProperties::route('/'),
            'create' => CreateProperty::route('/create'),
            'edit' => EditProperty::route('/{record}/edit'),
        ];
    }
}
