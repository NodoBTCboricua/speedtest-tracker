<?php

namespace App\Filament\Resources\PingResults\Tables;

use App\Filament\Exports\PingResultExporter;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class PingResultTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('general.id'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('pingTarget.name')
                    ->label(__('ping.target'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('latency')
                    ->label(__('ping.latency_ms'))
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format((float) $state, 2, '.', '').' ms' : '—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('is_reachable')
                    ->label(__('ping.is_reachable'))
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignment(Alignment::Center)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('created_at')
                    ->label(__('general.created_at'))
                    ->dateTime(config('app.datetime_format'))
                    ->timezone(config('app.display_timezone'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('ping_target_id')
                    ->label(__('ping.target'))
                    ->relationship('pingTarget', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('created_at')
                    ->label(__('general.created_at'))
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('results.created_from'))
                            ->closeOnDateSelection()
                            ->native(false),
                        DatePicker::make('created_until')
                            ->label(__('results.created_until'))
                            ->closeOnDateSelection()
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
                ExportAction::make()
                    ->exporter(PingResultExporter::class)
                    ->columnMapping(false)
                    ->modalHeading(__('results.export_all_results'))
                    ->modalDescription(__('results.export_all_results_description'))
                    ->fileName(fn (): string => 'ping-results-'.now()->timestamp),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistFilters()
            ->persistSort()
            ->persistColumnVisibility()
            ->poll('60s');
    }
}
