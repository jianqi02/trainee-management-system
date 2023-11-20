<?php

namespace App\Http\Controllers;

use DateTime;
use Ramsey\Uuid\Uuid;
use App\Models\Logbook;
use App\Models\Seating;
use App\Models\Trainee;
use Illuminate\View\View;
use App\Models\AllTrainee;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\TraineeAssign;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TraineeController;

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
            // Assuming that 'sains_email' is the email field in your 'trainees' table
            $trainee = Trainee::where('sains_email', $user->email)->first();
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
            
        // Check if the user is a trainee
        if ($user->role_id == 3) {
            // Assuming that 'sains_email' is the email field in your 'trainees' table
            $trainee = Trainee::where('sains_email', $user->email)->first();

            if (!$trainee) {
                // Handle the case where the trainee is not found, e.g., show an error message.
                return redirect()->back()->with('error', 'Trainee not found');
            }

            return view('trainee-edit-profile', compact('trainee'));
        } else {
            // Handle the case where the user is not a trainee (e.g., supervisor or other role)
            return redirect()->back()->with('error', 'User is not a trainee');
        }
    }

    public function updateProfile(Request $request){
        $validatedData = $request->validate([
            'fullName' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'phoneNum' => ['required', 'string', 'max:255', 'regex:/^[0-9\+]+$/'],
            'expertise' => 'nullable|string',
            'personalEmail' => ['required', 'email', 'regex:/^(?=.{1,64}@)[A-Za-z0-9_-]+(\\.[A-Za-z0-9_-]+)*@[^-][A-Za-z0-9-]+(\\.[A-Za-z0-9-]+)*(\\.[A-Za-z]{2,})$/'],
            'graduateDate' => 'nullable|date',
            'profilePicture' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);
        // Get the currently logged-in user
        $user = Auth::user();
    
        $user->name = $request->input('fullName');
        $user->save();

        $trainee = Trainee::where('sains_email', $user->email)->first();

        if ($trainee) {
            $trainee->name = $request->input('fullName');
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
        }
    
        return redirect()->route('trainee-profile')->with('success', 'Profile updated successfully');
    }

        public function uploadResume(Request $request)
    {
        $user = Auth::user();
        // Validate the uploaded file
        $request->validate([
            'resume' => 'required|mimes:pdf|max:2048',
        ]);

        $resumeExistCheck = Trainee::where('sains_email', $user->email)->first();

        if ($resumeExistCheck->resume_path != null) {
            return redirect()->route('trainee-upload-resume')->with('warning', 'You can only upload one resume!');
        }

        // Get the uploaded file
        $file = $request->file('resume');

        // Get the original filename
        $originalFileName = $file->getClientOriginalName();

        // Store the file in the "public" disk (you may configure other disks as needed)
        $file->storeAs('public/resumes', $originalFileName);

        // Save the file path in the database for the user
        $trainee = Trainee::where('name', $user->name)->first();
        $trainee->resume_path = 'storage/resumes/' . $originalFileName;
        $trainee->save();

        // Redirect the user to a success page
        return redirect()->route('trainee-upload-resume')->with('success', 'Resume uploaded successfully');
    }

    public function traineeResume()
    {
        $user = Auth::user();
        $trainee = Trainee::where('sains_email', $user->email)->first();
        return view('trainee-upload-resume', compact('trainee'));
    }

    public function uploadLogbook(Request $request)
    {
        $user = Auth::user();
        $trainee = Trainee::where('sains_email', $user->email)->pluck('name')->first();
        $id = Trainee::where('sains_email', $user->email)->pluck('id')->first();

        // Validate the uploaded file
        $request->validate([
            'logbook' => 'required|mimes:pdf,doc,docx|max:2048',
        ]);

        // The unsigned logbook (uploaded by trainee) cannot be more than 4.
        $logbookCount = Logbook::where('trainee_id', $id)
            ->where('status', 'Unsigned')
            ->count();

        if ($logbookCount >= 4) {
            return redirect()->route('trainee-upload-logbook')->with('error', 'You can only upload a maximum of 4 logbooks.');
        }

        // Get the uploaded file
        $file = $request->file('logbook');

        // Get the original filename
        $originalFileName = $file->getClientOriginalName();

        // Store the file in the "public" disk
        $file->storeAs('public/logbooks', $originalFileName);

        // Save the file path in the database for the user
        Logbook::create([
            'trainee_id' => $id,
            'logbook_path' => 'storage/logbooks/' . $originalFileName,
            'status' => 'Unsigned',
        ]);

        //get the id of the trainee in the list (all trainee list)
        $id_in_list = AllTrainee::where('name', 'LIKE', $trainee)->pluck('id');

        //use the id in list to search for the trainee's supervisor
        $assigned_supervisor_ids = TraineeAssign::whereIn('trainee_id', $id_in_list)
            ->pluck('assigned_supervisor_id');
        
        //send the notification about the updated logbook to the trainee's supervisor(s).
        foreach($assigned_supervisor_ids as $assigned_supervisor_id){
            $notification = new Notification();
            $notification->id = Uuid::uuid4(); // Generate a UUID for the id
            $notification->type = 'logbook';
            $notification->notifiable_type = 'App\Models\User';
            $notification->notifiable_id = $assigned_supervisor_id;
            $notification->data = json_encode([
                'data' => 'Your trainee ' . $trainee . ' has uploaded a logbook.',
            ]);
            $notification->save(); // Save the notification to the database
        }

        // Redirect the user to a success page
        return redirect()->route('trainee-upload-logbook')->with('success', 'Logbook uploaded successfully');
    }

    public function traineeLogbook()
    {
        $user = Auth::user();
        $id = Trainee::where('sains_email', $user->email)->pluck('id')->first();
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

        // Replace the resume path in the database with null
        $trainee->resume_path = null;
        $trainee->save();

        return redirect()->route('trainee-upload-resume')->with('success', 'Resume deleted successfully');
    }

    public function destroy(Logbook $logbook)
    {
        // Delete the logbook file from storage
        $logbookPath = storage_path('app/public/logbooks/') . basename($logbook->logbook_path);
        if (file_exists($logbookPath)) {
            unlink($logbookPath);
        }

        // Delete the logbook record from the database
        $logbook->delete();

        return redirect()->route('trainee-upload-logbook')->with('success', 'Logbook deleted successfully');
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
        return view('trainee-view-seating', compact('weeksInMonth', 'seatingData'));
    }
}






