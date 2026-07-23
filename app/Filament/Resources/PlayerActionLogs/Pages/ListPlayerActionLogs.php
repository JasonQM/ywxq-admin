<?php

namespace App\Filament\Resources\PlayerActionLogs\Pages;

use App\Filament\Resources\PlayerActionLogs\PlayerActionLogResource;
use Filament\Resources\Pages\ListRecords;

class ListPlayerActionLogs extends ListRecords
{
    protected static string $resource = PlayerActionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
