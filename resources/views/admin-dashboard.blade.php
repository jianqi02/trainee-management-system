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

            @if (is_null($currentSeatingPlan))
            <div class="alert alert-warning text-center">No seating plan is created for this week.</div>
            @else
                <!-- Current Week's Seating Plan Table -->
                <div class="card mt-4" style="width: 100%; "> <!-- Add margin-top here -->
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

            <form action="{{ route('admin-dashboard') }}" method="GET" style="margin-top: 20px;">
                <div style="display: flex; align-items: center;">
                    <label for="year" style="margin-right: 10px; font-weight: bold; color: #555;">Select a year:</label>
                    
                    <!-- Use a number input for year picker -->
                    <input type="number" id="year" name="year" min="2000" max="2100" value="{{ request('year', Carbon\Carbon::now()->year) }}" style="padding: 5px; border: 1px solid #ccc; border-radius: 3px; font-size: 16px;">
                    
                    <button type="submit" style="background-color: #007BFF; color: #fff; border: none; border-radius: 3px; padding: 5px 10px; font-size: 16px; cursor: pointer; margin-left: 20px;">
                        Display Information
                    </button>
                </div>
            </form>
            

            <div class="container">
                <!-- Chart for New Trainees per Month -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">New Trainees Per Month</h5>
                        <canvas id="newTraineesChart" width="400" height="200"></canvas>
                    </div>
                </div>
            
                <!-- Chart for Total Trainees per Month -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Total Trainees Per Month</h5>
                        <canvas id="totalTraineesChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <br>
            <h5>Trainee Task Statistics</h5>

            <!-- Search Box -->
            <div class="mb-3">
                <input type="text" id="traineeSearch" class="form-control" placeholder="Search for trainee name..." onkeyup="filterTrainees()">
            </div>
        
            <!-- Table with Sorting and Search -->
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered" id="traineeTable">
                    <thead>
                        <tr>
                            <th onclick="sortTable(0)">Trainee Name &#9650;&#9660;</th>
                            <th onclick="sortTable(1)">Total Tasks &#9650;&#9660;</th>
                            <th onclick="sortTable(2)">Not Started &#9650;&#9660;</th>
                            <th onclick="sortTable(3)">Ongoing &#9650;&#9660;</th>
                            <th onclick="sortTable(4)">Completed &#9650;&#9660;</th>
                            <th onclick="sortTable(5)">Postponed &#9650;&#9660;</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trainees as $trainee)
                            <tr>
                                <td>{{ $trainee->name }}</td>
                                <td>{{ $traineeTaskStats[$trainee->id]['total'] }}</td>
                                <td>{{ $traineeTaskStats[$trainee->id]['not_started'] }}</td>
                                <td>{{ $traineeTaskStats[$trainee->id]['ongoing'] }}</td>
                                <td>{{ $traineeTaskStats[$trainee->id]['completed'] }}</td>
                                <td>{{ $traineeTaskStats[$trainee->id]['postponed'] }}</td>
                                <td>
                                    <button class="btn btn-primary" onclick="generatePieChart('{{ $trainee->name }}', {{ json_encode($traineeTaskStats[$trainee->id]) }})">
                                        Generate Pie Chart
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="pieChartModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:1000; background:white; padding:20px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.5);">
            <h4 id="traineeName" style="text-align:center"></h4>
            <canvas id="pieChartCanvas"></canvas>
            <button onclick="closeModal()" style="display:block; margin:20px auto; padding:10px 20px; background-color:#007BFF; color:white; border:none; border-radius:5px;">
                Close
            </button>
        </div>

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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data from the controller
    var months = @json($months);
    var newTrainees = @json($newTrainees);
    var totalTraineesPerMonth = @json($totalTraineesPerMonth);

    // New Trainees Per Month Chart
    var ctx1 = document.getElementById('newTraineesChart').getContext('2d');
    var newTraineesChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'New Trainees',
                data: newTrainees,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                fill: true,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Total Trainees Per Month Chart
    var ctx2 = document.getElementById('totalTraineesChart').getContext('2d');
    var totalTraineesChart = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Total Trainees',
                data: totalTraineesPerMonth,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                fill: true,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<script>
    function filterTrainees() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("traineeSearch");
        filter = input.value.toUpperCase();
        table = document.getElementById("traineeTable");
        tr = table.getElementsByTagName("tr");

        // Loop through all table rows, and hide those that don't match the search query
        for (i = 1; i < tr.length; i++) { // Skip the header row (i = 1)
            td = tr[i].getElementsByTagName("td")[0]; // Get the trainee name column
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    // Sort function
    function sortTable(n) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById("traineeTable");
        switching = true;
        // Set the sorting direction to ascending:
        dir = "asc"; 
        // Make a loop that will continue until no switching has been done:
        while (switching) {
            switching = false;
            rows = table.rows;
            // Loop through all table rows (except the first, which is the header):
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                // Get the two elements you want to compare, one from the current row and one from the next:
                x = rows[i].getElementsByTagName("td")[n];
                y = rows[i + 1].getElementsByTagName("td")[n];
                // Check if the two rows should switch place, based on the direction, asc or desc:
                if (dir == "asc") {
                    if (isNaN(x.innerHTML)) {
                        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    } else {
                        if (Number(x.innerHTML) > Number(y.innerHTML)) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                } else if (dir == "desc") {
                    if (isNaN(x.innerHTML)) {
                        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    } else {
                        if (Number(x.innerHTML) < Number(y.innerHTML)) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                }
            }
            if (shouldSwitch) {
                // If a switch has been marked, make the switch and mark that a switch has been done:
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                // Each time a switch is done, increase this count by 1:
                switchcount++; 
            } else {
                // If no switching has been done AND the direction is "asc", set the direction to "desc" and run the loop again.
                if (switchcount == 0 && dir == "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }
    }

    function generatePieChart(traineeName, taskStats) {
        // Show modal
        document.getElementById('pieChartModal').style.display = 'block';

        // Calculate total tasks
        var totalTasks = taskStats.total;
        
        // Calculate percentage for each task status
        var notStartedPercent = ((taskStats.not_started / totalTasks) * 100).toFixed(2);
        var ongoingPercent = ((taskStats.ongoing / totalTasks) * 100).toFixed(2);
        var completedPercent = ((taskStats.completed / totalTasks) * 100).toFixed(2);
        var postponedPercent = ((taskStats.postponed / totalTasks) * 100).toFixed(2);

        // Set trainee name and total tasks in the modal
        document.getElementById('traineeName').innerHTML = `Task Breakdown for ${traineeName} (Total Tasks: ${totalTasks})`;

        // Data for the chart
        var data = {
            labels: [
                `Not Started (${notStartedPercent}%)`, 
                `Ongoing (${ongoingPercent}%)`, 
                `Completed (${completedPercent}%)`, 
                `Postponed (${postponedPercent}%)`
            ],
            datasets: [{
                data: [
                    taskStats.not_started,
                    taskStats.ongoing,
                    taskStats.completed,
                    taskStats.postponed
                ],
                backgroundColor: ['#FF6384', '#FFCE56', '#36A2EB', '#4BC0C0'],
                hoverBackgroundColor: ['#FF6384', '#FFCE56', '#36A2EB', '#4BC0C0']
            }]
        };

        // Get the canvas element
        var ctx = document.getElementById('pieChartCanvas').getContext('2d');

        // Destroy existing chart instance (if any) to prevent overlap
        if (window.pieChartInstance) {
            window.pieChartInstance.destroy();
        }

        // Create a new pie chart
        window.pieChartInstance = new Chart(ctx, {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: `Task Breakdown for ${traineeName} (Total Tasks: ${totalTasks})`
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            var total = dataset.data.reduce((prev, curr) => prev + curr, 0);
                            var currentValue = dataset.data[tooltipItem.index];
                            var percentage = Math.floor(((currentValue / total) * 100) + 0.5);         
                            return data.labels[tooltipItem.index] + ': ' + percentage + '%';
                        }
                    }
                }
            }
        });
    }

    // Function to close the modal
    function closeModal() {
        document.getElementById('pieChartModal').style.display = 'none';
    }
</script>
</html>
@endsection