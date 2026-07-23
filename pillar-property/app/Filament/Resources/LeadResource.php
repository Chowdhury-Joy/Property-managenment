<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Contact Info')->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\TextInput::make('phone')->tel(),
            ])->columns(3),
            Forms\Components\Section::make('Property Details')->schema([
                Forms\Components\TextInput::make('property_address')->required()->columnSpanFull(),
                Forms\Components\Select::make('property_type')->options([
                    'single_family' => 'Single Family', 'multi_unit' => 'Multi-Unit', 'commercial' => 'Commercial',
                ])->required(),
                Forms\Components\TextInput::make('current_rent')->label('Current Rent (or "Not sure")'),
                Forms\Components\Textarea::make('reason_for_switching')->label('Why are they considering switching?')->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('Staff Follow-up')->schema([
                Forms\Components\Select::make('status')->options([
                    'new' => 'New', 'contacted' => 'Contacted', 'proposal_sent' => 'Proposal Sent', 
                    'converted' => 'Converted', 'lost' => 'Lost',
                ])->default('new')->required(),
                Forms\Components\Textarea::make('staff_notes')->label('Internal Notes')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\TextColumn::make('property_address')->searchable()->limit(30),
            Tables\Columns\TextColumn::make('property_type')->badge(),
            Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state): string => match($state) {
                'new' => 'info', 'contacted' => 'warning', 'converted' => 'success', 'lost' => 'danger', default => 'gray',
            }),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])->filters([])->actions([
            Tables\Actions\EditAction::make(),
        ]);
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
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
