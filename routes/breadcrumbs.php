<?php

use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

/* breadcrumb for trainee starts*/

// Home
Breadcrumbs::for('homepage', function ($trail) {
    $trail->push('Home', route('homepage'));
});

// Home > Profile
Breadcrumbs::for('profile', function ($trail) {
    $trail->parent('homepage');
    $trail->push('Profile', route('trainee-profile'));
});

// Home > Profile > Edit Profile
Breadcrumbs::for('editprofile', function ($trail) {
    $trail->parent('profile');
    $trail->push('Edit Profile', route('trainee-edit-profile'));
});

// Home > Resume || Home > Profile > Resume
Breadcrumbs::for('resume', function ($trail) {
    $url = url()->previous();
    $prevRoute = app('router')->getRoutes($url)->match(app('request')->create($url))->getName();
    
    // Common part for all contexts
    $trail->parent('homepage');
    
    if ($prevRoute === 'trainee-profile') {
        // Additional breadcrumb for the profile context
        $trail->push('Profile', route('trainee-profile'));
    }

    // Breadcrumb for the resume page
    $trail->push('Resume', route('trainee-upload-resume'));
});

// Home > Logbook || Home > Profile > Logbook
Breadcrumbs::for('logbook', function ($trail) {
    $url = url()->previous();
    $prevRoute = app('router')->getRoutes($url)->match(app('request')->create($url))->getName();
    
    // Common part for all contexts
    $trail->parent('homepage');
    
    if ($prevRoute === 'trainee-profile') {
        // Additional breadcrumb for the profile context
        $trail->push('Profile', route('trainee-profile'));
    }

    // Breadcrumb for the resume page
    $trail->push('Logbook', route('trainee-upload-logbook'));
});

// Home > Task Timeline || Home > Profile > Task Timeline
Breadcrumbs::for('timeline', function ($trail) {
    $url = url()->previous();
    $prevRoute = app('router')->getRoutes($url)->match(app('request')->create($url))->getName();

    if ($prevRoute === 'trainee-profile') {
        // Additional breadcrumb for the profile context
        $trail->parent('profile');
    }
    else{
        $trail->parent('homepage');
    }

    // Breadcrumb for the resume page
    $trail->push('Task Timeline', route('trainee-task-timeline'));
});

// Home > Change Password
Breadcrumbs::for('change-password', function ($trail) {
    $trail->parent('homepage');
    $trail->push('Change Password', route('trainee-change-password'));
});

// Task Timeline > Task Detail
Breadcrumbs::for('task-detail', function ($trail, $taskID) {
    $trail->parent('timeline');
    $trail->push('Task Detail', route('trainee-task-detail', $taskID));
});

// Task Timeline > Task Detail > Daily Task Detail
Breadcrumbs::for('daily-task-detail', function ($trail, $date, $taskID) {
    $trail->parent('task-detail', $taskID);
    $trail->push('Daily Task Detail', route('trainee-daily-task-detail', ['date' => $date, 'taskID' => $taskID]));
});

// Home > Seat Plan
Breadcrumbs::for('seat-plan', function ($trail) {
    $trail->parent('homepage');
    $trail->push('Seat Plan', route('view-seat-plan'));
});

// Home > My Supervisor
Breadcrumbs::for('my-supervisor', function ($trail) {
    $trail->parent('homepage');
    $trail->push('My Supervisor', route('my-supervisor'));
});


/* breadcrumbs for trainee ends */

/* breadcrumbs for supervisor starts */

// Home
Breadcrumbs::for('sv-homepage', function ($trail) {
    $trail->push('Home', route('homepage'));
});

// Home > Profile
Breadcrumbs::for('sv-profile', function ($trail) {
    $trail->parent('sv-homepage');
    $trail->push('Profile', route('sv-profile'));
});

// Home > Profile > Edit Profile
Breadcrumbs::for('sv-edit-profile', function ($trail) {
    $trail->parent('sv-profile');
    $trail->push('Edit Profile', route('sv-edit-profile'));
});

// Home > Seat Plan
Breadcrumbs::for('sv-seat-plan', function ($trail) {
    $trail->parent('sv-homepage');
    $trail->push('Seat Plan', route('sv-view-seat-plan'));
});

// Home > Change Password
Breadcrumbs::for('sv-change-password', function ($trail) {
    $trail->parent('sv-homepage');
    $trail->push('Change Password', route('sv-change-password'));
});

// Home > My Trainee
Breadcrumbs::for('my-trainee', function ($trail) {
    $trail->parent('sv-homepage');
    $trail->push('My Trainee', route('sv-trainee-assign'));
});

// My Trainee > Go Profile
Breadcrumbs::for('go-profile', function ($trail, $name) {
    $trail->parent('my-trainee');
    $trail->push('Trainee Profile', route('go-profile', $name));
});

// My Trainee > Go Profile > Trainee Logbook
Breadcrumbs::for('view-trainee-logbook', function ($trail, $name) {
    $trail->parent('go-profile', $name);
    $trail->push('Trainee Logbook', route('sv-view-and-upload-logbook', $name));
});

// My Trainee > Go Profile > Personal Comment
Breadcrumbs::for('sv-comment', function ($trail, $name) {
    $trail->parent('go-profile', $name);
    $trail->push('Personal Comment', route('sv-comment', $name));
});

