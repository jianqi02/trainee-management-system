<?php

namespace App\Http\Controllers;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Trainee;
use App\Models\Supervisor;
use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NotificationController;

class NotificationController extends Controller
{
    public function index()
    {
        //get the current login user information 
        $user = Auth::user();

        //notification for admin
        if($user->role_id == 1){
            // Check if there are more than 99 notifications ( maximum number of notifications: 99)
            $notificationCount = DB::table('notifications')
            ->where('notifiable_id', 0)
            ->count();

            if ($notificationCount >= 99) {
                // Calculate how many notifications to delete (the oldest ones)
                $notificationsToDelete = DB::table('notifications')
                    ->where('notifiable_id', 0)
                    ->orderBy('created_at', 'asc')
                    ->limit($notificationCount - 99)
                    ->pluck('id');

                // Delete the oldest notifications
                DB::table('notifications')
                    ->whereIn('id', $notificationsToDelete)
                    ->delete();
            }

            $notifications = DB::table('notifications')->where('notifiable_id', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

            return view('admin-notifications', compact('notifications'));
        }
        //notification for supervisor
        elseif($user->role_id == 2){
            $supervisorID = Supervisor::where('sains_email', $user->email)->pluck('id')->first();
            $notificationCount = DB::table('notifications')
            ->where('notifiable_id', $supervisorID)
            ->whereNot('notifiable_type', 'App\Models\Supervisor')
            ->count();

            //limit the maximum numbers of notifications to 99.
            if ($notificationCount >= 99) {
                // Calculate how many notifications to delete (the oldest ones)
                $notificationsToDelete = DB::table('notifications')
                    ->where('notifiable_id', $supervisorID)
                    ->whereNot('notifiable_type', 'App\Models\Supervisor')
                    ->orderBy('created_at', 'asc')
                    ->limit($notificationCount - 99)
                    ->pluck('id');

                // Delete the oldest notifications
                DB::table('notifications')
                    ->whereIn('id', $notificationsToDelete)
                    ->delete();
            }

            $notifications = DB::table('notifications')->where('notifiable_id', $supervisorID)
            ->whereNot('notifiable_type', 'App\Models\Supervisor')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

            return view('sv-notifications', compact('notifications'));
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

            //limit the maximum numbers of notifications to 99.
            if ($notificationCount >= 99) {
                // Calculate how many notifications to delete (the oldest ones)
                $notificationsToDelete = DB::table('notifications')
                    ->where('notifiable_id', $trainee_id)
                    ->where('notifiable_type', 'App\Models\Supervisor')
                    ->orderBy('created_at', 'asc')
                    ->limit($notificationCount - 99)
                    ->pluck('id');

                // Delete the oldest notifications
                DB::table('notifications')
                    ->whereIn('id', $notificationsToDelete)
                    ->delete();
            }

            $notifications = DB::table('notifications')->where('notifiable_id', $trainee_id)
            ->whereJsonContains('data->name', $trainee_name)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

            return view('trainee-notifications', compact('notifications'));
        }

    }

    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::where('id', $id)->first();
        if ($notification) {
            $notification->read_at = now();
            $notification->save();
        }
    
        return redirect()->back();
    }

    public function markAllAsRead(){
        $user = Auth::user();

        //admin
        if($user->role_id == 1){
            $notifications = Notification::where('notifiable_id', 0)->whereNull('read_at')->get();

            foreach ($notifications as $notification) {
                $notification->update(['read_at' => now()]);
            }
    
            return redirect()->back()->with('success', 'All notifications have been marked as read.');
        }
        //supervisor
        else if($user->role_id == 2){
            $supervisorID = Supervisor::where('sains_email', $user->email)->pluck('id')->first();
            $notifications = Notification::where('notifiable_id', $supervisorID)->whereNull('read_at')->get();

            foreach ($notifications as $notification) {
                $notification->update(['read_at' => now()]);
            }
    
            return redirect()->back()->with('success', 'All notifications have been marked as read.');
        }  
        //trainee
        else{
            $trainee_name = Trainee::where('sains_email', $user->email)->pluck('name')->first();
            $traineeID = Trainee::where('sains_email', $user->email)->pluck('id')->first();
            $notifications = Notification::where('notifiable_id', $traineeID)
                ->whereJsonContains('data->name', $trainee_name)
                ->whereNull('read_at')
                ->get();

            foreach ($notifications as $notification) {
                $notification->update(['read_at' => now()]);
            }
    
            return redirect()->back()->with('success', 'All notifications have been marked as read.');
        }
    
    }

    public function forgotPasswordNotification(Request $request){
        $email = $request->input('email');
        $userRecord = User::where('email', $email)->first();

        if($userRecord){
            //to check whether this is supervisor or trainee record. 
            $userRole = $userRecord->role_id;

            //2 = supervisor , 3 = trainee
            if($userRole == 2){
                $supervisorName = Supervisor::where('sains_email', $email)->pluck('name')->first();

                //generate a notification to admin.
                $notification = new Notification();
                $notification->id = Uuid::uuid4(); // Generate a UUID for the id
                $notification->type = 'password reset';
                $notification->notifiable_type = 'App\Models\Supervisor';
                $notification->notifiable_id = 0;
                $notification->data = json_encode([
                    'data' => 'Supervisor ' . $supervisorName . ' has requested to reset the password.',
                ]);
                $notification->save(); // Save the notification to the database

                $activityLog = new ActivityLog([
                    'username' => $supervisorName,
                    'action' => 'Password Reset Request',
                    'outcome' => 'success',
                    'details' => 'Role: Supervisor',
                ]);
        
                $activityLog->save();
            }
            elseif($userRole == 3){
                $traineeName = Trainee::where('sains_email', $email)->pluck('name')->first();

                //generate a notification to admin.
                $notification = new Notification();
                $notification->id = Uuid::uuid4(); // Generate a UUID for the id
                $notification->type = 'password reset';
                $notification->notifiable_type = 'App\Models\Trainee';
                $notification->notifiable_id = 0;
                $notification->data = json_encode([
                    'data' => 'Trainee ' . $traineeName . ' has requested to reset the password.',
                ]);
                $notification->save(); // Save the notification to the database

                $activityLog = new ActivityLog([
                    'username' => $traineeName,
                    'action' => 'Password Reset Request',
                    'outcome' => 'success',
                    'details' => 'Role: Trainee',
                ]);
        
                $activityLog->save();
            }
            else{
                $activityLog = new ActivityLog([
                    'username' => $email,
                    'action' => 'Password Reset Request',
                    'outcome' => 'failed',
                    'details' => 'It is an admin account.',
                ]);
        
                $activityLog->save();
                return redirect()->back()->with('status', 'If the email address submitted is valid, a notification will be sent to the admin to reset your password.');
            }
        }else{
            $activityLog = new ActivityLog([
                'username' => $email,
                'action' => 'Password Reset Request',
                'outcome' => 'failed',
                'details' => 'Unknown email address.',
            ]);
    
            $activityLog->save();
            return redirect()->back()->with('status', 'If the email address submitted is valid, a notification will be sent to the admin to reset your password.');
        }

        return redirect()->back()->with('success', 'If the email address submitted is valid, a notification will be sent to the admin to reset your password.');
    }
}
