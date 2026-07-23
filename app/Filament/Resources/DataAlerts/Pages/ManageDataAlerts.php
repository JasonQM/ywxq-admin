<?php

namespace App\Filament\Resources\DataAlerts\Pages;

use App\Filament\Resources\DataAlerts\DataAlertResource;
use Filament\Resources\Pages\ManageRecords;

class ManageDataAlerts extends ManageRecords
{
    protected static string $resource = DataAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
