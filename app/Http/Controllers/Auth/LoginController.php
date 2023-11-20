<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use App\Models\Trainee;
use App\Models\Notification;
use App\Models\TaskTimeline;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Redirect user to homepage after login.
     *
     * @var string
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated($request, $user)
    {
        $trainee = Trainee::where('name', $user->name)->first();
        if($trainee != null){
            // Check the trainee's account status
            if ($trainee->acc_status === 'Active') {
                if($trainee->personal_email == null || $trainee->phone_number == null){
                    return redirect('/trainee-edit-profile')->with('alert', 'Please complete your profile first!'); // Redirect trainees to trainee edit profile page
                }
                else{
                    return redirect()->intended($this->redirectPath());
                }
            } else {
                // If the account is inactive, log the user out and show a message
                Auth::logout();
                return redirect('/login')->with('status', 'Your account is inactive. Please contact admin.');
            }
            
        } else {
            return redirect()->intended($this->redirectPath());
        }
    }

    protected function redirectTo()
    {
        // Check the role of the authenticated user
        $user = Auth::user();
        $trainee = Trainee::where('name', $user->name)->first();
        if ($user->role_id === 1) {
            return '/admin-dashboard'; // Redirect trainees to admin dashboard
        } elseif ($user->role_id === 2) {
            return '/sv-homepage'; // Redirect supervisors to supervisor homepage
        } elseif ($user->role_id === 3 ) {
            // check the task information
            // when today is 1 day before the due date, send a notification to the trainee.
            $traineeID = $trainee->id;
            $tasks = TaskTimeline::where('trainee_id', $traineeID)->get();
            
            foreach ($tasks as $task) {
                $dueDate = Carbon::parse($task->task_end_date);
                $oneDayAfterCurrentTime = Carbon::now()->addDay();
                
                // Check if the task end date is 1 day after the current time
                if ($dueDate->isSameDay($oneDayAfterCurrentTime)) {
                    $traineeName = $trainee->name;
            
                    // Check if a similar notification already exists
                    $existingNotification = Notification::where([
                        'type' => 'Task Due Date',
                        'notifiable_type' => 'App\Models\TaskTimeline',
                        'notifiable_id' => $traineeID,
                        'data->name' => $traineeName, // Assuming 'name' is stored in the 'data' field
                    ])->first();
            
                    // If no similar notification exists, create and save a new one
                    if (!$existingNotification) {
                        $notification = new Notification();
                        $notification->id = Uuid::uuid4(); // Generate a UUID for the id
                        $notification->type = 'Task Due Date';
                        $notification->notifiable_type = 'App\Models\TaskTimeline';
                        $notification->notifiable_id = $traineeID;
                        $notification->data = json_encode([
                            'data' => 'Your task ' . $task->task_name . ' is due tomorrow.',
                            'name' => $traineeName,
                        ]);
                        $notification->save(); // Save the notification to the database
                    }
                }
            }

            return '/homepage'; // Redirect trainees to trainee homepage

        } else{
            return redirect('/login')->with('status', 'Invalid Account.');
        }
    }
}
