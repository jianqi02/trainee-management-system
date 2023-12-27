@extends('layouts.admin')
@section('pageTitle', 'Trainee List')

@section('breadcrumbs', Breadcrumbs::render('trainee-list'))

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

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search:</label>
                    <input type="text" class="form-control" id="search" placeholder="Type to search...">
                </div>
                <div class="col-md-3">
                    <label for="filterMonth" class="form-label">Filter by Start Date:</label>
                    <select id="filterMonth" class="form-select">
                        <option value="">All Months</option>
                        <option value="-01-">January</option>
                        <option value="-02-">February</option>
                        <option value="-03-">March</option>
                        <option value="-04-">April</option>
                        <option value="-05-">May</option>
                        <option value="-06-">June</option>
                        <option value="-07-">July</option>
                        <option value="-08-">August</option>
                        <option value="-09-">September</option>
                        <option value="-10-">October</option>
                        <option value="-11-">November</option>
                        <option value="-12-">December</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterEndMonth" class="form-label">Filter by End Date:</label>
                    <select id="filterEndMonth" class="form-select">
                        <option value="">All Months</option>
                        <option value="-01-">January</option>
                        <option value="-02-">February</option>
                        <option value="-03-">March</option>
                        <option value="-04-">April</option>
                        <option value="-05-">May</option>
                        <option value="-06-">June</option>
                        <option value="-07-">July</option>
                        <option value="-08-">August</option>
                        <option value="-09-">September</option>
                        <option value="-10-">October</option>
                        <option value="-11-">November</option>
                        <option value="-12-">December</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterStatus" class="form-label">Filter by Status:</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">All</option>
                        <option value="Registered">Registered</option>
                        <option value="Unregistered">Unregistered</option>
                    </select>
                </div>
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
                                                Unregistered
                                            @endif
                                        </td>                                      
                                        <td class="trainee-list-td">
                                            @if($trainee->traineeRecordExists())
                                            <a class="icon-link" href="{{ route('admin-go-profile', ['traineeName' => urlencode($trainee->name)]) }}" style="text-decoration: none; font-size: 16.5px; color: grey; margin-right: 10px;">
                                                <i class="fa fa-user" aria-hidden="true"></i>
                                                <span class="tooltip">View Profile</span>
                                            </a>
                                            @endif
                                            <a class="icon-link" href="{{ route('admin-assign-supervisor-function', ['selected_trainee' => urlencode($trainee->name)]) }}">
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

    $(document).ready(function () {
  // Handle keyup event on the search input
  $('#search').on('keyup', function () {
    updateTableFilters();
  });

  // Handle change event on the start date filter dropdown
  $('#filterMonth').on('change', function () {
    updateTableFilters();
  });

  // Handle change event on the end date filter dropdown
  $('#filterEndMonth').on('change', function () {
    updateTableFilters();
  });

  // Handle change event on the status filter dropdown
  $('#filterStatus').on('change', function () {
    updateTableFilters();
  });

  function updateTableFilters() {
    const searchText = $('#search').val().toLowerCase();
    const selectedMonth = $('#filterMonth').val();
    const selectedEndMonth = $('#filterEndMonth').val();
    const selectedStatus = $('#filterStatus').val();

    // Iterate through each table row
    $('.trainee-list-tr').each(function () {
      const rowText = $(this).text().toLowerCase();
      const rowMonth = $(this).find('.trainee-list-td').eq(1).text(); // Assuming the month is in the second column
      const rowEndMonth = $(this).find('.trainee-list-td').eq(2).text(); // Assuming the end month is in the third column 
      const rowStatus = $(this).find('.trainee-list-td').eq(3).text(); // Assuming the status is in the fourth column

      // Show or hide the row based on search text and selected filters
      const matchesSearch = rowText.includes(searchText);
      const matchesMonth = selectedMonth === '' || rowMonth.includes(selectedMonth);
      const matchesEndMonth = selectedEndMonth === '' || rowEndMonth.includes(selectedEndMonth);
      const matchesStatus = selectedStatus === '' || rowStatus.includes(selectedStatus);

      $(this).toggle(matchesSearch && matchesMonth && matchesEndMonth && matchesStatus);
    });
  }
});
  </script>
</html>

@endsection