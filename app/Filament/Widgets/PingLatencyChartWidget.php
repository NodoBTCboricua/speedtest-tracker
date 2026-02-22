<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasChartFilters;
use App\Models\PingResult;
use App\Models\PingTarget;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;

class PingLatencyChartWidget extends ChartWidget
{
    use HasChartFilters;

    protected ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '250px';

    protected ?string $pollingInterval = '60s';

    public ?string $filter = null;

    /**
     * Optional: show only this ping target. When null, show all targets (one line per target).
     */
    public ?int $pingTargetId = null;

    public function getHeading(): ?string
    {
        return __('ping.latency_chart');
    }

    public function mount(): void
    {
        $this->filter = $this->filter ?? config('speedtest.default_chart_range', '24h');
    }

    protected function getData(): array
    {
        $query = PingResult::query()
            ->select(['id', 'ping_target_id', 'latency', 'created_at'])
            ->when($this->filter === '24h', fn ($q) => $q->where('created_at', '>=', now()->subDay()))
            ->when($this->filter === 'week', fn ($q) => $q->where('created_at', '>=', now()->subWeek()))
            ->when($this->filter === 'month', fn ($q) => $q->where('created_at', '>=', now()->subMonth()))
            ->when($this->pingTargetId !== null, fn ($q) => $q->where('ping_target_id', $this->pingTargetId))
            ->orderBy('created_at');

        $results = $query->get();

        if ($results->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $targets = $this->pingTargetId !== null
            ? PingTarget::query()->where('id', $this->pingTargetId)->get()
            : PingTarget::query()->whereIn('id', $results->pluck('ping_target_id')->unique())->orderBy('name')->get();

        $labels = $results->pluck('created_at')->unique()->sort()->values();

        $colors = [
            'rgba(16, 185, 129, 1)',   // green (same as RecentPingChart)
            'rgba(14, 165, 233, 1)',   // blue
            'rgba(249, 115, 22, 1)',   // orange
            'rgba(168, 85, 247, 1)',   // purple
            'rgba(236, 72, 153, 1)',   // pink
        ];

        $datasets = [];
        $resultsByTargetAndTime = $results->groupBy('ping_target_id')->map(
            fn (Collection $group) => $group->keyBy(fn ($r) => $r->created_at->format('Y-m-d H:i:s'))
        );

        $showAverageLine = $targets->count() === 1;

        foreach ($targets as $index => $target) {
            $targetResults = $results->where('ping_target_id', $target->id);
            $avgRounded = $showAverageLine
                ? $targetResults->filter(fn ($r) => $r->latency !== null)->avg('latency')
                : null;
            if ($avgRounded !== null) {
                $avgRounded = round($avgRounded, 2);
            }

            $color = $colors[$index % count($colors)];
            $data = $labels->map(fn ($ts) => $resultsByTargetAndTime->get($target->id)?->get($ts->format('Y-m-d H:i:s'))?->latency);

            $datasets[] = [
                'label' => $target->name,
                'data' => $data->all(),
                'borderColor' => $color,
                'backgroundColor' => str_replace('1)', '0.1)', $color),
                'pointBackgroundColor' => $color,
                'fill' => true,
                'cubicInterpolationMode' => 'monotone',
                'tension' => 0.4,
                'pointRadius' => $labels->count() <= 24 ? 3 : 0,
            ];

            if ($showAverageLine && $avgRounded !== null) {
                $datasets[] = [
                    'label' => __('general.average'),
                    'data' => array_fill(0, $labels->count(), $avgRounded),
                    'borderColor' => 'rgb(243, 7, 6, 1)',
                    'pointBackgroundColor' => 'rgb(243, 7, 6, 1)',
                    'fill' => false,
                    'cubicInterpolationMode' => 'monotone',
                    'tension' => 0.4,
                    'pointRadius' => 0,
                ];
            }
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels->map(fn ($ts) => $ts->timezone(config('app.display_timezone'))->format(config('app.chart_datetime_format')))->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false,
                    'position' => 'nearest',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => config('app.chart_begin_at_zero'),
                    'grace' => 2,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
