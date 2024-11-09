@extends('layouts.admin')
@section('pageTitle', 'Create Seating Plan')

@section('breadcrumbs', Breadcrumbs::render('seating-plan.create'))

@section('content')
<div class="container">
    <h2>Create Seating Plan for Week: {{ $selectedWeek }}</h2>
    
    <form method="POST" action="{{ route('seating-plan.createNew') }}" enctype="multipart/form-data">

        @csrf

        <input type="hidden" name="selected_week" value="{{ $selectedWeek }}">

        <div class="card">
            <div class="card-header">Seating Plan for the Week {{ $startDate }} to {{ $endDate }}</div>

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
                        <!-- Initially, no rows will be present. Rows will be added dynamically. -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="form-group">
            <label for="image">Upload Floor Plan or Real Image</label>
            <input type="file" name="image" id="image" class="form-control" accept=".jpg, .jpeg, .png">
        </div>

        <button type="submit" class="btn btn-success mt-3">Create Seating Plan</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Trainees data passed from Blade to JavaScript
    const trainees = @json($trainees);

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

                // Seat code input
                let seatCodeTd = document.createElement('td');
                let seatCodeInput = document.createElement('input');
                seatCodeInput.type = 'text';
                seatCodeInput.className = 'form-control';
                seatCodeInput.name = `seat_detail[new_${i}_seat_code]`;  // Use new seat names to avoid conflicts
                seatCodeTd.appendChild(seatCodeInput);
                row.appendChild(seatCodeTd);

                // Trainee dropdown
                let traineeTd = document.createElement('td');
                let traineeSelect = document.createElement('select');
                traineeSelect.className = 'form-control';
                traineeSelect.name = `seat_detail[new_${i}_assigned_to]`;

                // Add "Select Trainee" option
                let selectOption = document.createElement('option');
                selectOption.value = '';  // Empty value for no selection
                selectOption.text = 'Select Trainee';  // Placeholder text
                traineeSelect.appendChild(selectOption);

                // Populate dropdown with trainees
                trainees.forEach(function(trainee) {
                    let option = document.createElement('option');
                    option.value = trainee.id;
                    option.text = trainee.name;
                    traineeSelect.appendChild(option);
                });

                traineeTd.appendChild(traineeSelect);
                row.appendChild(traineeTd);

                // Add delete button
                let actionTd = document.createElement('td');
                let deleteButton = document.createElement('button');
                deleteButton.type = 'button';
                deleteButton.className = 'btn btn-danger btn-sm delete-row';
                deleteButton.innerText = 'Delete';
                actionTd.appendChild(deleteButton);
                row.appendChild(actionTd);

                tbody.appendChild(row);
            }
        }
    });

    // Add Row button click
    document.getElementById('add-row').addEventListener('click', function() {
        let tbody = document.querySelector('#seating-plan-table tbody');
        let rowCount = tbody.querySelectorAll('tr').length;

        let row = document.createElement('tr');

        // Seat code input
        let seatCodeTd = document.createElement('td');
        let seatCodeInput = document.createElement('input');
        seatCodeInput.type = 'text';
        seatCodeInput.className = 'form-control';
        seatCodeInput.name = `seat_detail[new_${rowCount}_seat_code]`;
        seatCodeTd.appendChild(seatCodeInput);
        row.appendChild(seatCodeTd);

        // Trainee dropdown
        let traineeTd = document.createElement('td');
        let traineeSelect = document.createElement('select');
        traineeSelect.className = 'form-control';
        traineeSelect.name = `seat_detail[new_${rowCount}_assigned_to]`;

        // Add "Select Trainee" option
        let selectOption = document.createElement('option');
        selectOption.value = '';  // Empty value for no selection
        selectOption.text = 'Select Trainee';  // Placeholder text
        traineeSelect.appendChild(selectOption);

        // Populate dropdown with trainees
        trainees.forEach(function(trainee) {
            let option = document.createElement('option');
            option.value = trainee.id;
            option.text = trainee.name;
            traineeSelect.appendChild(option);
        });

        traineeTd.appendChild(traineeSelect);
        row.appendChild(traineeTd);

        // Add delete button
        let actionTd = document.createElement('td');
        let deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'btn btn-danger btn-sm delete-row';
        deleteButton.innerText = 'Delete';
        actionTd.appendChild(deleteButton);
        row.appendChild(actionTd);

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
