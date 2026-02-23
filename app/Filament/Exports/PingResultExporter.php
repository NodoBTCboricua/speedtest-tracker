<?php

namespace App\Filament\Exports;

use App\Models\PingResult;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PingResultExporter extends Exporter
{
    protected static ?string $model = PingResult::class;

    public function getFormats(): array
    {
        return [
            ExportFormat::Csv,
        ];
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('pingTarget.name')->label('Target'),
            ExportColumn::make('pingTarget.host')->label('Host'),
            ExportColumn::make('latency')->label('Latency (ms)'),
            ExportColumn::make('packet_loss')->label('Packet Loss (%)'),
            ExportColumn::make('is_reachable')
                ->label('Reachable')
                ->state(fn (PingResult $r) => $r->is_reachable ? 'Yes' : 'No'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your ping result export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
