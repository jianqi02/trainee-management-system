@extends('layouts.admin')
@section('pageTitle', 'Admin Dashboard')

@section('breadcrumbs', Breadcrumbs::render('dashboard'))

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0-beta2/css/all.min.css" integrity="sha384-4ByBMk1MxXrdS6JEIo0DDXkBC32b4V4or9jG7r1B4mXs6wM4Xf+OBo0IfkCFC73J4" crossorigin="anonymous">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="content">
        <div class="container mt-5">
            <h1 style="margin-top: -50px;">Dashboard</h1>     
            <form action="{{ route('admin-dashboard') }}" style="margin-top: 20px;">
                <div style="display: flex; align-items: center;">
                    <label for="week" style="margin-right: 10px; font-weight: bold; color: #555;">Select a week:</label>
                    <input type="week" id="week" name="week" value="{{ $weekRequired }}" style="padding: 5px; border: 1px solid #ccc; border-radius: 3px; font-size: 16px;">
                    <button type="submit" style="background-color: #007BFF; color: #fff; border: none; border-radius: 3px; padding: 5px 10px; font-size: 16px; cursor: pointer; margin-left: 20px;">Display Information</button>
                </div>
            </form>
            <div class="row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 22px;">Active Trainee(s)</h5>
                            <p class="card-text">{{ $count }}</p>
                        </div>
                    </div>
                </div>
        
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 22px;">Total Trainee(s)</h5>
                            <h6 class="card-title" style="font-size: 14px;">Total records of trainee(s)</h6>
                            <p class="card-text">{{ $totalTrainee }}</p>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="row" style="margin-bottom: 60px;">
                <div class="col-md-4 mb-3">
                    <div class="card seating-card">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 22px;">Empty Seat(s) Available</h5>
                            <h6 class="card-title" style="font-size: 14px;">from {{ $start_date }} to {{ $end_date }}</h6>
                            <p class="card-text">{{ $weeklyData['empty_seat_count'] }}</p>
                        </div>
                    </div>
                </div>
    
                <div class="col-md-4 mb-3">
                    <div class="card seating-card">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 22px;">Seat(s) Occupied</h5>
                            <h6 class="card-title" style="font-size: 14px;">from {{ $start_date }} to {{ $end_date }}</h6>
                            <p class="card-text">{{ $weeklyData['occupied_seat_count'] }}</p>
                        </div>
                    </div>
                </div>
    
                <div class="col-md-4 mb-3">
                    <div class="card seating-card">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 22px;">Total Seat(s)</h5>
                            <h6 class="card-title" style="font-size: 14px;">from {{ $start_date }} to {{ $end_date }}</h6>
                            <p class="card-text">{{ $weeklyData['total_seat_count'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (is_null($currentSeatingPlan))
            <div class="alert alert-warning text-center">No seating plan is created for this week.</div>
        @else
            <!-- Current Week's Seating Plan Table -->
            <div class="card mt-4" style="width: 83%; margin-left: 115px;"> <!-- Add margin-top here -->
                <div class="card-body">
                    <h5 class="card-title">Current Week Seating Plan</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped"> <!-- Adjust width as needed -->
                            <thead>
                                <tr>
                                    <th>Seat Code</th>
                                    <th>Assigned To</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($currentSeatingDetails as $seatCode => $assignedTo)
                                    <tr>
                                        <td>{{ $seatCode }}</td>
                                        <td>{{ $assignedTo }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>                        
                    </div>
                </div>
            </div>
        @endif

        

        <div class="container trainee-list-container">
            <div class="row">
                <div class="col-md-4">
                    <!-- Search Bar -->
                    <div class="input-group mb-3" style="width: 1075px;">
                        <input type="text" class="form-control" placeholder="Search trainees..." id="search-input">
                        <button class="btn btn-outline-secondary" type="button" id="search-button">Search</button>
                    </div>
                </div>
                <div class="col-md-8">
                </div>
            </div>
        </div>
            
        <div class="container mt-4 trainee-list-table">
            <table class="table table-striped" id="trainee-table">
                <thead>
                    <tr>
                        <th>Name
                            <button class="sort-button-trainee" data-column="0" style="border: none; background: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                    <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                </svg>
                            </button>
                        </th>
                        <th>Internship Date (Start)
                            <button class="sort-button-trainee" data-column="1" style="border: none; background: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                    <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                </svg>
                            </button>
                        </th>
                        <th>Internship Date (End)
                            <button class="sort-button-trainee" data-column="2" style="border: none; background: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                    <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                </svg>
                            </button>
                        </th>
                        <th>Graduation Date
                            <button class="sort-button-trainee" data-column="3" style="border: none; background: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                    <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                </svg>
                            </button>
                        </th>
                        <th>Logbook Submitted
                            <button class="sort-button-trainee" data-column="4" style="border: none; background: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                    <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                </svg>
                            </button>
                        </th>
                        <th>Expertise
                            <button class="sort-button-trainee" data-column="5" style="border: none; background: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                    <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                </svg>
                            </button>
                        </th>
                    </tr> 
                </thead>
                <tbody>
                    @foreach ($trainees as $trainee)
                        <tr id="trainee-{{ $trainee->name }}">
                            <td>{{ $trainee->name }}</td>
                            <td>{{ $trainee->internship_start }}</td>
                            <td>{{ $trainee->internship_end }}</td>
                            <td>{{ $trainee->graduate_date }}</td>
                            <td>
                                @if($trainee->logbooks->isNotEmpty())
                                    <a href="{{ route('view-and-upload-logbook', ['traineeName' => $trainee->name]) }}">Yes</a>
                                @else
                                    No
                                @endif
                            </td>
                            <td>{{ $trainee->expertise }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        
    </div>
</body>
<script>
    const traineeFilterButtons = document.querySelectorAll('.sort-button-trainee');
    let columnToSort = -1; // Track the currently sorted column
    let ascending = true; // Track the sorting order

    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("search-input");
        const traineeTable = document.getElementById("trainee-table");

        searchInput.addEventListener("keyup", function () {
            const searchValue = searchInput.value.toLowerCase();

            for (let i = 1; i < traineeTable.rows.length; i++) {
                const row = traineeTable.rows[i];
                const name = row.cells[0].textContent.toLowerCase();
                
                if (name.includes(searchValue)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        });
    });

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

    

    function sortTableTrainee(column, ascending) {
        const table = document.getElementById('trainee-table');
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