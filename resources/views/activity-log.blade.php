@extends('layouts.admin')
@section('pageTitle', 'Activity Log')

@section('breadcrumbs', Breadcrumbs::render('activity-log'))

@section('content')<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <style>
        .container {
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
            width: 100%;
            border-collapse: collapse;
        }

        .table-responsive th {
            font-size: 16px;
        }

        .table-responsive td {
            font-size: 12px;
            overflow: hidden;
        }

        .table td.details {
            max-width: 600px; /* Adjust the max-width as needed */
            word-wrap: break-word;
        }

    </style>
</head>
<body>

<div class="container">
    <h2 class="mt-5 mb-4">Activity Log</h2>

    <form method="POST" action="{{ route('activity-log-filter') }}">
        @csrf
        <div class="form-row mb-4">
            <div class="col-md-3 mb-3">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" value= {{ $username ?? ''}}>
            </div>
            <div class="col-md-2 mb-3">
                <label for="fromDate">From Date:</label>
                <input type="date" class="form-control" id="fromDate" name="fromDate" value="{{ $start_date_input ?? '' }}">
            </div>
            <div class="col-md-2 mb-3">
                <label for="toDate">To Date:</label>
                <input type="date" class="form-control" id="toDate" name="toDate" value="{{ $end_date_input ?? '' }}">
            </div>
            <div class="col-md-2 mb-3">
                <label for="outcome">Outcome:</label>
                <select class="form-control" id="outcome" name="outcome">
                    <option value="" {{ ($outcome ?? '') === '' ? 'selected' : '' }}>Select Outcome</option>
                    <option value="success" {{ ($outcome ?? '') === 'success' ? 'selected' : '' }}>Success</option>
                    <option value="failed" {{ ($outcome ?? '') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <button type="submit" class="btn btn-primary" style="margin-top: 33px;">Filter</button>
            </div>
        </div>
    </form>    

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
                        <td><div style="background-color: {{ $log->outcome === 'success' ? '#39e75f' : 'red' }}; border-radius: 20px; color: white; width: 85px; height: 20px; text-align: center; font-weight: bold;">{{ $log->outcome }}</div></td>
                        <td class="details">{{ $log->details }}</td>
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
