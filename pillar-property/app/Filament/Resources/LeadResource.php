<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use App\Filament\Resources\LeadResource\Pages\ListLeads;
use App\Filament\Resources\LeadResource\Pages\CreateLead;
use App\Filament\Resources\LeadResource\Pages\EditLead;
use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-inbox-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'CRM';

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
            Section::make('Contact Info')->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->email()->required(),
                TextInput::make('phone')->tel(),
            ])->columns(3),
            Section::make('Property Details')->schema([
                TextInput::make('property_address')->required()->columnSpanFull(),
                Select::make('property_type')->options([
                    'single_family' => 'Single Family', 'multi_unit' => 'Multi-Unit', 'commercial' => 'Commercial',
                ])->required(),
                TextInput::make('current_rent')->label('Current Rent (or "Not sure")'),
                Textarea::make('reason_for_switching')->label('Why are they considering switching?')->columnSpanFull(),
            ])->columns(2),
            Section::make('Staff Follow-up')->schema([
                Select::make('status')->options([
                    'new' => 'New', 'contacted' => 'Contacted', 'proposal_sent' => 'Proposal Sent',
                    'converted' => 'Converted', 'lost' => 'Lost',
                ])->default('new')->required(),
                Textarea::make('staff_notes')->label('Internal Notes')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('property_address')->searchable()->limit(30),
            TextColumn::make('property_type')->badge(),
            TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                'new' => 'info', 'contacted' => 'warning', 'converted' => 'success', 'lost' => 'danger', default => 'gray',
            }),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])->filters([TrashedFilter::make()])->recordActions([
            EditAction::make(), DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make(),
        ])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make(), RestoreBulkAction::make(), ForceDeleteBulkAction::make()])]);
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
            'index' => ListLeads::route('/'),
            'create' => CreateLead::route('/create'),
            'edit' => EditLead::route('/{record}/edit'),
        ];
    }
}
