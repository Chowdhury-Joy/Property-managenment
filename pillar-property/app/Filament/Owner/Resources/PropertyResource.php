<?php

namespace App\Filament\Owner\Resources;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\ViewAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Filament\Owner\Resources\PropertyResource\Pages\ListProperties;
use App\Filament\Owner\Resources\PropertyResource\Pages\ViewProperty;
use App\Filament\Owner\Resources\PropertyResource\Pages;
use App\Models\Property;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('owner_id', auth()->guard('owner')->id());
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('image'),
            TextColumn::make('name')->searchable(),
            TextColumn::make('address')->searchable()->limit(30),
            TextColumn::make('type')->badge()->formatStateUsing(fn (string $state) => ucfirst(str_replace('_', ' ', $state))),
            TextColumn::make('units_count')->counts('units')->label('Units'),
            TextColumn::make('status')->badge()->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray'),
        ])->recordActions([
            ViewAction::make(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Property Details')
                    ->schema([
                        ImageEntry::make('image')->hiddenLabel(),
                        TextEntry::make('name'),
                        TextEntry::make('address'),
                        TextEntry::make('city'),
                        TextEntry::make('state'),
                        TextEntry::make('zip'),
                        TextEntry::make('type')->badge()->formatStateUsing(fn (string $state) => ucfirst(str_replace('_', ' ', $state))),
                        TextEntry::make('status')->badge()->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray'),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProperties::route('/'),
            'view' => ViewProperty::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
