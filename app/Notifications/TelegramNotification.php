
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
        $message = $this->formatMessage();

        // The URL you want to send the request to
        $url = "http://10.17.98.251:6080/tms/";

        // Data you want to send in the request (if any)
        $data = array(
            'msg' => $message,
        );

        // Convert the data array to JSON
        $jsonData = json_encode($data);

        // Set up cURL
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData),
        ]);

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        
        // Check for cURL errors
        if (curl_errno($ch)) {      
            echo 'cURL error: ' . curl_error($ch);      
        }

        // Close cURL session
        curl_close($ch);
        // Display the response
        //echo $response;

        //return TelegramMessage::create()
        //    ->to($telegram_chat_id)
        //    ->content($message)
        //    ->options(['parse_mode' => 'HTML']);
        return TelegramMessage::create();
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
