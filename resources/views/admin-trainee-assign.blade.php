@extends('layouts.admin')

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Assignment</title>
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

    .container{
        height: 45px;
        
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

</style>
<body>
    <div class="content">
        <h1>Supervisor Assignment For Trainee</h1>
    <main>
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <div class="tab-content" id="myTabContent">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search trainee or supervisor..." id="assign-trainee-for-sv-search">
                    <button class="btn btn-outline-secondary" type="button" id="search-button">Search</button>
                </div>
                    <div style="max-height: 350px; overflow-y: scroll;">
                        <table class="assign-supervisor-to-trainee-list" id="assign-supervisor-to-trainee-list">
                            <thead>
                                <tr>
                                    <th>Trainee Name
                                        <button class="sort-button" data-column="0" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th>Current Assigned Supervisor
                                        <button class="sort-button" data-column="1" style="border: none;">
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
                                    <tr id="supervisor-{{ $trainee->name }}">
                                        <td>{{ $trainee->name}}</td>
                                        <td>
                                            @foreach ($assignedSupervisorList as $assignment)
                                            <!-- Check if the current trainee is assigned to the current supervisor -->
                                                @if (strcasecmp($assignment->trainee->name, $trainee->name) === 0)
                                                    {{ $assignment->supervisor->name }}<br>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            <!-- Add your buttons/actions here -->
                                            
                                            <a class="btn btn-secondary" href="{{ route('admin-assign-supervisor-function', ['selected_trainee' => urlencode($trainee->name)]) }}">Assign Supervisor</a>
                                            <a class="btn btn-secondary" href="{{ route('admin-remove-assigned-supervisor-function', ['selected_trainee' => urlencode($trainee->name)]) }}">Remove Assigned Supervisor</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </main>
</div>
</body>
<script>
    const filterButtons = document.querySelectorAll('.sort-button');
    let columnToSort = -1; // Track the currently sorted column
    let ascending = true; // Track the sorting order

    //search function for searching supervisor
    document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("assign-trainee-for-sv-search");
    const svTable = document.getElementById("assign-supervisor-to-trainee-list");

        searchInput.addEventListener("keyup", function () {
            const searchValue = searchInput.value.toLowerCase();

            for (let i = 1; i < svTable.rows.length; i++) {
                const row = svTable.rows[i];
                const traineeName = row.cells[0].textContent.toLowerCase();
                const svName = row.cells[1].textContent.toLowerCase();
                
                if (traineeName.includes(searchValue) || svName.includes(searchValue)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        });
    });
    
    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const column = button.dataset.column;
            if (column === columnToSort) {
                ascending = !ascending; // Toggle sorting order if the same column is clicked
            } else {
                columnToSort = column;
                ascending = true; // Default to ascending order for the clicked column
            }

            // Call the function to sort the table
            sortTable(column, ascending);
        });
    });

    function sortTable(column, ascending) {
        const table = document.getElementById('assign-supervisor-to-trainee-list');
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