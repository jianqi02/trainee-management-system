<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Trainee;
use App\Models\Settings;
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

        // Fetch the disable_registration setting
        $settings = Settings::first();

        // If registration is disabled, abort with a 403 error
        if ($settings && $settings->disable_registration) {
            abort(403, 'Account registration is currently disabled.');
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // Fetch the allowed email domains from the settings table
        $settings = Settings::first(); 
        $allowedDomains = explode(',', $settings->email_domain); // Get array of allowed domains set by admin
    
        return Validator::make($data, [
            'name' => [
                'required', 
                'string', 
                'max:255', 
                'regex:/^[a-zA-Z\s]+$/',
                function ($attribute, $value, $fail) {
                    // Check if the name already exists in the users table
                    if (\App\Models\User::where('name', $value)->exists()) {
                        $fail('The name has already been taken.');
                    }
                }
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                function ($attribute, $value, $fail) use ($allowedDomains) {
                    $emailDomain = substr(strrchr($value, "@"), 1); // Extract the domain from the email
    
                    // Check if the email domain is in the allowed domains
                    if (!in_array($emailDomain, $allowedDomains)) {
                        $fail("The email domain must be one of the allowed domains. Please contact the admin for more information." );
                    }
                },
                function ($attribute, $value, $fail) {
                    // Check if a special character is the first or last character
                    if (preg_match('/^[^A-Za-z0-9_]/', $value) || preg_match('/[^A-Za-z0-9_]$/', $value)) {
                        $fail($attribute . ' is invalid.');
                    }
    
                    // Check if special characters appear consecutively two or more times
                    if (preg_match('/[^A-Za-z0-9_]{2,}/', $value)) {
                        $fail($attribute . ' is invalid.');
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
            'email.regex' => 'The email field should be a valid email address.',
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
