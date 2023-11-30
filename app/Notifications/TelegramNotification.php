<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Telegram\Telegram;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class TelegramNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $message;

    public function __construct($title, $svName = "-", $traineeName = "-", $content)
    {
        $this->title = $title;
        $this->svName = $svName;
        $this->traineeName = $traineeName;
        $this->content = $content;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable)
    {
        return [TelegramChannel::class];
    }

    public function toTelegram($notifiable)
    {
        $telegram_chat_id = env('CHANNEL_CHAT_ID');
        $message = $this->formatMessage();

        return TelegramMessage::create()
            ->to($telegram_chat_id)
            ->content($message)
            ->options(['parse_mode' => 'HTML']);

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    private function formatMessage()
    {
        $message = '';
        if($this->svName != '' && $this->traineeName != '' ){
            $message = 
            "<strong><u>$this->title</u></strong>\n".
             'Supervisor: ' . $this->svName . "\n".
             'Trainee: ' . $this->traineeName . "\n\n".
             'Message: ' . $this->content . "\n";
        }
        elseif($this->traineeName != ''){
            $message = 
            "<strong><u>$this->title</u></strong>\n".
             'Trainee: ' . $this->traineeName . "\n\n".
             'Message: ' . $this->content . "\n";
        }
        elseif($this->svName != ''){
            $message = 
            "<strong><u>$this->title</u></strong>\n".
             'Supervisor: ' . $this->svName . "\n\n".
             'Message: ' . $this->content . "\n";
        }


        return $message;
    }
}
