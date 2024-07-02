<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Trainee;
use App\Models\AllTrainee;
use App\Models\Supervisor;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\TraineeAssign;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Notifications\tmsNotification;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // the name should not contain any special character and number
        // the email should follow "@sains.com.my" format
        // the password should contain at least 8 characters, 1 uppercase character and 1 special character.
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                'regex:/^(?=.{1,64}@)[A-Za-z0-9_]+(\.[A-Za-z0-9_]+)*@[^-][A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*(\.[A-Za-z]{2,})$/',
                'ends_with:@sains.com.my',
                function ($attribute, $value, $fail) {
                    // Check if a special character is the first or last character
                    if (preg_match('/^[^A-Za-z0-9_]/', $value) || preg_match('/[^A-Za-z0-9_]$/', $value)) {
                        $fail($attribute.' is invalid.');
                    }
        
                    // Check if special characters appear consecutively two or more times
                    if (preg_match('/[^A-Za-z0-9_]{2,}/', $value)) {
                        $fail($attribute.' is invalid.');
                    }
                },
            ],
            'role' => ['required', 'string', 'in:3,2'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*()_+])[a-zA-Z0-9!@#$%^&*()_+]+$/',
            ],
        ], [
            'name.regex' => 'The name field should only contain letters and spaces.',
            'email.regex' => 'The email field should be a valid SAINS email address.',
            'email.ends_with' => 'The email field should end with @sains.com.my.',
            'role.in' => 'The role field should be either 2 or 3.',
            'password.regex' => 'The password field should contain at least one uppercase letter and one special character.',
        ]);              
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Create the user record
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);
    
        if ($data['role'] == 3) { // Trainee
            $supervisor_status = 'Not Assigned';

            // Check if the trainee is in the list
            $existInList = AllTrainee::where(function($query) use ($data) {
                $query->whereRaw('LOWER(name) = ?', [strtolower($data['name'])]);
            })->first();  

            // Check if the trainee is in the list and has been assigned to a supervisor
            if($existInList !== null){
                $supervisor_checking = TraineeAssign::where('trainee_id', $existInList->id)->first();
                if($supervisor_status !== null){
                    $supervisor_status = 'Assigned';
                }
            }    

            // Create the trainee record
            Trainee::create([
                'name' => $data['name'],
                'personal_email' => NULL,
                'sains_email' => $data['email'],
                'phone_number' => NULL,
                'graduate_date' => NULL,
                'expertise' => 'Not Specified',
                'supervisor_status' => $supervisor_status,
                'resume_path' => NULL,
                'acc_status' => 'Active',
            ]);
        }
        $admin = User::where('role_id', 1)->first();
        // Ignore case sensitive when comparing the name 
        // and send notification to admin when there is a new trainee which is not in the list has registered.
        if (AllTrainee::whereRaw('LOWER(name) = ?', [strtolower($data['name'])])->first() === null) {    
            $notification = new Notification();
            $notification->id = Uuid::uuid4(); // Generate a UUID for the id
            $notification->type = 'App\Notifications\TraineeRegistered';
            $notification->notifiable_type = get_class($admin);
            $notification->notifiable_id = 0;
            $notification->data = json_encode([
                'data' => 'A new trainee ' . $data['name'] . ' which is not in the list has registered.',
                'style' => 'color: red; font-weight: bold;',
            ]);
            $notification->save(); // Save the notification to the database

            $activityLog = new ActivityLog([
                'username' => $data['name'],
                'action' => 'register',
                'outcome' => 'success',
                'details' => 'This trainee is not in the record.',
            ]);
    
            $activityLog->save();
        }
        else{
            $notification = new Notification();
            $notification->id = Uuid::uuid4(); // Generate a UUID for the id
            $notification->type = 'App\Notifications\TraineeRegistered';
            $notification->notifiable_type = get_class($admin);
            $notification->notifiable_id = 0;
            $notification->data = json_encode([
                'data' => 'Trainee ' . $data['name'] . ' has registered.',
            ]);
            $notification->save(); // Save the notification to the database

            $activityLog = new ActivityLog([
                'username' => $data['name'],
                'action' => 'register',
                'outcome' => 'success',
                'details' => 'This trainee is in the record.',
            ]);
    
            $activityLog->save();
        }
    
        return $user;
    }
}
