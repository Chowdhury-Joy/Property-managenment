<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\ContactMessageResource\Pages\ManageContactMessages;
use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    protected static string | \UnitEnum | null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Contact Messages';

    // Staff only triage submissions (mark read/replied) — the record itself is
    // created exclusively by the public Contact page's Livewire form.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('first_name')->required()->disabled(),
            TextInput::make('last_name')->required()->disabled(),
            TextInput::make('email')->email()->required()->disabled(),
            Textarea::make('message')->required()->disabled()->columnSpanFull(),
            Select::make('status')->options([
                'new' => 'New', 'read' => 'Read', 'replied' => 'Replied',
            ])->default('new')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('first_name')->label('Name')->formatStateUsing(fn ($record) => "{$record->first_name} {$record->last_name}")->searchable(['first_name', 'last_name']),
            TextColumn::make('email')->searchable(),
            TextColumn::make('message')->limit(50),
            TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                'new' => 'info', 'read' => 'warning', 'replied' => 'success', default => 'gray',
            }),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])->defaultSort('created_at', 'desc')->filters([
            SelectFilter::make('status')->options([
                'new' => 'New', 'read' => 'Read', 'replied' => 'Replied',
            ]),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageContactMessages::route('/'),
        ];
    }
}
