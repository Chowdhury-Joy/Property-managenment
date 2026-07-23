<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\OwnerPanelProvider;
use App\Providers\Filament\TenantPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    OwnerPanelProvider::class,
    TenantPanelProvider::class,
];
