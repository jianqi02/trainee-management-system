@extends('layouts.admin')
@section('pageTitle', 'User Management')

@section('breadcrumbs', Breadcrumbs::render('user-management'))

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- some icons are obtained from https://icons8.com/ -->
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    h1 {
        font-family: 'Roboto', sans-serif;
    }

    .content {
        margin-left: 150px;
        padding: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        margin-top: 20px;
    }

    th {
        background-color: #f2f2f2;
        color: #333;
        font-weight: bold;
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
    }

    tr {
        background-color: #fff;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
    }

    td a {
        text-decoration: none;
        color: #007bff;
    }

    td a:hover {
        text-decoration: underline;
    }

    .input-group {
        margin-top: 20px;
    }

    .dropdown {
        margin-top: 20px;
    }

    .navbar {
        height: 50px;
    }

    .notification {
        margin-top: 25px;
    }
    
    #navbarDropdown {
        margin-bottom: 15px;
    }

    img{
        width: 32px;
        height: 32px;
    }

</style>
<body>
    <div class="content">     
    <main>
        <div class="container">
            <h1>User Management</h1>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
            @endif
            <ul class="nav nav-tabs" id="myTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="trainee-list-tab" data-bs-toggle="tab" href="#trainee-list" role="tab" aria-controls="trainee-list" aria-selected="true">Trainee List</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="other-list-tab" data-bs-toggle="tab" href="#other-list" role="tab" aria-controls="other-list" aria-selected="false">Supervisor List</a>
                </li>
                <li class="nav-item ms-auto">
                    <a class="btn btn-secondary" href="/admin-create-account">Create Account</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="trainee-list" role="tabpanel" aria-labelledby="trainee-list-tab">
                    <div class="row">
                        <div class="col-md-4">
                            <!-- Search Bar -->
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Search trainee" id="search-input-trainee">
                                <button class="btn btn-outline-secondary" type="button" id="search-button">Search</button>
                            </div>
                        </div>
                        <div class="col-md-8">
                        </div>
                    </div>
                    <div style="max-height: 350px; overflow-y: scroll;">
                        <table class="all-trainee-list" id="all-trainee-list">
                            <thead>
                                <tr>
                                    <th>Name
                                        <button class="sort-button-trainee" data-column="0" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th>Personal Email
                                        <button class="sort-button-trainee" data-column="1" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th>SAINS Email
                                        <button class="sort-button-trainee" data-column="2" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th>Status
                                        <button class="sort-button-trainee" data-column="3" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trainees as $trainee)
                                    <tr id="trainee-{{ $trainee->name }}">
                                        <td>{{ $trainee->name }}</td>
                                        <td>{{ $trainee->personal_email }}</td>
                                        <td>{{ $trainee->sains_email }}</td>
                                        <td>{{ $trainee->acc_status }}</td>
                                        <td style="width: 30%;">
                                            <a href="{{ route('admin-go-profile', ['traineeName' => urlencode($trainee->name)]) }}" style="text-decoration: none; font-size: 24px; color: grey; margin-left: 20px;" title="View Profile">
                                                <i class="fa fa-user" aria-hidden="true"></i>
                                            </a>
                                            <a href="{{ route('admin-view-trainee-task-timeline', ['traineeID' => $trainee->id]) }}" style="text-decoration: none; font-size: 24px; color: grey; margin-left: 20px;" title="View Task Timeline">
                                                <img src="https://img.icons8.com/sf-black/64/808080/timeline.png" alt="timeline"/>
                                            </a>
                                            <a href="#" style="text-decoration: none; font-size: 24px; color: grey; margin-left: 20px;" title="Change Account Status" data-toggle="modal" data-target="#confirmChangeStatusModal{{ $trainee->id }}">
                                                <i class="fa fa-exchange" aria-hidden="true"></i>
                                            </a>                                           
                                            <a href="#" style="text-decoration: none; font-size: 24px; color: grey; margin-left: 20px;" title="Delete Account" data-toggle="modal" data-target="#deleteConfirmationModal{{ $trainee->id }}">
                                                <img src="https://img.icons8.com/fluency-systems-filled/48/808080/delete-trash.png" alt="delete-trash"/>
                                            </a>
                                            <a href="#" style="text-decoration: none; font-size: 24px; color: grey; margin-left: 20px;" title="Change Password" data-toggle="modal" data-target="#changePasswordModal{{ $trainee->id }}">
                                                <img src="https://img.icons8.com/external-sbts2018-outline-sbts2018/58/808080/external-change-password-basic-ui-elements-2.3-sbts2018-outline-sbts2018.png" alt="change-password"/>
                                            </a>
                                        </td>
                                        <div class="modal fade" id="confirmChangeStatusModal{{ $trainee->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmChangeStatusModalLabel{{ $trainee->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="confirmChangeStatusModalLabel">Confirm Change Account Status</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to change {{ $trainee->name }}'s account status?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <a id="confirmChangeStatusBtn{{ $trainee->id }}" class="btn btn-primary" href="{{ route('change-account-status', ['selected' => urlencode($trainee->name)]) }}">Confirm</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="deleteConfirmationModal{{ $trainee->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel{{ $trainee->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete this account?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <a href="{{ route('delete-exist-account', ['traineeID' => $trainee->id]) }}" class="btn btn-danger">Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="changePasswordModal{{ $trainee->id }}" tabindex="-1" role="dialog" aria-labelledby="changePasswordModal{{ $trainee->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Change Password</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="changePasswordForm" action="{{ route('admin-change-password', ['id' => $trainee->id, 'type' => 'Trainee']) }}" method="post">
                                                            @csrf
                                                            <div class="form-group">
                                                                <label for="newPassword">New Password:</label>
                                                                <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                                                                <small id="newPasswordHelp" class="form-text text-muted">
                                                                    The password should contain at least 8 characters, 1 uppercase letter and 1 special character.
                                                                </small>
                                                                @error('newPassword')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="confirmPassword">Confirm Password:</label>
                                                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                                                @error('confirmPassword')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Submit</button>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                                       
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="other-list" role="tabpanel" aria-labelledby="supervisor-list-tab">
                    <div class="row">
                        <div class="col-md-4">
                            <!-- Search Bar -->
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Search supervisor" id="search-input-sv">
                                <button class="btn btn-outline-secondary" type="button" id="search-button">Search</button>
                            </div>
                        </div>
                        <div class="col-md-8">
                        </div>
                    </div>
                    <div style="max-height: 350px; overflow-y: scroll;">
                        <table class="all-supervisor-list" id="all-supervisor-list">
                            <thead>
                                <tr>
                                    <th>Name
                                        <button class="sort-button-sv" data-column="0" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th>Section
                                        <button class="sort-button-sv" data-column="1" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th>Department
                                        <button class="sort-button-sv" data-column="2" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th>Phone Number
                                        <button class="sort-button-sv" data-column="3" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th>SAINS Email
                                        <button class="sort-button-sv" data-column="4" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($supervisors as $supervisor)
                                    <tr>
                                        <td>{{ $supervisor->name }}</td>
                                        <td>{{ $supervisor->section }}</td>
                                        <td>{{ $supervisor->department }}</td>
                                        <td>{{ $supervisor->phone_number }}</td>
                                        <td>{{ $supervisor->sains_email }}</td>
                                        <td style="width: 20%;">
                                            <a href="{{ route('admin-edit-profile', ['selected' => urlencode($supervisor->name)]) }}" style="text-decoration: none; color: grey; font-size: 24px; margin-left: 20px;" title="Edit Profile">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a href="#" style="text-decoration: none; font-size: 24px; color: grey; margin-left: 20px;" title="Delete Account" data-toggle="modal" data-target="#deleteSVaccConfirmationModal{{ $supervisor->id }}">
                                                <img src="https://img.icons8.com/fluency-systems-filled/48/808080/delete-trash.png" alt="delete-trash"/>
                                            </a>
                                            <a href="#" style="text-decoration: none; font-size: 24px; color: grey; margin-left: 20px;" title="Change Password" data-toggle="modal" data-target="#changeSVPasswordModal{{ $supervisor->id }}">
                                                <img src="https://img.icons8.com/external-sbts2018-outline-sbts2018/58/808080/external-change-password-basic-ui-elements-2.3-sbts2018-outline-sbts2018.png" alt="change-password"/>
                                            </a>
                                        </td>
                                        <div class="modal fade" id="deleteSVaccConfirmationModal{{ $supervisor->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteSVaccConfirmationModalLabel{{ $supervisor->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete this account?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <a href="{{ route('delete-exist-sv-account', ['supervisorID' => $supervisor->id]) }}" class="btn btn-danger">Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="changeSVPasswordModal{{ $supervisor->id }}" tabindex="-1" role="dialog" aria-labelledby="changeSVPasswordModal{{ $supervisor->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Change Password</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="changeSVPasswordForm" action="{{ route('admin-change-password', ['id' => $supervisor->id, 'type' => 'Supervisor']) }}" method="post">
                                                            @csrf
                                                            <div class="form-group">
                                                                <label for="newPassword">New Password:</label>
                                                                <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                                                                <small id="newPasswordHelp" class="form-text text-muted">
                                                                    The password should contain at least 8 characters, 1 uppercase letter and 1 special character.
                                                                </small>
                                                                @error('newPassword')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="confirmPassword">Confirm Password:</label>
                                                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>

                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Submit</button>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
<script>
    const supervisorFilterButtons = document.querySelectorAll('.sort-button-sv');
    const traineeFilterButtons = document.querySelectorAll('.sort-button-trainee');
    let columnToSort = -1; // Track the currently sorted column
    let ascending = true; // Track the sorting order

    supervisorFilterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const column = button.dataset.column;
            if (column === columnToSort) {
                ascending = !ascending; // Toggle sorting order if the same column is clicked
            } else {
                columnToSort = column;
                ascending = true; // Default to ascending order for the clicked column
            }

            // Call the function to sort the table
            sortTableSV(column, ascending);
        });
    });

    //search function for searching trainee
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("search-input-trainee");
        const traineeTable = document.getElementById("all-trainee-list");

        searchInput.addEventListener("keyup", function () {
            const searchValue = searchInput.value.toLowerCase();

            for (let i = 1; i < traineeTable.rows.length; i++) {
                const row = traineeTable.rows[i];
                const name = row.cells[0].textContent.toLowerCase();
                const personalEmail = row.cells[1].textContent.toLowerCase();
                const sainsEmail = row.cells[2].textContent.toLowerCase();
                
                if (name.includes(searchValue) || personalEmail.includes(searchValue) || sainsEmail.includes(searchValue)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        });
    });

    //search function for searching supervisor
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("search-input-sv");
        const svTable = document.getElementById("all-supervisor-list");

        searchInput.addEventListener("keyup", function () {
            const searchValue = searchInput.value.toLowerCase();

            for (let i = 1; i < svTable.rows.length; i++) {
                const row = svTable.rows[i];
                const name = row.cells[0].textContent.toLowerCase();
                const personalEmail = row.cells[3].textContent.toLowerCase();
                const sainsEmail = row.cells[4].textContent.toLowerCase();
                
                if (name.includes(searchValue) || personalEmail.includes(searchValue) || sainsEmail.includes(searchValue)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        });
    });

    //sort function at the table head
    traineeFilterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const column = button.dataset.column;
            if (column === columnToSort) {
                ascending = !ascending; // Toggle sorting order if the same column is clicked
            } else {
                columnToSort = column;
                ascending = true; // Default to ascending order for the clicked column
            }

            // Call the function to sort the table
            sortTableTrainee(column, ascending);
        });
    });

    function sortTableSV(column, ascending) {
        const table = document.getElementById('all-supervisor-list');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        rows.sort((a, b) => {
            const cellA = a.querySelectorAll('td')[column].textContent;
            const cellB = b.querySelectorAll('td')[column].textContent;
            return ascending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
        });

        tbody.innerHTML = '';
        rows.forEach((row) => {
            tbody.appendChild(row);
        });
    }

    function sortTableTrainee(column, ascending) {
        const table = document.getElementById('all-trainee-list');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        rows.sort((a, b) => {
            const cellA = a.querySelectorAll('td')[column].textContent;
            const cellB = b.querySelectorAll('td')[column].textContent;
            return ascending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
        });

        tbody.innerHTML = '';
        rows.forEach((row) => {
            tbody.appendChild(row);
        });
    }
</script>
</html>
@endsection