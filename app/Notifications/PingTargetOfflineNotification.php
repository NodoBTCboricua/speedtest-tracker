<?php

namespace App\Notifications;

use App\Models\PingTarget;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class PingTargetOfflineNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public PingTarget $pingTarget,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        if (config('services.telegram-bot-api.token')) {
            $channels[] = 'telegram';
        }

        if (config('mail.from.address')) {
            $channels[] = 'mail';
        }

        $channels[] = 'database';

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject('Ping Target Offline: '.$this->pingTarget->name)
            ->line('The ping target "'.$this->pingTarget->name.'" ('.$this->pingTarget->host.') is unreachable.')
            ->action('View Results', url('/admin/ping-results'))
            ->line('Thank you for using Speedtest Tracker!');
    }

    /**
     * Get the Telegram message representation of the notification.
     */
    public function toTelegram($notifiable): TelegramMessage
    {
        return TelegramMessage::create()
            ->to($notifiable->routes['telegram_chat_id'] ?? null)
            ->content(sprintf('⚠️ *Ping Target Offline* %sThe host "%s" (%s) is unreachable.', PHP_EOL, $this->pingTarget->name, $this->pingTarget->host));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ping_target_id' => $this->pingTarget->id,
            'name' => $this->pingTarget->name,
            'host' => $this->pingTarget->host,
            'message' => 'Ping target is unreachable.',
        ];
    }
}
