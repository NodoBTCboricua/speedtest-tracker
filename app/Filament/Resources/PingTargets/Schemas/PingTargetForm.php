<?php

namespace App\Filament\Resources\PingTargets\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class PingTargetForm
{
    public static function schema(): array
    {
        return [
            Grid::make(['default' => 1])
                ->columnSpan('full')
                ->schema([
                    Section::make(__('general.details'))
                        ->schema([
                            TextInput::make('name')
                                ->label(__('ping.name'))
                                ->required()
                                ->maxLength(255),

                            TextInput::make('host')
                                ->label(__('ping.host'))
                                ->placeholder(__('ping.host_placeholder'))
                                ->required()
                                ->maxLength(255),

                            Select::make('interval_seconds')
                                ->label(__('ping.interval_seconds'))
                                ->helperText(__('ping.interval_seconds_help'))
                                ->options([
                                    15 => '15s',
                                    30 => '30s',
                                    45 => '45s',
                                    60 => '1m',
                                    300 => '5m',
                                    600 => '10m',
                                    900 => '15m',
                                    1800 => '30m',
                                    3600 => '1h',
                                    10800 => '3h',
                                    21600 => '6h',
                                    43200 => '12h',
                                    86400 => '24h',
                                ])
                                ->required()
                                ->default(60),

                            Select::make('packet_count')
                                ->label(__('ping.packet_count'))
                                ->options([
                                    5 => '5',
                                    10 => '10',
                                    15 => '15',
                                    20 => '20',
                                ])
                                ->required()
                                ->default(5),

                            Checkbox::make('is_active')
                                ->label(__('ping.is_active'))
                                ->default(true),
                        ]),
                ]),
        ];
    }
}
