<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout()
    {
        // Clear the session_id from the database
        $user = Auth::user();
        $user->session_id = null;
        $user->save();

        // Log out the user
        Auth::logout();

        // Redirect to the login page
        return redirect('/login');
    }
}
