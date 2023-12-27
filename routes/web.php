<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\SeatingController;
use App\Http\Controllers\TraineeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TaskTimelineController;
use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

#when users enter the landing page, redirect the user to other page according to their role id.
Route::get('/', function () {
    if (Auth::check()) {
        // Get the user's role ID
        $role_id = Auth::user()->role_id;

        //admin = 1, supevisor = 2 , trainee = 3
        if($role_id == 1){
            return redirect()->route('admin-dashboard');
        }
        elseif($role_id == 2){
            return view('sv-homepage');
        }
        elseif($role_id == 3){
            return view('homepage');
        }
        else{
            return view('home');
        }
    }

    return view('home');
})->name('homepage');

// Fallback route
Route::fallback(function () {
    return redirect('/');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/home', [HomeController::class, 'index'])->name('home');

//trainee-related function
Route::middleware(['role:3'])->group(function () {
    Route::get('/trainee-profile', [TraineeController::class, 'showProfile'])->name('trainee-profile');
    Route::get('/view-seat-plan', [TraineeController::class, 'viewSeatPlan'])->name('view-seat-plan');
    Route::get('/trainee-edit-profile', [TraineeController::class, 'placeholderProfile'])->name('trainee-edit-profile');
    Route::get('/trainee-upload-resume', [TraineeController::class, 'traineeResume'])->name('trainee-upload-resume');
    Route::get('/trainee-upload-logbook', [TraineeController::class, 'traineeLogbook'])->name('trainee-upload-logbook');
    Route::post('trainee-update-password', [TraineeController::class, 'traineeUpdatePassword'])->name('trainee-update-password');
    Route::post('/update-profile', [TraineeController::class, 'updateProfile'])->name('update-profile');
    Route::post('/upload', [TraineeController::class, 'uploadResume'])->name('upload');
    Route::post('/upload-logbook', [TraineeController::class, 'uploadLogbook'])->name('upload-logbook');
    Route::delete('/logbooks/{logbook}', [TraineeController::class, 'destroy'])->name('logbooks.destroy');
    Route::delete('/resumes/{trainee}', [TraineeController::class, 'destroyResume'])->name('resumes.destroyResume');
    Route::get('/my-supervisor', [TraineeController::class, 'mySupervisorPage'])->name('my-supervisor');

});
//supervisor-related function
Route::middleware(['role:2'])->group(function () {
    Route::get('/sv-profile', [SupervisorController::class, 'showProfileSV'])->name('sv-profile');
    Route::get('/sv-edit-profile', [SupervisorController::class, 'placeholderProfileSV'])->name('sv-edit-profile');
    Route::post('/update-profile-sv', [SupervisorController::class, 'updateProfileSV'])->name('update-profile-sv');
    Route::post('sv-update-password', [SupervisorController::class, 'svUpdatePassword'])->name('sv-update-password');
    Route::get('/sv-trainee-assign', [SupervisorController::class, 'showAllTraineeProfileForSV'])->name('sv-trainee-assign');
    Route::get('/view-and-upload-logbook-sv/{traineeName}', [SupervisorController::class, 'svViewTraineeLogbook'])->name('view-and-upload-logbook-sv');
    Route::get('/go-profile/{traineeName}', [SupervisorController::class, 'goToTraineeProfile'])->name('go-profile');
    Route::get('/go-task-timeline/{traineeName}', [SupervisorController::class, 'goToTraineeTaskTimeline'])->name('go-task-timeline');
    Route::get('/sv-view-and-upload-logbook/{traineeName}', [SupervisorController::class, 'svViewTraineeLogbook'])->name('sv-view-and-upload-logbook');
    Route::post('/sv-upload-logbook/{name}', [SupervisorController::class, 'svUploadLogbook'])->name('sv-upload-logbook');
    Route::delete('/remove-logbooks-sv/{logbook}/{name}', [SupervisorController::class, 'destroy'])->name('remove-logbooks-sv.destroy');
    Route::get('/sv-view-resume/{traineeName}', [SupervisorController::class, 'svViewTraineeResume'])->name('sv-view-resume');
    Route::get('/sv-comment/{traineeName}', [SupervisorController::class, 'svCommentPage'])->name('sv-comment');
    Route::post('/sv-submit-comment', [SupervisorController::class, 'svSubmitComment'])->name('sv-submit-comment');
    Route::get('/sv-view-seat-plan', [SupervisorController::class, 'viewSeatPlan'])->name('sv-view-seat-plan');
});

//admin-related function
Route::middleware(['role:1'])->group(function () {
    Route::get('/admin-dashboard', [AdminController::class, 'showDashboard'])->name('admin-dashboard');
    Route::get('/user-management', [AdminController::class, 'index'])->name('user-management');
    Route::get('/all-trainee-list', [AdminController::class, 'showAllTrainee'])->name('all-trainee-list');
    Route::get('/admin-create-new-trainee-record', [AdminController::class, 'createNewTraineeRecord'])->name('admin-create-new-trainee-record');
    Route::get('/create-new-record', [AdminController::class, 'showCreateRecordForm'])->name('create-new-record');
    Route::post('/create-new-record', [AdminController::class, 'createRecord'])->name('create-new-record');
    Route::get('/get-supervisor-current-trainee/{selectedSupervisorName}', [AdminController::class, 'getSupervisorCurrentTraineeName'])->name('get-supervisor-current-trainee');
    Route::get('/create-user', [AdminController::class, 'showCreateUserForm'])->name('create-user');
    Route::post('/create-user', [AdminController::class, 'createUser'])->name('create-user');
    Route::get('/admin-trainee-assign', [AdminController::class, 'traineeAssign'])->name('admin-trainee-assign');
    Route::get('/delete-trainee-record/{id}', [AdminController::class, 'deleteTraineeRecord'])->name('delete-trainee-record');
    Route::get('/edit-record/{id}', [AdminController::class, 'goToEditRecordPage'])->name('edit-record');
    Route::post('/edit-exist-trainee-record', [AdminController::class, 'editRecordMethod'])->name('edit-exist-trainee-record');
    Route::get('/admin-assign-supervisor-function/{selected_trainee}', [AdminController::class, 'assignSupervisorToTrainee'])->name('admin-assign-supervisor-function');
    Route::post('/supervisor-assign-method', [AdminController::class, 'supervisorAssignMethod'])->name('supervisor-assign-method');
    Route::get('/admin-remove-assigned-supervisor-function/{selected_trainee}', [AdminController::class, 'removeAssignedSupervisor'])->name('admin-remove-assigned-supervisor-function');
    Route::post('/remove-supervisor-method', [AdminController::class, 'removeSupervisorMethod'])->name('remove-supervisor-method');
    Route::get('/admin-edit-profile/{selected}', [AdminController::class, 'editProfile'])->name('admin-edit-profile');
    Route::post('/admin-update-profile/{selected}', [AdminController::class, 'updateProfile'])->name('admin-update-profile');
    Route::get('/change-account-status/{selected}', [AdminController::class, 'changeAccountStatus'])->name('change-account-status');
    Route::get('/view-and-upload-logbook/{traineeName}', [AdminController::class, 'viewTraineeLogbook'])->name('view-and-upload-logbook');
    Route::delete('/remove-logbooks/{logbook}/{name}', [AdminController::class, 'destroy'])->name('remove-logbooks.destroy');
    Route::post('/admin-upload-logbook/{name}', [AdminController::class, 'uploadLogbook'])->name('admin-upload-logbook');
    Route::get('/admin-go-profile/{traineeName}', [AdminController::class, 'adminGoTraineeProfile'])->name('admin-go-profile');
    Route::post('/admin-upload-resume/{traineeName}', [AdminController::class, 'adminUploadResume'])->name('admin-upload-resume');
    Route::post('/change-sv-comment/{commentID}', [AdminController::class, 'changeSVComment'])->name('change-sv-comment');
    Route::get('/delete-exist-account/{traineeID}', [AdminController::class, 'deleteAccount'])->name('delete-exist-account');
    Route::get('/delete-exist-sv-account/{supervisorID}', [AdminController::class, 'deleteSVAccount'])->name('delete-exist-sv-account');
    Route::post('/admin-change-password/{id}/{type}', [AdminController::class, 'adminChangePassword'])->name('admin-change-password');
    Route::post('admin-update-password', [AdminController::class, 'adminUpdatePassword'])->name('admin-update-password');
    Route::get('/activity-log', [AdminController::class, 'displayActivityLog'])->name('activity-log');
    Route::post('/activity-log-filter', [AdminController::class, 'activityLogFilter'])->name('activity-log-filter');
});


//seating-related function
Route::middleware(['role:1'])->group(function () {
    Route::get('/seating-arrange', [SeatingController::class, 'index'])->name('seating-arrange');
    Route::get('/seating-arrange/random', [SeatingController::class, 'getRandomTrainee'])->name('seating-arrange.random');
    Route::get('/get-seat-data/{seat}', [SeatingController::class, 'getSeatData'])->name('get-seat-data');
    Route::get('/remove-seat/{seat}', [SeatingController::class, 'removeSeat'])->name('remove-seat');
    Route::get('/change-ownership/{seat}', [SeatingController::class, 'changeOwnership'])->name('change-ownership');
    Route::get('/assign-seat-for-trainee/{trainee_selected}/{seat}', [SeatingController::class, 'assignSeatForTrainee'])->name('assign-seat-for-trainee');
    Route::get('/seating-arrange/get-weekly-data', [SeatingController::class, 'getWeeklyData'])->name('seating-arrange.get-weekly-data');
});

//notification-related function
Route::middleware(['role:1,2,3'])->group(function () {
    Route::get('/notification', [NotificationController::class, 'index'])->name('notification');
    Route::post('/mark-notification-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('mark-notification-as-read');
    Route::post('/mark-all-notifications-as-read', [NotificationController::class,'markAllAsRead'])->name('mark-all-notifications-as-read');
});

// Task Timeline function for trainee
Route::middleware(['role:3'])->group(function () {
    Route::get('/trainee-task-timeline', [TaskTimelineController::class, 'traineeTaskTimeline'])->name('trainee-task-timeline');
    Route::post('/trainee-add-new-task', [TaskTimelineController::class, 'traineeAddNewTask'])->name('trainee-add-new-task');
    Route::post('/apply-filter',[TaskTimelineController::class, 'applyFilter'])->name('apply-filter');
});

Route::middleware(['role:1,2,3'])->group(function () {
    Route::get('/trainee-task-detail/{taskID}', [TaskTimelineController::class, 'showTaskDetailForTrainee'])->name('trainee-task-detail');
    Route::post('/trainee-edit-task/{taskID}', [TaskTimelineController::class, 'traineeEditTask'])->name('trainee-edit-task');
    Route::get('/trainee-daily-task-detail/{date}/{taskID}', [TaskTimelineController::class, 'showDailyTaskDetailForTrainee'])->name('trainee-daily-task-detail');
    Route::post('/trainee-edit-daily-task/{date}/{taskID}', [TaskTimelineController::class, 'traineeEditDailyTask'])->name('trainee-edit-daily-task');
    Route::post('/task-timeline-overall-comment/{taskID}', [TaskTimelineController::class, 'taskTimelineOverallComment'])->name('task-timeline-overall-comment');
    Route::post('/task-timeline-daily-comment/{date}/{taskID}', [TaskTimelineController::class, 'taskTimelineDailyComment'])->name('task-timeline-daily-comment');
    Route::get('/delete-task/{taskId}', [TaskTimelineController::class, 'deleteTask'])->name('delete-task');
});

// Task Timeline function for supervisor
Route::middleware(['role:2'])->group(function () {
    Route::get('/sv-view-trainee-task-timeline/{traineeID}', [TaskTimelineController::class, 'svViewTraineeTaskTimeline'])->name('sv-view-trainee-task-timeline');
});

Route::middleware(['role:1,2'])->group(function () {
    Route::post('/trainee-add-new-task-sv/{traineeID}', [TaskTimelineController::class, 'traineeAddNewTaskSV'])->name('trainee-add-new-task-sv');
    Route::post('/apply-filters/{traineeID}',[TaskTimelineController::class, 'applyFilterWithID'])->name('apply-filters');
});

Route::middleware(['role:1,2,3'])->group(function () {
    Route::get('/sort-tasks/{sort?}/{order?}/{traineeID?}', [TaskTimelineController::class, 'index'])->name('sort-tasks');
});

// Task Timeline function for admin
Route::middleware(['role:1'])->group(function () {
    Route::get('/admin-view-trainee-task-timeline/{traineeID}', [TaskTimelineController::class, 'adminViewTraineeTaskTimeline'])->name('admin-view-trainee-task-timeline');
});

Route::middleware(['role:3'])->group(function () {
    Route::get('/homepage', function () {
        return view('homepage');
    });
    Route::get('/trainee-change-password', function () {
        return view('trainee-change-password');
    })->name('trainee-change-password');
});

Route::middleware(['role:1'])->group(function () {
    Route::get('/admin-create-account', function () {
        return view('admin-create-account');
    })->name('admin-create-account');
    Route::get('/admin-change-self-password', function () {
        return view('admin-change-password');
    })->name('admin-change-self-password');
});

Route::middleware(['role:2'])->group(function () {
    Route::get('/sv-homepage', function () {
        return view('sv-homepage');
    });
    Route::get('/sv-change-password', function () {
        return view('sv-change-password');
    })->name('sv-change-password');
});

//forgot password : this will send a notification to the admin
Route::post('/forgot-password', [NotificationController::class, 'forgotPasswordNotification'])->name('forgot-password');


Auth::routes();