<?php

namespace App\Jobs;

use App\Actions\PingHostname;
use App\Models\PingResult;
use App\Models\PingTarget;
use App\Models\User;
use App\Notifications\PingTargetOfflineNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

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
        $lastResult = $this->pingTarget->pingResults()->latest('created_at')->first();

        $result = $pingHostname->run($this->pingTarget->host, $this->pingTarget->packet_count ?? 1);

        if ($result === null || ! $result->isSuccess()) {
            $this->pingTarget->pingResults()->create([
                'latency' => null,
                'packet_loss' => $result ? (float) $result->packetLossPercentage() : null,
                'is_reachable' => false,
            ]);

            if ($lastResult === null || $lastResult->is_reachable) {
                $this->notifyAdmins();
            }

            return;
        }

        $latency = $result->averageTimeInMs();
        $packetLoss = $result->packetLossPercentage();
        $isReachable = $result->isSuccess();

        $this->pingTarget->pingResults()->create([
            'latency' => round($latency, 3),
            'packet_loss' => (float) $packetLoss,
            'is_reachable' => $isReachable,
        ]);
    }

    protected function notifyAdmins(): void
    {
        $admins = User::where('role', \App\Enums\UserRole::Admin)->get();

        Notification::send($admins, new PingTargetOfflineNotification($this->pingTarget));
    }
}
