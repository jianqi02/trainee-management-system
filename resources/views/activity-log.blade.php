@extends('layouts.admin')
@section('pageTitle', 'Activity Log')

@section('breadcrumbs', Breadcrumbs::render('activity-log'))

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <style>

        .container-activity {
            max-width: 1200px;
            margin-left: 130px;
        }

        .activity-log {
            margin-top: 20px;
        }

        .table-responsive {
            margin-top: 20px;
            max-width: 1000px;
            max-height: 400px;
            overflow-y: auto;
            overflow-x: auto;
        }

        .table-responsive table {
            width: 50%;
            border-collapse: collapse;
        }

        .table-responsive th {
            font-size: 16px;
        }

        .table-responsive td {
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

    </style>
</head>
<body>

<div class="container container-activity">
    <h2 class="mt-5 mb-4">Activity Log</h2>

    <!-- Filter Options (if needed) -->
    <!-- <div class="form-row mb-4">
        <div class="col-md-3">
            <input type="text" class="form-control" placeholder="Search...">
        </div>
        <div class="col-md-3">
            <select class="form-control">
                <option>Select User</option>
                     Add user options dynamically if needed
            </select> 
        </div>
        <div class="col-md-3">
            <input type="date" class="form-control">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary">Filter</button>
        </div>
    </div> -->

    <!-- Activity Log Table -->
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Username</th>
                <th>Action</th>
                <th>Outcome</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <!-- Loop through your activity log records and populate the table rows -->
            @foreach($activityLogs as $log)
                <tr>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $log->username }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->outcome }}</td>
                    <td>{{ $log->details }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


<!-- Link to Bootstrap JS (you may need to adjust the path based on your project setup) -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>
</html>
@endsection
