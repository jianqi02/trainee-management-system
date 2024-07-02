<?php

namespace App\Http\ViewComposers;

use Ramsey\Uuid\Uuid;
use App\Models\Trainee;
use Illuminate\View\View;
use App\Models\AllTrainee;
use App\Models\Supervisor;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    public function compose(View $view)
    {
        //get the current login user information 
        $user = Auth::user();

        //notification for admin
        if($user->role_id == 1){
            // Check if there are more than 40 notifications ( maximum number of notifications: 40)
            $notificationCount = DB::table('notifications')
            ->where('notifiable_id', 0)
            ->count();

            if ($notificationCount >= 40) {
                // Calculate how many notifications to delete (the oldest ones)
                $notificationsToDelete = DB::table('notifications')
                    ->where('notifiable_id', 0)
                    ->orderBy('created_at', 'asc')
                    ->limit($notificationCount - 40)
                    ->pluck('id');

                // Delete the oldest notifications
                DB::table('notifications')
                    ->whereIn('id', $notificationsToDelete)
                    ->delete();
            }

            $notifications = DB::table('notifications')->where('notifiable_id', 0)
            ->orderBy('created_at', 'desc')
            ->get();
        }
        //notification for supervisor
        elseif($user->role_id == 2){
            $supervisorID = Supervisor::where('sains_email', $user->email)->pluck('id')->first();
            $notificationCount = DB::table('notifications')
            ->where('notifiable_id', $supervisorID)
            ->whereNot('notifiable_type', 'App\Models\Supervisor')
            ->count();

            //limit the maximum numbers of notifications to 40.
            if ($notificationCount >= 40) {
                // Calculate how many notifications to delete (the oldest ones)
                $notificationsToDelete = DB::table('notifications')
                    ->where('notifiable_id', $supervisorID)
                    ->whereNot('notifiable_type', 'App\Models\Supervisor')
                    ->orderBy('created_at', 'asc')
                    ->limit($notificationCount - 40)
                    ->pluck('id');

                // Delete the oldest notifications
                DB::table('notifications')
                    ->whereIn('id', $notificationsToDelete)
                    ->delete();
            }

            $notifications = DB::table('notifications')->where('notifiable_id', $supervisorID)
            ->whereNot('notifiable_type', 'App\Models\Supervisor')
            ->orderBy('created_at', 'desc')
            ->get();
        }
        //notification for trainee
        else{
            $trainee_id = Trainee::where('sains_email', $user->email)->pluck('id')->first();
            $trainee_name = Trainee::where('sains_email', $user->email)->pluck('name')->first();

            // calculate the total number of notification that related to this trainee
            $notificationCount = DB::table('notifications')
                ->where('notifiable_id', $trainee_id)
                ->whereJsonContains('data->name', $trainee_name)
                ->count();

            //limit the maximum numbers of notifications to 40.
            if ($notificationCount >= 40) {
                // Calculate how many notifications to delete (the oldest ones)
                $notificationsToDelete = DB::table('notifications')
                    ->where('notifiable_id', $trainee_id)
                    ->where('notifiable_type', 'App\Models\Supervisor')
                    ->orderBy('created_at', 'asc')
                    ->limit($notificationCount - 40)
                    ->pluck('id');

                // Delete the oldest notifications
                DB::table('notifications')
                    ->whereIn('id', $notificationsToDelete)
                    ->delete();
            }

            $notifications = DB::table('notifications')->where('notifiable_id', $trainee_id)
            ->whereJsonContains('data->name', $trainee_name)
            ->orderBy('created_at', 'desc')
            ->get();
        }

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
        $view->with('notifications', $notifications);
    }
}


