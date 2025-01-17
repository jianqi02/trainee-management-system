<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Comment;
use App\Models\Logbook;
use App\Models\Seating;
use App\Models\Section;
use App\Models\Trainee;
use App\Models\Settings;
use App\Models\Expertise;
use App\Models\AllTrainee;
use App\Models\Department;
use App\Models\Supervisor;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\TaskTimeline;
use Illuminate\Http\Request;
use App\Models\TraineeAssign;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class AdminController extends Controller
{
    use RegistersUsers;
    
    public function index()
    {
        $trainees = Trainee::all();
        $supervisors = Supervisor::all();
        return view('user-management', compact('trainees','supervisors'));
    }

    public function traineeAssign()
    {
        $trainees = AllTrainee::all();
        $assignedSupervisorList = TraineeAssign::all();
        return view('admin-trainee-assign', compact('trainees','assignedSupervisorList'));
    }

    public function showDashboard(Request $request)
    {
        // Get the week from the request, or use the current week if not provided
        $weekRequired = $request->input('week', date('o-\WW'));

        // Get the start and end dates of the week
        $dateTime = new DateTime($weekRequired);
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 7);
        $end_date = $dateTime->format('d/m/Y');  // End of the week for display at the top
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 1);
        $start_date = $dateTime->format('d/m/Y');  // Start of the week for display at the top
        $month = $dateTime->format('m'); // Month in two digits, e.g., 01 for January
        $year = $dateTime->format('Y'); // Year in four digits, e.g., 2023

        $trainees = Trainee::join('alltrainees', function ($join) {
            $join->on('trainees.name', '=', 'alltrainees.name')
                ->whereRaw('LOWER(trainees.name) LIKE LOWER(alltrainees.name)');
        })
        ->select('trainees.*', 'alltrainees.internship_start', 'alltrainees.internship_end')
        ->get();
    

        $traineeInfo = AllTrainee::all();
        $seatings = Seating::all();
        $logbooks = Logbook::all();
        $count = Trainee::where('acc_status', 'Active')->count();
        /*
        $weeksInMonth = Seating::whereYear(DB::raw("STR_TO_DATE(start_date, '%d/%m/%Y')"), $year)
            ->whereMonth(DB::raw("STR_TO_DATE(start_date, '%d/%m/%Y')"), $month)
            ->select('week')
            ->distinct()
            ->orderby('week', 'asc')
            ->get()
            ->pluck('week')
            ->toArray();
        */

        $totalTrainee = AllTrainee::count();

        $get_the_seat_detail = Seating::where('week', $weekRequired)->pluck('seat_detail')->first();
        $emptySeatCount = 0;
        $occupiedSeatCount = 0;
        $totalSeatCount = 0;

        $seatDetail = json_decode($get_the_seat_detail, true);

        // Check if $seatDetail is not null before using it
        if ($seatDetail !== null) {
            // Check if $seatDetail is an array
            if (is_array($seatDetail)) {
                // Total seat count is the number of entries in $seatDetail
                $totalSeatCount = count($seatDetail);

                // Iterate over each seat code in $seatDetail
                foreach ($seatDetail as $seatCode => $traineeName) {
                    // If the seat is empty (i.e., the value is an empty string)
                    if ($traineeName === "" || $traineeName === null) {
                        $emptySeatCount++;
                    } else {
                        // If the seat is occupied (i.e., the value is not empty)
                        $occupiedSeatCount++;
                    }
                }
            }
        }


        // Calculate the total available seat ,occupied seat number, total seat number and for that week
        $weeklyData = [];
        $weeklyData['empty_seat_count'] = $emptySeatCount;
        $weeklyData['occupied_seat_count'] = $occupiedSeatCount; 
        $weeklyData['total_seat_count'] = $totalSeatCount;
        
        $currentSeatingPlan = Seating::where('week', $weekRequired)->first(); // Get the first seating plan if it exists
        $hasCurrentSeatingPlan = !is_null($currentSeatingPlan);
        $currentSeatingDetails = $hasCurrentSeatingPlan ? json_decode($currentSeatingPlan->seat_detail, true) : [];

        $newTraineesPerMonth = AllTrainee::selectRaw('YEAR(internship_start) as year, MONTH(internship_start) as month, COUNT(*) as count')
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

        // Get the selected year or default to the current year
        $selectedYear = $request->input('year', Carbon::now()->year);

        // Fetch new trainees per month for the selected year
        $newTraineesPerMonth = AllTrainee::selectRaw('YEAR(internship_start) as year, MONTH(internship_start) as month, COUNT(*) as count')
            ->whereYear('internship_start', $selectedYear)
            ->groupBy('year', 'month')
            ->orderBy('month', 'asc')
            ->get();

        // Prepare data for the chart
        $months = [];
        $newTrainees = [];

        // Loop through all months (January to December)
        for ($month = 1; $month <= 12; $month++) {
            // Format month names like "January 2024"
            $months[] = Carbon::create($selectedYear, $month, 1)->format('F Y');

            // Check if there's trainee data for this month
            $monthlyData = $newTraineesPerMonth->firstWhere('month', $month);

            // If no data for this month, add 0 new trainees
            $newTrainees[] = $monthlyData ? $monthlyData->count : 0;
        }

        // Prepare total trainees per month (cumulative count)
        $totalTraineesPerMonth = [];
        $cumulativeCount = 0;

        // Loop through each month and calculate total trainees by the end of that month
        for ($month = 1; $month <= 12; $month++) {
            // Count the trainees who started in or before this month of the selected year
            $traineesUpToThisMonth = AllTrainee::where('internship_start', '<=', Carbon::create($selectedYear, $month, 1)->endOfMonth())
                ->count();
            
            // Store cumulative total trainees
            $totalTraineesPerMonth[] = $traineesUpToThisMonth;
        }

        $traineeTaskStats = [];

        foreach ($trainees as $trainee) {
            // Fetch task data for each trainee using trainee_id
            $totalTasks = TaskTimeline::where('trainee_id', $trainee->id)->count();
            $taskStatusCounts = TaskTimeline::select('task_status', \DB::raw('count(*) as count'))
                ->where('trainee_id', $trainee->id)
                ->groupBy('task_status')
                ->pluck('count', 'task_status');

            // Prepare the stats for this trainee
            $traineeTaskStats[$trainee->id] = [
                'total' => $totalTasks,
                'not_started' => $taskStatusCounts->get('Not Started', 0),
                'completed' => $taskStatusCounts->get('Completed', 0),
                'ongoing' => $taskStatusCounts->get('Ongoing', 0),
                'postponed' => $taskStatusCounts->get('Postponed', 0),
            ];
        }

        return view('admin-dashboard', compact('trainees','count', 'currentSeatingPlan', 'currentSeatingDetails', 'totalTrainee','logbooks', 'weeklyData','weekRequired','start_date','end_date', 'months', 'newTrainees', 'totalTraineesPerMonth', 'traineeTaskStats'));
    
    }

    public function showAllTrainee()
    {
        $trainees = AllTrainee::all();
        return view('all-trainee-list', compact('trainees'));
    }

    public function createNewTraineeRecord()
    {
        return view('admin-create-new-trainee-record');
    }

    public function deleteTraineeRecord($id)
    {
        // Find the record
        $assignRecord = TraineeAssign::where('trainee_id', $id)->get();
        $record = AllTrainee::where('id', $id)->first();

        // Check if the record exists
        if (!$record) {
            return redirect()->route('all-trainee-list')->with('error', 'Record not found.');
        }
    
        try {
            // Delete the record
            foreach($assignRecord as $assign){
                $assign->delete();
            }
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Trainee Record Deletion',
                'outcome' => 'success',
                'details' => 'Trainee record deleted: ' . $record->name,
            ]);
    
            $activityLog->save();

            $record->delete();
    
            return redirect()->route('all-trainee-list')->with('status', 'Record successfully deleted.');
        } catch (\Exception $e) {
            // Handle any deletion errors
            return redirect()->route('all-trainee-list')->with('error', 'An error occurred while deleting the record.');
        }
    }

    public function goToEditRecordPage($id)
    {
        $record = AllTrainee::find($id);
        return view('admin-edit-exist-trainee-record', compact('record'));
    }

    public function editRecordMethod(Request $request)
    {
        $trainee_id = $request->input('selected_trainee');

        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[A-Za-z\s]+$/',
            'internship_start' => 'required|date',
            'internship_end' => 'required|date',
        ]);
        
        if ($validator->fails()) {
            // Extract error messages
            $errorMessages = implode(' ', $validator->errors()->all());

            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Edit Trainee Record',
                'outcome' => 'failed',
                'details' => $errorMessages,
            ]);
    
            $activityLog->save();
            return redirect()->route('all-trainee-list')
                        ->withErrors($validator)
                        ->withInput();
        }

        $updated_trainee_name = $request->input('name');
        $updated_internship_start = $request->input('internship_start');
        $updated_internship_end = $request->input('internship_end');

        // return an error message when the admin choose invalid date (end date <= start date)
        if($updated_internship_end <= $updated_internship_start){
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Edit Trainee Record',
                'outcome' => 'failed',
                'details' => 'Invalid internship date chosen.',
            ]);
    
            $activityLog->save();
            return redirect()->route('all-trainee-list')->with('error', 'Invalid internship date!');
        }

        $record = AllTrainee::find($trainee_id);

        $record->name = $updated_trainee_name;
        $record->internship_start = $updated_internship_start;
        $record->internship_end = $updated_internship_end;


        $record->save();

        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Edit Trainee Record',
            'outcome' => 'success',
            'details' => 'Updated trainee record: ' . $record->name,
        ]);

        $activityLog->save();

        return redirect()->route('all-trainee-list')->with('status', 'Record has updated successfully!');
    }

    public function assignSupervisorToTrainee($selected_trainee)
    {
        // Get trainee details
        $traineeName = urldecode($selected_trainee);
        $trainee = AllTrainee::where('name', $traineeName)->first();
        $traineeID = $trainee->id;
    
        // Get trainee's expertise
        $traineeExpertises = $trainee->expertise;
    
        // Get supervisors who are not already assigned to this trainee
        $assignedSupervisorList = TraineeAssign::where('trainee_id', $traineeID)->pluck('assigned_supervisor_id')->toArray();
        $supervisors = Supervisor::whereNotIn('id', $assignedSupervisorList)->get();
    
        // Supervisor Recommendation Algorithm
        // If the supervisor does not have any trainee will get 2 points.
        // If the supervisor has same expertise with the selected trainee will get 2 points.
        // When every supervisors in the list have at least 1 supervisor, the supervisor with the least number of supervisor will gain 1 points.
        // The supervisor with highest points will be marked as "recommended"
        // If there is more than 1 supervisor with same highest points, all of them will be marked as "recommended"

        // Initialize a list to store supervisors with their calculated points
        $supervisorScores = [];

        // Check is the condition "all supervisors in the list have at least one supervisor"
        $leastTraineeCount = Supervisor::min('trainee_count');
    
        foreach ($supervisors as $supervisor) {
            $points = 0;
    
            // 1. Supervisors without any trainee
            $traineeCount = $supervisor->trainee_count;
            if ($traineeCount === 0) {
                $points += 2;
            }
    
            // 2. Supervisors sharing the same expertise with the trainee
            $supervisorExpertises = $supervisor->expertise;
            if ($traineeExpertises == $supervisorExpertises) {
                $points += 2;
            }
    
            // 3. Supervisors with the least number of trainees (when all have at least 1)
            if($leastTraineeCount >= 1){
                $traineeCount = $supervisor->trainee_count;
                if ($traineeCount == $leastTraineeCount) {
                    $points += 1;
                }
            }
    
            // Add supervisor and their points to the list
            $supervisorScores[] = [
                'supervisor' => $supervisor,
                'points' => $points
            ];
        }
    
        // Sort supervisors by points in descending order
        usort($supervisorScores, function ($a, $b) {
            return $b['points'] - $a['points'];
        });
    
        // Filter out the highest point supervisors for recommendation
        $highestPoints = $supervisorScores[0]['points'] ?? 0;
        $recommendedSupervisors = array_filter($supervisorScores, function ($supervisor) use ($highestPoints) {
            return $supervisor['points'] == $highestPoints;
        });
    
        // Only extract supervisor names for the recommendation
        $recommendedSupervisorNames = array_map(function($supervisorScore) {
            return $supervisorScore['supervisor']->name;
        }, $recommendedSupervisors);
    
        return view('admin-assign-trainee-function', [
            'trainee' => $trainee,
            'supervisorScores' => $supervisorScores,
            'supervisors' => $supervisors,
            'recommendedSupervisors' => $recommendedSupervisorNames,
            'leastTraineeCount' => $leastTraineeCount
        ]);
    }

    public function removeAssignedSupervisor($selected_trainee)
    {
        $traineeName = urldecode($selected_trainee);
        $traineeID = AllTrainee::where('name', $traineeName)
            ->value('id');
        $currentSupervisors = TraineeAssign::where('trainee_id', $traineeID)->get();
        return view('admin-remove-assigned-trainee-function', compact('traineeName','traineeID','currentSupervisors'));
    }

    public function supervisorAssignMethod(Request $request){

        $selectedTrainee = $request->input('selected_trainee');
        $selectedTraineeID = AllTrainee::where('name', $selectedTrainee)
            ->value('id');

        $selectedSupervisors = $request->input('selected_supervisors');
        if($selectedSupervisors){
            $selectedSupervisorIDs = Supervisor::whereIn('name', $selectedSupervisors)
            ->pluck('id')
            ->all();
        }
        else{
            return redirect()->route('admin-trainee-assign');
        }


        if(!empty($selectedSupervisorIDs)){
            // Loop through the selected supervisors and create records only if they are not already assigned
            foreach ($selectedSupervisorIDs as $supervisorID) {
                // Check if the supervisor is already assigned to the trainee
                $existingAssignment = TraineeAssign::where('assigned_supervisor_id', $supervisorID)
                    ->where('trainee_id', $selectedTraineeID)
                    ->first();

                if (!$existingAssignment) {
                    TraineeAssign::create([
                        'assigned_supervisor_id' => $supervisorID,
                        'trainee_id' => $selectedTraineeID,
                    ]);
                    Supervisor::where('id', $supervisorID)->update(['trainee_status' => 'Assigned']);
                    if (Trainee::where('id', $selectedTraineeID) != null) {
                        Trainee::where('id', $selectedTraineeID)->update(['supervisor_status' => 'Assigned']);
                    }
                }
                $traineeCount = TraineeAssign::where('assigned_supervisor_id', $supervisorID)->count();
                Supervisor::where('id', $supervisorID)->update(['trainee_count' => $traineeCount]);
            }
        }

        //convert the array to string
        $supervisorsString = implode(', ', $selectedSupervisors);

        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Supervisor Assignment',
            'outcome' => 'success',
            'details' => $supervisorsString . ' are assigned to trainee ' . $selectedTrainee,
        ]);

        $activityLog->save();
        return redirect()->route('admin-trainee-assign')->with('status', 'Supervisor Assigned Successfully');
    }
    
    public function removeSupervisorMethod(Request $request){
        $selectedSupervisors = $request->input('selected_supervisors');
        if($selectedSupervisors){
            $selectedSupervisorIDs = Supervisor::whereIn('name', $selectedSupervisors)
            ->pluck('id')
            ->all();
        }
        else{
            return redirect()->route('admin-trainee-assign');
        }
        $selectedTrainee = $request->input('selected_trainee');
        $selectedTraineeID = AllTrainee::where('name', $selectedTrainee)
            ->value('id');

        if(!empty($selectedSupervisorIDs)){
            // Loop through the selected trainees and create records only if they are not already assigned
            foreach ($selectedSupervisorIDs as $supervisorID) {
                // Check if the trainee is already assigned to the supervisor
                $existingAssignment = TraineeAssign::where('assigned_supervisor_id', $supervisorID)
                    ->where('trainee_id', $selectedTraineeID)
                    ->first();

                // Delete the assignment if it exists
                if ($existingAssignment) {
                    $existingAssignment->delete();
                }

                $trainee_counts = TraineeAssign::where('assigned_supervisor_id', $supervisorID)->count();
                Supervisor::where('id', $supervisorID)->update(['trainee_count' => $trainee_counts ]);

                // Check if there are any assignments left for this supervisor
                if(TraineeAssign::where('assigned_supervisor_id', $supervisorID)->count() == 0){
                    Supervisor::where('id', $supervisorID)->update(['trainee_status' => 'Not Assigned']);
                }

                // Check if there are any assignments left for this trainee
                if (TraineeAssign::where('trainee_id', $selectedTraineeID)->count() == 0) {
                    $trainee = Trainee::where('id', $selectedTraineeID)->first();
                    if ($trainee !== null) {
                        $trainee->update(['supervisor_status' => 'Not Assigned']);
                    }
                }
            }
        }

        //convert the array to string
        $supervisorsString = implode(', ', $selectedSupervisors);

        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Supervisor Assignment',
            'outcome' => 'success',
            'details' => $supervisorsString . ' are removed from trainee ' . $selectedTrainee,
        ]);

        $activityLog->save();
        return redirect()->route('admin-trainee-assign')->with('status', 'Supervisor Removed Successfully');
    }

    public function showCreateUserForm()
    {
        return view('admin-create-user');
    }

    public function createUser(Request $request){
        $allowedDomains = Settings::pluck('email_domain')->first(); // Get the email_domain value

        $allowedDomainsArray = explode(',', $allowedDomains);
        $request->validate([
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
                'regex:/^(?=.{1,64}@)[A-Za-z0-9_]+(\.[A-Za-z0-9_]+)*@[^-][A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*(\.[A-Za-z]{2,})$/',
                'ends_with:' . implode(',', $allowedDomainsArray),
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
            'role' => 'required|in:2,3',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*()_+])[a-zA-Z0-9!@#$%^&*()_+]+$/',
            ],
        ],[
            'name.regex' => 'The name field should only contain letters and spaces.',
            'email.regex' => 'The email field should be a valid email address.',
            'email.ends_with' => 'The email domain must be one of the allowed domains: ' . implode(', ', $allowedDomainsArray),
            'role.in' => 'The role field should be either 2 or 3.',
            'password.regex' => 'The password field should contain at least one uppercase letter and one special character.',
        ]);

        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role_id' => $request->input('role'),
            'password' => Hash::make($request->input('password')),
        ]);

        if ($request->input('role') == 3) { // Trainee
            Trainee::create([
                'name' => $request->input('name'),
                'personal_email' => NULL,
                'email' => $request->input('email'),
                'phone_number' => NULL,
                'graduate_date' => NULL,
                'expertise' => 'Not Specified',
                'supervisor_status' => 'Not Assigned',
                'resume_path' => NULL,
                'acc_status' => 'Active',
            ]);
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Create New Account',
                'outcome' => 'success',
                'details' => 'A new trainee account ' . $request->input('name') . ' is created.',
            ]);
    
            $activityLog->save();
        } elseif ($request->input('role') == 2) { // Supervisor
            Supervisor::create([
                'name' => $request->input('name'), 
                'section' => '',
                'department' => 'CSM',
                'email' => $request->input('email'),
                'phone_number' => '',
                'trainee_status' => 'Not Assigned',
            ]);
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Create New Account',
                'outcome' => 'success',
                'details' => 'A new supervisor account ' . $request->input('name') . ' is created.',
            ]);
    
            $activityLog->save();
        }

        return redirect()->back()->with('success', 'A new account successfully added.');
    }

    public function showCreateRecordForm()
    {
        return view('admin-create-record');
    }

    public function createRecord(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[A-Za-z\s]+$/|unique:users,name',
            'internship_start' => 'required|date',
            'internship_end' => 'required|date',
        ], [
            'name.unique' => 'The name has already existed.',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }
        
        if ($validator->fails()) {
            // Extract error messages
            $errorMessages = implode(' ', $validator->errors()->all());

            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Create Trainee Record',
                'outcome' => 'failed',
                'details' => $errorMessages,
            ]);
    
            $activityLog->save();
            return redirect()->route('all-trainee-list')
                        ->withErrors($validator)
                        ->withInput();
        }

        $internship_start = $request->input('internship_start');
        $internship_end = $request->input('internship_end');

        // return an error message when the admin choose invalid date (end date <= start date)
        if($internship_end <= $internship_start){            
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Create Trainee Record',
                'outcome' => 'failed',
                'details' => 'Invalid internship date chosen.',
            ]);

            $activityLog->save();
            return redirect()->route('all-trainee-list')->with('error', 'Invalid internship date!');
        }

        AllTrainee::create([
            'name' => $request->input('name'),
            'internship_start' => $internship_start,
            'internship_end' => $internship_end,
        ]);

        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Create Trainee Record',
            'outcome' => 'success',
            'details' => 'New trainee record ' . $request->input('name') . ' is created.',
        ]);

        $activityLog->save();

        return redirect()->route('all-trainee-list')->with('status', 'Trainee Created Successfully');
    }

    public function editProfile($selected)
    {
        $targetName = urldecode($selected);
        $user = User::where('name', $targetName)->first();
        $expertises = Expertise::pluck('expertise');
        $departments = Department::pluck('department_name');
        $sections = Section::pluck('section_name');

        if($user == null){
            return redirect()->route('user-management');
        }
        if ($user->role_id === 2) { //supervisor
            $supervisor = Supervisor::where('name', $user->name)->first();
            return view('admin-edit-profile', compact('user', 'supervisor', 'expertises', 'sections', 'departments'));
        } elseif ($user->role_id === 3) { //trainee
            $trainee = Trainee::where('name', $user->name)->first();
            $internship_date = AllTrainee::where('name', 'LIKE', $user->name)
                ->select('internship_start', 'internship_end')
                ->first();
            return view('admin-edit-profile', compact('user', 'trainee', 'internship_date', 'expertises'));
        } 
    }

    public function updateProfile(Request $request, $selected)
    {
        $targetName = urldecode($selected);
        $target = User::where('name', $targetName)->first();

        if($target == null){
            return redirect()->route('user-management');
        }

        if ($target->role_id === 2) { //supervisor

            $validatedData = $request->validate([
                'fullName' => 'required|regex:/^[A-Za-z\s]+$/',
                'expertise' => 'nullable|string',
                'department' => 'nullable|string',
                'section' => 'nullable|string',
            ]);
        
            $target->name = $request->input('fullName');
            $target->save();

            Supervisor::where('email', $target->email)
            ->update([
                'name' => $request->input('fullName'),
                'expertise' => $request->input('expertise'),
                'department' => $request->input('department'),
                'section' => $request->input('section'),
            ]);

            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Edit Profile',
                'outcome' => 'success',
                'details' => 'Profile for ' . $target->name . ' has been updated. ',
            ]);

            $activityLog->save();
        
            return redirect()->route('user-management')->with('success', ' Profile updated.');

        } elseif ($target->role_id === 3) { //trainee

            $target_trainee = Trainee::where('email', $target->email)->first();

            $validatedData = $request->validate([
                'fullName' => 'required|regex:/^[A-Za-z\s]+$/',
                'phoneNum' => ['nullable', 'string', 'regex:/^(\+?6?01)[02-46-9][0-9]{7}$|^(\+?6?01)[1][0-9]{8}$/'],
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
                'startDate' => 'nullable|date',
                'endDate' => 'nullable|date',
                'graduateDate' => 'nullable|date',
                'profilePicture' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
        
            $target->name = $request->input('fullName');
            $target->save();

            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');

            if($endDate != null && $startDate != null){
                if($endDate <= $startDate){
                    $activityLog = new ActivityLog([
                        'username' => Auth::user()->name,
                        'action' => 'Edit Profile',
                        'outcome' => 'failed',
                        'details' => 'Invalid internship start or end date chosen.',
                    ]);
    
                    $activityLog->save();
    
                    return redirect()->back()->with('error', 'Invalid internship date!');
                }
            }
    
            //Update the trainee basic information to table 'trainees'.
            Trainee::where('email', $target->email)
            ->update([
                'name' => $request->input('fullName'),
                'phone_number' => $request->input('phoneNum'),
                'expertise' => $request->input('expertise'),
                'personal_email' => $request->input('personalEmail'),
                'graduate_date' => $request->input('graduateDate'),
            ]);

            //Update the trainee internship start date and end date to table 'alltrainees'.
            AllTrainee::where('name', 'LIKE', $targetName)
            ->update([
                'internship_start' => $startDate,
                'internship_end' => $endDate,
            ]);  
        
            if ($request->hasFile('profilePicture')) {
                // Delete the old profile image
                if ($target_trainee->profile_image) {
                    Storage::delete($target_trainee->profile_image);
                }
            
                // Store the new profile image

                $imagePath = $request->file('profilePicture')->store('public/profile_pictures');
                $target_trainee->profile_image = $imagePath;
            }
            $target_trainee->save();

            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Edit Profile',
                'outcome' => 'success',
                'details' => 'Profile for ' . $target_trainee->name . 'has been updated.',
            ]);
    
            $activityLog->save();

            return redirect()->route('admin-go-profile', $target->name)->with('success', 'Profile updated successfully.');

        }
    }

    public function changeAccountStatus($selected)
    {
        $targetName = urldecode($selected);
        $trainee = Trainee::where('name', $targetName)->first();

        if($trainee->acc_status === 'Active'){
            $trainee->acc_status = 'Inactive';
            $trainee->save();

            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Change Account Status',
                'outcome' => 'success',
                'details' => 'Account status is changed to Inactive for trainee ' . $trainee->name,
            ]);
    
            $activityLog->save();
        } else {
            $trainee->acc_status = 'Active';
            $trainee->save();

            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Change Account Status',
                'outcome' => 'success',
                'details' => 'Account status is changed to Active for trainee ' . $trainee->name,
            ]);
    
            $activityLog->save();
        }

        return redirect()->route('user-management');
    }

    public function viewTraineeLogbook($traineeName)
    {
        $name = urldecode($traineeName);
        $trainee_id = Trainee::where('name', 'LIKE', $name)->pluck('id')->first();
        $logbooks = Logbook::where('trainee_id', $trainee_id)->get();
        return view('view-and-upload-logbook', compact('logbooks','name'));
    }

    public function uploadLogbook(Request $request, $name)
    {
        //get the trainee id
        $trainee_id = Trainee::where('name', 'LIKE', $name)->pluck('id')->first();

        //validate the uploaded logbook
        $validator = Validator::make($request->all(), [
            'logbook' => 'required|mimes:pdf,doc,docx|max:2048',
            'logbook_name' => 'required|string|max:255',
        ],[
            'logbook.max' => 'The logbook must not exceed 2MB in size.',
            'logbook.mimes' => 'Accepted logbook types are .pdf, .doc and .docx only.',
            'logbook_name.required' => 'Please provide a name for the logbook.',
            'logbook_name.max' => 'The logbook name must not exceed 255 characters.',
        ]);
        
        if ($validator->fails()) {
            // Extract error messages
            $errorMessages = implode(' ', $validator->errors()->all());

            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
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
                'username' => Auth::user()->name,
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
            return redirect()->route('view-and-upload-logbook', $name)->with('error', 'Cannot upload a file with an already existing name.');
        }
        else{
            // Save the file path in the database for the user
            Logbook::create([
                'trainee_id' => $trainee_id,
                'logbook_path' => 'storage/logbooks/' . $newFileName,
                'name' => $request->input('logbook_name'),
                'status' => 'Signed',
            ]);
            // Store the file in the "public" disk
            $file->storeAs('public/logbooks/', $newFileName);
        }

        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Logbook Upload',
            'outcome' => 'success',
            'details' => '',
        ]);

        $activityLog->save();


        // Redirect the user to a success page
        return redirect()->route('view-and-upload-logbook', $name)->with('success', 'Logbook uploaded successfully');
    }

    public function adminGenerateLogbook(Request $request, $name) {
        $name = urldecode($name); // Retrieve the trainee name from the query string
    
        // Retrieve the trainee's full data
        $trainee = Trainee::where('name', 'LIKE', $name)->first();
    
        // Retrieve tasks for the trainee in the selected date range
        $startMonth = $request->query('startMonth');
        $endMonth = $request->query('endMonth');
    
        // Validate the date range
        if (!$startMonth || !$endMonth) {
            return redirect()->back()->withErrors('Please select both start and end periods.');
        }
    
        $start = \Carbon\Carbon::parse($startMonth)->startOfMonth();
        $end = \Carbon\Carbon::parse($endMonth)->endOfMonth();

        if ($end->lt($start)) {
            return redirect()->back()->withErrors(['endMonth' => 'The end date must be the same or later than the start date.'])->withInput();
        }
    
        // Fetch tasks for the trainee within the specified range
        $tasks = TaskTimeline::where('trainee_id', $trainee->id)
                             ->where(function($query) use ($start, $end) {
                                 $query->whereBetween('task_start_date', [$start, $end])
                                       ->orWhereBetween('task_end_date', [$start, $end])
                                       ->orWhere(function($query) use ($start, $end) {
                                           $query->where('task_start_date', '<=', $start)
                                                 ->where('task_end_date', '>=', $end);
                                       });
                             })
                             ->get();
    
        // Decode task data
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
        $allTrainees = AllTrainee::where('name', $name)->first();
        if (!$allTrainees) {
            return redirect()->route('view-and-upload-logbook')->with('error', 'Trainee not assigned to supervisor.');
        }
    
        // Fetch associated supervisors
        $supervisors = $allTrainees->supervisors;
    
        // Load the view and pass data to generate the PDF
        $pdf = PDF::loadView('logbook-summary', compact('tasks', 'trainee', 'supervisors', 'dateGenerated', 'startMonth', 'endMonth', 'isSingleMonth'));
    
        // Optionally, set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
    
        // Return the PDF to be displayed in the browser
        return $pdf->stream($fileName);
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

        return redirect()->route('view-and-upload-logbook', $name)->with('success', 'Logbook deleted successfully');
    }

    public function adminGoTraineeProfile($traineeName){
        $name = urldecode($traineeName);
        $trainee = Trainee::where('name', $name)->first();

        $internship_dates = AllTrainee::where('name', 'LIKE', $name)
        ->select('internship_start', 'internship_end')
        ->first();

        $comments = Comment::where('trainee_id', $trainee->id)
        ->select('comments.comment', 'supervisors.name','comments.id')
        ->join('supervisors', 'comments.supervisor_id', '=', 'supervisors.id')
        ->get();

        $trainee_id = $trainee->id;
        $logbooks = Logbook::where('trainee_id', $trainee_id)->get();
    
        return view('admin-view-trainee-profile', compact('trainee','internship_dates', 'comments', 'logbooks'));
    }

    public function adminUploadResume(Request $request, $traineeName){
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
                'username' => Auth::user()->name,
                'action' => 'Resume Upload',
                'outcome' => 'failed',
                'details' => $errorMessages,
            ]);

            $activityLog->save();
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $trainee = Trainee::where('name', $traineeName)->first();

        if ($trainee->resume_path !== null) {
            $resumePath = storage_path('app/public/resumes/') . basename($trainee->resume_path);
            if (file_exists($resumePath)) {
                unlink($resumePath);
            }
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
        $trainee->resume_path = 'storage/resumes/' . $newFileName;
        $trainee->save();

        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Resume Upload',
            'outcome' => 'success',
            'details' => '' ,
        ]);

        $activityLog->save();

        // Redirect the user to a success page
        return redirect()->route('admin-go-profile', $traineeName)->with('success', 'Resume uploaded successfully');
    }

    public function changeSVComment(Request $request, $commentID){
        $comment = Comment::where('id', $commentID)->first();
        $editedComment = $request->input('editedComment');
        $comment->comment = $editedComment;
        $comment->save();

        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Personal Comment',
            'outcome' => 'success',
            'details' => 'Comment has edited: ' . $editedComment ,
        ]);

        $activityLog->save();

        return redirect()->back()->with('success', 'Comment edited successfully.');
    }

    public function deleteAccount($traineeID){
        //find for the account need to be deleted.
        $acc = Trainee::where('id', $traineeID)->first();

        //delete all related information from DB.

        $comment = Comment::where('trainee_id', $traineeID)->first();
        if($comment){
            $comment->delete();
        }
       
        $tasks = TaskTimeline::where('trainee_id', $traineeID)->get();
        if($tasks){
            foreach($tasks as $task){
                $task->delete();
            }
        }

        $logbooks = Logbook::where('trainee_id', $traineeID)->get();
        if($logbooks){
            foreach($logbooks as $logbook){
                $logbookPath = storage_path('app/public/logbooks/') . basename($logbook->logbook_path);
                if (file_exists($logbookPath)) {
                    unlink($logbookPath);
                }
                $logbook->delete();
            }
        }

        $notifications = Notification::where('notifiable_id', $traineeID)
        ->whereJsonContains('data->name', $acc->name)
        ->get();
        if($notifications){
            foreach ($notifications as $notification) {
                $notification->delete();
            }
        }

        $user_record = User::where('email', $acc->email)->first();

        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Account Deletion',
            'outcome' => 'success',
            'details' => 'Deleted Account: ' . $user_record->name ,
        ]);

        $activityLog->save();

        if($user_record){
            $user_record->delete();
        }

        $acc->delete();
        
        return redirect()->back()->with('success', 'Account deleted successfully.');
    }

    public function deleteSVAccount($supervisorID){
        //find for the account need to be deleted.
        $acc = Supervisor::where('id', $supervisorID)->first();

        //delete all related information from DB.

        $comment = Comment::where('supervisor_id', $supervisorID)->first();
        if($comment){
            $comment->delete();
        }

        $notifications = Notification::where('notifiable_id', $supervisorID)
        ->whereJsonContains('data->name', null)
        ->get();
        if($notifications){
            foreach ($notifications as $notification) {
                $notification->delete();
            }
        }

        $assignments = TraineeAssign::where('assigned_supervisor_id', $supervisorID)->get();
        if($assignments){
            foreach($assignments as $assignment){
                $assignment->delete();
            }
        }

        $user_record = User::where('email', $acc->email)->first();

        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Account Deletion',
            'outcome' => 'success',
            'details' => 'Deleted Account: ' . $user_record->name ,
        ]);

        $activityLog->save();

        if($user_record){
            $user_record->delete();
        }

        $acc->delete();
        
        return redirect()->back()->with('success', 'Account deleted successfully.');
    }

    public function adminChangePassword(Request $request, $id, $type){
        if($type == 'Trainee'){
            $traineeRecord = Trainee::where('id', $id)->first();
            $userRecord = User::where('email', $traineeRecord->email)->first();
            
            $validator = Validator::make($request->all(), [
                'newPassword' => ['required','string','min:8','regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).*$/'],
                'confirmPassword' => ['required','string','min:8','same:newPassword'],
            ]);
        
            // Check if the validation fails
            if ($validator->fails()) {
                // Extract error messages
                $errorMessages = implode(' ', $validator->errors()->all());

                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Change Trainee Password',
                    'outcome' => 'failed',
                    'details' => $errorMessages,
                ]);
        
                $activityLog->save();
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $password = $request->input('newPassword');
            $confirmedPassword = $request->input('confirmPassword');

            // check the password and confirmed password is matched or not.
            if($password != $confirmedPassword){
                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Change Trainee Password',
                    'outcome' => 'failed',
                    'details' => 'Password and confirmed password do not match.',
                ]);
        
                $activityLog->save();
                return redirect()->back()->with('warning', 'Password and confirmed password do not match.');
            }

            $newPassword = Hash::make($password);

            $userRecord->password= $newPassword;
            $userRecord->save();

            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Change Trainee Password',
                'outcome' => 'success',
                'details' => 'Successfully changed the password for trainee ' . $userRecord->name,
            ]);
    
            $activityLog->save();

            return redirect()->back()->with('success', 'Password for this trainee has changed successfully.');
        }
        else{
            $supervisorRecord = Supervisor::where('id',$id)->first();
            $userRecord = User::where('email', $supervisorRecord->email)->first();
            
            $validator = Validator::make($request->all(), [
                'newPassword' => ['required','string','min:8','regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).*$/'],
                'confirmPassword' => ['required','string','min:8','same:newPassword'],
            ]);
        
            // Check if the validation fails
            if ($validator->fails()) {
                 // Extract error messages
                $errorMessages = implode(' ', $validator->errors()->all());

                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Change Supervisor Password',
                    'outcome' => 'failed',
                    'details' => $errorMessages,
                ]);
        
                $activityLog->save();
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $password = $request->input('newPassword');
            $confirmedPassword = $request->input('confirmPassword');

            // check the password and confirmed password is matched or not.
            if($password != $confirmedPassword){
                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Change Supervisor Password',
                    'outcome' => 'failed',
                    'details' => 'Password and confirmed password do not match.',
                ]);
        
                $activityLog->save();
                return redirect()->back()->with('warning', 'Password and confirmed password do not match.');
            }

            $newPassword = Hash::make($password);
            $userRecord->password = $newPassword;
            $userRecord->save();

            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Change Supevisor Password',
                'outcome' => 'success',
                'details' => 'Successfully changed the password for supervisor ' . $userRecord->name,
            ]);
    
            $activityLog->save();

            return redirect()->back()->with('success', 'Password for this supervisor has changed successfully.');
        }
    }

    public function adminUpdatePassword(Request $request){
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

    //obtain the activity log from the last record to the first record
    public function displayActivityLog(){
        $activityLogs = ActivityLog::orderBy('id', 'desc')->get();
        return view('activity-log', compact('activityLogs'));
    }

    //obtain the activity log according to the filter option
    public function activityLogFilter(Request $request){
        $username = $request->input('username');
        $start_date_input = $request->input('fromDate');
        $end_date_input = $request->input('toDate');
        $outcome = $request->input('outcome');
    
        // Start the query
        $query = ActivityLog::query();
    
        if($username){
            $query->where('username', 'like', '%' . $username . '%');
        }

        if ($start_date_input && $end_date_input) {
            // Filter between the start and end date inclusively
            $query->whereBetween('created_at', [
                $start_date_input . ' 00:00:00',
                $end_date_input . ' 23:59:59'
            ]);
        } elseif ($start_date_input) {
            // Only start date is provided
            $query->where('created_at', '>=', $start_date_input . ' 00:00:00');
        } elseif ($end_date_input) {
            // Only end date is provided
            $query->where('created_at', '<=', $end_date_input . ' 23:59:59');
        }
        
    
        if ($outcome) {
            // Filter by outcome
            $query->where('outcome', $outcome);
        }
    
        // Get the results
        $activityLogs = $query->orderBy('id', 'desc')->get();
    
        return view('activity-log', compact('activityLogs', 'username', 'start_date_input', 'end_date_input', 'outcome'));
    }

    public function showSettings()
    {
        // Get the first settings record or return an empty Settings instance if none exists
        $settings = Settings::first() ?? new Settings();
        $expertises = Expertise::pluck('expertise');
        $departments = Department::pluck('department_name');
        $sections = Section::pluck('section_name');
        return view('admin-settings', compact('settings', 'expertises', 'departments', 'sections'));
    }
    

    public function storeSettings(Request $request)
    {    
        // Validate the request
        $request->validate([
            'allowed_domains' => 'array',
            'allowed_domains.*' => 'string|regex:/^[a-z0-9.-]+\.[a-z]{2,}$/', // Accept domain names
            'expertises' => 'array', // Validate expertises input
            'expertises.*' => 'string|max:255', 
            'departments' => 'array', // Validate departments input
            'departments.*' => 'string|max:255', 
            'sections' => 'array', // Validate sections input
            'sections.*' => 'string|max:255', 
        ]);        
    
        // Retrieve the settings or create a new instance if none exists
        $settings = Settings::first() ?? new Settings();
    
        // Handle allowed domains
        $allowedDomains = $request->input('allowed_domains', []);
        $settings->email_domain = implode(',', $allowedDomains); 
    
        // Handle disable registration checkbox
        $settings->disable_registration = $request->input('disable_registration') === 'on';

        // Handle comapny name
        $settings->company_name = $request->input('company_name');
    
        // Save settings
        $settings->save();

        Expertise::truncate();  // clear all existing records in the 'expertises' table, so that no need to compare two arrays (database vs input)
        $expertises = $request->input('expertises', []); 
        foreach ($expertises as $expertiseName) {
            Expertise::create(['expertise' => $expertiseName]);  // Create new expertise entries
        }

        Department::truncate();  // clear all existing records in the 'departments' table, so that no need to compare two arrays (database vs input)
        $departments = $request->input('departments', []); 
        foreach ($departments as $departmentName) {
            Department::create(['department_name' => $departmentName]);  // Create new department entries
        }

        Section::truncate();  // clear all existing records in the 'sections' table, so that no need to compare two arrays (database vs input)
        $sections = $request->input('sections', []); 
        foreach ($sections as $sectionName) {
            Section::create(['section_name' => $sectionName]);  // Create new section entries
        }

        // Redirect back with a success message
        return redirect()->route('settings')->with('success', 'Settings updated successfully.');
    }
}
    

