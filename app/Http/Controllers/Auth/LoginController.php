<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use App\Models\Trainee;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\TaskTimeline;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Notifications\Notifiable;
use App\Notifications\TelegramNotification;
use Illuminate\Validation\ValidationException;
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

    protected $maxAttempts = 5; 
    protected $decayMinutes = 30;

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
         // Retrieve the session_id from the current session
        $currentSessionId = Session::getId();

        // Check if the user has a stored session_id and if it's different from the current session
        if ($user->session_id && $user->session_id !== $currentSessionId) {
            $lastLoginTime = $user->last_login;

            if ($lastLoginTime) {
                $lastLoginTime = Carbon::parse($lastLoginTime);
                $timeDifference = $lastLoginTime->diffInSeconds(Carbon::now());
                $sessionLifetime = env('CUSTOMIZED_LIFETIME'); // the lifetime in seconds

                if ( $timeDifference > $sessionLifetime) {
                    // Session has expired
                    $user->session_id = null;
                    $user->last_login = null;
                    $user->save();

                    $activityLog = new ActivityLog([
                        'username' => $user->name,
                        'action' => 'Session Expired',
                        'outcome' => 'success',
                        'details' => '',
                    ]);
                    
                    $activityLog->save();

                    // Log out the user
                    Auth::logout();
                    
                    return redirect('/login')->with('status', 'Your session has expired. Please log in again.');
                }
            } 
        }

        $trainee = Trainee::where('name', $user->name)->first();
        if($trainee != null && $user->role_id == 3){
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
            // Check the trainee's account status
            if ($trainee->acc_status === 'Active') {

                $activityLog = new ActivityLog([
                    'username' => $trainee->name,
                    'action' => 'Login',
                    'outcome' => 'success',
                    'details' => '',
                ]);
                
                $activityLog->save();

                if($trainee->personal_email == null || $trainee->phone_number == null){

                    //save the session in the db
                    $user->session_id = session_id();
                    $user->last_login = now();
                    $user->save();
                    return redirect('/trainee-edit-profile')->with('alert', 'Please complete your profile first!'); // Redirect trainees to trainee edit profile page
                }
                else{
                    return redirect('/homepage');
                }
            } else {
                // If the account is inactive, log the user out and show a message
                $user->session_id = null;
                $user->save();

                $activityLog = new ActivityLog([
                    'username' => $trainee->name,
                    'action' => 'Login',
                    'outcome' => 'failed',
                    'details' => 'Account is inactive',
                ]);
                
                $activityLog->save();

                Auth::logout();
                return redirect('/login')->with('status', 'Your account is inactive. Please contact admin.');
            }
            
        } else {
            if($user->role_id == 2){
                $user->session_id = session_id();
                $user->last_login = now();
                $user->save();

                $activityLog = new ActivityLog([
                    'username' => $user->name,
                    'action' => 'Login',
                    'outcome' => 'success',
                    'details' => '',
                ]);
                
                $activityLog->save();

                return redirect('/sv-homepage');
            }
            elseif($user->role_id == 1){
                $user->session_id = session_id();
                $user->last_login = now();
                $user->save();

                $activityLog = new ActivityLog([
                    'username' => $user->name,
                    'action' => 'Login',
                    'outcome' => 'success',
                    'details' => '',
                ]);
                
                $activityLog->save();

                return redirect('/admin-dashboard');
            }
            else{

                $activityLog = new ActivityLog([
                    'username' => $user->name,
                    'action' => 'Login',
                    'outcome' => 'failed',
                    'details' => 'Unexpected or invalid role',
                ]);
                
                $activityLog->save();

                return redirect('/login')->with('status', 'Please try again.');
            }
        }
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $activityLog = new ActivityLog([
            'username' => $request->input('email'),
            'action' => 'Login',
            'outcome' => 'failed',
            'details' => 'Invalid credentials entered',
        ]);
    
        $activityLog->save();

        throw ValidationException::withMessages([
           'Invalid credentials.'
        ]);
    }
}