// My Trainee > Task Timeline
Breadcrumbs::for('sv-timeline', function ($trail, $traineeID) {
    $trail->parent('my-trainee');
    $trail->push('Task Timeline', route('sv-view-trainee-task-timeline', $traineeID));
});

// Task Timeline > Trainee Task Detail
Breadcrumbs::for('sv-task-detail', function ($trail, $traineeID, $taskID) {
    $trail->parent('sv-timeline', $traineeID);
    $trail->push('Trainee Task Detail', route('trainee-task-detail', $taskID));
});

// Task Timeline > Trainee Task Detail > Trainee Daily Task Detail
Breadcrumbs::for('sv-daily-task-detail', function ($trail, $trainee_id, $date, $taskID) {
    $trail->parent('sv-task-detail', $trainee_id, $taskID);
    $trail->push('Trainee Daily Task Detail', route('trainee-daily-task-detail', ['date' => $date, 'taskID' => $taskID]));
});
/* breadcrumbs for supervisor ends */

/* breadcrumbs for admin starts */

// Dashboard
Breadcrumbs::for('dashboard', function ($trail) {
    $trail->push('Dashboard', route('homepage'));
});

// Dashboard > Trainee List
Breadcrumbs::for('trainee-list', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Trainee List', route('all-trainee-list'));
});

// Dashboard > Trainee List > Create New Trainee Record
Breadcrumbs::for('new-record', function ($trail) {
    $trail->parent('trainee-list');
    $trail->push('Create New Trainee Record', route('admin-create-new-trainee-record'));
});

// Dashboard > Trainee List > Edit Trainee Record
Breadcrumbs::for('edit-record', function ($trail, $id) {
    $trail->parent('trainee-list');
    $trail->push('Edit Trainee Record', route('edit-record', $id));
});

// Dashboard > Supervisor Assignment
Breadcrumbs::for('sv-assign', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Supervisor Assignment', route('admin-trainee-assign'));
});

// Dashboard > Supervisor Assignment > Assign
Breadcrumbs::for('assign-sv-to-trainee', function ($trail, $name) {
    $url = url()->previous();
    $prevRoute = app('router')->getRoutes($url)->match(app('request')->create($url))->getName();
    if ($prevRoute === 'all-trainee-list') {
        $trail->parent('trainee-list');
    }
    else{
        $trail->parent('sv-assign');
    }

    $trail->push('Assign', route('admin-assign-supervisor-function', $name));
});

// Dashboard > Supervisor Assignment > Remove
Breadcrumbs::for('remove-sv-from-trainee', function ($trail, $name) {
    $trail->parent('sv-assign');
    $trail->push('Remove', route('admin-remove-assigned-supervisor-function', $name));
});

// Dashboard > Seating Arrangement
Breadcrumbs::for('seating-arrangement', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Seating Arrangement', route('seating-arrange'));
});

// Dashboard > User Management
Breadcrumbs::for('user-management', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('User Management', route('user-management'));
});

// Dashboard > User Management > Trainee Profile
Breadcrumbs::for('admin-view-trainee-profile', function ($trail, $name) {
    $trail->parent('user-management');
    $trail->push('Trainee Profile', route('admin-go-profile', $name));
});

// Dashboard > User Management > Trainee Profile > Edit Profile
Breadcrumbs::for('admin-edit-trainee-profile', function ($trail, $name) {
    $trail->parent('admin-view-trainee-profile', $name);
    $trail->push('Edit Profile', route('admin-edit-profile', $name));
});

// Dashboard > User Management > Edit Profile
Breadcrumbs::for('admin-edit-sv-profile', function ($trail, $name) {
    $trail->parent('user-management', $name);
    $trail->push('Edit Profile', route('admin-edit-profile', $name));
});

// Dashboard > User Management > Trainee Profile > Trainee Logbook
Breadcrumbs::for('admin-view-trainee-logbook', function ($trail, $name) {
    $trail->parent('admin-view-trainee-profile', $name);
    $trail->push('Trainee Logbook', route('view-and-upload-logbook', $name));
});

// Dashboard > User Management > Trainee Profile > Trainee Task Timeline
Breadcrumbs::for('admin-timeline', function ($trail, $name, $id) {
    $trail->parent('admin-view-trainee-profile', $name);
    $trail->push('Trainee Task Timeline', route('admin-view-trainee-task-timeline', $id));
});

// Task Timeline > Trainee Task Detail
Breadcrumbs::for('admin-task-detail', function ($trail, $name, $traineeID, $taskID) {
    $trail->parent('admin-timeline', $name, $traineeID);
    $trail->push('Trainee Task Detail', route('trainee-task-detail', $taskID));
});

// Task Timeline > Trainee Task Detail > Trainee Daily Task Detail
Breadcrumbs::for('admin-daily-task-detail', function ($trail, $name, $traineeID, $date, $taskID) {
    $trail->parent('admin-task-detail', $name, $traineeID, $taskID);
    $trail->push('Trainee Daily Task Detail', route('trainee-daily-task-detail', ['date' => $date, 'taskID' => $taskID]));
});

// Dashboard > Create New Account
Breadcrumbs::for('create-new-account', function ($trail) {
    $trail->parent('user-management');
    $trail->push('Create New Account', route('admin-create-account'));
});

// Dashboard > Change Password
Breadcrumbs::for('admin-change-password', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Change Password', route('admin-change-self-password'));
});

// Dashboard > Activity Log
Breadcrumbs::for('activity-log', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Activity Log', route('activity-log'));
});
/* breadcrumbs for admin ends */