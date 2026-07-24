<?php

namespace App\Filament\Resources\LeaseResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\LeaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeases extends ListRecords
{
    protected static string $resource = LeaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
