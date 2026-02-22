<?php

namespace App\Filament\Resources\PingTargets\Schemas;

use Filament\Forms\Components\Checkbox;
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

                            TextInput::make('interval_seconds')
                                ->label(__('ping.interval_seconds'))
                                ->helperText(__('ping.interval_seconds_help'))
                                ->numeric()
                                ->minValue(1)
                                ->required()
                                ->default(60)
                                ->suffix('s'),

                            Checkbox::make('is_active')
                                ->label(__('ping.is_active'))
                                ->default(true),
                        ]),
                ]),
        ];
    }
}
