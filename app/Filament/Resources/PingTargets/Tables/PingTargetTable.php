<?php

namespace App\Filament\Resources\PingTargets\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;

class PingTargetTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('general.id'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('name')
                    ->label(__('ping.name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('host')
                    ->label(__('ping.host'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('interval_seconds')
                    ->label(__('ping.interval_seconds'))
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        if ($state >= 3600) {
                            return round($state / 3600, 1) . 'h';
                        }
                        if ($state >= 60) {
                            return round($state / 60, 1) . 'm';
                        }
                        return $state . 's';
                    })
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('is_active')
                    ->label(__('ping.is_active'))
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->alignment(Alignment::Center)
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('created_at')
                    ->label(__('general.created_at'))
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('ping.is_active'))
                    ->nullable()
                    ->native(false)
                    ->trueLabel(__('general.yes'))
                    ->falseLabel(__('general.no')),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }
}
