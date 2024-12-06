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


class SeatingController extends Controller
{
    public function index(Request $request)
    {
        
    }

    public function getWeeklyData(Request $request) {
        $week = $request->input('week'); // Get the selected week from the request
    
        // Query the database to fetch seating data for the selected week
        $seatingData = Seating::where('week', $week)->get();
    
        // You can return the data as JSON (or any other format you prefer)
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

        // Get the current week data (for initial page load)
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
        elseif($role_id ==3 ){
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
    
        // Validate the form input
        $request->validate([
            'seat_detail' => 'required|array',
            'existing_images' => 'nullable|array', 
            'new_images' => 'nullable|array', 
            'new_images.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

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
    
        // Initialize an array to hold updated seat details
        $updatedSeatDetails = [];
    
        // Iterate through the seat details provided in the form
        foreach ($request->input('seat_detail') as $key => $value) {
            // Check if the key represents a new seat entry
            if (strpos($key, 'new_') !== false && strpos($key, 'col_0') !== false) {
                // This is a new seat code
                $seatCode = $value; // This is the new seat code (like "A2")
                $assignedToKey = str_replace('col_0', 'col_1', $key); // Find the matching assigned name key
                $assignedTo = $request->input("seat_detail.$assignedToKey", null); // Get the assigned person's name
    
                // Only include the seat in the final seating plan if seat code and assignedTo are not empty
                if (!empty($seatCode) && !empty($assignedTo)) {
                    $updatedSeatDetails[$seatCode] = $assignedTo; // Map new seat code to assigned person
                }
            } elseif (strpos($key, 'new_') === false) {
                // This is an existing seat (not starting with 'new_')
                $updatedSeatDetails[$key] = $value; // Keep existing seat code and its value
            }

            
        }
    
        // Update the seating plan details with only the updated data
        $seatingPlan->seat_detail = json_encode($updatedSeatDetails);
        $seatingPlan->save();
    
        // Redirect with success message
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
    
        // Pass the selected week to the view
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
        $request->validate([
            'seat_detail' => 'required|array',
            'selected_week' => 'required', // Ensure the week is also provided
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);
    
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

                    // Check if the seat has a code (not empty)
                    if (!empty($seatCode)) {
                        // If assignedToId is empty, assign an empty string to the seat
                        if (empty($assignedToId)) {
                            $newSeatDetails[$seatCode] = ""; // No trainee assigned
                        } else {
                            // Find the trainee by ID and get the name
                            $trainee = AllTrainee::find($assignedToId);
                            if ($trainee) {
                                // Store trainee name instead of ID
                                $newSeatDetails[$seatCode] = $trainee->name;
                            }
                        }
                    }
                }
            }
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
    
        // Save the new seating plan to the database
        $seatingPlan->save();
    
        // Redirect with success message
        return redirect()->route('seating-arrangement')->with('success', 'Seating plan created successfully.');
    }
      
}
