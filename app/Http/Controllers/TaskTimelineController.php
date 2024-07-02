<?php

namespace App\Http\Controllers;

use DateTime;
use DatePeriod;
use DateInterval;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use App\Models\Trainee;
use App\Models\AllTrainee;
use App\Models\Supervisor;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\TaskTimeline;
use Illuminate\Http\Request;
use App\Models\TraineeAssign;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskTimelineController extends Controller
{
    public function index($sort = null, $order = null, $traineeID = null,){
        $user = Auth::user();
        $role = $user->role_id;

        if($traineeID == null){
            //error handling
            if($role == 2){
                return redirect()->back();
            }
            $traineeID = Trainee::where('sains_email', $user->email)->pluck('id')->first();
        }
        else{
            //broken access handling
            if($role == 3){
                return redirect()->back();
            }

            $trainee_name = Trainee::where('id', $traineeID)->pluck('name')->first();
            $trainee_ref_id = AllTrainee::where('name', $trainee_name)->pluck('id')->first();

            //prevent other supervisor to access the task for the trainee that is not assigned to them.
            $supervisorID = Supervisor::where('sains_email', Auth::user()->email)->pluck('id')->first();
            if(TraineeAssign::where('trainee_id', $trainee_ref_id)->where('assigned_supervisor_id', $supervisorID)->first() == null){
                return redirect()->back()->with('error', 'You do not have access to view this page.');
            }
        }

        $tasks = TaskTimeline::where('trainee_id', $traineeID)->get();

        if ($sort) {
            // Perform sorting based on the $sort parameter
            switch ($sort) {
                case 'priority':
                    $tasks = $tasks->sortBy(function ($task) {
                        // Define the custom sorting order
                        $priorityOrder = ['High' => 1, 'Medium' => 2, 'Low' => 3];
                
                        // Return the corresponding order for each task's status
                        return $priorityOrder[$task->task_priority];
                    });
                    break;
                case 'status':
                    $tasks = $tasks->sortBy(function ($task) {
                        // Define the custom sorting order
                        $statusOrder = ['Not Started' => 1, 'Ongoing' => 2, 'Postponed' => 3, 'Completed' => 4];
                
                        // Return the corresponding order for each task's status
                        return $statusOrder[$task->task_status];
                    });
                    break;
                case 'end-date':
                    $tasks = $tasks->sortBy('task_end_date');
                    break;
                case 'start-date':
                    $tasks = $tasks->sortBy('task_start_date');
                    break;
            }

            if ($order === 'desc') {
                $tasks = $tasks->reverse();
            }
        }
        //for trainee
        if($role == 3){
            return view('trainee-task-timeline', compact('tasks'));
        }
        //for supervisor 
        elseif($role == 2){
            return view('sv-view-trainee-task-timeline', compact('tasks', 'traineeID'));
        }
        // for admin
        else{
            return view('admin-view-trainee-task-timeline', compact('tasks', 'traineeID'));
        }
    }

    public function traineeTaskTimeline(){
        $user = Auth::user();

        //get trainee id
        $trainee_id = Trainee::where('sains_email', $user->email)->pluck('id')->first();

        //get all the task for this trainee
        $tasks = TaskTimeline::where('trainee_id', $trainee_id)->get();

        return view('trainee-task-timeline', compact('tasks'));
    }

    public function svViewTraineeTaskTimeline($traineeID){
        //get all the task for this trainee
        $tasks = TaskTimeline::where('trainee_id', $traineeID)->get();

        $trainee_name = Trainee::where('id', $traineeID)->pluck('name')->first();
        $trainee_ref_id = AllTrainee::where('name', $trainee_name)->pluck('id')->first();

        //prevent other supervisor to access the task for the trainee that is not assigned to them.
        $supervisorID = Supervisor::where('sains_email', Auth::user()->email)->pluck('id')->first();
        if(TraineeAssign::where('trainee_id', $trainee_ref_id)->where('assigned_supervisor_id', $supervisorID)->first() == null){
            return redirect()->back()->with('error', 'You do not have access to view this page.');
        }

        return view('sv-view-trainee-task-timeline', compact('tasks', 'traineeID'));
    }

    public function adminViewTraineeTaskTimeline($traineeID){
        //get all the task for this trainee
        $tasks = TaskTimeline::where('trainee_id', $traineeID)->get();

        //get the trainee name
        $traineeName = Trainee::where('id', $traineeID)->pluck('name')->first();

        return view('admin-view-trainee-task-timeline', compact('tasks', 'traineeID', 'traineeName'));
    }

    public function traineeAddNewTask(Request $request){
        $user = Auth::user();

        //get trainee id
        $trainee_id = Trainee::where('sains_email', $user->email)->pluck('id')->first();

        $startDate = new DateTime($request->input('startDate'));
        $endDate = new DateTime($request->input('endDate'));

        //terminate the function when the user chooses invalid date (end date < start date)
        if($endDate < $startDate){
            $activityLog = new ActivityLog([
                'username' => $user->name,
                'action' => 'Add New Task',
                'outcome' => 'failed',
                'details' => 'Invalid date chosen.',
            ]);
    
            $activityLog->save();
            return redirect()->route('trainee-task-timeline')->with('warning', 'Failed to add new task! Invalid date chosen!');
        }

        // to check is the duration of the task is too long.
        $parsed_start = Carbon::parse($startDate);
        $parsed_end = Carbon::parse($endDate);

        if($parsed_end->diffInDays($parsed_start) > 180){
            $activityLog = new ActivityLog([
                'username' => $user->name,
                'action' => 'Add New Task',
                'outcome' => 'failed',
                'details' => 'Duration of the task is over 180 days.',
            ]);
    
            $activityLog->save();
            return redirect()->route('trainee-task-timeline')->with('warning', 'The duration of the task is too long!');
        }

        //input validation
        $validator = Validator::make($request->all(), [
            'taskName' => ['required', 'string', 'max:100'],
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            $activityLog = new ActivityLog([
                'username' => $user->name,
                'action' => 'Add New Task',
                'outcome' => 'failed',
                'details' => 'Task name is too long.',
            ]);
    
            $activityLog->save();
            return redirect()->back()->with('warning', 'The task name is too long. Please try again.');
        }

        //add a new task to DB
        $task = new TaskTimeline();
        $task->trainee_id = $trainee_id;
        $task->task_name = $request->input('taskName');
        $task->task_start_date = $request->input('startDate');
        $task->task_end_date = $request->input('endDate');
        $task->task_status = 'Not Started';
        $task->task_priority = $request->input('priority');
        $taskDetail = [
            "Description" => "Put your description here.",
        ];
        $task->task_detail = json_encode($taskDetail);
        $task->save();

        $activityLog = new ActivityLog([
            'username' => $user->name,
            'action' => 'Add New Task',
            'outcome' => 'success',
            'details' => 'Added task: ' . $task->task_name,
        ]);

        $activityLog->save();

        return redirect()->route('trainee-task-timeline')->with('success', 'New task added.');
    }

    public function traineeAddNewTaskSV(Request $request, $traineeID){
        $startDate = new DateTime($request->input('startDate'));
        $endDate = new DateTime($request->input('endDate'));

        $user_role = Auth::user()->role_id;

        //terminate the function when the user chooses invalid date (end date < start date)
        if($endDate < $startDate){
            if($user_role == 1){            
                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Add New Task',
                    'outcome' => 'failed',
                    'details' => 'Invalid date chosen.',
                ]);
        
                $activityLog->save();
                return redirect()->route('admin-view-trainee-task-timeline', $traineeID)->with('warning', 'Failed to add new task! Invalid date chosen!');
            }
            elseif($user_role == 2){
                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Add New Task',
                    'outcome' => 'failed',
                    'details' => 'Invalid date chosen.',
                ]);
        
                $activityLog->save();
                return redirect()->route('sv-view-trainee-task-timeline', $traineeID)->with('warning', 'Failed to add new task! Invalid date chosen!');
            }          
        }

        //to check if the task is too long in duration.
        $parsed_start = Carbon::parse($startDate);
        $parsed_end = Carbon::parse($endDate);

        if($parsed_end->diffInDays($parsed_start) > 180){
            if($user_role == 1){
                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Add New Task',
                    'outcome' => 'failed',
                    'details' => 'The duration of the task is more than 180 days.',
                ]);
        
                $activityLog->save();
                return redirect()->route('admin-view-trainee-task-timeline', $traineeID)->with('warning', 'The duration of the task is too long!');
            }
            elseif($user_role == 2){
                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Add New Task',
                    'outcome' => 'failed',
                    'details' => 'The duration of the task is more than 180 days.',
                ]);
        
                $activityLog->save();
                return redirect()->route('sv-view-trainee-task-timeline', $traineeID)->with('warning', 'The duration of the task is too long!');
            }
            
        }

        //input validation
        $validator = Validator::make($request->all(), [
            'taskName' => ['required', 'string', 'max:100'],
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Add New Task',
                'outcome' => 'failed',
                'details' => 'The task name is too long.',
            ]);
    
            $activityLog->save();
            return redirect()->back()->with('warning', 'The task name is too long. Please try again.');
        }

        //add a new task to DB
        $task = new TaskTimeline();
        $task->trainee_id = $traineeID;
        $task->task_name = $request->input('taskName');
        $task->task_start_date = $request->input('startDate');
        $task->task_end_date = $request->input('endDate');
        $task->task_status = 'Not Started';
        $task->task_priority = $request->input('priority');
        $taskDetail = [
            "Description" => "Put your description here.",
        ];
        $task->task_detail = json_encode($taskDetail);
        $task->save();
        
        //get the trainee name
        $traineeName = Trainee::where('id' , $traineeID)->pluck('name')->first();

        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Add New Task',
            'outcome' => 'success',
            'details' => 'Added Task: ' . $task->task_name . ' to trainee ' . $traineeName,
        ]);

        $activityLog->save();

        if($user_role == 2){
            return redirect()->route('sv-view-trainee-task-timeline', $traineeID)->with('success', 'New task added.');
        }
        elseif($user_role == 1){
            return redirect()->route('admin-view-trainee-task-timeline', $traineeID)->with('success', 'New task added.');
        }
       
    }

    public function traineeEditTask(Request $request, $taskID){

        $startDate = new DateTime($request->input('startDate'));
        $endDate = new DateTime($request->input('endDate'));

        //terminate the function when the user chooses invalid date (end date < start date)
        if($endDate < $startDate){
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Edit Task',
                'outcome' => 'failed',
                'details' => 'Invalid date chosen.',
            ]);
    
            $activityLog->save();
            return redirect()->route('trainee-task-detail', $taskID)->with('warning', 'Failed to change the task! Invalid date chosen!');
        }

        // to check is the duration of the task is too long.
        $parsed_start = Carbon::parse($startDate);
        $parsed_end = Carbon::parse($endDate);

        if($parsed_end->diffInDays($parsed_start) > 180){
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Edit Task',
                'outcome' => 'failed',
                'details' => 'The duration of the task is more than 180 days.',
            ]);
    
            $activityLog->save();
            return redirect()->route('trainee-task-detail', $taskID)->with('warning', 'The duration of the task is too long!');
        }

        //get the status 
        $status = $request->input('status');

        //input validation
        $validator = Validator::make($request->all(), [
            'taskName' => ['required', 'string', 'max: 100'],
            'taskDescription' => ['required', 'string', 'max: 1000'],
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Edit Task',
                'outcome' => 'failed',
                'details' => 'The task name or task description is too long.',
            ]);
    
            $activityLog->save();
            return redirect()->back()->with('warning', 'The task name or task description is too long. Please try again.');
        }

        //add a new task to DB
        $task = TaskTimeline::where('id', $taskID)->first();
        $task->task_name = $request->input('taskName');
        $task->task_start_date = $startDate;
        $task->task_end_date = $endDate;
        $task->task_status = $status;
        $task->task_priority = $request->input('priority');
        $taskDetail = [
            "Description" => $request->input('taskDescription'),
        ];
        $task->task_detail = json_encode($taskDetail);

        $task->save();

        //get trainee name & task name for reference
        $traineeName = Trainee::where('id', $task->trainee_id)->pluck('name')->first();
        $taskName = $task->task_name;
        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Edit Task',
            'outcome' => 'success',
            'details' => 'Edited Task: ' . $task->task_name . ', Trainee: ' . $traineeName,
        ]);

        $activityLog->save();
        
        $ref = Trainee::where('id', $task->trainee_id)->pluck('name')->first();
        $traineeID = AllTrainee::where('name', $ref)->pluck('id')->first();

        //use the id in list to search for the trainee's supervisor
        $assigned_supervisor_ids = TraineeAssign::where('trainee_id', $traineeID)
            ->pluck('assigned_supervisor_id');

        $user_role = Auth::user()->role_id;

        if($user_role == 3){
            //send a notification to this trainee's supervisor when the trainee mark his or her task as Completed. 
            if ($status == 'Completed') {
                foreach ($assigned_supervisor_ids as $assigned_supervisor_id) {
                    // Check if a similar notification already exists
                    $existingNotification = Notification::where('type', 'task completed')
                        ->where('notifiable_type', 'App\Models\Trainee')
                        ->where('notifiable_id', $assigned_supervisor_id)
                        ->where('data', json_encode([
                            'data' => 'Your trainee ' . $traineeName . ' has completed task ' . $taskName,
                        ]))
                        ->first();

                    // If the notification doesn't exist, create and save a new one
                    if (!$existingNotification) {
                        $notification = new Notification();
                        $notification->id = Uuid::uuid4(); // Generate a UUID for the id
                        $notification->type = 'task completed';
                        $notification->notifiable_type = 'App\Models\Trainee';
                        $notification->notifiable_id = $assigned_supervisor_id;
                        $notification->data = json_encode([
                            'data' => 'Your trainee ' . $traineeName . ' has completed task ' . $taskName,
                        ]);
                        $notification->save(); // Save the notification to the database

                        $supervisor_name = Supervisor::where('id', $assigned_supervisor_id)->pluck('name')->first();
                    }
                }
            }
        }
        return redirect()->route('trainee-task-detail', $taskID)->with('success', 'New task added.');
    }

    public function showTaskDetailForTrainee($taskID){
        $task = TaskTimeline::where('id', $taskID)->first();
        if($task == null){
            return redirect()->back();
        }
        $startDate = new DateTime($task->task_start_date);
        $endDate = new DateTime($task->task_end_date);
    
        // Define a function to check if a given date is a Saturday or Sunday
        $isWeekend = function($date) {
            return $date->format('N') >= 6; // 6 is Saturday, 7 is Sunday
        };
    
        // Create a DateInterval for 1 day
        $interval = new DateInterval('P1D');
    
        // Create a DatePeriod, excluding weekends
        $dateRange = new DatePeriod($startDate, $interval, $endDate->modify('+1 day'));
        $dateRange = array_filter(iterator_to_array($dateRange), function($date) use ($isWeekend) {
            return !$isWeekend($date);
        });
    
        $comments = json_decode($task->task_overall_comment, true);
        $timelineData = json_decode($task->timeline, true);
    
        $user_role = Auth::user()->role_id;
    
        if ($user_role == 3) {
            $trainee_id = Trainee::where('sains_email', Auth::user()->email)->pluck('id')->first();

            //prevent trainee to access other trainee's task.
            if(TaskTimeline::where('id', $taskID)->pluck('trainee_id')->first() != $trainee_id){
                return redirect()->back()->with('error', 'You do not have access to view this page.');
            }
            return view('trainee-task-detail', compact('task', 'dateRange', 'timelineData', 'comments'));
        } elseif ($user_role == 2) {
            $trainee_id = TaskTimeline::where('id', $taskID)->pluck('trainee_id')->first();
            $trainee_ref_id = AllTrainee::where('name', Trainee::where('id', $trainee_id)->pluck('name')->first())->pluck('id')->first();

            //prevent other supervisor to access the task for the trainee that is not assigned to them.
            $supervisorID = Supervisor::where('sains_email', Auth::user()->email)->pluck('id')->first();
            if(TraineeAssign::where('trainee_id', $trainee_ref_id)->where('assigned_supervisor_id', $supervisorID)->first() == null){
                return redirect()->back()->with('error', 'You do not have access to view this page.');
            }
            return view('sv-view-trainee-task-detail', compact('task', 'dateRange', 'timelineData', 'comments', 'trainee_id'));
        } elseif ($user_role == 1 ){
            //get the trainee name and id
            $trainee_id = TaskTimeline::where('id', $taskID)->pluck('trainee_id')->first();
            $traineeName = Trainee::where('id', $trainee_id)->pluck('name')->first();
            return view('admin-view-trainee-task-detail', compact('task', 'dateRange', 'timelineData', 'comments', 'trainee_id', 'traineeName'));
        }
    }
    

    public function showDailyTaskDetailForTrainee($date, $taskID){
        $dailyTask = TaskTimeline::where('id', $taskID)->first();

        $taskName = $dailyTask->task_name;

        if($dailyTask == null){
            return redirect()->back();
        }
    
        $timelineData = json_decode($dailyTask->timeline, true);

        //the date must valid
        $rules = [
            'date' => 'required|date|date_format:Y-m-d',
        ];

        //check the date
        $data = ['date' => $date];
        $validator = Validator::make($data, $rules);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back();
        }

        // Convert the date string to a DateTime object
        $dateTime = new DateTime($date);

        // Get the day of the week (e.g., Monday, Tuesday, etc.)
        $dayOfWeek = $dateTime->format('l');
    
        if(isset($timelineData[$date])){
            // Get the specific date's task detail
            $taskDetail = $timelineData[$date];
        }
        else{
            if($date < $dailyTask->task_start_date || $date > $dailyTask->task_end_date){
                return redirect()->back();
            }
            else{
                $taskDetail = null;
            }
        }
    
        $user_role = Auth::user()->role_id;
        if($user_role == 3){
            $trainee_id = Trainee::where('sains_email', Auth::user()->email)->pluck('id')->first();

            //prevent trainee to access other trainee's task.
            if(TaskTimeline::where('id', $taskID)->pluck('trainee_id')->first() != $trainee_id){
                return redirect()->back()->with('error', 'You do not have access to view this page.');
            }
            return view('trainee-daily-task-detail', compact('date', 'taskDetail', 'taskID', 'dayOfWeek', 'taskName'));
        }
        elseif($user_role == 2){
            $trainee_id = TaskTimeline::where('id', $taskID)->pluck('trainee_id')->first();
            $trainee_ref_id = AllTrainee::where('name', Trainee::where('id', $trainee_id)->pluck('name')->first())->pluck('id')->first();

            //prevent other supervisor to access the task for the trainee that is not assigned to them.
            $supervisorID = Supervisor::where('sains_email', Auth::user()->email)->pluck('id')->first();
            if(TraineeAssign::where('trainee_id', $trainee_ref_id)->where('assigned_supervisor_id', $supervisorID)->first() == null){
                return redirect()->back()->with('error', 'You do not have access to view this page.');
            }
            return view('sv-view-trainee-daily-task-detail', compact('date', 'taskDetail', 'taskID', 'dayOfWeek', 'trainee_id', 'taskName'));
        }
        elseif($user_role == 1){
            //get the trainee id and trainee name
            $trainee_id = TaskTimeline::where('id', $taskID)->pluck('trainee_id')->first();
            $traineeName = Trainee::where('id', $trainee_id)->pluck('name')->first();
            return view('admin-view-trainee-daily-task-detail', compact('date', 'taskDetail', 'taskID', 'dayOfWeek', 'trainee_id', 'traineeName', 'taskName'));
        }
       
    }

    public function traineeEditDailyTask(Request $request, $date, $taskID){
        // find the daily task to be edited.
        $task = TaskTimeline::where('id', $taskID)->first();
        $timeline = json_decode($task->timeline, true);

        //input validation
        $validator = Validator::make($request->all(), [
            'taskName' => ['required', 'string', 'max:100'],
            'taskDescription' => ['required', 'string', 'max:1000'],
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Edit Daily Task',
                'outcome' => 'failed',
                'details' => 'The task name or task description is too long.',
            ]);
    
            $activityLog->save();
            return redirect()->back()->with('warning', 'The task name or task description is too long. Please try again.');
        }

        if(isset($timeline[$date])){
            $timeline[$date]['Name'] = $request->input('taskName');
            $timeline[$date]['Description'] = $request->input('taskDescription');
            $timeline[$date]['Status'] = $request->input('status');
    
            // Update the timeline in the database
            $task->timeline = json_encode($timeline);
            $task->save();

            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Edit Daily Task',
                'outcome' => 'success',
                'details' => 'Task: ' . $task->task_name . ', Date: ' . $date . ' , Daily task name: ' . $timeline[$date]['Name'] . ' , Daily task description: ' . $timeline[$date]['Description'] . ' , Daily task status: ' . $timeline[$date]['Status'],
            ]);
    
            $activityLog->save();
    
            return redirect()->route('trainee-daily-task-detail', ['date' => $date, 'taskID' => $taskID])->with('success', 'Task edited.');  
        } else {
            // add new information into $timeline[$date]
            $timeline[$date] = [
                'Name' => $request->input('taskName'),
                'Description' => $request->input('taskDescription'),
                'Status' => $request->input('status')
            ];
    
            // Update the timeline in the database
            $task->timeline = json_encode($timeline);
            $task->save();
            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Edit Daily Task',
                'outcome' => 'success',
                'details' => 'Task: ' . $task->task_name . ', Date: ' . $date . ' , Daily task name: ' . $timeline[$date]['Name'] . ' , Daily task description: ' . $timeline[$date]['Description'] . ' , Daily task status: ' . $timeline[$date]['Status'],
            ]);
    
            $activityLog->save();
            return redirect()->route('trainee-daily-task-detail', ['date' => $date, 'taskID' => $taskID])->with('success', 'Task added.');
        }
    }

    public function taskTimelineOverallComment(Request $request, $taskID){
        $task = TaskTimeline::find($taskID);
        $comment = json_decode($task->task_overall_comment, true);

        $user_role = Auth::user()->role_id;

        if($user_role == 3){
            //input validation
            $validator = Validator::make($request->all(), [
                'comment' => ['required', 'string', 'max:500'],
            ]);

            // Check if the validation fails
            if ($validator->fails()) {
                // Extract error messages
                $errorMessages = implode(' ', $validator->errors()->all());

                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Overall Note',
                    'outcome' => 'failed',
                    'details' => $errorMessages,
                ]);
        
                $activityLog->save();

                return redirect()->back()->with('warning', 'Invalid note. Please Try Again.');
            }
            $comment['Trainee'] = $request->input('comment');

            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Overall Note',
                'outcome' => 'success',
                'details' => 'Comment from trainee changed: ' . $request->input('comment'),
            ]);
    
            $activityLog->save();
        }
        elseif($user_role == 2){
            $sv_name = Auth::user()->name;
            $trainee_id = TaskTimeline::where('id', $taskID)->pluck('trainee_id')->first();
            $trainee_name = Trainee::where('id', $trainee_id)->pluck('name')->first();
            //input validation
            $validator = Validator::make($request->all(), [
                'comment' => ['required', 'string', 'max:500'],
            ]);

            // Check if the validation fails
            if ($validator->fails()) {
                // Extract error messages
                $errorMessages = implode(' ', $validator->errors()->all());

                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Overall Note',
                    'outcome' => 'failed',
                    'details' => $errorMessages,
                ]);
        
                $activityLog->save();
                return redirect()->back()->with('warning', 'Invalid note. Please try again!');
            }
            $comment['Supervisor'] = $request->input('comment');

            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Overall Note',
                'outcome' => 'success',
                'details' => 'Note from supervisor changed: ' . $request->input('comment'),
            ]);
    
            $activityLog->save();

            $task_name = TaskTimeline::where('id', $taskID)->pluck('task_name')->first();

            $notification = new Notification();
            $notification->id = Uuid::uuid4(); // Generate a UUID for the id
            $notification->type = 'signed_logbook';
            $notification->notifiable_type = 'App\Models\Supervisor';
            $notification->notifiable_id = $trainee_id;
            $notification->data = json_encode([
                'data' => 'Your supervisor ' . $sv_name . ' has added a note to the task ' . $task_name . '.',
                'name' => $trainee_name,
            ]);
            $notification->save(); // Save the notification to the database
        }
        elseif($user_role == 1){
            //input validation
            $validator = Validator::make($request->all(), [
                'commentSV' => ['required', 'string', 'max:500'],
                'commentTR' => ['required', 'string', 'max:500'],
            ]);

            // Check if the validation fails
            if ($validator->fails()) {
                // Extract error messages
                $errorMessages = implode(' ', $validator->errors()->all());

                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Overall Note',
                    'outcome' => 'failed',
                    'details' => $errorMessages,
                ]);
        
                $activityLog->save();
                return redirect()->back()->with('warning', 'Invalid note. Please try again!');
            }
            $comment['Supervisor'] = $request->input('commentSV');
            $comment['Trainee'] = $request->input('commentTR');

            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Overall Note',
                'outcome' => 'success',
                'details' => 'Note from supervisor changed: ' . $request->input('commentSV') . ' , Comment from trainee changed: ' . $request->input('commentTR'),
            ]);
    
            $activityLog->save();
        }

        $task->task_overall_comment = json_encode($comment);
        $task->save();

        return redirect()->route('trainee-task-detail', ['taskID' => $taskID])->with('success', 'Comment has changed successfully.');
    }

    public function taskTimelineDailyComment(Request $request, $date, $taskID){
        $task = TaskTimeline::where('id', $taskID)->first();
        $timeline = json_decode($task->timeline, true);
        $user_role = Auth::user()->role_id;
        if(isset($timeline[$date])){
            if($user_role == 2){
                //input validation
                $validator = Validator::make($request->all(), [
                    'comment' => ['required', 'string', 'max:500'],
                ]);

                // Check if the validation fails
                if ($validator->fails()) {
                    // Extract error messages
                    $errorMessages = implode(' ', $validator->errors()->all());

                    $activityLog = new ActivityLog([
                        'username' => Auth::user()->name,
                        'action' => 'Dail Task Note',
                        'outcome' => 'failed',
                        'details' => $errorMessages,
                    ]);
            
                    $activityLog->save();
                    return redirect()->back()->with('warning', 'Invalid note. Please try again.');
                }
                $timeline[$date]['Supervisor'] = $request->input('comment');
                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Daily Task Note',
                    'outcome' => 'success',
                    'details' => 'Note from supervisor changed: ' . $request->input('comment'),
                ]);
        
                $activityLog->save();
            }
            elseif($user_role == 3){
                //input validation
                $validator = Validator::make($request->all(), [
                    'comment' => ['required', 'string', 'max:500'],
                ]);

                // Check if the validation fails
                if ($validator->fails()) {
                    // Extract error messages
                    $errorMessages = implode(' ', $validator->errors()->all());

                    $activityLog = new ActivityLog([
                        'username' => Auth::user()->name,
                        'action' => 'Dail Task Note',
                        'outcome' => 'failed',
                        'details' => $errorMessages,
                    ]);
            
                    $activityLog->save();
                    return redirect()->back()->with('warning', 'Invalid note. Please try again!');
                }
                $timeline[$date]['Trainee'] = $request->input('comment');
                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Daily Task Note',
                    'outcome' => 'success',
                    'details' => 'Note from trainee changed: ' . $request->input('comment'),
                ]);
        
                $activityLog->save();
            }
            elseif($user_role == 1){
                //input validation
                $validator = Validator::make($request->all(), [
                    'commentSV' => ['required', 'string', 'max:500'],
                    'commentTR' => ['required', 'string', 'max:500'],
                ]);

                // Check if the validation fails
                if ($validator->fails()) {
                    // Extract error messages
                    $errorMessages = implode(' ', $validator->errors()->all());

                    $activityLog = new ActivityLog([
                        'username' => Auth::user()->name,
                        'action' => 'Dail Task Note',
                        'outcome' => 'failed',
                        'details' => $errorMessages,
                    ]);
            
                    $activityLog->save();
                    return redirect()->back()->with('warning', 'Invalid note. Please try again!');
                }
                $timeline[$date]['Supervisor'] = $request->input('commentSV');
                $timeline[$date]['Trainee'] = $request->input('commentTR');

                $activityLog = new ActivityLog([
                    'username' => Auth::user()->name,
                    'action' => 'Daily Task Note',
                    'outcome' => 'success',
                    'details' => 'Note from supervisor changed: ' . $request->input('commentSV') . ' , Comment from trainee changed: ' . $request->input('commentTR'),
                ]);
        
                $activityLog->save();
            }
    
            // Update the timeline in the database
            $task->timeline = json_encode($timeline);
            $task->save();
    
            return redirect()->route('trainee-daily-task-detail', ['date' => $date, 'taskID' => $taskID])->with('success', 'Comment changed.');  
        } else {
            if($user_role == 2){
                // add supervisor comment into $timeline[$date]
                $timeline[$date] = [
                    'Supervisor' => $request->input('comment'),
                ];
            }
            elseif($user_role == 3){
                // add trainee comment into $timeline[$date]
                $timeline[$date] = [
                    'Trainee' => $request->input('comment'),
                ];
            }
            // Update the timeline in the database
            $task->timeline = json_encode($timeline);
            $task->save();

            $activityLog = new ActivityLog([
                'username' => Auth::user()->name,
                'action' => 'Daily Task Note',
                'outcome' => 'success',
                'details' => 'Note added: ' . $request->input('comment'),
            ]);
    
            $activityLog->save();
            return redirect()->route('trainee-daily-task-detail', ['date' => $date, 'taskID' => $taskID])->with('success', 'Comment added.');
        }
    }

    public function deleteTask($taskID){
        $targetTask = TaskTimeline::where('id',$taskID)->first();
        $traineeID = $targetTask->trainee_id;

        $activityLog = new ActivityLog([
            'username' => Auth::user()->name,
            'action' => 'Task Deletion',
            'outcome' => 'success',
            'details' => 'Task Deleted: ' . $targetTask->task_name,
        ]);

        $activityLog->save();

        $targetTask->delete();

        $user_role = Auth::user()->role_id;

        if($user_role == 3){
            return redirect()->route('trainee-task-timeline')->with('success', 'Task deleted.');
        }
        //admin and supervisor will use same page.
        elseif($user_role == 2){
            return redirect()->route('sv-view-trainee-task-timeline', ['traineeID' => $traineeID])->with('success', 'Task deleted.');
        }
        elseif($user_role == 1 ){
            return redirect()->route('admin-view-trainee-task-timeline', ['traineeID' => $traineeID])->with('success', 'Task deleted.');
        }
    }

    public function applyFilter(Request $request){
        $user = Auth::user();
        $trainee_id = Trainee::where('sains_email', $user->email)->pluck('id')->first();
        $search_input = $request->input('search');
        $start_date_input = $request->input('taskStartDate');
        $end_date_input = $request->input('taskEndDate');
        $priority_input = $request->input('taskPriority');
        $status_input = $request->input('taskStatus');

        //filter the task based on the input
        $query = TaskTimeline::where('trainee_id', $trainee_id);

        if ($search_input) {
            $query->where('task_name', 'like', '%' . $search_input . '%');
        }
        
        if ($start_date_input) {
            $query->where('task_start_date', 'like', '%' . $start_date_input . '%');
        }
        
        if ($end_date_input) {
            $query->where('task_end_date', 'like', '%' . $end_date_input . '%');
        }
        
        if ($priority_input) {
            $query->where('task_priority', $priority_input);
        }
        
        if ($status_input) {
            $query->where('task_status', $status_input);
        }
        
        $tasks = $query->get();

        return view('trainee-task-timeline', compact('tasks'));
    }

    //admin & spervisor method to apply the filter on the task
    public function applyFilterWithID(Request $request, $traineeID){
        $search_input = $request->input('search');
        $start_date_input = $request->input('taskStartDate');
        $end_date_input = $request->input('taskEndDate');
        $priority_input = $request->input('taskPriority');
        $status_input = $request->input('taskStatus');

        //filter the task based on the input
        $query = TaskTimeline::where('trainee_id', $traineeID);

        if ($search_input) {
            $query->where('task_name', 'like', '%' . $search_input . '%');
        }
        
        if ($start_date_input) {
            $query->where('task_start_date', 'like', '%' . $start_date_input . '%');
        }
        
        if ($end_date_input) {
            $query->where('task_end_date', 'like', '%' . $end_date_input . '%');
        }
        
        if ($priority_input) {
            $query->where('task_priority', $priority_input);
        }
        
        if ($status_input) {
            $query->where('task_status', $status_input);
        }
        
        $tasks = $query->get();

        // get the user role
        $role_id = Auth::user()->role_id;
      
        //supervisor
        if($role_id == 2){
            return view('sv-view-trainee-task-timeline', compact('tasks', 'traineeID'));
        }
        //admin
        else{
            $traineeName = Trainee::where('id', $traineeID)->pluck('name')->first();
            return view('admin-view-trainee-task-timeline', compact('tasks', 'traineeName', 'traineeID'));
        }
    }
}
