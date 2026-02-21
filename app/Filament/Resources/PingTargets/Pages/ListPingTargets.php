<?php

namespace App\Filament\Resources\PingTargets\Pages;

use App\Filament\Resources\PingTargets\PingTargetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPingTargets extends ListRecords
{
    protected static string $resource = PingTargetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
