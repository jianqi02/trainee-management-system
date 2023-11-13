<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Trainee;

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
            return '/homepage'; // Redirect trainees to trainee homepage
        } else{
            return redirect('/login')->with('status', 'Invalid Account.');
        }
    }
}
