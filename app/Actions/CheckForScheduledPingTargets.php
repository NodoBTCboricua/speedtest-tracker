<?php

namespace App\Actions;

use App\Jobs\RunPingTargetJob;
use App\Models\PingTarget;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class CheckForScheduledPingTargets
{
    use AsAction;

    /**
     * For each active ping target, dispatch RunPingTargetJob according to interval_seconds.
     * Handles sub-minute intervals by dispatching multiple jobs with delays within the next minute.
     */
    public function handle(): void
    {
        PingTarget::query()
            ->where('is_active', true)
            ->get()
            ->each(fn (PingTarget $target) => $this->dispatchForTarget($target));
    }

    private function dispatchForTarget(PingTarget $target): void
    {
        $interval = $target->interval_seconds;

        if ($interval >= 60) {
            $this->dispatchOnceIfDue($target, $interval);

            return;
        }

        // Sub-minute: dispatch one job per slot in the next 60 seconds with delay.
        for ($delaySeconds = 0; $delaySeconds < 60; $delaySeconds += $interval) {
            RunPingTargetJob::dispatch($target)
                ->delay(now()->addSeconds($delaySeconds));
        }
    }

    private function dispatchOnceIfDue(PingTarget $target, int $intervalSeconds): void
    {
        $lastResult = $target->pingResults()->latest('created_at')->first();
        $nextDueAt = $lastResult
            ? Carbon::parse($lastResult->created_at)->addSeconds($intervalSeconds)
            : now();

        if ($nextDueAt->isPast() || $nextDueAt->equalTo(now())) {
            RunPingTargetJob::dispatch($target);
        }
    }
}
