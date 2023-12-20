<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Comment;
use App\Models\Logbook;
use App\Models\Seating;
use App\Models\Trainee;
use App\Models\AllTrainee;
use App\Models\Supervisor;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\TaskTimeline;
use Illuminate\Http\Request;
use App\Models\TraineeAssign;
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
        $weeksInMonth = Seating::whereYear(DB::raw("STR_TO_DATE(start_date, '%d/%m/%Y')"), $year)
            ->whereMonth(DB::raw("STR_TO_DATE(start_date, '%d/%m/%Y')"), $month)
            ->select('week')
            ->distinct()
            ->orderby('week', 'asc')
            ->get()
            ->pluck('week')
            ->toArray();

        $totalTrainee = AllTrainee::count();

        $get_the_seat_detail = Seating::where('week', $weekRequired)->pluck('seat_detail')->first();
        $emptySeatCount = 0;
        $occupiedSeatCount = 0;
        $totalSeatCount = 0;
        $seatDetail = json_decode($get_the_seat_detail, true);
         // Check if $seatDetail is not null before using it
         if ($seatDetail !== null) {
             // Check if $seatDetail is not null and is an array before using it
             if (is_array($seatDetail)) {
                 //to get the total number of empty seats (trainee id = Not Assigned & seat status = Available)
                 $emptySeatCount = count(
                     array_filter($seatDetail, function ($seat) {
                         return isset($seat['trainee_id']) && isset($seat['seat_status']) &&
                             $seat['trainee_id'] === 'Not Assigned' && $seat['seat_status'] === 'Available';
                     })
                 );
                 $occupiedSeatCount = count(
                    array_filter($seatDetail, function ($seat) {
                        return isset($seat['trainee_id']) && isset($seat['seat_status']) &&
                            $seat['trainee_id'] !== 'Not Assigned' && $seat['seat_status'] === 'Available';
                    })
                );
                $totalSeatCount = count(
                    array_filter($seatDetail, function ($seat) {
                        return isset($seat['seat_status']) &&
                            $seat['seat_status'] === 'Available';
                    })
                );
             } 
         }

        // Calculate the total available seat ,occupied seat number, total seat number and for that week
        $weeklyData = [];
        $weeklyData['empty_seat_count'] = $emptySeatCount;
        $weeklyData['occupied_seat_count'] = $occupiedSeatCount; 
        $weeklyData['total_seat_count'] = $totalSeatCount;

        // Define an array of all possible seat names
        $allSeatNames = [];
        for ($i = 1; $i <= env('TOTAL_SEATS_1ST_FLOOR'); $i++) {
            $seatNumber = str_pad($i, 2, '0', STR_PAD_LEFT); // Format as CSM01 to CSM20
            $allSeatNames[] = 'CSM' . $seatNumber; 
        }

        $tSeatNames = [];
        for ($i = 1; $i <= 17; $i++) {
            $tSeatNumber = str_pad($i, 2, '0', STR_PAD_LEFT); // Format as T01 to T17
            $tSeatNames[] = 'T' . $tSeatNumber;
        }
        
        // Check if a record doesn't exist with the specified conditions
        //to check whether the seat information for that week is exist or not.
        $exist = true;
        if (Seating::where('week', $weekRequired)->doesntExist()) {
           $exist = false;
        }

        // Fetch trainee_id data from the seatings table for the selected week
        $seatingData = Seating::where('week', $weekRequired)
            ->first();

        if ($seatingData) {
            // Decode the seat_detail JSON
            $seatDetail = json_decode($seatingData->seat_detail, true);
        
            // Replace the trainee_id with trainee name
            foreach ($seatDetail as &$seatInfo) {
                $trainee_name = AllTrainee::where('id',$seatInfo['trainee_id'])->pluck('name')->first();
                $seatInfo['trainee_id'] = $trainee_name ?? 'Not Assigned';
            }
        
            // Encode the updated seat_detail back to JSON
            $seatingData = json_encode($seatDetail);

            return view('admin-dashboard', compact('trainees','seatingData','count','totalTrainee','logbooks','weeksInMonth', 'weeklyData','weekRequired','start_date','end_date','exist'));
        }
        else{
            return view('admin-dashboard', compact('trainees','seatingData','count','totalTrainee','logbooks','weeksInMonth', 'weeklyData','weekRequired','start_date','end_date','exist'));
        }
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
            return redirect()->route('all-trainee-list')
                        ->withErrors($validator)
                        ->withInput();
        }

        $updated_trainee_name = $request->input('name');
        $updated_internship_start = $request->input('internship_start');
        $updated_internship_end = $request->input('internship_end');

        // return an error messae when the admin choose invalid date (end date <= start date)
        if($updated_internship_end <= $updated_internship_start){
            return redirect()->route('all-trainee-list')->with('error', 'Invalid internship date!');
        }

        $record = AllTrainee::find($trainee_id);

        $record->name = $updated_trainee_name;
        $record->internship_start = $updated_internship_start;
        $record->internship_end = $updated_internship_end;


        $record->save();

        return redirect()->route('all-trainee-list')->with('status', 'Record has updated successfully!');
    }

    public function assignSupervisorToTrainee($selected_trainee)
    {
        $traineeName = urldecode($selected_trainee);
        $trainee = AllTrainee::where('name', $traineeName)->first();
        $traineeID = AllTrainee::where('name', $traineeName)
            ->value('id');
        
        $assignedSupervisorList = TraineeAssign::where('trainee_id', $traineeID)->pluck('assigned_supervisor_id')->toArray();
        $filteredSupervisors = Supervisor::whereNotIn('id', $assignedSupervisorList)->get();

        return view('admin-assign-trainee-function', compact('trainee','filteredSupervisors'));
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
            }
        }
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
        return redirect()->route('admin-trainee-assign')->with('status', 'Supervisor Removed Successfully');
    }

    public function showCreateUserForm()
    {
        return view('admin-create-user');
    }

    public function createUser(Request $request){
        $request->validate([
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
            'email.regex' => 'The email field should be a valid SAINS email address.',
            'email.ends_with' => 'The email field should end with @sains.com.my.',
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
                'sains_email' => $request->input('email'),
                'phone_number' => NULL,
                'password' => Hash::make($request->input('password')),
                'graduate_date' => NULL,
                'expertise' => 'Not Specified',
                'supervisor_status' => 'Not Assigned',
                'resume_path' => NULL,
                'acc_status' => 'Active',
            ]);
        } elseif ($request->input('role') == 2) { // Supervisor
            Supervisor::create([
                'name' => $request->input('name'), 
                'section' => '',
                'department' => 'CSM',
                'personal_email' => NULL,
                'sains_email' => $request->input('email'),
                'phone_number' => '',
                'password' => Hash::make($request->input('password')),
                'trainee_status' => 'Not Assigned',
            ]);
        }

        // Redirect to a success page or any other desired action
        return redirect()->route('admin-create-account')->with('success', 'A new account successfully added.');
    }

    public function showCreateRecordForm()
    {
        return view('admin-create-record');
    }

    public function createRecord(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[A-Za-z\s]+$/',
            'internship_start' => 'required|date',
            'internship_end' => 'required|date',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('all-trainee-list')
                        ->withErrors($validator)
                        ->withInput();
        }

        $internship_start = $request->input('internship_start');
        $internship_end = $request->input('internship_end');

        // return an error messae when the admin choose invalid date (end date <= start date)
        if($internship_end <= $internship_start){
            return redirect()->route('all-trainee-list')->with('error', 'Invalid internship date!');
        }

        AllTrainee::create([
            'name' => $request->input('name'),
            'internship_start' => $internship_start,
            'internship_end' => $internship_end,
        ]);

        // Redirect to a success page or any other desired action
        return redirect()->route('all-trainee-list')->with('status', 'Trainee Created Successfully');
    }

    public function editProfile($selected)
    {
        $targetName = urldecode($selected);
        $user = User::where('name', $targetName)->first();

        if($user == null){
            return redirect()->route('user-management');
        }
        if ($user->role_id === 2) { //supervisor
            $supervisor = Supervisor::where('name', $user->name)->first();
            return view('admin-edit-profile', compact('user', 'supervisor'));
        } elseif ($user->role_id === 3) { //trainee
            $trainee = Trainee::where('name', $user->name)->first();
            $internship_date = AllTrainee::where('name', 'LIKE', $user->name)
                ->select('internship_start', 'internship_end')
                ->first();
            return view('admin-edit-profile', compact('user', 'trainee', 'internship_date'));
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
                'phoneNum' => ['nullable', 'string', 'regex:/^(\+?6?01)[02-46-9][0-9]{7}$|^(\+?6?01)[1][0-9]{8}$/'],
                'section' => 'nullable|string',
                'profilePicture' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        
            $target->name = $request->input('fullName');
            $target->save();

            Supervisor::where('sains_email', $target->email)
            ->update([
                'name' => $request->input('fullName'),
                'phone_number' => $request->input('phoneNum'),
                'section' => $request->input('section'),
            ]);
        
            return redirect()->route('sv-profile');

        } elseif ($target->role_id === 3) { //trainee

            $target_trainee = Trainee::where('sains_email', $target->email)->first();

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

            if($endDate <= $startDate){
                return redirect()->back()->with('error', 'Invalid internship date!');
            }
    
            //Update the trainee basic information to table 'trainees'.
            Trainee::where('sains_email', $target->email)
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
        } else {
            $trainee->acc_status = 'Active';
            $trainee->save();
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
        ],[
            'logbook.max' => 'The logbook must not exceed 2MB in size.',
            'logbook.mimes' => 'Accepted logbook types are .pdf, .doc and .docx only.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // The unsigned logbook (uploaded by trainee) cannot be more than 4.
        $logbookCount = Logbook::where('trainee_id', $trainee_id)
        ->where('status', 'Signed')
        ->count();

        if ($logbookCount >= 4) {
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
                'status' => 'Signed',
            ]);
            // Store the file in the "public" disk
            $file->storeAs('public/logbooks/', $newFileName);
        }


        // Redirect the user to a success page
        return redirect()->route('view-and-upload-logbook', $name)->with('success', 'Logbook uploaded successfully');
    }

    public function destroy(Logbook $logbook, $name)
    {
        // Delete the logbook file from storage
        $logbookPath = storage_path('app/public/logbooks/') . basename($logbook->logbook_path);
        if (file_exists($logbookPath)) {
            unlink($logbookPath);
        }

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
        $request->validate([
            'resume' => 'required|mimes:pdf|max:2048',
        ]);

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

        // Redirect the user to a success page
        return redirect()->route('admin-go-profile', $traineeName)->with('success', 'Resume uploaded successfully');
    }

    public function changeSVComment(Request $request, $commentID){
        $comment = Comment::find($commentID);
        $editedComment = $request->input('editedComment');
        $comment->comment = $editedComment;
        $comment->save();

        return redirect()->back()->with('success', 'Comment edited successfully.');
    }

    public function deleteAccount($traineeID){
        //find for the account need to be deleted.
        $acc = Trainee::find($traineeID);

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

        $user_record = User::where('email', $acc->sains_email)->first();
        if($user_record){
            $user_record->delete();
        }

        $acc->delete();
        
        return redirect()->back()->with('success', 'Account deleted successfully.');
    }

    public function deleteSVAccount($supervisorID){
        //find for the account need to be deleted.
        $acc = Supervisor::find($supervisorID);

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

        $user_record = User::where('email', $acc->sains_email)->first();
        if($user_record){
            $user_record->delete();
        }

        $acc->delete();
        
        return redirect()->back()->with('success', 'Account deleted successfully.');
    }

    public function adminChangePassword(Request $request, $id, $type){
        if($type == 'Trainee'){
            $traineeRecord = Trainee::find($id);
            $userRecord = User::where('email', $traineeRecord->sains_email)->first();
            
            $validator = Validator::make($request->all(), [
                'newPassword' => ['required','string','min:8','regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).*$/'],
                'confirmPassword' => ['required','string','min:8','same:newPassword'],
            ]);
        
            // Check if the validation fails
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $password = $request->input('newPassword');
            $confirmedPassword = $request->input('confirmPassword');

            // check the password and confirmed password is matched or not.
            if($password != $confirmedPassword){
                return redirect()->back()->with('warning', 'Password and confirmed password do not match.');

            }

            $newPassword = Hash::make($password);
            $traineeRecord->password = $newPassword;
            $traineeRecord->save();

            $userRecord->password= $newPassword;
            $userRecord->save();

            return redirect()->back()->with('success', 'Password for this trainee has changed successfully.');
        }
        else{
            $supervisorRecord = Supervisor::find($id);
            $userRecord = User::where('email', $supervisorRecord->sains_email)->first();
            
            $validator = Validator::make($request->all(), [
                'newPassword' => ['required','string','min:8','regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).*$/'],
                'confirmPassword' => ['required','string','min:8','same:newPassword'],
            ]);
        
            // Check if the validation fails
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $password = $request->input('newPassword');
            $confirmedPassword = $request->input('confirmPassword');

            // check the password and confirmed password is matched or not.
            if($password != $confirmedPassword){
                return redirect()->back()->with('warning', 'Password and confirmed password do not match.');
            }

            $newPassword = Hash::make($password);
            $supervisorRecord->password = $newPassword;
            $supervisorRecord->save();

            $userRecord->password= $newPassword;
            $userRecord->save();

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
                'min:12',
                'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*()_+])[a-zA-Z0-9!@#$%^&*()_+]+$/',
            ],
            'confirm_password' => 'required|string|same:new_password',
        ], [
            'new_password.min' => 'The password must have at least 12 characters.',
            'new_password.regex' => 'The format of the password is incorrect.',
            'confirm_password.same' => 'The confirm password does not match the new password.',
        ]);

        if ($validator->fails()) {
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
                return redirect()->back()->with('error', 'Cannot set the same password as new password.');
            }
            else{
                $user->password = $new_password;
                $user->save();
            }
        }
        else{
            return redirect()->back()->with('error', 'Wrong current password.');
        }

        return redirect()->back()->with('success', 'Password successfully changed!');
    }
}
