<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\PropertyResource\Pages;
use App\Models\Property;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('owner_id', auth()->guard('owner')->id());
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('address')->searchable()->limit(30),
            Tables\Columns\TextColumn::make('type')->badge(),
            Tables\Columns\TextColumn::make('units_count')->counts('units')->label('Units'),
            Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray'),
        ])->actions([
            Tables\Actions\ViewAction::make(),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Property Details')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('address'),
                        TextEntry::make('city'),
                        TextEntry::make('state'),
                        TextEntry::make('zip'),
                        TextEntry::make('type')->badge(),
                        TextEntry::make('status')->badge()->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray'),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'view' => Pages\ViewProperty::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
