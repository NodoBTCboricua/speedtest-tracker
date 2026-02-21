<?php

namespace App\Jobs;

use App\Actions\PingHostname;
use App\Models\PingResult;
use App\Models\PingTarget;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RunPingTargetJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public PingTarget $pingTarget,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PingHostname $pingHostname): void
    {
        $result = $pingHostname->run($this->pingTarget->host);

        if ($result === null) {
            $this->pingTarget->pingResults()->create([
                'latency' => null,
                'is_reachable' => false,
            ]);

            return;
        }

        $latency = $result->averageTimeInMs();
        $isReachable = $result->isSuccess();

        $this->pingTarget->pingResults()->create([
            'latency' => $isReachable ? round($latency, 3) : null,
            'is_reachable' => $isReachable,
        ]);
    }
}
