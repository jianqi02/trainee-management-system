@extends('layouts.admin')
@section('pageTitle', 'Trainee List')

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    @error('name')
        <span class="text-danger" style="margin-left: 150px;">{{ $message }}</span>
    @enderror
    <div class="content">
        <h1>Trainee List</h1>
    <main>
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <a class="btn btn-secondary" href="/admin-create-new-trainee-record">Create new trainee record</a>
        <div class="trainee-list-container">

            <div class="tab-content" id="myTabContent">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search trainee" id="assign-trainee-for-sv-search">
                    <button class="btn btn-outline-secondary" type="button" id="search-button">Search</button>
                </div>
                    <div style="max-height: 300px; overflow-y: scroll;">
                        <table class="all-trainee-list-table" id="all-trainee-list-table">
                            <thead>
                                <tr>
                                    <th class="trainee-list-th">Trainee Name
                                        <button class="sort-button" data-column="0" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="trainee-list-th">Internship Start Date
                                        <button class="sort-button" data-column="1" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="trainee-list-th">Internship End Date
                                        <button class="sort-button" data-column="2" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="trainee-list-th">Status
                                        <button class="sort-button" data-column="3" style="border: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                                <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="trainee-list-th">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trainees as $trainee)
                                    <tr id="trainee-{{ $trainee->name }}" class="trainee-list-tr">
                                        <td class="trainee-list-td">{{ $trainee->name}}</td>
                                        <td class="trainee-list-td">
                                            @if ($trainee->internship_start)
                                                {{ $trainee->internship_start }}
                                            @else
                                                Not Assigned
                                            @endif
                                        </td>
                                        <td class="trainee-list-td">
                                            @if ($trainee->internship_end)
                                                {{ $trainee->internship_end }}
                                            @else
                                                Not Assigned
                                            @endif
                                        </td>
                                        <td class="trainee-list-td">
                                            @if ($trainee->traineeRecordExists())
                                                Registered
                                            @else
                                                Not Registered
                                            @endif
                                        </td>                                      
                                        <td class="trainee-list-td">
                                            <a class="icon-link" href="/admin-trainee-assign">
                                                <i class="fas fa-user-plus action-btn"></i>
                                                <span class="tooltip">Assign Supervisor</span>
                                            </a>
                                            <a class="icon-link" href="#" data-toggle="modal" data-target="#confirmDeleteModal" data-record-id="{{ $trainee->id }}">
                                                <i class="fas fa-trash-alt action-btn"></i>
                                                <span class="tooltip">Delete Record</span>
                                            </a>

                                            <!-- Modal for double confirmation -->
                                            <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="confirmDeleteModalLabel" style="color: inherit; text-decoration: none;">Confirm Deletion</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete this record?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <a id="confirmDeleteButton" href="#" class="btn btn-danger" style="color: white; text-decoration: none;">Confirm Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <a class="icon-link" href="{{ route('edit-record' , ['id' => $trainee->id])}}">
                                                <i class="fas fa-edit action-btn"></i>
                                                <span class="tooltip">Edit Record</span>
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

    //search function for searching trainee
    document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("assign-trainee-for-sv-search");
    const svTable = document.getElementById("all-trainee-list-table");

        searchInput.addEventListener("keyup", function () {
            const searchValue = searchInput.value.toLowerCase();

            for (let i = 1; i < svTable.rows.length; i++) {
                const row = svTable.rows[i];
                const name = row.cells[0].textContent.toLowerCase();
                
                if (name.includes(searchValue)) {
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
        const table = document.getElementById('all-trainee-list-table');
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

    $('#confirmDeleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var recordId = button.data('record-id'); // Extract record ID from data- attribute
        var confirmDeleteButton = $('#confirmDeleteButton');
        
        // Update the href attribute with the correct route including the recordId
        confirmDeleteButton.attr('href', "{{ url('delete-trainee-record') }}/" + recordId);
    });
</script>
</html>

@endsection