<?php

namespace App\Filament\Resources\PingResults;

use App\Filament\Resources\PingResults\Pages\ListPingResults;
use App\Filament\Resources\PingResults\Tables\PingResultTable;
use App\Filament\Widgets\PingLatencyChartWidget;
use App\Models\PingResult;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PingResultResource extends Resource
{
    protected static ?string $model = PingResult::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-chart-line';

    protected static string|\UnitEnum|null $navigationGroup = 'Monitor de Ping';

    public static function getNavigationLabel(): string
    {
        return __('ping.ping_results');
    }

    public static function getModelLabel(): string
    {
        return __('ping.ping_result');
    }

    public static function getPluralModelLabel(): string
    {
        return __('ping.ping_results');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return PingResultTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPingResults::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getWidgets(): array
    {
        return [
            PingLatencyChartWidget::class,
        ];
    }
}
