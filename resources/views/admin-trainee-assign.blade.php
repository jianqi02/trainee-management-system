@extends('layouts.admin')
@section('pageTitle', 'Trainee Assignment')

@section('breadcrumbs', Breadcrumbs::render('sv-assign'))

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .breadcrumb{
            width: 1500px;
        } 
    </style>
</head>
<body>
    <div class="content">
        <h1>Supervisor Assignment For Trainee</h1>
    <main>
        <div class="trainee-assign-container">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <div class="tab-content" id="myTabContent">
                <div class="input-group trainee-assign-input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search trainee or supervisor..." id="assign-trainee-for-sv-search">
                    <button class="btn btn-outline-secondary" type="button" id="search-button">Search</button>
                </div>
                    <div style="max-height: 350px; overflow-y: scroll;">
                        <table class="assign-supervisor-to-trainee-list" id="assign-supervisor-to-trainee-list">
                            <thead>
                                <tr class="trainee-assign-tr">
                                    <th class="trainee-assign-th">Trainee Name
                                        <button class="sort-button" data-column="0" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="trainee-assign-th">Current Assigned Supervisor
                                        <button class="sort-button" data-column="1" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="trainee-assign-th">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trainees as $trainee)
                                    <tr id="supervisor-{{ $trainee->name }}" class="trainee-assign-tr">
                                        <td style="width: 30%;" class="trainee-assign-td">{{ $trainee->name}}</td>
                                        <td class="trainee-assign-td">
                                            @foreach ($assignedSupervisorList as $assignment)
                                            <!-- Check if the current trainee is assigned to the current supervisor -->
                                                @if (strcasecmp($assignment->trainee->name, $trainee->name) === 0)
                                                    {{ $assignment->supervisor->name }}<br>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td class="trainee-assign-td">
                                            <a href="{{ route('admin-assign-supervisor-function', ['selected_trainee' => urlencode($trainee->name)]) }}" style="text-decoration: none;" title="Assign Supervisor">
                                                <i class="fas fa-user-plus action-btn" style="color: grey; font-size: 24px;"></i>
                                            </a>
                                            <a href="{{ route('admin-remove-assigned-supervisor-function', ['selected_trainee' => urlencode($trainee->name)]) }}" style="text-decoration: none; margin-left: 20px;" title="Remove Assigned Supervisor">
                                                <i class="fa fa-trash" style="color: grey; font-size: 24px;"></i>
                                            </a>
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