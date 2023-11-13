<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\Seating;
use App\Models\Trainee;
use App\Models\AllTrainee;
use Illuminate\Http\Request;


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

        //finding all trainees that have not been assigned in the selected week yet.
        $trainees = AllTrainee::leftJoin('seatings', function ($join) use ($week) {
            $join->on('alltrainees.id', '=', 'seatings.trainee_id')
                 ->where('seatings.week', '=', $week);
         })
         //the trainee internship start date should earlier than this end date.
         ->whereDate('alltrainees.internship_start', '<=', $formattedEndDate)
         //exclude the trainees that already ended their internship.
         //ex. The trainee internship ends at 10 Nov, the current date is 11Nov
         ->whereDate('alltrainees.internship_end', '>=', $currentDate) 
         ->whereDate('alltrainees.internship_end', '>=', $formattedStartDate)
         ->whereNull('seatings.trainee_id')
         ->select('alltrainees.*')
         ->get();

         $traineeInfo = AllTrainee::all();

         $emptySeatCount = Seating::where('trainee_id', null)
             ->where('week', $week)
            ->where('seat_status', 'Available')
             ->count();

        // Define an array of all possible seat names
        $allSeatNames = [];
        for ($i = 1; $i <= env('TOTAL_SEATS_1ST_FLOOR'); $i++) {
            $seatNumber = str_pad($i, env('SEAT_NO_FIRST_DIGIT'), '0', STR_PAD_LEFT); // Format as CSM01 to CSM20
            $allSeatNames[] = 'CSM' . $seatNumber; 
        }

        $tSeatNames = [];
        for ($i = 1; $i <= 2; $i++) {
            $tSeatNames[] = 'T' . $i;
        }

        foreach ($tSeatNames as $tSeatName) {
            $existingSeat = Seating::where('seat_name', $tSeatName)
                ->where('week', $week)
                ->first();
        
            if (!$existingSeat) {
                // If the seat doesn't exist, create a new record
                Seating::create([
                    'seat_name' => $tSeatName,
                    'trainee_id' => null, // Set to null or a default value
                    'seat_status' => 'Available', // Set the default status
                    'week' => $week, // Set the default week
                    'start_date' => $startDate,
                ]);
            }
        }

        $roundTableSeatNames = ['Round-Table'];

        foreach ($roundTableSeatNames as $roundTableSeatName) {
            $existingSeat = Seating::where('seat_name', $roundTableSeatName)
                ->where('week', $week)
                ->first();
        
            if (!$existingSeat) {
                // If the seat doesn't exist, create a new record
                Seating::create([
                    'seat_name' => $roundTableSeatName,
                    'trainee_id' => null, // Set to null or a default value
                    'seat_status' => 'Available', // Set the default status
                    'week' => $week, // Set the default week
                    'start_date' => $startDate,
                ]);
            }
        }

        // Fetch trainee_id data from the seatings table for the selected week
        $traineeIdData = Seating::where('week', $week)
            ->select('seat_name', 'trainee_id', 'seat_status')
            ->get()
            ->keyBy('seat_name') // Organize data by seat_name
            ->toArray();

        // Create the predefined array and populate trainee_id values
        $seatingArray = [];
        foreach ($allSeatNames as $seatName) {
            $traineeId = $traineeIdData[$seatName]['trainee_id'] ?? null;
            $seatStatus = $traineeIdData[$seatName]['seat_status'] ?? 'Not Available';
            $trainee = $traineeInfo->where('id', $traineeId)->first();
            $traineeName = $trainee ? $trainee->name : 'Not Assigned';
            $seatingArray[] = [
                'seat_name' => $seatName,
                'trainee_name' => $traineeName,
                'seat_status' => $seatStatus
            ];
        }

        foreach ($tSeatNames as $seatName) {
            $traineeId = $traineeIdData[$seatName]['trainee_id'] ?? null;
            $seatStatus = $traineeIdData[$seatName]['seat_status'] ?? 'Not Available';
            $trainee = $traineeInfo->where('id', $traineeId)->first();
            $traineeName = $trainee ? $trainee->name : 'Not Assigned';
            $seatingArray[] = [
                'seat_name' => $seatName,
                'trainee_name' => $traineeName,
                'seat_status' => $seatStatus
            ];
        }

        foreach ($roundTableSeatNames as $seatName) {
            $traineeId = $traineeIdData[$seatName]['trainee_id'] ?? null;
            $seatStatus = $traineeIdData[$seatName]['seat_status'] ?? 'Not Available';
            $trainee = $traineeInfo->where('id', $traineeId)->first();
            $traineeName = $trainee ? $trainee->name : 'Not Assigned';
            $seatingArray[] = [
                'seat_name' => $seatName,
                'trainee_name' => $traineeName,
                'seat_status' => $seatStatus
            ];
        }

        return view('seating-arrange', compact('seatingArray','trainees','emptySeatCount', 'week', 'startDate', 'endDate'));
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
        Seating::where('week', $week)->update(['trainee_id' => null]);

        $trainees = AllTrainee::whereDate('internship_start', '<=', $formattedEndDate)
            ->whereDate('internship_end', '>=', $currentDate)
            ->whereDate('internship_end', '>=', $formattedStartDate)
            ->get(['id']);

        $shuffledIDs = $trainees->pluck('id')->shuffle();
        $assignedTrainees = [];

        // Get all seatings and loop through them to assign shuffled names
        $seatings = Seating::where('week', $week)->orderBy('seat_name')->get();

        $index = 0;

        //predefined seat order according to the priority (first tier > second tier > third tier)
        $first_tier_seats = Seating::where('seat_name', 'REGEXP', '^T')
            ->where('week', $week)
            ->orderBy('seat_name')
            ->pluck('seat_name')
            ->toArray();
        $second_tier_seats = Seating::where('seat_name', 'REGEXP', 'CSM[0-9]+')
            ->where('week', $week)
            ->orderBy('seat_name')
            ->pluck('seat_name')
            ->toArray();
        $third_tier_seats = Seating::where('seat_name', 'Round-Table')
            ->where('week', $week)
            ->pluck('seat_name')
            ->toArray();
        

        //assign seat for first tier seats
        foreach ($first_tier_seats as $first_tier_seat_name) {
            foreach ($seatings as $seating) {
                if ($seating->seat_name === $first_tier_seat_name) {
                    // Check if there are more shuffled names to assign
                    if ($index < count($shuffledIDs)) {
                        // Assign a shuffled name to the 'trainee_id' column
                        $seating->trainee_id = $shuffledIDs[$index];
                        $assignedTrainees[] = $shuffledIDs[$index];
                        $seating->save();
                        $index++;
                    } else {
                        // If there are no more shuffled names, you can choose to break the loop or handle it differently
                        break;
                    }
                }
            }
        }

        //assign seat for second tier seats
        foreach ($second_tier_seats as $second_tier_seat_name) {
            foreach ($seatings as $seating) {
                if ($seating->seat_name === $second_tier_seat_name) {
                    // Check if there are more shuffled names to assign
                    if ($index < count($shuffledIDs)) {
                        // Assign a shuffled name to the 'trainee_id' column
                        $seating->trainee_id = $shuffledIDs[$index];
                        $assignedTrainees[] = $shuffledIDs[$index];
                        $seating->save();
                        $index++;
                    } else {
                        // If there are no more shuffled names, you can choose to break the loop or handle it differently
                        break;
                    }
                }
            }
        }

        //assign seat for third tier seats
        foreach ($third_tier_seats as $third_tier_seat_name) {
            foreach ($seatings as $seating) {
                if ($seating->seat_name === $third_tier_seat_name) {
                    // Check if there are more shuffled names to assign
                    if ($index < count($shuffledIDs)) {
                        // Assign a shuffled name to the 'trainee_id' column
                        $seating->trainee_id = $shuffledIDs[$index];
                        $assignedTrainees[] = $shuffledIDs[$index];
                        $seating->save();
                        $index++;
                    } else {
                        // If there are no more shuffled names, you can choose to break the loop or handle it differently
                        break;
                    }
                }
            }
        }
            if (count($assignedTrainees) < count($shuffledIDs)) {
                $unassignedTrainee = array_diff($shuffledIDs->toArray(), $assignedTrainees);
                return redirect()
                    ->route('seating-arrange', ['week' => $week])
                    ->with('warning', count($unassignedTrainee) . ' trainees are not assigned to any seats. Please assign them manually.');
            }
            $seatingsArray = $seatings->values()->toArray();
            return redirect()->route('seating-arrange', ['week' => $week])->with('success', 'Seats random-assigned successfully');
    }

    public function getSeatData($seat, Request $request)
    {
        $week = $request->query('week');
        // Retrieve seat data from the "seatings" table based on the seat identifier
        $seatData = Seating::where('seat_name', $seat)->where('week', $week)->first();
        if($seatData == null){
            return response()->json(['trainee_id' => 'Not Assigned']);
        }
        $trainee = AllTrainee::where('id', $seatData->trainee_id)->first();
        $traineeName = $trainee ? $trainee->name : 'Not Assigned';

        return response()->json(['trainee_id' => $traineeName]);
    }

    public function removeSeat($seat, Request $request)
    {
        $week = $request->query('week');
        // Find the seat data in the "seatings" table
        $seatData = Seating::where('seat_name', $seat)->where('week', $week)->first();
    
        if ($seatData) {
            
            // Clear the seat by setting the trainee_id column to an empty string
            $seatData->trainee_id = null;
            $seatData->save();
    
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
        $seatData = Seating::where('seat_name', $seat)->where('week', $week)->first();
        if($seatData != null){
            $traineeId = $seatData->trainee_id;

            // Delete the row
            $seatData->delete();
        } else{
            Seating::create([
                'seat_name' => $seat,
                'trainee_id' => null,
                'seat_status' => 'Available',
                'week' => $week,
                'start_date' => $startDate,
            ]);
        }

        return redirect()->back()->with('success', 'Seat changed successfully');
    }

    public function assignSeatForTrainee($trainee_selected, $seat, Request $request)
    {
        // Find the seat data in the "seatings" table
        $week = $request->query('week');
        $seatData = Seating::where('seat_name', $seat)->where('week', $week)->first();
        $id = AllTrainee::where('name', $trainee_selected)->first()->id;
    
        if ($seatData) {
            if($seatData == null){
                return redirect()->back()->with('error', 'You cannot assign a trainee to a seat that is not available.');
            }
            
            // Assign the seat to the trainee by setting the trainee_id column to the trainee's name
            $seatData->trainee_id = $id;
            $seatData->save();
    
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
