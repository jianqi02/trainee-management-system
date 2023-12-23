<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\Seating;
use App\Models\Trainee;
use App\Models\AllTrainee;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class SeatingController extends Controller
{
    public function index(Request $request)
    {
        $week = $request->input('week', date('o-\WW')); // Default to 1 if 'week' is not provided in the query parameters.
        $dateTime = new DateTime($week);

        //get the start date and end date from the selected week
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 1);
        $startDate = $dateTime->format('d/m/Y');  // Start of the week
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 7);
        $endDate = $dateTime->format('d/m/Y');  // End of the week 

        $currentDate = date("Y-m-d");
        $formattedEndDate = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $formattedStartDate = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');

        //to get the assigned trainee id list
        $assignedTraineeIds = Seating::where('week', $week)
        ->pluck('seat_detail')
        ->map(function ($seatDetail) {
            $seatDetailArray = json_decode($seatDetail, true);
            return collect($seatDetailArray)->pluck('trainee_id')->toArray();
        })
        ->flatten()
        ->filter(function ($traineeId) {
            return $traineeId !== 'Not Assigned';
        })
        ->toArray();

        $trainees = AllTrainee::leftJoin('seatings', function ($join) use ($week) {
            $join->on('alltrainees.id', '=', \DB::raw("JSON_UNQUOTE(JSON_EXTRACT(seatings.seat_detail, '$.*.trainee_id'))"))
                ->where('seatings.week', '=', $week);
        })
        // the trainee internship start date should be earlier than this end date.
        ->whereDate('alltrainees.internship_start', '<=', $formattedEndDate)
        // exclude the trainees that already ended their internship.
        // ex. The trainee internship ends at 10 Nov, the current date is 11 Nov
        ->whereDate('alltrainees.internship_end', '>=', $currentDate)
        ->whereDate('alltrainees.internship_end', '>=', $formattedStartDate)
        //get the trainee id which is not yet assigned.
        ->whereNotIn('alltrainees.id', $assignedTraineeIds)
        ->get();
    
         $traineeInfo = AllTrainee::all();

         $emptySeatCount = 0;
 
         $get_the_seat_detail = Seating::where('week', $week)->pluck('seat_detail')->first();
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
             } 
         }
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
        if (Seating::where('week', $week)->doesntExist()) {
            // If the record doesn't exist, create a new one
            Seating::create([
                'seat_detail' => null,
                'week' => $week,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            foreach ($allSeatNames as $allSeatName) {
                // Fetch the existing seat inside the loop
                $existingSeat = Seating::where('week', $week)->first();
            
                // If the seat doesn't exist, set the seat_detail values
                if (!$existingSeat || !data_get($existingSeat->seat_detail, $allSeatName)) {
                    // If $existingSeat is null or $allSeatName doesn't exist in seat_detail
                    $seatDetail = $existingSeat ? json_decode($existingSeat->seat_detail, true) : [];
            
                    $seatDetail[$allSeatName] = [
                        'trainee_id' => 'Not Assigned',
                        'seat_status' => 'Not Available',
                    ];
            
                    $existingSeat->seat_detail = json_encode($seatDetail);
                    $existingSeat->save();
                }
            }
            
            foreach ($tSeatNames as $tSeatName) {
                // Fetch the existing seat inside the loop
                $existingSeat = Seating::where('week', $week)->first();
            
                // If the seat doesn't exist, set the seat_detail values
                if (!$existingSeat || !data_get($existingSeat->seat_detail, $tSeatName)) {
                    // If $existingSeat is null or $tSeatName doesn't exist in seat_detail
                    $seatDetail = $existingSeat ? json_decode($existingSeat->seat_detail, true) : [];
            
                    $seatDetail[$tSeatName] = [
                        'trainee_id' => 'Not Assigned',
                        'seat_status' => 'Available',
                    ];
            
                    $existingSeat->seat_detail = json_encode($seatDetail);
                    $existingSeat->save();
                }
            }

            //hardcoded seat that is not available for second floor (Level 3), add or remove if needed
            $seatDetail['T11'] = [
                'trainee_id' => 'Not Assigned',
                'seat_status' => 'Not Available',
            ];

            $seatDetail['T12'] = [
                'trainee_id' => 'Not Assigned',
                'seat_status' => 'Not Available',
            ];

            $seatDetail['T13'] = [
                'trainee_id' => 'Not Assigned',
                'seat_status' => 'Not Available',
            ];
            $seatDetail['T14'] = [
                'trainee_id' => 'Not Assigned',
                'seat_status' => 'Not Available',
            ];

            $existingSeat->seat_detail = json_encode($seatDetail);
            $existingSeat->save();
    
    
            $roundTableSeatNames = ['Round-Table'];
    
            foreach ($roundTableSeatNames as $roundTableSeatName) {
                $existingSeat = Seating::where('week', $week)->first();
            
                // If the seat doesn't exist, set the seat_detail values
                if (!$existingSeat || !data_get($existingSeat->seat_detail, $roundTableSeatName)) {
                    // If $existingSeat is null or $roundTableSeatName doesn't exist in seat_detail
                    $seatDetail = $existingSeat ? json_decode($existingSeat->seat_detail, true) : [];
            
                    $seatDetail[$roundTableSeatName] = [
                        'trainee_id' => 'Not Assigned',
                        'seat_status' => 'Available',
                    ];
            
                    $existingSeat->seat_detail = json_encode($seatDetail);
                    $existingSeat->save();
                }
            }
        }

        // Fetch trainee_id data from the seatings table for the selected week
        $seatingData = Seating::where('week', $week)
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
        
            return view('seating-arrange', compact('seatingData','trainees','emptySeatCount', 'week', 'startDate', 'endDate'));
        }
    }

    public function getRandomTrainee(Request $request)
    {

        $week = $request->query('week');
        $dateTime = new DateTime($week);

        //get the start date and end date from the selected week
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 1);
        $startDate = $dateTime->format('d/m/Y');  // Start of the week
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 7);
        $endDate = $dateTime->format('d/m/Y');  // End of the week 

        $currentDate = date("Y-m-d");
        $formattedEndDate = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        $formattedStartDate = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');

        //Clear all the record before perform the random assign method.
        $newTraineeId = 'Not Assigned';
        $seatData = Seating::where('week', $week)->first();

        if ($seatData) {
            // Decode the seat_detail JSON
            $seat = json_decode($seatData->seat_detail, true);

            // Loop over each seat and update trainee_id to "Not Assigned"
            foreach ($seat as $seatName => &$seatInfo) {
                $seatInfo['trainee_id'] = 'Not Assigned';
            }
        
            // Update the seat_detail in the database
            $seatData->update(['seat_detail' => json_encode($seat)]);
        }

        // Define the predefined tiers
        $firstTierSeats = [];
        $secondTierSeats = [];
        $thirdTierSeats = [];

        // Loop over each seat and categorize it into the tiers
        foreach ($seat as $seatName => $seatInfo) {
            if (preg_match('/^T/', $seatName)) {
                $firstTierSeats[] = $seatName;
            } elseif (preg_match('/^CSM[0-9]+/', $seatName)) {
                $secondTierSeats[] = $seatName;
            } elseif ($seatName === 'Round-Table') {
                $thirdTierSeats[] = $seatName;
            }
        }

        // Sort the seats within each tier
        sort($firstTierSeats);
        sort($secondTierSeats);
        sort($thirdTierSeats);

        // Combine the seats from all tiers (first -> second -> third tier)
        $orderedSeats = array_merge($firstTierSeats, $secondTierSeats, $thirdTierSeats);

        //shuffle all the trainee for generating a random trainee list to perform the random assign.
        $trainees = AllTrainee::whereDate('internship_start', '<=', $formattedEndDate)
            ->whereDate('internship_end', '>=', $currentDate)
            ->whereDate('internship_end', '>=', $formattedStartDate)
            ->get(['id']);
        $shuffledIDs = $trainees->pluck('id')->shuffle();
        $assignedTrainees = [];
        $index = 0;

        $seat = json_decode($seatData->seat_detail, true);

        //perform random assign function
        foreach($orderedSeats as $seatName){
            //only can assign to a seat that is available
            if($seat[$seatName]['seat_status'] == 'Available'){
                if ($index < count($shuffledIDs)) {
                    //assign the trainee to the seat and udpate the record.
                    $seat[$seatName]['trainee_id'] = $shuffledIDs[$index];
    
                    //add the trainee into another array ( for assigned only )
                    $assignedTrainees[] = $shuffledIDs[$index];
    
                    $index++;
                }
            }
        }

        // Encode the updated $seat array back to JSON and save it
        $updatedSeatDetail = json_encode($seat);
        Seating::where('week', $week)
        ->update(['seat_detail' => $updatedSeatDetail]);

        if (count($assignedTrainees) < count($shuffledIDs)) {
            $unassignedTrainee = array_diff($shuffledIDs->toArray(), $assignedTrainees);
            return redirect()
                ->route('seating-arrange', ['week' => $week])
                ->with('warning', count($unassignedTrainee) . ' trainees are not assigned to any seats. Please assign them manually.');
        }
        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Seating Arrangement',
            'outcome' => 'success',
            'details' => 'Random seating assignment is successful for week ' . $week,
        ]);

        $activityLog->save();
        return redirect()->route('seating-arrange', ['week' => $week])->with('success', 'Seats random-assigned successfully');
    }

    public function getSeatData($seat, Request $request)
    {
        $week = $request->query('week');
        // Retrieve seat data from the "seatings" table based on the seat identifier
        $seatData = Seating::where('week', $week)->pluck('seat_detail')->first();
        $seatDetail = json_decode($seatData, true);

        if($seatDetail[$seat] == null){
            return response()->json(['trainee_id' => 'Not Assigned']);
        }

        $trainee = AllTrainee::find($seatDetail[$seat]['trainee_id']);
        $traineeName = $trainee ? $trainee->name : 'Not Assigned';

        return response()->json(['trainee_id' => $traineeName]);
    }

    public function removeSeat($seat, Request $request)
    {
        $week = $request->query('week');
        // Find the seat data in the "seatings" table
        $seatData = Seating::where('week', $week)->pluck('seat_detail')->first();
        $seatDetail = json_decode($seatData, true);
    
        if ($seatDetail[$seat]) {
            //get the trainee name
            $traineeName = AllTrainee::where('id', $seatDetail[$seat]['trainee_id'])->pluck('name')->first();
            
            // Clear the seat by setting the trainee_id column to an empty string
            $seatDetail[$seat]['trainee_id'] = 'Not Assigned';
            Seating::where('week', $week)->update(['seat_detail' => json_encode($seatDetail)]);
    
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Seating Arrangement',
                'outcome' => 'success',
                'details' => 'Removed trainee ' . $traineeName . ' from seat ' . $seat. ' at week ' . $week,
            ]);
    
            $activityLog->save();

            return redirect()->back()->with('success', 'Trainee removed successfully');
        }
    
        return redirect()->back()->with('error', 'Seat not found');
    }

    public function changeOwnership($seat, Request $request)
    {
        $week = $request->query('week');
        $dateTime = new DateTime($week);
        $dateTime->setISODate($dateTime->format('o'), $dateTime->format('W'), 1);
        $startDate = $dateTime->format('d/m/Y');  // Start of the week
        // Retrieve seat data from the "seatings" table based on the seat identifier
        $seatData = Seating::where('week', $week)->pluck('seat_detail')->first();
        if ($seatData) {
            $seatDetail = json_decode($seatData, true);
    
            if (isset($seatDetail[$seat])) { // Check if the key exists in the array
                if ($seatDetail[$seat]['seat_status'] == 'Available') {
                    $seatDetail[$seat]['trainee_id'] = 'Not Assigned';
                    $seatDetail[$seat]['seat_status'] = 'Not Available';
                } else {
                    $seatDetail[$seat]['seat_status'] = 'Available';
                }
    
                // Update the seat_detail column in the database
                Seating::where('week', $week)->update(['seat_detail' => json_encode($seatDetail)]);
    
                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Seating Arrangement',
                    'outcome' => 'success',
                    'details' => 'Changed the status of seat ' . $seat . ' at week ' . $week,
                ]);
        
                $activityLog->save();

                return redirect()->back()->with('success', 'Seat changed successfully');
            }
        }
    
        return redirect()->back()->with('error', 'Seat not found');
    }

    public function assignSeatForTrainee($trainee_selected, $seat, Request $request)
    {
        // Find the seat data in the "seatings" table
        $week = $request->query('week');
        $seatData = Seating::where('week', $week)->pluck('seat_detail')->first();
        $seatDetail = json_decode($seatData, true);
        
        $id = AllTrainee::where('name', $trainee_selected)->first()->id;
    
        if ($seatDetail[$seat]) {
            if($seatDetail[$seat]['seat_status'] == 'Not Available'){
                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Seating Arrangement',
                    'outcome' => 'failed',
                    'details' => 'Trying to assign a trainee to a seat that is not available at week ' . $week,
                ]);
        
                $activityLog->save();
                return redirect()->back()->with('error', 'You cannot assign a trainee to a seat that is not available.');
            }
            
            // Assign the seat to the trainee by setting the trainee_id column to the trainee's name
            $seatDetail[$seat]['trainee_id'] = $id;
            Seating::where('week', $week)->update(['seat_detail' => json_encode($seatDetail)]);

            $traineeName = AllTrainee::where('id', $id)->pluck('name')->first();
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Seating Arrangement',
                'outcome' => 'success',
                'details' => 'Assigned trainee ' . $traineeName. ' to seat ' . $seat . ' at week ' . $week,
            ]);
    
            $activityLog->save();
    
            return redirect()->back()->with('success', 'Seat assigned successfully');
        }
    
        return redirect()->back()->with('error', 'Seat not found');
    }

    public function getWeeklyData(Request $request) {
        $week = $request->input('week'); // Get the selected week from the request
    
        // Query the database to fetch seating data for the selected week
        $seatingData = Seating::where('week', $week)->get();
    
        // You can return the data as JSON (or any other format you prefer)
        return response()->json($seatingData);
    }
}
