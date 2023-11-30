<?php

namespace App\Http\ViewComposers;

use Ramsey\Uuid\Uuid;
use App\Models\Trainee;
use Illuminate\View\View;
use App\Models\AllTrainee;
use App\Models\Supervisor;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Notifications\TelegramNotification;

class NotificationComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();

        if($user->role_id == 1){
            //get unread notification number
            $notification_number = Notification::where('notifiable_id', 0)->where('read_at', null)->count();
            $sevenDaysFromNow = now()->addDays(7);

            $traineeInternshipsStart = AllTrainee::where(function ($query) use ($sevenDaysFromNow) {
                // Make a notification for 7 days before a trainee internship start and end
                $query->whereDate('internship_start', '=', $sevenDaysFromNow->toDateString());
            })->get();

            $traineeInternshipsEnd = AllTrainee::where(function ($query) use ($sevenDaysFromNow) {
                // Make a notification for 7 days before a trainee internship start and end
                $query->whereDate('internship_end', '=', $sevenDaysFromNow->toDateString());
            })->get();

            foreach ($traineeInternshipsStart as $intern) {
                // Create a unique type for the notification
                $notificationTypeStart = 'Internship-start/' . $intern->name;

                // Create a notification for the internship start
                $notification = Notification::firstOrNew(['type' => $notificationTypeStart]);

                // Check if the notification already exists
                if (!$notification->exists) {
                    $uuid = Uuid::uuid4();
                    $notifyData = json_encode([
                        'data' => 'The internship of trainee ' . $intern->name . ' will start on ' . $intern->internship_start,
                    ]);

                    $notification->id = $uuid;
                    $notification->type = $notificationTypeStart;
                    $notification->notifiable_type = 'App\Models\Trainee';
                    $notification->data = $notifyData;
                    $notification->notifiable_id = 0; 
                    $notification->save();

                    $notification->notify(new TelegramNotification('Trainee Internship Start', '', $intern->name, 'The internship of trainee ' . $intern->name . ' will start on ' . $intern->internship_start . '.'));
                }        
            }

            foreach ($traineeInternshipsEnd as $intern) {
                // Create a unique type for the notification
                $notificationTypeEnd = 'Internship-end/' . $intern->name;

                // Create a notification for the internship end
                $notification = Notification::firstOrNew(['type' => $notificationTypeEnd]);

                // Check if the notification already exists
                if (!$notification->exists) {
                    $uuid = Uuid::uuid4();
                    $notifyData = json_encode([
                        'data' => 'The internship of trainee ' . $intern->name . ' will end on ' . $intern->internship_end,
                    ]);

                    $notification->id = $uuid;
                    $notification->type = $notificationTypeEnd;
                    $notification->notifiable_type = 'App\Models\Trainee';
                    $notification->data = $notifyData;
                    $notification->notifiable_id = 0; 
                    $notification->save();

                    $notification->notify(new TelegramNotification('Trainee Internship End', '', $intern->name, 'The internship of trainee ' . $intern->name . ' will end on ' . $intern->internship_end . '.'));
                }
            }
        }
        elseif($user->role_id == 2){
            $supervisorID = Supervisor::where('sains_email', $user->email)->pluck('id')->first();
            $notification_number = Notification::where('notifiable_id', $supervisorID)->where('read_at', null)->count();
        }
        else{
            $traineeID = Trainee::where('sains_email', $user->email)->pluck('id')->first();
            $traineeName = Trainee::where('sains_email', $user->email)->pluck('name')->first();
            $notification_number = Notification::where('notifiable_id', $traineeID)
                ->where('read_at', null)
                ->whereJsonContains('data->name', $traineeName)
                ->count();
        }
        

        $view->with('notification_number', $notification_number);
    }
}
?>