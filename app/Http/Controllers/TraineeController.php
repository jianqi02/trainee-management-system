<?php

namespace App\Http\Controllers;

use DateTime;
use Ramsey\Uuid\Uuid;
use App\Models\Logbook;
use App\Models\Seating;
use App\Models\Trainee;
use App\Models\Settings;
use App\Models\Expertise;
use Illuminate\View\View;
use App\Models\AllTrainee;
use App\Models\Supervisor;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\TaskTimeline;
use Illuminate\Http\Request;
use App\Models\TraineeAssign;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Notifications\TelegramNotification;

class TraineeController extends Controller
{

    public function index()
    {
        $trainees = Trainee::all();
        return view('user-management', compact('trainees'));
    }

    public function showProfile() {
        // Get the currently logged-in user
        $user = Auth::user();
    
        // Check if the user is a trainee
        if ($user->role_id == 3) {
            $trainee = Trainee::where('email', $user->email)->first();
            $internship_dates = AllTrainee::where('name', 'LIKE', $user->name)
                ->select('internship_start', 'internship_end')
                ->first();
            $trainee_id = $trainee->id;
            $logbooks = Logbook::where('trainee_id', $trainee_id)->get();
    
            if (!$trainee) {
                // Handle the case where the trainee is not found, e.g., show an error message.
                return redirect()->back()->with('error', 'Trainee not found');
            }
    
            return view('trainee-profile', compact('trainee', 'internship_dates', 'logbooks'));
        } else {
            // Handle the case where the user is not a trainee (e.g., supervisor or other role)
            return redirect()->back()->with('error', 'User is not a trainee');
        }
    }

    public function placeholderProfile(){
        // Get the currently logged-in user
        $user = Auth::user();
        $expertises = Expertise::pluck('expertise');
            
        // Check if the user is a trainee
        if ($user->role_id == 3) {
            $trainee = Trainee::where('email', $user->email)->first();

            if (!$trainee) {
                // Handle the case where the trainee is not found, e.g., show an error message.
                return redirect()->back()->with('error', 'Trainee not found');
            }

            return view('trainee-edit-profile', compact('trainee', 'expertises'));
        } else {
            // Handle the case where the user is not a trainee (e.g., supervisor or other role)
            return redirect()->back()->with('error', 'User is not a trainee');
        }
    }

