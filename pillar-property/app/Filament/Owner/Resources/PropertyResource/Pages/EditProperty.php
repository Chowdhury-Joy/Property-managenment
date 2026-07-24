<?php

namespace App\Filament\Owner\Resources\PropertyResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Owner\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
