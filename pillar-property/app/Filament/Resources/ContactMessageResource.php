<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Contact Messages';

    // Staff only triage submissions (mark read/replied) — the record itself is
    // created exclusively by the public Contact page's Livewire form.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('first_name')->required()->disabled(),
            Forms\Components\TextInput::make('last_name')->required()->disabled(),
            Forms\Components\TextInput::make('email')->email()->required()->disabled(),
            Forms\Components\Textarea::make('message')->required()->disabled()->columnSpanFull(),
            Forms\Components\Select::make('status')->options([
                'new' => 'New', 'read' => 'Read', 'replied' => 'Replied',
            ])->default('new')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('first_name')->label('Name')->formatStateUsing(fn ($record) => "{$record->first_name} {$record->last_name}")->searchable(['first_name', 'last_name']),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\TextColumn::make('message')->limit(50),
            Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                'new' => 'info', 'read' => 'warning', 'replied' => 'success', default => 'gray',
            }),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])->defaultSort('created_at', 'desc')->filters([
            Tables\Filters\SelectFilter::make('status')->options([
                'new' => 'New', 'read' => 'Read', 'replied' => 'Replied',
            ]),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageContactMessages::route('/'),
        ];
    }
}
