<?php

namespace App\Filament\Resources\RentPaymentResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use App\Filament\Resources\RentPaymentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListRentPayments extends ListRecords
{
    protected static string $resource = RentPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateInvoices')
                ->label('Generate Monthly Invoices')
                ->icon('heroicon-o-sparkles')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Generate Rent Invoices?')
                ->modalDescription('This will create rent records for all active leases for the current month if they don\'t already exist.')
                ->action(function () {
                    Artisan::call('rent:generate');

                    Notification::make()
                        ->title('Invoices Generated')
                        ->body('Monthly rent records have been created for all active leases.')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
