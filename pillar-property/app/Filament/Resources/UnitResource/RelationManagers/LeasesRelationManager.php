<?php

namespace App\Filament\Resources\UnitResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
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

class LeasesRelationManager extends RelationManager
{
    protected static string $relationship = 'leases';

    // Mirrors LeaseResource::form() minus unit_id, which this relation manager
    // already scopes to the parent Unit automatically.
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                DatePicker::make('start_date')->required(),
                DatePicker::make('end_date')->required(),
                TextInput::make('rent_amount')->numeric()->prefix('$')->required(),
                TextInput::make('due_day')->numeric()->minValue(1)->maxValue(28)->default(1)->label('Due Day of Month'),
                TextInput::make('security_deposit')->numeric()->prefix('$')->default(0),
                Select::make('status')->options([
                    'draft' => 'Draft', 'active' => 'Active', 'ending_soon' => 'Ending Soon', 'ended' => 'Ended', 'terminated' => 'Terminated',
                ])->default('active')->required(),
                FileUpload::make('document_path')->label('Lease Document')->directory('leases'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('tenant.name')->label('Tenant')->searchable(),
                TextColumn::make('rent_amount')->money('USD'),
                TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'draft' => 'gray', 'active' => 'success', 'ending_soon' => 'warning', 'ended' => 'info', 'terminated' => 'danger', default => 'gray',
                }),
                TextColumn::make('start_date')->date(),
                TextColumn::make('end_date')->date(),
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
