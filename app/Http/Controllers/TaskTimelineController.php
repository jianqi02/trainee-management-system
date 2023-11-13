<?php

namespace App\Http\Controllers;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\Trainee;
use App\Models\TaskTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskTimelineController extends Controller
{
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

    public function traineeAddNewTask(Request $request){
        $user = Auth::user();

        //get trainee id
        $trainee_id = Trainee::where('sains_email', $user->email)->pluck('id')->first();

        //add a new task to DB
        $task = new TaskTimeline();
        $task->trainee_id = $trainee_id;
        $task->task_name = $request->input('taskName');
        $task->task_start_date = $request->input('startDate');
        $task->task_end_date = $request->input('endDate');
        $task->task_status = 'Not Started';
        $task->task_priority = $request->input('priority');
        $taskDetail = [
            "Description" => "",
        ];
        $task->task_detail = json_encode($taskDetail);
        $task->save();

        return redirect()->route('trainee-task-timeline')->with('success', 'New task added.');
    }

    public function traineeAddNewTaskSV(Request $request, $traineeID){
        //add a new task to DB
        $task = new TaskTimeline();
        $task->trainee_id = $traineeID;
        $task->task_name = $request->input('taskName');
        $task->task_start_date = $request->input('startDate');
        $task->task_end_date = $request->input('endDate');
        $task->task_status = 'Not Started';
        $task->task_priority = $request->input('priority');
        $taskDetail = [
            "Description" => "",
        ];
        $task->task_detail = json_encode($taskDetail);
        $task->save();

        return redirect()->route('sv-view-trainee-task-timeline', $traineeID)->with('success', 'New task added.');
    }

    public function traineeEditTask(Request $request, $taskID){
        //add a new task to DB
        $task = TaskTimeline::where('id', $taskID)->first();
        $task->task_name = $request->input('taskName');
        $task->task_start_date = $request->input('startDate');
        $task->task_end_date = $request->input('endDate');
        $task->task_status = $request->input('status');
        $task->task_priority = $request->input('priority');
        $taskDetail = [
            "Description" => $request->input('taskDescription'),
        ];
        $task->task_detail = json_encode($taskDetail);
        $task->save();

        return redirect()->route('trainee-task-detail', $taskID)->with('success', 'New task added.');
    }

    public function showTaskDetailForTrainee($taskID){
        $task = TaskTimeline::find($taskID);
        $startDate = new DateTime($task->task_start_date);
        $endDate = new DateTime($task->task_end_date);
        $interval = new DateInterval('P1D'); // 1 day interval
        $dateRange = new DatePeriod($startDate, $interval, $endDate->modify('+1 day'));
        $comments = json_decode($task->task_overall_comment, true);

        $timelineData = json_decode($task->timeline, true);

        $user_role = Auth::user()->role_id;
        if($user_role == 3){
            return view('trainee-task-detail', compact('task', 'dateRange', 'timelineData','comments'));
        }
        elseif($user_role ==2){
            return view('sv-view-trainee-task-detail', compact('task', 'dateRange', 'timelineData', 'comments'));
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
       
    }

    public function traineeEditDailyTask(Request $request, $date, $taskID){
        // find the daily task to be edited.
        $task = TaskTimeline::where('id', $taskID)->first();
        $timeline = json_decode($task->timeline, true);
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
            $comment['Trainee'] = $request->input('comment');
        }
        elseif($user_role == 2){
            $comment['Supervisor'] = $request->input('comment');
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
                $timeline[$date]['Supervisor'] = $request->input('comment');
            }
            elseif($user_role == 3){
                $timeline[$date]['Trainee'] = $request->input('comment');
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
        elseif($user_role == 2){
            return redirect()->route('sv-view-trainee-task-timeline', ['traineeID' => $traineeID])->with('success', 'Task deleted.');
        }
    }
}
