<?php

namespace App\Http\Controllers;

use DateTime;
use Ramsey\Uuid\Uuid;
use App\Models\Comment;
use App\Models\Logbook;
use App\Models\Seating;
use App\Models\Trainee;
use App\Models\AllTrainee;
use App\Models\Supervisor;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\TaskTimeline;
use Illuminate\Http\Request;
use App\Models\TraineeAssign;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SupervisorController extends Controller
{
    public function index()
    {
        $supervisors = Supervisor::all();
        return view('user-management', compact('supervisors'));
    }

    public function showProfileSV()
    {
        $user = Auth::user();
        $supervisor = Supervisor::where('sains_email', $user->email)->first();
        return view('sv-profile', compact('supervisor'));
    }

    public function showAllTraineeProfileForSV() {
        $user = Auth::user();

        //Get the supervisor ID using the email.
        $supervisorID = Supervisor::where('sains_email', $user->email)->pluck('id')->first();

        //search for his or her trainees
        $traineeIDs = TraineeAssign::where('assigned_supervisor_id', $supervisorID)
            ->pluck('trainee_id')
            ->toArray();

        $traineeBasicDatas[] = null;

        //This code retrieves all trainees associated with a given supervisor.
        foreach ($traineeIDs as $traineeID) {
            //get the trainee name
            $traineeName = AllTrainee::where('id', $traineeID)->pluck('name')->first();

            //search the trainee using the name to check whether the trainee is registered or not
            $trainee = Trainee::where('name', $traineeName)->first();
            if($trainee != null){
                $traineeBasicDatas[] = $trainee;
            }
            else{
                // Trainee not found, create a new object with default values
                $defaultTrainee = new Trainee([
                    'name' => $traineeName,
                    'expertise' => 'Not Specified',
                    'phone_number' => '', 
                    'personal_email' => '', 
                    'sains_email' => '', 
                ]);
                $traineeBasicDatas[] = $defaultTrainee;
            }
        }

        return view('sv-trainee-assign', compact('traineeBasicDatas'));

    }

    public function placeholderProfileSV(){
        // Get the currently logged-in user
        $user = Auth::user();
            
        // Check if the user is a supervisor
        if ($user->role_id == 2) {
            $supervisor = Supervisor::where('sains_email', $user->email)->first();

            if (!$supervisor) {
                // Handle the case where the trainee is not found, e.g., show an error message.
                return redirect()->back()->with('error', 'Supervisor not found');
            }

            return view('sv-edit-profile', compact('supervisor'));
        } else {
            // Handle the case where the user is not a trainee (e.g., supervisor or other role)
            return redirect()->back()->with('error', 'User is not a Supervisor');
        }
    }

    public function updateProfileSV(Request $request){
        $validatedData = $request->validate([
            'phoneNum' => ['required', 'string', 'regex:/^(\+?6?01)[02-46-9][0-9]{7}$|^(\+?6?01)[1][0-9]{8}$/'],
        ]);
        // Get the currently logged-in user
        $user = Auth::user();

        Supervisor::where('sains_email', $user->email)
        ->update([
            'phone_number' => $request->input('phoneNum'),
        ]);

        $activityLog = new ActivityLog([
            'username' => $user->name,
            'action' => 'Edit Profile',
            'outcome' => 'success',
            'details' => 'Update phone number to ' . $request->input('phoneNum'),
        ]);

        $activityLog->save();
    
        return redirect()->route('sv-profile');
    }

    public function goToTraineeProfile($traineeName){

        $name = urldecode($traineeName);
        $trainee = Trainee::where('name', $name)->first();

        if($trainee == null){
            return redirect()->back()
            ->with('error', 'Trainee does not exist.');
        }

        $internship_dates = AllTrainee::where('name', 'LIKE', $name)
        ->select('internship_start', 'internship_end')
        ->first();

        $trainee_id = $trainee->id;
        $logbooks = Logbook::where('trainee_id', $trainee_id)->get();

        $supervisor_id = Supervisor::where('sains_email', Auth::user()->email)
            ->pluck('id')
            ->first();

        //reference id for trainee
        $trainee_ref_id = AllTrainee::where('name', $name)->pluck('id')->first();
        
        //check whether this trainee is under this supervisor.
        if (TraineeAssign::where('trainee_id', $trainee_ref_id)->where('assigned_supervisor_id', $supervisor_id)->first() == null) {
            return redirect()->back()->with('error', 'You do not have access to view this page.');
        }

        $comment = Comment::where('supervisor_id', $supervisor_id)
            ->where('trainee_id', $trainee_id)
            ->pluck('comment')
            ->first();

        return view('sv-view-trainee-profile', compact('trainee','internship_dates', 'comment', 'logbooks'));
    } 

    public function goToTraineeTaskTimeline($traineeName){
        //get the trainee id
        $traineeRef = AllTrainee::where('name' , $traineeName)->pluck('id')->first();
        $traineeID = Trainee::where('name' , $traineeName)->pluck('id')->first();

        //prevent other supervisor to access the task for the trainee that is not assigned to them.
        $supervisorID = Supervisor::where('sains_email', Auth::user()->email)->pluck('id')->first();
        if(TraineeAssign::where('trainee_id', $traineeRef)->where('assigned_supervisor_id', $supervisorID)->first() == null){
            return redirect()->back()->with('error', 'You do not have access to view this page.');
        }

        //get all the task for this trainee
        $tasks = TaskTimeline::where('trainee_id', $traineeID)->get();

        return view('sv-view-trainee-task-timeline', compact('tasks', 'traineeID'));
    }

    public function svViewTraineeLogbook($traineeName)
    {
        $name = urldecode($traineeName);

        //reference id for trainee
        $trainee_ref_id = AllTrainee::where('name', $name)->pluck('id')->first();

        //reference id for supervisor
        $sv_ref_id = Supervisor::where('sains_email', Auth::user()->email)->pluck('id')->first();

        //check whether this trainee is under this supervisor.
        if (TraineeAssign::where('trainee_id', $trainee_ref_id)->where('assigned_supervisor_id', $sv_ref_id)->first() == null) {
            return redirect()->back()->with('error', 'You do not have access to view this page.');
        }

        $trainee_id = Trainee::where('name','LIKE', $name)->pluck('id')->first();



        $logbooks = Logbook::where('trainee_id', $trainee_id)->get();
        return view('view-and-upload-logbook-sv', compact('logbooks','name'));
    }

    public function svUploadLogbook(Request $request, $name)
    {
        $user = Auth::user();
        $supervisor_name = Supervisor::where('sains_email', $user->email)->pluck('name')->first();

        //get the trainee id
        $trainee_id = Trainee::where('name', 'LIKE', $name)->pluck('id')->first();

        //validate the uploaded logbook
        $validator = Validator::make($request->all(), [
            'logbook' => 'required|mimes:pdf,doc,docx|max:2048',
        ],[
            'logbook.max' => 'The logbook must not exceed 2MB in size.',
            'logbook.mimes' => 'Accepted logbook types are .pdf, .doc and .docx only.',
        ]);
        
        if ($validator->fails()) {
            // Extract error messages
            $errorMessages = implode(' ', $validator->errors()->all());

            $activityLog = new ActivityLog([
                'username' => $supervisor_name,
                'action' => 'Logbook Upload',
                'outcome' => 'failed',
                'details' => $errorMessages,
            ]);
    
            $activityLog->save();
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // The unsigned logbook (uploaded by trainee) cannot be more than 4.
        $logbookCount = Logbook::where('trainee_id', $trainee_id)
        ->where('status', 'Signed')
        ->count();

        if ($logbookCount >= 4) {
            $activityLog = new ActivityLog([
                'username' => $supervisor_name,
                'action' => 'Logbook Upload',
                'outcome' => 'failed',
                'details' => 'Trying to upload more than 4 logbooks.',
            ]);
    
            $activityLog->save();
            return redirect()->back()->with('error', 'You can only upload a maximum of 4 logbooks.');
        }

        // Get the uploaded file
        $file = $request->file('logbook');

        // Get the random filename
        $randomFileName = Str::random(32);

        // Get the original extension of the file
        $extension = $file->getClientOriginalExtension();

        // Concatenate the random filename and the original extension
        $newFileName = $randomFileName . '.' . $extension;

        $logbook_path = 'storage/logbooks/' . $newFileName;

        if(Logbook::where('logbook_path', $logbook_path)->exists()){
            // If the user upload a pdf with same name
            return redirect()->route('view-and-upload-logbook-sv', $name)->with('error', 'Cannot upload a file with an already existing name.');
        }
        else{
            // Save the file path in the database for the user
            Logbook::create([
                'trainee_id' => $trainee_id,
                'logbook_path' => 'storage/logbooks/' . $newFileName,
                'status' => 'Signed',
            ]);

            // Store the file in the "public" disk
            $file->storeAs('public/logbooks/', $newFileName);
        }
        
        //send the notification about the signed logbook to the trainee.
        $notification = new Notification();
        $notification->id = Uuid::uuid4(); // Generate a UUID for the id
        $notification->type = 'signed_logbook';
        $notification->notifiable_type = 'App\Models\Supervisor';
        $notification->notifiable_id = $trainee_id;
        $notification->data = json_encode([
            'data' => 'Your supervisor ' . $supervisor_name . ' has signed your logbook.',
            'name' => $name,
        ]);
        $notification->save(); // Save the notification to the database

        $activityLog = new ActivityLog([
            'username' => $supervisor_name,
            'action' => 'Logbook Upload',
            'outcome' => 'success',
            'details' => '',
        ]);

        $activityLog->save();

        // Redirect the user to a success page
        return redirect()->route('view-and-upload-logbook-sv', $name)->with('success', 'Logbook uploaded successfully');
    }

    public function destroy(Logbook $logbook, $name)
    {
        // Delete the logbook file from storage
        $logbookPath = storage_path('app/public/logbooks/') . basename($logbook->logbook_path);
        if (file_exists($logbookPath)) {
            unlink($logbookPath);
        }

        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Logbook Deletion',
            'outcome' => 'success',
            'details' => '',
        ]);

        $activityLog->save();

        // Delete the logbook record from the database
        $logbook->delete();

        return redirect()->route('view-and-upload-logbook-sv', $name)->with('success', 'Logbook deleted successfully');
    }

    public function svViewTraineeResume($traineeName){
        $name = urldecode($traineeName);
        $trainee = Trainee::where('name', $name)->first();
        return view('sv-view-trainee-resume', compact('trainee'));
    }

    public function svCommentPage($traineeName){
        $name = urldecode($traineeName);
        $trainee = Trainee::where('name', $name)->first();

        if($trainee == null){
            return redirect()->back()->with('error', 'Trainee not found');
        }

        $user = Auth::user();
        $supervisor_id = Supervisor::where('sains_email', $user->email)->pluck('id')->first();

        //reference id for trainee
        $trainee_ref_id = AllTrainee::where('name', $name)->pluck('id')->first();

        //check whether this trainee is under this supervisor.
        if (TraineeAssign::where('trainee_id', $trainee_ref_id)->where('assigned_supervisor_id', $supervisor_id)->first() == null) {
            return redirect()->back()->with('error', 'You do not have access to view this page.');
        }

        $trainee_id = $trainee->id;
        $comment = Comment::where('supervisor_id', $supervisor_id)
            ->where('trainee_id', $trainee_id)
            ->pluck('comment')
            ->first();

        return view('sv-comment', compact('trainee', 'comment'));
    }

    public function svSubmitComment(Request $request){
        $validatedData = $request->validate([
            'trainee_id' => 'required',
            'comment' => 'required',
        ]);

        $user = Auth::user();
        $supervisor_id = Supervisor::where('sains_email', $user->email)->pluck('id')->first();
        $trainee_id = $request->trainee_id;
        $comment = $request->comment;

        $target = Comment::where('trainee_id', $trainee_id)
        ->where('supervisor_id', $supervisor_id)
        ->first();

        //to check whether this supervisor already give this trainee comment or not.
        if($target == null){
            Comment::create([
                'comment' => $comment,
                'trainee_id' => $trainee_id,
                'supervisor_id' => $supervisor_id,
            ]);
        }
        else{
            $target->comment = $comment;
            $target->save();
        }

        $traineeName = Trainee::where('id', $trainee_id)->first()->name;

        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Personal Comment',
            'outcome' => 'success',
            'details' => 'Comment: '. $comment,
        ]);

        $activityLog->save();

        return redirect()->route('go-profile', $traineeName)->with('success', 'Comment submitted successfully');
    }

    public function viewSeatPlan()
    {
        //get the current year and month.
        $year = date('Y');
        $month = date('m');

        // use current year and month to retrieve the seating plan related to this month.
        $weeksInMonth = Seating::where(function($query) use ($year, $month) {
            $query->whereYear(DB::raw("STR_TO_DATE(start_date, '%d/%m/%Y')"), $year)
                ->whereMonth(DB::raw("STR_TO_DATE(start_date, '%d/%m/%Y')"), $month);
        })
        ->orWhere(function($query) use ($year, $month) {
            $query->whereYear(DB::raw("STR_TO_DATE(end_date, '%d/%m/%Y')"), $year)
                ->whereMonth(DB::raw("STR_TO_DATE(end_date, '%d/%m/%Y')"), $month);
        })
        ->select('week')
        ->distinct()
        ->orderBy('week', 'asc')
        ->get()
        ->pluck('week')
        ->toArray();

        $seatingArray = []; // Initialize the main array to hold weeks

        foreach ($weeksInMonth as $week) {
            $dateTime = new DateTime($week);
            $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 1);
            $startDate = $dateTime->format('d/m/Y');  // Start of the week
            $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 7);
            $endDate = $dateTime->format('d/m/Y');  // End of the week 

            // Fetch the seat detail from DB
            $seatingDetailForAWeek = Seating::where('week', $week)->first();

            $seatDetail = json_decode($seatingDetailForAWeek->seat_detail, true);


            //replace the trainee id with trainee name
            foreach ($seatDetail as &$seatInfo) {
                $trainee_name = AllTrainee::where('id',$seatInfo['trainee_id'])->pluck('name')->first();
                $seatInfo['trainee_id'] = $trainee_name ?? 'Not Assigned';
            }

            $seatingData[$week] = [
                'seating_plan' => $seatDetail,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
            

        }
        return view('sv-view-seating', compact('weeksInMonth', 'seatingData'));
    }

    public function svUpdatePassword(Request $request){
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*()_+])[a-zA-Z0-9!@#$%^&*()_+]+$/',
            ],
            'confirm_password' => 'required|string|same:new_password',
        ], [
            'new_password.min' => 'The password must have at least 8 characters.',
            'new_password.regex' => 'The format of the password is incorrect.',
            'confirm_password.same' => 'The confirm password does not match the new password.',
        ]);
        
        if ($validator->fails()) {
            // Extract error messages
           $errorMessages = implode(' ', $validator->errors()->all());

           $activityLog = new ActivityLog([
               'username' => $user->name,
               'action' => 'Change Password',
               'outcome' => 'failed',
               'details' => $errorMessages,
           ]);
   
           $activityLog->save();

           return redirect()->back()
               ->withErrors($validator)
               ->withInput();
       }

        $current_password = $request->input('current_password');
        $new_password = $request->input('new_password');
        $confirm_password = $request->input('confirm_password');

        // check the password inputed is same as the original password or not
        if (Hash::check($current_password, $user->password)) {
            //check the new password is same as the current password or not
            if(Hash::check($new_password, $user->password)){
                $activityLog = new ActivityLog([
                    'username' => $user->name,
                    'action' => 'Change Password',
                    'outcome' => 'failed',
                    'details' => 'Try to set the new password same as previous password',
                ]);
        
                $activityLog->save();
                return redirect()->back()->with('error', 'Cannot set the same password as new password.');
            }
            else{
                $user->password = $new_password;
                $user->save();
            }
        }
        else{
            $activityLog = new ActivityLog([
                'username' => $user->name,
                'action' => 'Change Password',
                'outcome' => 'failed',
                'details' => 'Wrong current password entered',
            ]);
    
            $activityLog->save();
            return redirect()->back()->with('error', 'Wrong current password.');
        }
        $activityLog = new ActivityLog([
            'username' => $user->name,
            'action' => 'Change Password',
            'outcome' => 'success',
            'details' => '',
        ]);

        $activityLog->save();
        return redirect()->back()->with('success', 'Password successfully changed!');
    }

}
