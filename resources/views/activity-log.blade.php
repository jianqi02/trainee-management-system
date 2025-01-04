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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .activity-log {
            margin-top: 20px;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .form-control {
            border-radius: 0.25rem;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .table-responsive {
            margin-top: 30px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            max-height: 500px; /* Set a maximum height for scroll */
            overflow-y: auto;
        }

        .table th {
            background-color: #343a40;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }

        .badge-success {
            background-color: #28a745;
            font-weight: bold;
        }

        .badge-danger {
            background-color: #dc3545;
            font-weight: bold;
        }

        .filter-card {
            background-color: #f1f3f5;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .filter-card label {
            font-weight: 600;
            font-size: 14px;
        }

        .page-title {
            font-weight: bold;
            color: #343a40;
        }

        .no-logs {
            text-align: center;
            color: #888;
            padding: 20px 0;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="page-title mt-5 mb-4">Activity Log</h2>

    <div class="filter-card">
        <form method="POST" action="{{ route('activity-log-filter') }}">
            @csrf
            <div class="form-row">
                <div class="col-md-3 mb-3">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" value="{{ $username ?? '' }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="fromDate">From Date</label>
                    <input type="date" class="form-control" id="fromDate" name="fromDate" value="{{ $start_date_input ?? '' }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="toDate">To Date</label>
                    <input type="date" class="form-control" id="toDate" name="toDate" value="{{ $end_date_input ?? '' }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="outcome">Outcome</label>
                    <select class="form-control" id="outcome" name="outcome">
                        <option value="" {{ ($outcome ?? '') === '' ? 'selected' : '' }}>Select Outcome</option>
                        <option value="success" {{ ($outcome ?? '') === 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failed" {{ ($outcome ?? '') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>    
    </div>

    <div class="table-responsive">
        @if ($activityLogs->count() > 0)
        <table id="activityLogTable" class="table table-bordered table-striped table-hover">
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
                @foreach($activityLogs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>{{ $log->username }}</td>
                        <td>{{ $log->action }}</td>
                        <td><span class="badge badge-{{ $log->outcome === 'success' ? 'success' : 'danger' }}">{{ ucfirst($log->outcome) }}</span></td>
                        <td class="details">{{ $log->details }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <div class="no-logs">No activity logs found</div>
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        $('#activityLogTable').DataTable({
            "paging": true,
            "searching": true,
            "info": true,
            "order": [[0, "desc"]]
        });
    });
</script>

</body>
</html>
@endsection
