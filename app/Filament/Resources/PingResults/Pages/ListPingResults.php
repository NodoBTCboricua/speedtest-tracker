<?php

namespace App\Filament\Resources\PingResults\Pages;

use App\Filament\Resources\PingResults\PingResultResource;
use Filament\Resources\Pages\ListRecords;

class ListPingResults extends ListRecords
{
    protected static string $resource = PingResultResource::class;

    protected function getHeaderWidgets(): array
    {
        return PingResultResource::getWidgets();
    }
}
