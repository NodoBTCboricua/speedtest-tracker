<?php

namespace App\Filament\Widgets;

use App\Models\PingTarget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IcmpStatusWidget extends BaseWidget
{
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $targets = PingTarget::where('is_active', true)->get();

        $stats = [];

        foreach ($targets as $target) {
            $latestResult = $target->pingResults()->latest('created_at')->first();

            // Get last few results for chart
            $chartData = $target->pingResults()
                ->latest('created_at')
                ->take(10)
                ->pluck('latency')
                ->reverse()
                ->toArray();

            if (! $latestResult) {
                $status = 'Pending';
                $color = 'gray';
                $description = 'No data yet';
            } else {
                $packetLoss = $latestResult->packet_loss ?? 100;
                $isReachable = $latestResult->is_reachable;

                // Calculate average of displayed chart data
                $avgLatency = count($chartData) > 0 ? array_sum($chartData) / count($chartData) : 0;

                // User logic: < 100% loss = Online, 100% loss = Offline
                // Also consider is_reachable flag
                $lastRun = $latestResult->created_at->timezone(config('app.display_timezone'))->format('g:i a');

                if ($isReachable && $packetLoss < 100) {
                    $status = 'Online';
                    $color = 'success';
                    $description = "Last: {$lastRun} | Loss: " . round($packetLoss, 1) . "% | " . round($latestResult->latency, 1) . "ms | Avg: " . round($avgLatency, 1) . "ms";
                } else {
                    $status = 'Offline';
                    $color = 'danger';
                    $description = "Last: {$lastRun} | Loss: " . round($packetLoss, 1) . "%";
                }
            }

            $stats[] = Stat::make($target->name, $status)
                ->description($description)
                ->color($color)
                ->chart($chartData);
        }

        return $stats;
    }
}
