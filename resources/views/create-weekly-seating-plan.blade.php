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
                        <!-- Initially, no rows will be present. Rows will be added dynamically. -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="form-group">
            <label for="images">Upload Floor Plan or Real Images</label>
            <input type="file" name="images[]" id="images" class="form-control" accept=".jpg, .jpeg, .png" multiple>
        </div>
        
        <div id="image-preview-container" class="mt-3">
            <!-- Preview uploaded images here -->
        </div>

        <button type="submit" class="btn btn-success mt-3">Create Seating Plan</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Trainees data passed from Blade to JavaScript
    const trainees = @json($trainees);

    // Random Assign button click
    document.getElementById('random-assign').addEventListener('click', function() {
        // Get all the rows in the seating plan table
        let rows = document.querySelectorAll('#seating-plan-table tbody tr');

        // Shuffle the trainees array to get a random order
        let shuffledTrainees = shuffleArray(trainees);

        // Loop through each row and assign a random trainee
        rows.forEach(function(row, index) {
            let traineeSelect = row.querySelector('select');

            // Assign a trainee from the shuffled array, if there are more trainees than rows
            if (shuffledTrainees[index]) {
                traineeSelect.value = shuffledTrainees[index].id;
            } else {
                traineeSelect.value = ''; // If no trainee available, set to empty
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

    document.getElementById('images').addEventListener('change', function(event) {
        const files = event.target.files;
        const previewContainer = document.getElementById('image-preview-container');

        // Iterate through all the uploaded files
        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();

            // Once file is loaded, show the preview
            reader.onload = function(e) {
                // Create image element
                const imgElement = document.createElement('img');
                imgElement.src = e.target.result;
                imgElement.classList.add('img-thumbnail');
                imgElement.style.width = '150px';
                imgElement.style.height = 'auto'; // maintain aspect ratio
                imgElement.style.marginRight = '10px';

                // Create a remove button for each image
                const removeButton = document.createElement('button');
                removeButton.innerText = 'Remove';
                removeButton.classList.add('btn', 'btn-danger', 'btn-sm');
                removeButton.style.marginLeft = '10px';

                // Remove image on clicking the remove button
                removeButton.addEventListener('click', function() {
                    previewContainer.removeChild(imageWrapper);
                });

                // Create a wrapper div for each image and remove button
                const imageWrapper = document.createElement('div');
                imageWrapper.style.display = 'flex';
                imageWrapper.style.alignItems = 'center';
                imageWrapper.style.marginBottom = '15px';
                imageWrapper.appendChild(imgElement);
                imageWrapper.appendChild(removeButton);

                // Add the image wrapper to the preview container
                previewContainer.appendChild(imageWrapper);
            };

            // Read the file as data URL
            reader.readAsDataURL(file);
        });
    });
    
});

</script>

@endsection
