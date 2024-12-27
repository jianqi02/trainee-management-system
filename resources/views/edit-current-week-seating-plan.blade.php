@extends('layouts.admin')
@section('pageTitle', 'Edit Seating Plan')

@section('breadcrumbs', Breadcrumbs::render('seating-plan.edit'))

@section('content')
<div class="container">
    <h2>Edit Seating Plan for Current Week</h2>
    @error('seat_detail')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    @error('images.*')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
    
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
                <button type="button" id="random-assign" class="btn btn-secondary" style="background-color:green;">Random Assign</button>
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
                                <td><input type="text" name="seat_detail[{{ $loop->index }}][seat_code]" value="{{ $seatCode }}" class="form-control" /></td>
                                <td>
                                    <select name="seat_detail[{{ $loop->index }}][trainee_name]" class="form-control">
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
            <input type="file" name="new_images[]" id="image" class="form-control" accept=".jpg, .jpeg, .png" multiple>
        </div>
        
        <!-- Display existing images -->
        <div id="image-preview">
            <h5>Uploaded Images</h5>
            <ul id="uploaded-image-list">
                @foreach ($existingImages as $image)
                    <li data-filename="{{ $image }}">
                        <img src="{{ asset('storage/' . $image) }}" alt="Uploaded Image" style="width: 150px;">
                        <button type="button" class="btn btn-danger btn-sm remove-image">Remove</button>
        
                        <!-- Hidden input for existing images but named differently -->
                        <input type="hidden" name="existing_images[]" value="{{ $image }}">
                    </li>
                @endforeach
            </ul>
        </div>

        <button type="submit" class="btn btn-success mt-3">Update Seating Plan</button>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Handle image uploads
        const imageInput = document.getElementById('image');
        const uploadedImageList = document.getElementById('uploaded-image-list');

        // Event listener for new image uploads
        imageInput.addEventListener('change', function(event) {
            const files = event.target.files;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const li = document.createElement('li');
                    li.innerHTML = `
                        <img src="${e.target.result}" alt="Uploaded Image" style="width: 150px;">
                        <button type="button" class="btn btn-danger btn-sm remove-image">Remove</button>
                    `;
                    li.dataset.filename = file.name;  // Store the filename for later
                    uploadedImageList.appendChild(li);
                };

                reader.readAsDataURL(file);
            }
        });

        // Handle removing images
        uploadedImageList.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-image')) {
                const li = event.target.closest('li');
                li.remove();
            }
        });

        // Pre-generate the trainees list as a JavaScript array
        let trainees = @json($trainees->pluck('name'));

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

                    // Seat Code column (col_0)
                    let seatCodeTd = document.createElement('td');
                    let seatCodeInput = document.createElement('input');
                    seatCodeInput.type = 'text';
                    seatCodeInput.className = 'form-control';
                    seatCodeInput.name = `seat_detail[${i}][seat_code]`; 
                    seatCodeTd.appendChild(seatCodeInput);
                    row.appendChild(seatCodeTd);

                    // Assigned To column (col_1)
                    let assignedToTd = document.createElement('td');
                    let select = document.createElement('select');
                    select.className = 'form-control';
                    select.name = `seat_detail[${i}][trainee_name]`;  

                    // Default option
                    let defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.text = 'Select Trainee';
                    select.appendChild(defaultOption);

                    // Add trainees to the dropdown
                    trainees.forEach(function(trainee) {
                        let option = document.createElement('option');
                        option.value = trainee;
                        option.text = trainee;
                        select.appendChild(option);
                    });

                    assignedToTd.appendChild(select);
                    row.appendChild(assignedToTd);

                    // Add delete button
                    let deleteTd = document.createElement('td');
                    let deleteButton = document.createElement('button');
                    deleteButton.type = 'button';
                    deleteButton.className = 'btn btn-danger btn-sm delete-row';
                    deleteButton.innerText = 'Delete';
                    deleteTd.appendChild(deleteButton);
                    row.appendChild(deleteTd);

                    tbody.appendChild(row);
                }
            }
        });

    // Random Assign button click
    document.getElementById('random-assign').addEventListener('click', function() {
        let trainees = @json($trainees); 

        // Get all the rows in the seating plan table
        let rows = document.querySelectorAll('#seating-plan-table tbody tr');

        let shuffledTrainees = shuffleArray(trainees);

        // Loop through each row and assign a random trainee
        rows.forEach(function(row, index) {
            let traineeSelect = row.querySelector('select');

            // Assign a trainee from the shuffled array, if there are more trainees than rows
            if (shuffledTrainees[index]) {
                // Find the option in the dropdown that matches the trainee's name and set it as selected
                let traineeName = shuffledTrainees[index].name;
                Array.from(traineeSelect.options).forEach(function(option) {
                    if (option.value === traineeName) {
                        option.selected = true;
                    }
                });
            } else {
                // If no more trainees available, set to empty
                traineeSelect.value = ''; 
            }
        });
    });

    // Utility function to shuffle the trainees array
    function shuffleArray(array) {
        let shuffledArray = array.slice(); // Create a copy of the array
        for (let i = shuffledArray.length - 1; i > 0; i--) {
            let j = Math.floor(Math.random() * (i + 1));
            [shuffledArray[i], shuffledArray[j]] = [shuffledArray[j], shuffledArray[i]];
        }
        return shuffledArray;
    }

    // Add Row button click
    document.getElementById('add-row').addEventListener('click', function() {
        let tbody = document.querySelector('#seating-plan-table tbody');
        let rowCount = tbody.querySelectorAll('tr').length;

        let row = document.createElement('tr');

        // Seat Code column (col_0)
        let seatCodeTd = document.createElement('td');
        let seatCodeInput = document.createElement('input');
        seatCodeInput.type = 'text';
        seatCodeInput.className = 'form-control';
        seatCodeInput.name = `seat_detail[${rowCount}][seat_code]`; 
        seatCodeTd.appendChild(seatCodeInput);
        row.appendChild(seatCodeTd);

        // Assigned To column (Dropdown) (col_1)
        let assignedToTd = document.createElement('td');
        let select = document.createElement('select');
        select.className = 'form-control';
        select.name = `seat_detail[${rowCount}][trainee_name]`;

        // Default option
        let defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.text = 'Select Trainee';
        select.appendChild(defaultOption);

        // Add trainees to the dropdown
        trainees.forEach(function(trainee) {
            let option = document.createElement('option');
            option.value = trainee;
            option.text = trainee;
            select.appendChild(option);
        });

        assignedToTd.appendChild(select);
        row.appendChild(assignedToTd);

        // Add delete button
        let deleteTd = document.createElement('td');
        let deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'btn btn-danger btn-sm delete-row';
        deleteButton.innerText = 'Delete';
        deleteTd.appendChild(deleteButton);
        row.appendChild(deleteTd);

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
