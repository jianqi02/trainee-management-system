@extends('layouts.admin')
@section('pageTitle', 'Edit Seating Plan')

@section('breadcrumbs', Breadcrumbs::render('seating-plan.edit'))

@section('content')
<div class="container">
    <h2>Edit Seating Plan for Current Week</h2>
    
    <form method="POST" action="{{ route('seating-plan.update') }}" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="selected_week" value="{{ $currentSeatingPlan->week }}">

        <div class="card">
            <div class="card-header">Seating Plan for the Week {{ \Carbon\Carbon::createFromFormat('d/m/Y', $currentSeatingPlan->start_date ?? now()->format('d/m/Y'))->format('d/m/Y') }} to
                {{ \Carbon\Carbon::createFromFormat('d/m/Y', $currentSeatingPlan->end_date ?? now()->format('d/m/Y'))->format('d/m/Y') }}</div>
            <!-- Buttons for creating table and adding row -->
            <div class="mb-3" style="margin-top: 15px; margin-left: 20px;">
                <button type="button" id="create-table" class="btn btn-primary">Create Table</button>
                <button type="button" id="add-row" class="btn btn-secondary">Add Row</button>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="seating-plan-table">
                    <thead>
                        <tr>
                            <th>Seat Code</th>
                            <th>Assigned To</th>
                            <th>Action</th> <!-- Column for the delete icon -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (json_decode($currentSeatingPlan->seat_detail, true) as $seatCode => $assignedTo)
                            <tr>
                                <td><input type="text" name="seat_detail[{{ $seatCode }}]" value="{{ $seatCode }}" class="form-control" /></td>
                                <td>
                                    <select name="seat_detail[{{ $seatCode }}]" class="form-control">
                                        <option value="">Select Trainee</option> <!-- Default option -->
                                        @foreach ($trainees as $trainee)
                                            <option value="{{ $trainee->name }}" {{ $assignedTo == $trainee->name ? 'selected' : '' }}>
                                                {{ $trainee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><button type="button" class="btn btn-danger btn-sm delete-row">Delete</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="form-group">
            <label for="image">Upload Floor Plan or Real Image</label>
            <input type="file" name="image" id="image" class="form-control" accept=".jpg, .jpeg, .png">
        </div>
        
        <button type="submit" class="btn btn-success mt-3">Update Seating Plan</button>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Create Table button click
    document.getElementById('create-table').addEventListener('click', function() {
        let rows = prompt("Enter number of rows:", "5");
        let cols = 2; // Since we're working with seat code and assigned to

        if (rows !== null && rows > 0) {
            let tbody = document.querySelector('#seating-plan-table tbody');
            tbody.innerHTML = '';  // Clear existing table rows

            // Create the specified number of rows
            for (let i = 0; i < rows; i++) {
                let row = document.createElement('tr');

                for (let j = 0; j < cols; j++) {
                    let td = document.createElement('td');
                    let input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'form-control';
                    input.name = `seat_detail[new_${i}_col_${j}]`;  // Use new seat names to avoid conflicts
                    td.appendChild(input);
                    row.appendChild(td);
                }

                // Add delete button
                let td = document.createElement('td');
                let deleteButton = document.createElement('button');
                deleteButton.type = 'button';
                deleteButton.className = 'btn btn-danger btn-sm delete-row';
                deleteButton.innerText = 'Delete';
                td.appendChild(deleteButton);
                row.appendChild(td);

                tbody.appendChild(row);
            }
        }
    });

    // Add Row button click
    document.getElementById('add-row').addEventListener('click', function() {
        let tbody = document.querySelector('#seating-plan-table tbody');
        let rowCount = tbody.querySelectorAll('tr').length;

        let row = document.createElement('tr');

        // Create columns for seat code and assigned to
        for (let i = 0; i < 2; i++) {
            let td = document.createElement('td');
            let input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-control';
            input.name = `seat_detail[new_${rowCount}_col_${i}]`;
            td.appendChild(input);
            row.appendChild(td);
        }

        // Add delete button
        let td = document.createElement('td');
        let deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'btn btn-danger btn-sm delete-row';
        deleteButton.innerText = 'Delete';
        td.appendChild(deleteButton);
        row.appendChild(td);

        tbody.appendChild(row);
    });

    // Event delegation to handle delete button clicks
    document.querySelector('#seating-plan-table').addEventListener('click', function(event) {
        if (event.target && event.target.matches('.delete-row')) {
            let row = event.target.closest('tr');
            row.remove();
        }
    });
});
</script>

@endsection
