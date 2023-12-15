<?php

namespace App\Http\Controllers;

use DateTime;
use DatePeriod;
use DateInterval;
use Ramsey\Uuid\Uuid;
use App\Models\Trainee;
use App\Models\AllTrainee;
use App\Models\Supervisor;
use App\Models\Notification;
use App\Models\TaskTimeline;
use Illuminate\Http\Request;
use App\Models\TraineeAssign;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Notifications\TelegramNotification;

class TaskTimelineController extends Controller
{
    public function index($sort = null, $order = null, $traineeID = null,){
        $user = Auth::user();
        $role = $user->role_id;

        if($traineeID == null){
            $traineeID = Trainee::where('sains_email', $user->email)->pluck('id')->first();
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

        return view('sv-view-trainee-task-timeline', compact('tasks', 'traineeID'));
    }

    public function adminViewTraineeTaskTimeline($traineeID){
        //get all the task for this trainee
        $tasks = TaskTimeline::where('trainee_id', $traineeID)->get();

        return view('admin-view-trainee-task-timeline', compact('tasks', 'traineeID'));
    }

    public function traineeAddNewTask(Request $request){
        $user = Auth::user();

        //get trainee id
        $trainee_id = Trainee::where('sains_email', $user->email)->pluck('id')->first();

        $startDate = new DateTime($request->input('startDate'));
        $endDate = new DateTime($request->input('endDate'));

        //terminate the function when the user chooses invalid date (end date < start date)
        if($endDate < $startDate){
            return redirect()->route('trainee-task-timeline')->with('warning', 'Failed to add new task! Invalid date chosen!');
        }

        //input validation
        $validator = Validator::make($request->all(), [
            'taskName' => ['required', 'string','regex:/^[A-Za-z0-9?!,.&\'"\s()-]+$/'],
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return redirect()->back()->with('warning', 'Some special characters are not allowed in Task Name. Please try again.');
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

        return redirect()->route('trainee-task-timeline')->with('success', 'New task added.');
    }

    public function traineeAddNewTaskSV(Request $request, $traineeID){
        $startDate = new DateTime($request->input('startDate'));
        $endDate = new DateTime($request->input('endDate'));

        $user_role = Auth::user()->role_id;

        //terminate the function when the user chooses invalid date (end date < start date)
        if($endDate < $startDate){
            if($user_role == 1){
                return redirect()->route('admin-view-trainee-task-timeline', $traineeID)->with('warning', 'Failed to add new task! Invalid date chosen!');
            }
            elseif($user_role == 2){
                return redirect()->route('sv-view-trainee-task-timeline', $traineeID)->with('warning', 'Failed to add new task! Invalid date chosen!');
            }
            
        }

        //input validation
        $validator = Validator::make($request->all(), [
            'taskName' => ['required', 'string','regex:/^[A-Za-z0-9?!,.&\'"\s()-]+$/'],
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return redirect()->back()->with('warning', 'Some special characters are not allowed in Task Name. Please try again.');
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

        $user = Auth::user();
        $traineeName = Trainee::where('sains_email', $user->email)->pluck('name')->first();
        $traineeID = AllTrainee::where('name', 'LIKE', $traineeName)->pluck('id')->first();
        
        $taskName = TaskTimeline::where('id', $taskID)->pluck('task_name')->first();

        //terminate the function when the user chooses invalid date (end date < start date)
        if($endDate < $startDate){
            return redirect()->route('trainee-task-detail', $taskID)->with('warning', 'Failed to change the task! Invalid date chosen!');
        }

        //get the status 
        $status = $request->input('status');

        //input validation
        $validator = Validator::make($request->all(), [
            'taskName' => ['required', 'string','regex:/^[A-Za-z0-9?!,.&\'"\s()-]+$/'],
            'taskDescription' => ['required', 'string','regex:/^[A-Za-z0-9?!,.&\'"\s()-]+$/'],
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return redirect()->back()->with('warning', 'Some special characters are not allowed in Task Name and Task Description. Please try again.');
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

        //use the id in list to search for the trainee's supervisor
        $assigned_supervisor_ids = TraineeAssign::where('trainee_id', $traineeID)
            ->pluck('assigned_supervisor_id');

        //send a notification to this trainee's supervisor when the trainee mark his or her task as Completed. 
        if ($status == 'Completed') {
            foreach ($assigned_supervisor_ids as $assigned_supervisor_id) {
                // Check if a similar notification already exists
                $existingNotification = Notification::where('type', 'task completed')
                    ->where('notifiable_type', 'App\Models\Trainee')
                    ->where('notifiable_id', $assigned_supervisor_id)
                    ->where('data', json_encode([
                        'data' => 'You trainee ' . $traineeName . ' has completed task ' . $taskName,
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
                        'data' => 'You trainee ' . $traineeName . ' has completed task ' . $taskName,
                    ]);
                    $notification->save(); // Save the notification to the database

                    $supervisor_name = Supervisor::where('id', $assigned_supervisor_id)->pluck('name')->first();
                    $notification->notify(new TelegramNotification('Task Completion', $supervisor_name , $traineeName , 'Your trainee has completed ' . $taskName . '.'));
                }
            }
        }


        return redirect()->route('trainee-task-detail', $taskID)->with('success', 'New task added.');
    }

    public function showTaskDetailForTrainee($taskID){
        $task = TaskTimeline::find($taskID);
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
            return view('trainee-task-detail', compact('task', 'dateRange', 'timelineData', 'comments'));
        } elseif ($user_role == 2) {
            return view('sv-view-trainee-task-detail', compact('task', 'dateRange', 'timelineData', 'comments'));
        } elseif ($user_role == 1 ){
            return view('admin-view-trainee-task-detail', compact('task', 'dateRange', 'timelineData', 'comments'));
        }
    }
    

    public function showDailyTaskDetailForTrainee($date, $taskID){
        $dailyTask = TaskTimeline::find($taskID);
    
        $timelineData = json_decode($dailyTask->timeline, true);

        // Convert the date string to a DateTime object
        $dateTime = new DateTime($date);

        // Get the day of the week (e.g., Monday, Tuesday, etc.)
        $dayOfWeek = $dateTime->format('l');
    
        // Get the specific date's task detail
        $taskDetail = isset($timelineData[$date]) ? $timelineData[$date] : null;
    
        $user_role = Auth::user()->role_id;
        if($user_role == 3){
            return view('trainee-daily-task-detail', compact('date', 'taskDetail', 'taskID', 'dayOfWeek'));
        }
        elseif($user_role == 2){
            return view('sv-view-trainee-daily-task-detail', compact('date', 'taskDetail', 'taskID', 'dayOfWeek'));
        }
        elseif($user_role == 1){
            return view('admin-view-trainee-daily-task-detail', compact('date', 'taskDetail', 'taskID', 'dayOfWeek'));
        }
       
    }

    public function traineeEditDailyTask(Request $request, $date, $taskID){
        // find the daily task to be edited.
        $task = TaskTimeline::where('id', $taskID)->first();
        $timeline = json_decode($task->timeline, true);

        //input validation
        $validator = Validator::make($request->all(), [
            'taskName' => ['required', 'string', 'regex:/^[A-Za-z0-9?!,.&\'"\s()-]+$/'],
            'taskDescription' => ['required', 'string', 'regex:/^[A-Za-z0-9?!,.&\'"\s()-]+$/'],
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return redirect()->back()->with('warning', 'Some special characters are not allowed in Task Name and Task Description. Please try again.');
        }

        if(isset($timeline[$date])){
            $timeline[$date]['Name'] = $request->input('taskName');
            $timeline[$date]['Description'] = $request->input('taskDescription');
            $timeline[$date]['Status'] = $request->input('status');
    
            // Update the timeline in the database
            $task->timeline = json_encode($timeline);
            $task->save();
    
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
                'comment' => ['required', 'string','regex:/^[A-Za-z0-9?!,.&\'"\s()-]+$/'],
            ]);

            // Check if the validation fails
            if ($validator->fails()) {
                return redirect()->back()->with('warning', 'Some special characters are not allowed in comment. Please try again.');
            }
            $comment['Trainee'] = $request->input('comment');
        }
        elseif($user_role == 2){
            $sv_name = Auth::user()->name;
            $trainee_id = TaskTimeline::where('id', $taskID)->pluck('trainee_id')->first();
            $trainee_name = Trainee::where('id', $trainee_id)->pluck('name')->first();
            //input validation
            $validator = Validator::make($request->all(), [
                'comment' => ['required', 'string','regex:/^[A-Za-z0-9?!,.&\'"\s()-]+$/'],
            ]);

            // Check if the validation fails
            if ($validator->fails()) {
                return redirect()->back()->with('warning', 'Some special characters are not allowed in comment. Please try again.');
            }
            $comment['Supervisor'] = $request->input('comment');

            $task_name = TaskTimeline::where('id', $taskID)->pluck('task_name')->first();

            $notification = new Notification();
            $notification->id = Uuid::uuid4(); // Generate a UUID for the id
            $notification->type = 'signed_logbook';
            $notification->notifiable_type = 'App\Models\Supervisor';
            $notification->notifiable_id = $trainee_id;
            $notification->data = json_encode([
                'data' => 'Your supervisor ' . $sv_name . ' has added a comment to the task ' . $task_name . '.',
                'name' => $trainee_name,
            ]);
            $notification->save(); // Save the notification to the database
        }
        elseif($user_role == 1){
            //input validation
            $validator = Validator::make($request->all(), [
                'commentSV' => ['required', 'string','regex:/^[A-Za-z0-9?!,.&\'"\s()-]+$/'],
                'commentTR' => ['required', 'string','regex:/^[A-Za-z0-9?!,.&\'"\s()-]+$/'],
            ]);

            // Check if the validation fails
            if ($validator->fails()) {
                return redirect()->back()->with('warning', 'Some special characters are not allowed in comment. Please try again.');
            }
            $comment['Supervisor'] = $request->input('commentSV');
            $comment['Trainee'] = $request->input('commentTR');
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
                    'comment' => ['required', 'string','regex:/^[A-Za-z0-9?!,.&\'"\s()-]+$/'],
                ]);

                // Check if the validation fails
                if ($validator->fails()) {
                    return redirect()->back()->with('warning', 'Some special characters are not allowed in comment. Please try again.');
                }
                $timeline[$date]['Supervisor'] = $request->input('comment');
            }
            elseif($user_role == 3){
                //input validation
                $validator = Validator::make($request->all(), [
                    'comment' => ['required', 'string','regex:/^[A-Za-z0-9?!,.&\'"\s()-]+$/'],
                ]);

                // Check if the validation fails
                if ($validator->fails()) {
                    return redirect()->back()->with('warning', 'Some special characters are not allowed in comment. Please try again.');
                }
                $timeline[$date]['Trainee'] = $request->input('comment');
            }
            elseif($user_role == 1){
                //input validation
                $validator = Validator::make($request->all(), [
                    'commentSV' => ['required', 'string','regex:/^[A-Za-z0-9?!,.&\'"\s()-]+$/'],
                    'commentTR' => ['required', 'string','regex:/^[A-Za-z0-9?!,.&\'"\s()-]+$/'],
                ]);

                // Check if the validation fails
                if ($validator->fails()) {
                    return redirect()->back()->with('warning', 'Some special characters are not allowed in comment. Please try again.');
                }
                $timeline[$date]['Supervisor'] = $request->input('commentSV');
                $timeline[$date]['Trainee'] = $request->input('commentTR');
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
            return redirect()->route('trainee-daily-task-detail', ['date' => $date, 'taskID' => $taskID])->with('success', 'Comment added.');
        }

        if($user_role == 3){
            $comment['Trainee'] = $request->input('comment');
        }
        elseif($user_role == 2){
            $comment['Supervisor'] = $request->input('comment');
        }

        $task->task_overall_comment = json_encode($comment);
        $task->save();

        return redirect()->route('trainee-task-detail', ['taskID' => $taskID])->with('success', 'Comment has changed successfully.');
    }

    public function deleteTask($taskID){
        $targetTask = TaskTimeline::find($taskID);
        $traineeID = $targetTask->trainee_id;
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
}
