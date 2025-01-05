<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Seating;
use App\Models\Trainee;
use App\Models\AllTrainee;
use App\Models\ActivityLog;
use App\Models\SeatingImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SeatingController extends Controller
{
    public function index(Request $request)
    {
        
    }

    public function getWeeklyData(Request $request) {
        $week = $request->input('week'); // Get the selected week from the request
    
        // Query the database to fetch seating data for the selected week
        $seatingData = Seating::where('week', $week)->get();
    
        return response()->json($seatingData);
    }

    public function seatingArrangement(Request $request)
    {
        // Get the current week data (for initial page load)
        $currentWeek = now()->format('Y-\WW');  // Get current week in YYYY-WW format
        $currentSeatingPlan = Seating::where('week', $currentWeek)->first(); // Get the first seating plan if it exists
    
        // Get the selected week from the request (default to current week)
        $selectedWeek = $request->input('week', $currentWeek);  // Default to the current week if no selection
        $selectedSeatingPlan = Seating::where('week', $selectedWeek)->first(); // Get the first seating plan if it exists

        $dateTime = new DateTime($currentWeek);
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 7);
        $current_end_date = $dateTime->format('d/m/Y');  // End of the week for display at the top
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 1);
        $current_start_date = $dateTime->format('d/m/Y');  // Start of the week for display at the top
        $dateTime = new DateTime($selectedWeek);
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 7);
        $selected_end_date = $dateTime->format('d/m/Y');  // End of the week for display at the top
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 1);
        $selected_start_date = $dateTime->format('d/m/Y');  // Start of the week for display at the top
    
        // Check if the selected week has a seating plan
        $hasSeatingPlan = !is_null($selectedSeatingPlan);
        $hasCurrentSeatingPlan = !is_null($currentSeatingPlan);
    
        // Decode the seat_detail only if the plans exist
        $currentSeatingDetails = $hasCurrentSeatingPlan ? json_decode($currentSeatingPlan->seat_detail, true) : [];
        $selectedSeatingDetails = $hasSeatingPlan ? json_decode($selectedSeatingPlan->seat_detail, true) : [];

        // Fetch all seating images of the current week if they exist
        $currentSeatingImages = SeatingImage::where('week', $currentWeek)->first();
        $currentImagePaths = $currentSeatingImages ? json_decode($currentSeatingImages->image_path, true) : [];

        // Fetch all seating images of the selected week if they exist
        $selectedSeatingImages = SeatingImage::where('week', $selectedWeek)->first();
        $selectedImagePaths = $selectedSeatingImages ? json_decode($selectedSeatingImages->image_path, true) : [];


        return view('seating-arrangement', [
            'currentSeatingPlan' => $currentSeatingPlan,
            'currentSeatingDetails' => $currentSeatingDetails,
            'currentWeek' => $currentWeek,
            'selectedSeatingPlan' => $selectedSeatingPlan,
            'selectedSeatingDetails' => $selectedSeatingDetails,
            'selectedWeek' => $selectedWeek,
            'hasSeatingPlan' => $hasSeatingPlan,
            'currentSeatingImages' => $currentImagePaths,
            'selectedSeatingImages' => $selectedImagePaths,
            'currentStartDate' => $current_start_date,
            'currentEndDate' => $current_end_date,
            'selectedStartDate' => $selected_start_date,
            'selectedEndDate' => $selected_end_date,
        ]);
    }

    public function traineeSVViewSeatingPlan(Request $request)
    {
        $role_id = Auth::user()->role_id;
        $user_name = Auth::user()->name;

        // Get the current week data
        $currentWeek = now()->format('Y-\WW');  // Get current week in YYYY-WW format
    
        // Get the selected week from the request (default to current week)
        $selectedWeek = $request->input('week', $currentWeek);  // Default to the current week if no selection
        $selectedSeatingPlan = Seating::where('week', $selectedWeek)->first(); // Get the first seating plan if it exists

        $dateTime = new DateTime($selectedWeek);
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 7);
        $end_date = $dateTime->format('d/m/Y');  // End of the week for display at the top
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 1);
        $start_date = $dateTime->format('d/m/Y');  // Start of the week for display at the top
    
        // Check if the selected week has a seating plan
        $hasSeatingPlan = !is_null($selectedSeatingPlan);
    
        // Decode the seat_detail only if the plans exist
        $selectedSeatingDetails = $hasSeatingPlan ? json_decode($selectedSeatingPlan->seat_detail, true) : [];

        // Fetch all seating images of the selected week if they exist
        $selectedSeatingImages = SeatingImage::where('week', $selectedWeek)->first();
        $selectedImagePaths = $selectedSeatingImages ? json_decode($selectedSeatingImages->image_path, true) : [];

        if($role_id == 2){
            return view('sv-view-seating-plan', [
                'currentWeek' => $currentWeek,
                'selectedSeatingPlan' => $selectedSeatingPlan,
                'selectedSeatingDetails' => $selectedSeatingDetails,
                'selectedWeek' => $selectedWeek,
                'hasSeatingPlan' => $hasSeatingPlan,
                'selectedSeatingImages' => $selectedImagePaths,
                'selectedStartDate' => $start_date,
                'selectedEndDate' => $end_date,
            ]);
        }
        elseif($role_id == 3 ){
            return view('trainee-view-seating-plan', [
                'traineeName' => $user_name,
                'currentWeek' => $currentWeek,
                'selectedSeatingPlan' => $selectedSeatingPlan,
                'selectedSeatingDetails' => $selectedSeatingDetails,
                'selectedWeek' => $selectedWeek,
                'hasSeatingPlan' => $hasSeatingPlan,
                'selectedSeatingImages' => $selectedImagePaths,
                'selectedStartDate' => $start_date,
                'selectedEndDate' => $end_date,
            ]);
        }

    }
    
    public function editWeeklySeatingPlan(Request $request)
    {
        // Check if a specific week is provided, otherwise use the current week
        $selectedWeek = $request->query('week', now()->format('Y-\WW')); // If no 'week' is provided, use the current week
        
        // Convert the selected week into a date for comparison
        $selectedWeekDate = Carbon::now()->startOfWeek()->setISODate(Carbon::now()->year, substr($selectedWeek, -2)); // Extract week number and use it
    
        // Fetch the seating plan for the selected week
        $currentSeatingPlan = Seating::where('week', $selectedWeek)->first();
        
        // Fetch trainees whose internship starts before or on the selected week and ends after today
        $trainees = AllTrainee::where('internship_start', '<=', $selectedWeekDate->format('Y-m-d'))
                    ->where(function($query) {
                        $query->whereNull('internship_end') // If no end date is specified
                            ->orWhere('internship_end', '>=', now()->format('Y-m-d')); // or internship has not ended yet
                    })
                    ->get();

        // Fetch all seating images of the selected week if they exist
        $selectedSeatingImages = SeatingImage::where('week', $selectedWeek)->first();
        $selectedImagePaths = $selectedSeatingImages ? json_decode($selectedSeatingImages->image_path, true) : [];
    
        // Pass the seating plan, selected week, and filtered trainees to the view
        return view('edit-current-week-seating-plan', [
            'currentSeatingPlan' => $currentSeatingPlan,
            'selectedWeek' => $selectedWeek,
            'trainees' => $trainees, 
            'existingImages' => $selectedImagePaths,
        ]);
    }
    

    public function updateWeeklySeatingPlan(Request $request)
    {
        // Get the week
        $week = $request->input('selected_week', now()->format('Y-\WW'));

        // Fetch the selected week's seating plan
        $seatingPlan = Seating::where('week', $week)->first();

        $validator = Validator::make($request->all(), [
            'seat_detail' => 'required|array',
            'existing_images' => 'nullable|array', 
            'new_images' => 'nullable|array', 
            'new_images.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'seat_detail.required' => 'You cannot have empty seating plan.',
            'new_images.*.mimes' => 'Only jpeg, png, and jpg images are allowed.',
            'new_images.*.max' => 'The image size must not exceed 5MB.',
        ]);

        if ($validator->fails()) {
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Edit Seating Plan',
                'outcome' => 'failed',
                'details' => 'Validation failed when editing seating plan for week ' . $week . '.',
            ]);
    
            $activityLog->save();
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $currentSeatingImage = SeatingImage::where('week', $week)->first();
        $currentImagePaths = $currentSeatingImage ? json_decode($currentSeatingImage->image_path, true) : [];

        // Get the array of existing images from the request
        $existingImages = $request->input('existing_images', []);

        // Find images that are in the database but not in the update request (i.e., removed by the user)
        $removedImages = array_diff($currentImagePaths, $existingImages);

        // Remove the images from storage
        foreach ($removedImages as $removedImage) {
            if (Storage::disk('public')->exists($removedImage)) {
                Storage::disk('public')->delete($removedImage); // Delete the image from storage
            }
        }

        $imagePathDetail = [];

        // Handle image upload if provided
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $file) {
                if ($file->isValid()) {
                    // Upload the image and store the path
                    $imagePath = $file->store('seating_images', 'public');
                    $imagePathDetail[] = $imagePath;  // Append each new image path to the array
                }
            }
        }

        // If there are existing images, add them to the array
        if ($request->has('existing_images')) {
            $existingImages = $request->input('existing_images'); 
            $imagePathDetail = array_merge($imagePathDetail, $existingImages); // Merge existing images with new ones
        }

        $seatingImage = SeatingImage::firstOrCreate(
            ['week' => $request->selected_week],  
            ['image_path' => json_encode([])]  // Default to an empty array if not found
        );
        
        // Update the record with the merged image paths as a JSON array
        $seatingImage->image_path = json_encode($imagePathDetail, JSON_UNESCAPED_SLASHES); // Store image paths as JSON
        $seatingImage->save();
    
        // Get the seating details from the database
        $existingSeatDetails = json_decode($seatingPlan->seat_detail, true) ?? [];

        // Initialize arrays to hold updated seat details and errors
        $updatedSeatDetails = [];
        $assignedTrainees = [];
        $errors = [];

        // Iterate through the seat details provided in the form
        foreach ($request->input('seat_detail') as $seatDetail) {
            // Get seat code and trainee name from the current array entry
            $seatCode = $seatDetail['seat_code'] ?? null;
            $assignedTo = $seatDetail['trainee_name'] ?? null;

            // Check if seat code is empty
            if (empty($seatCode)) {
                $errors[] = 'Seat code cannot be empty.';
                continue; 
            }

            // Check if the same trainee is assigned to multiple seats
            if (!empty($assignedTo)) {
                if (in_array($assignedTo, $assignedTrainees)) {
                    $errors[] = "Trainee $assignedTo is assigned to multiple seats.";
                } else {
                    $assignedTrainees[] = $assignedTo; // Track assigned trainees
                }
            }

            // Add to updated seat details if no errors
            if (empty($errors)) {
                $updatedSeatDetails[$seatCode] = $assignedTo; // Map seat code to trainee name
            }
        }

        // If there are any errors, redirect back with the errors
        if (!empty($errors)) {
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Edit Seating Plan',
                'outcome' => 'failed',
                'details' => 'Errors occurred when editing seating plan for week ' . $week . ' Errors: ' . implode(', ', $errors), 
            ]);        
    
            $activityLog->save();

            return redirect()->route('seating-arrangement')
                             ->withErrors($errors) 
                             ->withInput(); 
        }

        // Update the seating plan details with the updated seat details
        $seatingPlan->seat_detail = json_encode($updatedSeatDetails);
        $seatingPlan->save();

        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Edit Seating Plan',
            'outcome' => 'success',
            'details' => 'Seating Plan for Week ' . $week . ' is edited.',
        ]);        

        $activityLog->save();

        return redirect()->route('seating-arrangement')->with('success', 'Seating plan updated successfully.');

    }  
    
    public function createWeeklySeatingPlan(Request $request)
    {
        // Retrieve the selected week from the query parameters, default to the current week if not provided
        $selectedWeek = $request->input('week', now()->format('Y-\WW'));

        // Convert the selected week into a date for comparison
        $selectedWeekDate = Carbon::now()->startOfWeek()->setISODate(Carbon::now()->year, substr($selectedWeek, -2)); // Extract week number and use it

        $dateTime = new DateTime($selectedWeek);

        //get the start date and end date from the selected week
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 1);
        $startDate = $dateTime->format('d/m/Y');  // Start of the week
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 7);
        $endDate = $dateTime->format('d/m/Y');  // End of the week 

        // Fetch trainees whose internship starts before or on the selected week and ends after today
        $trainees = AllTrainee::where('internship_start', '<=', $selectedWeekDate->format('Y-m-d'))
        ->where(function($query) {
            $query->whereNull('internship_end') // If no end date is specified
                ->orWhere('internship_end', '>=', now()->format('Y-m-d')); // or internship has not ended yet
        })
        ->get();
    
        return view('create-weekly-seating-plan', compact('selectedWeek', 'startDate', 'endDate', 'trainees'));
    }

    public function removeWeeklySeatingPlan(Request $request)
    {
        // Retrieve the selected week from the query parameters, default to the current week if not provided
        $selectedWeek = $request->input('week', now()->format('Y-\WW'));
    
        // Find the seating plan for the selected week
        $seating = Seating::where('week', $selectedWeek)->first();
        $seatingImage = SeatingImage::where('week', $selectedWeek)->first();
       
        if ($seating) {
            // If seating plan exists, delete it
            $seating->delete();
            if($seatingImage){
                $currentImagePaths = json_decode($seatingImage->image_path, true);
                foreach ($currentImagePaths as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image); // Delete the image from storage
                    }
                }
            }
            SeatingImage::where('week', $selectedWeek)->delete();

            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Remove Seating Plan',
                'outcome' => 'success',
                'details' => 'Seating Plan for Week ' . $selectedWeek . ' is removed.',
            ]);        
    
            $activityLog->save();
            
            // Redirect back to the seating arrangement view with a success message
            return redirect()->route('seating-arrangement')
                ->with('success', 'Seating plan for the selected week has been successfully removed.');
        } else {
            // If no seating plan exists for the selected week, return with an error message
            return redirect()->route('seating-arrangement')
                ->with('error', 'No seating plan found for the selected week.');
        }
    }

    public function createNewWeeklySeatingPlan(Request $request)
    {
        // Validate the form input
        $validator = Validator::make($request->all(), [
            'seat_detail' => 'required|array',
            'selected_week' => 'required',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'seat_detail.required' => 'You cannot create empty seating plan.',
            'selected_week.required' => 'Please select a week.',
            'images.*.image' => 'The file must be an image.',
            'images.*.mimes' => 'Only jpeg, png, and jpg images are allowed.',
            'images.*.max' => 'The image size must not exceed 5MB.',
        ]);

        if ($validator->fails()) {
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Create New Seating Plan',
                'outcome' => 'failed',
                'details' => 'Validation failed when creating new seating plan for week ' . $request->input('selected_week'),
            ]);
    
            $activityLog->save();
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }
    
        // Initialize an array to hold the new seat details
        $newSeatDetails = [];
        $imagePaths = [];

        // Handle multiple image uploads if provided
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    // Store each image in 'seating_images' directory
                    $imagePath = $image->store('seating_images', 'public');
                    // Add image path to the array
                    $imagePaths[] = $imagePath;
                }
            }
        }

        // Retrieve existing record for the selected week or create a new one
        $seatingImage = SeatingImage::firstOrCreate(
            ['week' => $request->selected_week],  // Find record by week
            ['image_path' => json_encode([])]    // Default empty array if not found
        );

        // Update the record with the merged image paths as a JSON array
        $seatingImage->image_path = json_encode($imagePaths);
        $seatingImage->save();
    
        $errors = [];

        // Array to track assigned trainees
        $assignedTrainees = [];
        
        // Iterate through the seat details provided in the form
        foreach ($request->input('seat_detail') as $key => $value) {
            // Check if the key is related to seat code or assigned trainee ID
            if (strpos($key, 'new_') !== false) {
                // Extract the index number from the key (e.g., 0, 1)
                preg_match('/new_(\d+)_/', $key, $matches);
                $index = $matches[1];
        
                // Check if this key is for seat code or assigned trainee ID
                if (strpos($key, 'seat_code') !== false) {
                    // This is a seat code (like "B1")
                    $seatCode = $value;
        
                    // Find the corresponding assigned trainee ID (e.g., 'new_0_assigned_to')
                    $assignedToKey = "new_{$index}_assigned_to";
                    $assignedToId = $request->input("seat_detail.$assignedToKey");
        
                    // Check if the seat code is empty
                    if (empty($seatCode)) {
                        // Add an error message if the seat code is empty
                        $errors[] = "Seat code cannot be empty.";
                    }
        
                    // If the seat code is not empty, proceed with further validation
                    if (!empty($seatCode)) {
                        // If assignedToId is empty, assign an empty string to the seat
                        if (empty($assignedToId)) {
                            $newSeatDetails[$seatCode] = ""; // No trainee assigned
                        } else {
                            // Find the trainee by ID and get the name
                            $trainee = AllTrainee::find($assignedToId);
        
                            if ($trainee) {
                                // Check if the trainee has already been assigned to another seat
                                if (in_array($trainee->name, $assignedTrainees)) {
                                    $errors[] = "Trainee {$trainee->name} is assigned to multiple seats.";
                                } else {
                                    // Store trainee name instead of ID and mark trainee as assigned
                                    $newSeatDetails[$seatCode] = $trainee->name;
                                    $assignedTrainees[] = $trainee->name; // Keep track of assigned trainees
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // If there are any errors, redirect back with the errors
        if (!empty($errors)) {
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Create New Seating Plan',
                'outcome' => 'failed',
                'details' => 'Errors occurred when creating new seating plan for week ' . $request->input('selected_week') . ' Errors: ' . implode(', ', $errors), 
            ]);        
    
            $activityLog->save();

            return redirect()->route('seating-arrangement')
                             ->withErrors($errors) 
                             ->withInput(); 
        }
    
        // Get the selected week and format start and end dates
        $dateTime = new DateTime($request->input('selected_week'));
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 1);
        $startDate = $dateTime->format('d/m/Y');  // Start of the week
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 7);
        $endDate = $dateTime->format('d/m/Y');  // End of the week 
    
        // Create a new instance of the Seating model
        $seatingPlan = new Seating();
        $seatingPlan->week = $request->input('selected_week'); // Store the selected week
        $seatingPlan->seat_detail = json_encode($newSeatDetails); // Store the seat details as JSON (trainee names)
        $seatingPlan->start_date = $startDate;
        $seatingPlan->end_date = $endDate;
    
        $seatingPlan->save();

        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Create New Seating Plan',
            'outcome' => 'success',
            'details' => 'A new seating plan from ' . $startDate . ' to ' . $endDate . ' has created.',
        ]);

        $activityLog->save();
    
        return redirect()->route('seating-arrangement')->with('success', 'Seating plan created successfully.');
    }
      
}
