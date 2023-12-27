<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout()
    {
        // Clear the session_id from the database
        $user = Auth::user();
        $user->session_id = null;
        $user->last_login = null;
        $user->save();

        $activityLog = new ActivityLog([
            'username' => $user->name,
            'action' => 'Logout',
            'outcome' => 'success',
            'details' => '',
        ]);

        $activityLog->save();

        // Log out the user
        Auth::logout();

        // Redirect to the login page
        return redirect('/login');
    }
}
