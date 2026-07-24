<?php

namespace App\Filament\Resources\UnitResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LeasesRelationManager extends RelationManager
{
    protected static string $relationship = 'leases';

    // Mirrors LeaseResource::form() minus unit_id, which this relation manager
    // already scopes to the parent Unit automatically.
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('start_date')->required(),
                Forms\Components\DatePicker::make('end_date')->required(),
                Forms\Components\TextInput::make('rent_amount')->numeric()->prefix('$')->required(),
                Forms\Components\TextInput::make('due_day')->numeric()->minValue(1)->maxValue(28)->default(1)->label('Due Day of Month'),
                Forms\Components\TextInput::make('security_deposit')->numeric()->prefix('$')->default(0),
                Forms\Components\Select::make('status')->options([
                    'draft' => 'Draft', 'active' => 'Active', 'ending_soon' => 'Ending Soon', 'ended' => 'Ended', 'terminated' => 'Terminated',
                ])->default('active')->required(),
                Forms\Components\FileUpload::make('document_path')->label('Lease Document')->directory('leases'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')->label('Tenant')->searchable(),
                Tables\Columns\TextColumn::make('rent_amount')->money('USD'),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'draft' => 'gray', 'active' => 'success', 'ending_soon' => 'warning', 'ended' => 'info', 'terminated' => 'danger', default => 'gray',
                }),
                Tables\Columns\TextColumn::make('start_date')->date(),
                Tables\Columns\TextColumn::make('end_date')->date(),
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
