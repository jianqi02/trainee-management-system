@extends('layouts.sv')
@section('pageTitle', 'Task Timeline')

@section('breadcrumbs', Breadcrumbs::render('sv-timeline', $traineeID))

@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body{
            overflow-x: hidden;
        }
        
        .task-container{
            margin-left: 200px;
            width: 100%;
            overflow-x: hidden;
        }

        .btn-add-task {
            width: 100%;
            background-color: #7f7f7f;
            height: 50px;
            margin-bottom: 20px;
            border: none;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
        }

        /* Hover effect */
        .btn-add-task:hover {
            background-color: #d3d3d3; 
        }

        /* Focus effect (when the button is selected) */
        .btn-add-task:focus {
            outline: none; /* Remove the default outline */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Add a subtle shadow on focus */
        }

        .btn-secondary {
            background-color: #d3d3d3;
            color: #000000;
            width: 100%;
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
            gap: 10px; /* Space between buttons */
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .button-container-card {
            display: flex;
            justify-content: flex-end;
            position: absolute;
            bottom: 10px;
            right: 10px;
            display: flex;
            gap: 10px; /* Add space between the buttons */
            margin-bottom: 5px;
        }

        .button-container-card .btn {
            margin: 0px; /* Ensure no margin on buttons */
        }

        .card {
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-body {
            position: relative; /* Required for absolute positioning inside it */
        }

        .number-card {
            width: 100%;
            height: 100%;
            margin-bottom: 10px; /* Space below each card */
        }

        .number-card-title {
            color: #333;
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .number-card-text {
            font-size: 2rem;
            color: #333;
            font-weight: 500;
            margin-top: 15px;
        }

        .custom-filter-btn,
        .custom-addtask-btn {
            width: 150px; /* Set the same width */
            height: 40px; /* Set the same height */
            display: inline-block; /* Ensure they align consistently */
            text-align: center;
            font-size: 16px; /* Adjust as needed */
            border-radius: 4px; /* Optional: for rounded corners */
        }

        .custom-filter-btn {
            background-color: #337ab7;
            color: #fff;
            border-color: #337ab7;
        }

        .custom-filter-btn:hover {
            background-color: #286090;
            border-color: #204d74;
        }

        .custom-addtask-btn {
            background-color: #4caf50;
            color: #fff;
            border-color: #4caf50;
        }

        .custom-addtask-btn:hover {
            background-color: #45a049;
            border-color: #3e8e41;
        }

        .status-capsule {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px; /* Rounded capsule shape */
            font-size: 14px;
            color: white; /* Text color */
            font-weight: bold;
            text-transform: capitalize; /* Capitalize text */
        }

        .status-capsule.not-started {
            background-color: #ff4d4d; /* Red for Not Started */
        }

        .status-capsule.ongoing {
            background-color: #87cefa; /* Light blue for Ongoing */
        }

        .status-capsule.completed {
            background-color: #28a745; /* Green for Completed */
        }

        .status-capsule.postponed {
            background-color: #6c757d; /* Grey for Postponed */
        }

        .modal-add-task,
        .modal-delete {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content-add-task,
        .modal-content-delete {
            background-color: #f5f5f5; 
            margin: 2% auto;
            padding: 20px;
            border-radius: 8px;
            width: 40%; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .form-group {
            margin: 10px 0;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Remove underline from links */
        .task-card-link {
            text-decoration: none;
        }

        /* Add a hover animation to links */
        .task-card-link:hover {
            color: #007bff; 
            transition: color 0.2s ease; /* Add a smooth color transition effect */
        }

        img{
            margin-left: 10px;
            width: 20px;
            height: 20px;
        }

        .btn-secondary:hover img {
            filter: brightness(0) invert(1);
        }

        .task-card:hover {
            background-color: #f0f0f0;   
            transition: background-color 0.3s ease;                
        }
    </style>
</head>
<body>
<div class="task-container">
    <div class="row">
        <div class="col-md-8">
            <h2>{{ $traineeName }}'s Tasks</h2>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
            @endif
            <div class="button-container">
                <!-- Filter Task Button -->
                <button type="button" class="btn custom-filter-btn" data-bs-toggle="modal" data-bs-target="#filterModal">
                    Filter Task
                </button>
                <!-- Add New Task Button --> 
                <button type="button" id="addTaskButton" class="btn custom-addtask-btn">
                    + Add New Task
                </button> 
            </div>
            
              <!-- Task Statistic Cards -->
              <div class="container mt-5">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card number-card">
                            <div class="card-body">
                                <h5 class="number-card-title" style="font-size: 22px;">Total Tasks</h5>
                                <h6 class="" style="font-size: 14px;">Total tasks assigned</h6>
                                <p class="number-card-text">{{ $totalTasks }}</p>
                            </div>
                        </div>
                    </div>
        
                    <div class="col-md-4 mb-3">
                        <div class="card number-card">
                            <div class="card-body">
                                <h5 class="number-card-title" style="font-size: 22px;">Completed Tasks</h5>
                                <h6 class="" style="font-size: 14px;">Total tasks completed</h6>
                                <p class="number-card-text">{{ $completedTasks }}</p>
                            </div>
                        </div>
                    </div>
        
                    <div class="col-md-4 mb-3">
                        <div class="card number-card">
                            <div class="card-body">
                                <h5 class="number-card-title" style="font-size: 22px;">Pending Tasks</h5>
                                <h6 class="" style="font-size: 14px;">Not started, Ongoing, Postponed</h6>
                                <p class="number-card-text">{{ $pendingTasks  }}</p>
                            </div>
                        </div>
                    </div>
            </div>

            <!-- Sorting Option -->
            <p>Sort by</p>
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-3">
                    @if(Str::contains(request()->url(), 'sort-tasks/priority/asc'))
                        <a class="btn btn-secondary btn-block" href="{{ route('sort-tasks', ['sort' => 'priority', 'order'=> 'desc', 'traineeID' => $traineeID]) }}">Priority<img src="https://img.icons8.com/pastel-glyph/64/000000/sorting-arrows--v1.png" alt="sorting-arrows--v1"/></a>
                    @else
                        <a class="btn btn-secondary btn-block" href="{{ route('sort-tasks', ['sort' => 'priority', 'order' => 'asc', 'traineeID' => $traineeID]) }}">Priority<img src="https://img.icons8.com/pastel-glyph/64/000000/sorting-arrows--v1.png" alt="sorting-arrows--v1"/></a>
                    @endif
                </div>
                <div class="col-md-3">
                    @if(Str::contains(request()->url(), 'sort-tasks/status/asc'))
                        <a class="btn btn-secondary btn-block" href="{{ route('sort-tasks', ['sort' => 'status', 'order'=> 'desc', 'traineeID' => $traineeID]) }}">Status<img src="https://img.icons8.com/pastel-glyph/64/000000/sorting-arrows--v1.png" alt="sorting-arrows--v1"/></a>
                    @else
                        <a class="btn btn-secondary btn-block" href="{{ route('sort-tasks', ['sort' => 'status', 'order'=> 'asc', 'traineeID' => $traineeID]) }}">Status<img src="https://img.icons8.com/pastel-glyph/64/000000/sorting-arrows--v1.png" alt="sorting-arrows--v1"/></a>
                    @endif
                </div>
                <div class="col-md-3">
                    @if(Str::contains(request()->url(), 'sort-tasks/start-date/asc'))
                        <a class="btn btn-secondary btn-block" href="{{ route('sort-tasks', ['sort' => 'start-date', 'order'=> 'desc', 'traineeID' => $traineeID]) }}">Start Date<img src="https://img.icons8.com/pastel-glyph/64/000000/sorting-arrows--v1.png" alt="sorting-arrows--v1"/></a>
                    @else
                        <a class="btn btn-secondary btn-block" href="{{ route('sort-tasks', ['sort' => 'start-date', 'order'=> 'asc', 'traineeID' => $traineeID]) }}">Start Date<img src="https://img.icons8.com/pastel-glyph/64/000000/sorting-arrows--v1.png" alt="sorting-arrows--v1"/></a>
                    @endif
                </div>
                <div class="col-md-3">
                    @if(Str::contains(request()->url(), 'sort-tasks/end-date/asc'))
                        <a class="btn btn-secondary btn-block" href="{{ route('sort-tasks', ['sort' => 'end-date', 'order'=> 'desc', 'traineeID' => $traineeID]) }}">End Date<img src="https://img.icons8.com/pastel-glyph/64/000000/sorting-arrows--v1.png" alt="sorting-arrows--v1"/></a>
                    @else
                        <a class="btn btn-secondary btn-block" href="{{ route('sort-tasks', ['sort' => 'end-date', 'order'=> 'asc', 'traineeID' => $traineeID]) }}">End Date<img src="https://img.icons8.com/pastel-glyph/64/000000/sorting-arrows--v1.png" alt="sorting-arrows--v1"/></a>
                    @endif
                </div>
            </div>  

            <!-- List of Tasks -->
            @foreach ($tasks as $task)
                <div class="card mb-3 task-card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $task->task_name }}</h5>
                        <p class="card-text">
                            <strong>Status: </strong>
                                <span 
                                    class="status-capsule {{ strtolower(str_replace(' ', '-', $task->task_status)) }}">
                                    {{ $task->task_status }}
                                </span>
                            <br>
                            <strong>Priority: </strong>{{ $task->task_priority }}
                            <br>
                            <strong>Start Date: </strong> {{ $task->task_start_date }}
                            <br>
                            <strong>End Date: </strong>{{ $task->task_end_date }}
                        </p>
                        
                        <!-- Button Container with Flexbox -->
                        <div class="button-container-card">
                            <!-- View Details Button -->
                            <a href="{{ route('trainee-task-detail', ['taskID' => $task->id]) }}" class="btn btn-info"">View Details</a>
                            <!-- Delete Button -->
                            <button class="btn btn-danger delete-button" data-task-id="{{ $task->id }}">Delete</button>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="modal modal-delete" id="confirmDeleteModal">
                <div class="modal-content modal-content-delete">
                    <span class="close" id="closeConfirmDeleteModal">&times;</span>
                    <h2>Confirm Delete</h2>
                    <p>Are you sure you want to delete this task?</p>
                    <button class="btn btn-danger" id="confirmDeleteButton">Yes, Delete</button>
                </div>
            </div>
            <!-- The Modal -->
            <div id="taskModal" class="modal modal-add-task">
                <div class="modal-content modal-content-add-task">
                    <span class="close" id="closeModal">&times;</span>
                    <h2 style="text-align: center;">Add New Task</h2>
                    <form id="taskForm" action="{{ route('trainee-add-new-task-sv', ['traineeID' => $traineeID]) }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="taskName" style="display: block; margin-bottom: 8px;">Task Name:</label>
                            <input type="text" id="taskName" name="taskName" style="width: 100%; padding: 10px;" required>
                        </div>
                        <div class="form-group">
                            <label for="startDate" style="display: block; margin-bottom: 8px;">Start Date:</label>
                            <input type="date" id="startDate" name="startDate" style="width: 100%; padding: 10px;" required>
                        </div>
                        <div class="form-group">
                            <label for="endDate" style="display: block; margin-bottom: 8px;">End Date:</label>
                            <input type="date" id="endDate" name="endDate" style="width: 100%; padding: 10px;" required>
                        </div>
                        <div class="form-group">
                            <label for="priority" style="display: block; margin-bottom: 8px;">Priority:</label>
                            <select id="priority" name="priority" style="width: 100%; padding: 10px;" required>
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                        <div style="text-align: center; margin-top: 16px;">
                            <button type="submit" class="btn btn-primary btn-add-task" style="padding: 12px 24px;">Add Task</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="filterModalLabel">Filter Options</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="filterForm" action="{{ route('apply-filters', ['traineeID' => $traineeID]) }}" method="post">
                                @csrf
                                <!-- Search by -->
                                <div class="mb-3">
                                    <label for="search" class="form-label">Search by:</label>
                                    <input type="text" class="form-control" id="search" name="search">
                                </div>
            
                                <!-- Filter by task start date -->
                                <div class="mb-3">
                                    <label for="taskStartDate" class="form-label">Filter by Task Start Date:</label>
                                    <select class="form-select" id="taskStartDate" name="taskStartDate">
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
            
                                <!-- Filter by task end date -->
                                <div class="mb-3">
                                    <label for="taskEndDate" class="form-label">Filter by Task End Date:</label>
                                    <select class="form-select" id="taskEndDate" name="taskEndDate">
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
            
                                <!-- Filter by task priority -->
                                <div class="mb-3">
                                    <label for="taskPriority" class="form-label">Filter by Task Priority:</label>
                                    <select class="form-select" id="taskPriority" name="taskPriority">
                                        <option value="">None</option>
                                        <option value="High">High</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Low">Low</option>
                                    </select>
                                </div>
            
                                <!-- Filter by status -->
                                <div class="mb-3">
                                    <label for="taskStatus" class="form-label">Filter by Status:</label>
                                    <select class="form-select" id="taskStatus" name="taskStatus">
                                        <option value="">None</option>
                                        <option value="Not Started">Not Started</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Ongoing">On Going</option>
                                        <option value="Postponed">Postponed</option>
                                    </select>
                                </div>
            
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
    <script>
        // Get the button and the modal
        const addTaskButton = document.getElementById("addTaskButton");
        const taskModal = document.getElementById("taskModal");
        const closeModal = document.getElementById("closeModal");

        // Show the modal when the button is clicked
        addTaskButton.addEventListener("click", () => {
        taskModal.style.display = "block";
        });

        // Close the modal when the "x" button is clicked
        closeModal.addEventListener("click", () => {
        taskModal.style.display = "none";
        });

        // Close the modal when the user clicks outside of it
        window.addEventListener("click", (event) => {
            if (event.target == taskModal) {
                taskModal.style.display = "none";
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-button');
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');
        const closeConfirmDeleteModal = document.getElementById('closeConfirmDeleteModal');

        let taskIdToDelete;

        // Attach click event to delete buttons
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                // Stop the default behavior of the button click
                event.preventDefault();

                // Set the taskIdToDelete when a delete button is clicked
                taskIdToDelete = this.getAttribute('data-task-id');
                // Show the confirmation modal
                confirmDeleteModal.style.display = 'block';
            });
        });

        // Attach click event to confirm delete button
        confirmDeleteButton.addEventListener('click', function () {
            // Redirect to delete route with the task ID
            window.location.href = `/delete-task/${taskIdToDelete}`;
        });

        // Attach click event to close modal button
        closeConfirmDeleteModal.addEventListener('click', function () {
            confirmDeleteModal.style.display = 'none';
        });
    });
    </script>
</body>
@endsection