    public function updateProfile(Request $request){
        $validatedData = $request->validate([
            'phoneNum' => ['required', 'string', 'regex:/^(\+?6?01)[02-46-9][0-9]{7}$|^(\+?6?01)[1][0-9]{8}$/'],
            'expertise' => 'nullable|string',
            'personalEmail' => [
                'nullable',
                'string',
                'email',
                'max:255',
                'regex:/^(?=.{1,64}@)[A-Za-z0-9_]+(\.[A-Za-z0-9_]+)*@[^-][A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*(\.[A-Za-z]{2,})$/',
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
            'graduateDate' => 'nullable|date',
            'profilePicture' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Get the currently logged-in user
        $user = Auth::user();

        $trainee = Trainee::where('email', $user->email)->first();

        if ($trainee) {
            $trainee->phone_number = $request->input('phoneNum');
            $trainee->expertise = $request->input('expertise');
            $trainee->personal_email = $request->input('personalEmail');
            $trainee->graduate_date = $request->input('graduateDate');
    
            if ($request->hasFile('profilePicture')) {
                // Delete the old profile image
                if ($trainee->profile_image) {
                    Storage::delete($trainee->profile_image);
                }
            
                // Store the new profile image
                $imagePath = $request->file('profilePicture')->store('public/profile_pictures');
                $trainee->profile_image = $imagePath;
            }    
    
            $trainee->save();

            $activityLog = new ActivityLog([
                'username' => $trainee->name,
                'action' => 'Edit Profile',
                'outcome' => 'success',
                'details' => '',
            ]);
    
            $activityLog->save();
        }
    
        return redirect()->route('trainee-profile')->with('success', 'Profile updated successfully');
    }

    public function uploadResume(Request $request)
    {
        $user = Auth::user();
        //validate the uploaded resume
        $validator = Validator::make($request->all(), [
            'resume' => 'required|mimes:pdf|max:2048',
        ],[
            'resume.max' => 'The resume must not exceed 2MB in size.',
            'resume.mimes' => 'Accepted resume types are .pdf only.',
        ]);
        
        if ($validator->fails()) {
            // Extract error messages
            $errorMessages = implode(' ', $validator->errors()->all());

            $activityLog = new ActivityLog([
                'username' => $user->name,
                'action' => 'Resume Upload',
                'outcome' => 'failed',
                'details' => $errorMessages,
            ]);
    
            $activityLog->save();
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $resumeExistCheck = Trainee::where('email', $user->email)->first();

        if ($resumeExistCheck->resume_path != null) {
            $activityLog = new ActivityLog([
                'username' => $user->name,
                'action' => 'Resume Upload',
                'outcome' => 'failed',
                'details' => 'Trying to upload more than one resume',
            ]);
    
            $activityLog->save();
            return redirect()->route('trainee-upload-resume')->with('warning', 'You can only upload one resume!');
        }

        // Get the uploaded file
        $file = $request->file('resume');

        // Get the random filename
        $randomFileName = Str::random(32);

        // Get the original extension of the file
        $extension = $file->getClientOriginalExtension();

        // Concatenate the random filename and the original extension
        $newFileName = $randomFileName . '.' . $extension;

        // Store the file in the "public" disk (you may configure other disks as needed)
        $file->storeAs('public/resumes/', $newFileName);

        // Save the file path in the database for the user
        $trainee = Trainee::where('name', $user->name)->first();
        $trainee->resume_path = 'storage/resumes/' . $newFileName;
        $trainee->save();

        $activityLog = new ActivityLog([
            'username' => $trainee->name,
            'action' => 'Resume Upload',
            'outcome' => 'success',
            'details' => '',
        ]);

        $activityLog->save();

        // Redirect the user to a success page
        return redirect()->route('trainee-upload-resume')->with('success', 'Resume uploaded successfully');
    }

    public function traineeResume()
    {
        $user = Auth::user();
        $trainee = Trainee::where('email', $user->email)->first();
        return view('trainee-upload-resume', compact('trainee'));
    }

    public function uploadLogbook(Request $request){
        $user = Auth::user();
        $trainee = Trainee::where('email', $user->email)->pluck('name')->first();
        $id = Trainee::where('email', $user->email)->pluck('id')->first();

        // Validate the uploaded logbook
        $validator = Validator::make($request->all(), [
            'logbook' => 'required|mimes:pdf,doc,docx|max:2048',
            'logbook_name' => 'required|string|max:255',
        ], [
            'logbook.max' => 'The logbook must not exceed 2MB in size.',
            'logbook.mimes' => 'Accepted logbook types are .pdf, .doc and .docx only.',
            'logbook_name.required' => 'Please provide a name for the logbook.',
            'logbook_name.max' => 'The logbook name must not exceed 255 characters.',
        ]);

        if ($validator->fails()) {
            // Log failed validation attempt
            $errorMessages = implode(' ', $validator->errors()->all());

            $activityLog = new ActivityLog([
                'username' => $trainee,
                'action' => 'Logbook Upload',
                'outcome' => 'failed',
                'details' => $errorMessages,
            ]);

            $activityLog->save();

            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if the trainee has reached the maximum number of unsigned logbooks
        $logbookCount = Logbook::where('trainee_id', $id)
            ->where('status', 'Unsigned')
            ->count();

        if ($logbookCount >= 4) {
            $activityLog = new ActivityLog([
                'username' => $trainee,
                'action' => 'Logbook Upload',
                'outcome' => 'failed',
                'details' => 'Trying to upload more than 4 logbooks',
            ]);

            $activityLog->save();
            return redirect()->back()->with('error', 'You can only upload a maximum of 4 logbooks.');
        }

        // Process file upload
        $file = $request->file('logbook');
        $randomFileName = Str::random(32);
        $extension = $file->getClientOriginalExtension();
        $newFileName = $randomFileName . '.' . $extension;
        $logbook_path = 'storage/logbooks/' . $newFileName;

        // Check if a file with the same path already exists
        if (Logbook::where('logbook_path', $logbook_path)->exists()) {
            return redirect()->back()->with('error', 'Cannot upload a file with an already existing name.');
        } else {
            // Save the file path and details in the database
            Logbook::create([
                'trainee_id' => $id,
                'logbook_path' => 'storage/logbooks/' . $newFileName,
                'status' => 'Unsigned',
                'name' => $request->input('logbook_name'),
            ]);

            // Store the file in the "public" disk
            $file->storeAs('public/logbooks/', $newFileName);
        }

        // Find the trainee's supervisors and send notifications
        $id_in_list = AllTrainee::where('name', 'LIKE', $trainee)->pluck('id');
        $assigned_supervisor_ids = TraineeAssign::whereIn('trainee_id', $id_in_list)
            ->pluck('assigned_supervisor_id');

        foreach ($assigned_supervisor_ids as $assigned_supervisor_id) {
            $notification = new Notification();
            $notification->id = Uuid::uuid4(); // Generate a UUID for the id
            $notification->type = 'logbook';
            $notification->notifiable_type = 'App\Models\User';
            $notification->notifiable_id = $assigned_supervisor_id;
            $notification->data = json_encode([
                'data' => 'Your trainee ' . $trainee . ' has uploaded a logbook.',
            ]);
            $notification->save(); // Save the notification to the database

            $supervisor_name = Supervisor::where('id', $assigned_supervisor_id)->pluck('name')->first();

            // Log successful upload
            $activityLog = new ActivityLog([
                'username' => $trainee,
                'action' => 'Logbook Upload',
                'outcome' => 'success',
                'details' => '',
            ]);

            $activityLog->save();

            // Create and send the Telegram notification to the private channel
            $telegramNotification = new TelegramNotification(
                "Logbook Upload Successful",
                $supervisor_name,
                $trainee,
                "Your trainee has uploaded the logbook for supervisor: $supervisor_name."
            );
            $telegramNotification->toTelegram(null);
        }

        // Redirect back to the previous page with a success message
        return redirect()->back()->with('success', 'Logbook uploaded successfully');
    }

    public function generateLogbook(Request $request)
    {
        // Retrieve startMonth and endMonth from the query parameters
        $startMonth = $request->query('startMonth');
        $endMonth = $request->query('endMonth');

        // Check if both startMonth and endMonth are provided; if not, redirect back with an error message
        if (!$startMonth || !$endMonth) {
            return redirect()->back()->withErrors(['startMonth' => 'Please select both start and end periods.']);
        }

        // Convert startMonth and endMonth to Carbon instances
        $start = \Carbon\Carbon::parse($startMonth)->startOfMonth();
        $end = \Carbon\Carbon::parse($endMonth)->endOfMonth();

        // Validate that the end date is not earlier than the start date
        if ($end->lt($start)) {
            return redirect()->back()->withErrors(['endMonth' => 'The end date must be the same or later than the start date.'])->withInput();
        }

        // Retrieve the authenticated user
        $user = Auth::user();

        // Find the trainee associated with this user
        $trainee = Trainee::where('email', $user->email)->first();
        if (!$trainee) {
            return redirect()->route('trainee-upload-logbook')->with('error', 'Trainee not found');
        }

        // Fetch tasks for this trainee within the specified month/year range
        $tasks = TaskTimeline::where('trainee_id', $trainee->id)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('task_start_date', [$start, $end])
                    ->orWhereBetween('task_end_date', [$start, $end])
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->where('task_start_date', '<=', $start)
                            ->where('task_end_date', '>=', $end);
                    });
            })
            ->get();

        // Decode JSON data for each task
        foreach ($tasks as $task) {
            $task->timeline_data = json_decode($task->timeline, true);
            $task->task_detail_data = json_decode($task->task_detail, true);
            $task->task_overall_comment_data = json_decode($task->task_overall_comment, true);
        }

        // Generate the current date to display as the generated date
        $dateGenerated = now()->format('j F Y');

        // Determine if the start and end periods are the same
        $isSingleMonth = $start->equalTo($end->copy()->startOfMonth());

        $fileName = $isSingleMonth
            ? "{$trainee->name}_task_report_{$start->format('Y_m')}.pdf"
            : "{$trainee->name}_task_report_{$start->format('Y_m')}_to_{$end->format('Y_m')}.pdf";

        // Retrieve the AllTrainee record based on the trainee's name
        $allTrainees = AllTrainee::where('name', $trainee->name)->first();
        if (!$allTrainees) {
            return redirect()->route('trainee-upload-logbook')->with('error', 'Trainee not assigned to supervisor.');
        }

        // Fetch associated supervisors
        $supervisors = $allTrainees->supervisors;

        // Fetch company name
        $company_name = Settings::pluck('company_name')->first();

        // Load the view file and pass tasks, trainee, supervisors, dateGenerated, startMonth, and endMonth data
        $pdf = PDF::loadView('logbook-summary', compact('tasks', 'trainee', 'supervisors', 'dateGenerated', 'startMonth', 'endMonth', 'isSingleMonth', 'company_name'));

        // Optionally, set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Return the PDF to be displayed in the browser
        return $pdf->stream($fileName);
    }


    public function traineeLogbook()
    {
        $user = Auth::user();
        $id = Trainee::where('email', $user->email)->pluck('id')->first();
        $logbooks = Logbook::where('trainee_id', $id)->get();
        return view('trainee-upload-logbook', compact('logbooks'));
    }

    public function destroyResume(Trainee $trainee)
    {
        // Delete the resume file from storage
        $resumePath = storage_path('app/public/resumes/') . basename($trainee->resume_path);
        if (file_exists($resumePath)) {
            unlink($resumePath);
        }

        $activityLog = new ActivityLog([
            'username' => $trainee->name,
            'action' => 'Resume Deletion',
            'outcome' => 'success',
            'details' => '',
        ]);

        $activityLog->save();

        // Replace the resume path in the database with null
        $trainee->resume_path = null;
        $trainee->save();

        return redirect()->route('trainee-upload-resume')->with('success', 'Resume deleted successfully');
    }

    public function destroy(Logbook $logbook)
    {
        $name = Auth::user()->name;
        // Delete the logbook file from storage
        $logbookPath = storage_path('app/public/logbooks/') . basename($logbook->logbook_path);
        if (file_exists($logbookPath)) {
            unlink($logbookPath);
        }

        $activityLog = new ActivityLog([
            'username' => $name,
            'action' => 'Logbook Deletion',
            'outcome' => 'success',
            'details' => '',
        ]);

        $activityLog->save();

        // Delete the logbook record from the database
        $logbook->delete();

        return redirect()->route('trainee-upload-logbook')->with('success', 'Logbook deleted successfully');
    }
    public function viewSeatPlan()
    {
        $user = Auth::user();
        $name = Trainee::where('email', $user->email)->pluck('name')->first();

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
        return view('trainee-view-seating', compact('weeksInMonth', 'seatingData', 'name'));
    }
    public function traineeUpdatePassword(Request $request){
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
    public function mySupervisorPage(){
        //get the current login trainee information
        $user = Auth::user();

        //use the id from the trainee list to find all this trainee's supervisor
        $traineeID = AllTrainee::where('name', $user->name)->pluck('id')->first();

        if($traineeID){
            $supervisorIDs = TraineeAssign::where('trainee_id', $traineeID)->pluck('assigned_supervisor_id')->toArray();
            $supervisorBasicDatas = Supervisor::whereIn('id', $supervisorIDs)->get();
            return view('trainee-view-supervisor', compact('supervisorBasicDatas'));
        }else{
            return redirect()->back()->with('error', 'You do not have any supervisor yet.');
        }
    }
}






