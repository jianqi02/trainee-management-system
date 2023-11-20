<?php

namespace App\Http\Controllers;

use App\Models\Trainee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $trainee = Trainee::where('sains_email', $user->email)->first();
        $userRole = $user->role_id;
        
        if ($userRole == 2) {
            return view('sv-homepage');
        }
        elseif($userRole == 1){
            return view('admin-dashboard');
        } else {
            if ($trainee->personal_email == null || $trainee->internship_start == null || $trainee->internship_end == null || $trainee->phone_number == null) {
                return redirect('/trainee-edit-profile')->with('alert', 'Please complete your profile first'); 
            } else {
                return view('homepage');
            }
        }
    }

}
