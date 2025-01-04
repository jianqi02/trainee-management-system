@extends('layouts.admin')
@section('pageTitle', 'Trainee Assignment')

@section('breadcrumbs', Breadcrumbs::render('sv-assign'))

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
        }

        .sort-button i {
            font-size: 14px;
            color: black;
        }

        .content {
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        h1 {
            font-size: 22px;
            font-weight: bold;
            color: #343a40;
            text-align: center;
            margin-bottom: 20px;
        }

        .trainee-assign-container {
            background-color: #ffffff;
            padding: 20px;
            width: 100%; 
            max-width: 1200px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group input {
            border-radius: 20px;
            font-size: 14px;
        }

        .btn-outline-secondary {
            border-radius: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            transition: background-color 0.3s ease;
            font-size: 14px;
        }

        .btn-outline-secondary:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #343a40;
            color: white;
        }

        .sort-button {
            margin-left: 5px;
            cursor: pointer;
            color: white;
            background: none;
            border: none;
        }

        .sort-button svg {
            width: 10px;
            height: 10px;
        }

        tbody tr:hover {
            background-color: #f1f3f5;
        }

        .action-btn {
            cursor: pointer;
            transition: color 0.3s ease;
            font-size: 18px;
        }

        .action-btn:hover {
            color: #007bff;
        }

        .alert-success {
            margin-bottom: 15px;
            font-size: 14px;
        }

        /* Modern scrollbar */
        .scrollable-table {
            max-height: 350px; 
            overflow-y: auto;
        }

        .scrollable-table::-webkit-scrollbar {
            width: 6px;
        }

        .scrollable-table::-webkit-scrollbar-thumb {
            background-color: #007bff;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="content">
    <div class="trainee-assign-container">
        <h1>Supervisor Assignment</h1>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <!-- Search Bar -->
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Search trainee or supervisor..." id="assign-trainee-for-sv-search">
            <button class="btn btn-outline-secondary" type="button" id="search-button">Search</button>
        </div>

        <!-- Table -->
        <div class="scrollable-table">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Trainee Name
                            <button class="sort-button" data-column="0">
                                <i class="fas fa-sort"></i>
                            </button>
                        </th>
                        <th>Current Assigned Supervisor
                            <button class="sort-button" data-column="1">
                                <i class="fas fa-sort"></i>
                            </button>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trainees as $trainee)
                        <tr>
                            <td>{{ $trainee->name }}</td>
                            <td>
                                @foreach ($assignedSupervisorList as $assignment)
                                    @if (strcasecmp($assignment->trainee->name, $trainee->name) === 0)
                                        {{ $assignment->supervisor->name }}<br>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('admin-assign-supervisor-function', ['selected_trainee' => urlencode($trainee->name)]) }}" title="Assign Supervisor">
                                    <i class="fas fa-user-plus action-btn"></i>
                                </a>
                                <a href="{{ route('admin-remove-assigned-supervisor-function', ['selected_trainee' => urlencode($trainee->name)]) }}" title="Remove Assigned Supervisor" style="margin-left: 15px;">
                                    <i class="fa fa-trash action-btn"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    const filterButtons = document.querySelectorAll('.sort-button');
    let columnToSort = -1;
    let ascending = true;

    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("assign-trainee-for-sv-search");
        const svTable = document.querySelector('tbody');

        // Search filter
        searchInput.addEventListener("keyup", function () {
            const searchValue = searchInput.value.toLowerCase();

            for (const row of svTable.rows) {
                const traineeName = row.cells[0].textContent.toLowerCase();
                const svName = row.cells[1].textContent.toLowerCase();

                row.style.display = (traineeName.includes(searchValue) || svName.includes(searchValue)) ? "" : "none";
            }
        });
    });

    // Sorting logic
    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const column = button.dataset.column;
            ascending = column === columnToSort ? !ascending : true;
            columnToSort = column;
            sortTable(column, ascending);
        });
    });

    function sortTable(column, ascending) {
        const rows = Array.from(document.querySelector('tbody').rows);

        rows.sort((a, b) => {
            const cellA = a.cells[column].textContent.trim().toLowerCase();
            const cellB = b.cells[column].textContent.trim().toLowerCase();

            return ascending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
        });

        document.querySelector('tbody').append(...rows);
    }
</script>

</body>
</html>

@endsection
