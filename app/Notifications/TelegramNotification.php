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

    protected $title;
    protected $svName;
    protected $traineeName;
    protected $content;

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
        // Call formatMessage() to get the formatted message
        $message = $this->formatMessage();
        
        // Use environment variables for the bot token and chat ID
        $botToken = env('TELEGRAM_BOT_TOKEN');  // Ensure this is set in your config/services.php
        $chatId = env('CHANNEL_CHAT_ID');      // Make sure this is the correct channel chat ID

        // Define the Telegram API URL for sending messages
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        // Prepare the data to be sent in the POST request (use json_encode to format the data)
        $data = [
            'chat_id' => $chatId,  // The chat ID (your private channel ID)
            'text' => $message,    // The message text
            'parse_mode' => 'HTML' // To support HTML formatting (optional)
        ];

        // Convert the data array to JSON format
        $jsonData = json_encode($data);

        // Set up cURL
        $ch = curl_init($url);
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Get the response
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');  // Set request method to POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);  // Attach the JSON data
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',  // Specify the content type
            'Content-Length: ' . strlen($jsonData),  // Specify the length of the data
        ]);

        // Execute the cURL session and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);  // Output any cURL errors
        } else {
            // Log the response from Telegram API (successful response or error)
            echo 'Response: ' . $response;  // This will show you if the message was sent successfully
        }

        // Close the cURL session
        curl_close($ch);

        // Optionally handle the response if needed (e.g., logging, error handling)
        //echo $response;  // Uncomment to debug the response

        //return TelegramMessage::create()
        //    ->to($telegram_chat_id)
        //    ->content($message)
        //    ->options(['parse_mode' => 'HTML']);
        return TelegramMessage::create();  // Return a Telegram message instance if needed
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
