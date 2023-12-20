@extends('layouts.app')
@section('pageTitle', 'Daily Task Detail')

@section('breadcrumbs', Breadcrumbs::render('daily-task-detail', $date, $taskID))

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

        .modal-edit,
        .modal-note {
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

        .modal-content-note,
        .modal-content-edit {
            background-color: #fff; /* White background */
            margin: 5% auto;
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
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* Remove underline from links */
        .task-card-link {
            text-decoration: none;
        }

        /* Add a hover animation to links */
        .task-card-link:hover {
            color: #007bff; 
            transition: color 0.2s ease; 
        }

        .timeline {
            margin: 0 auto;
            max-width: 750px;
            padding: 25px;
            display: grid;
            grid-template-columns: 1fr 3px 1fr;
            font-family: "Fira Sans", sans-serif;
            }

        .timeline__date{
            font-size: 20px;
        }

            .timeline__component {
            margin: 0 20px 70px 20px;
            }

            .timeline__component--bg {
                padding: 1.5em;
                background: rgba(255, 255, 255, 0.2);
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
                border-radius: 10px;
                transition: background-color 0.3s ease;
                text-decoration: none;
                color: black;
            }

            .timeline__component--bg:hover {
                background-color: #f0f0f0; 
                cursor: pointer; 
            }

            /* LEAVE TILL LAST */
            .timeline__component--bottom {
            margin-bottom: 0;
            }

            .timeline__middle {
            position: relative;
            background: #000000;
            }

            .timeline__point {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 15px;
            height: 15px;
            background: #000000;
            border-radius: 50%;
            }

            /* LEAVE TILL LAST */
            .timeline__point--bottom {
            top: initial;
            bottom: 0;
            }

            .timeline__date--right {
            text-align: right;
            }

            .timeline__title {
            margin: 0;
            font-size: 1.15em;
            font-weight: bold;
            }

            .timeline__paragraph {
            line-height: 1.5;
            }
    </style>
</head>
<body>
    @php
        $daily_task_detail = isset($taskDetail['timeline']) ? json_decode($taskDetail['timeline']) : null;
    @endphp
    <div class="task-container">
        <div class="row">
            <p style="margin-left: 5px;"><small>You are currently viewing on the daily task for task <strong>{{ $taskName }}</strong>.</small></p>
            <h3>Daily Task Detail</h3>
            @if(session('warning'))
                <div class="alert alert-warning" style="width: 64.3%; margin-left: 15px;">{{ session('warning') }}</div>
            @endif
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        @if ($taskDetail)
                        <h5 class="card-title">Date: {{ $date }} ({{ $dayOfWeek }})</h5>
                        <br>
                        <h5 class="card-title">{{ $taskDetail['Name'] ?? 'Task Name' }}</h5>
                        <p class="card-text">
                            <strong>Description: </strong><br>
                            {!! nl2br(e($taskDetail['Description'] ?? 'Description')) !!}
                            <br>
                            <br>
                            <br>
                            <strong>Status: </strong>{{ $taskDetail['Status'] ?? 'Not Started' }}
                            <br>
                        </p>
                        @else
                            <h5 class="card-title">Date: {{ $date }} ({{ $dayOfWeek }})</h5>
                            <br>
                            <p>No task detail available for this day.</p>
                        @endif
                    </div>
                </div>
                <button type="button" id="editTaskButton" class="btn btn-primary btn-add-task" style="padding: 7px; height: 40px;">Edit Task</button>

                
                <div id="taskModal" class="modal modal-edit">
                    <div class="modal-content modal-content-edit">
                        <span class="close" id="closeModal">&times;</span>
                        <h2>Edit Task</h2>
                        <form id="taskForm" action="{{ route('trainee-edit-daily-task', ['date' => $date, 'taskID' => $taskID]) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="taskName">Task Name:</label>
                                <input type="text" id="taskName" name="taskName" value="{{ $taskDetail['Name'] ?? 'Task Name' }}" required>
                            </div>
                            <div class="form-group">
                                <label for="taskDescription">Description:</label>
                                <textarea id="taskDescription" name="taskDescription">{{ $taskDetail['Description'] ?? 'Description' }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="status">Status:</label>
                                <select id="status" name="status" required>
                                    <option value="Not Started" {{ ($taskDetail['Status'] ?? 'Not Started') === 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                    <option value="Ongoing" {{ ($taskDetail['Status'] ?? 'Ongoing') === 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="Completed" {{ ($taskDetail['Status'] ?? 'Completed') === 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="Postponed" {{ ($taskDetail['Status'] ?? 'Postponed') === 'Postponed' ? 'selected' : '' }}>Postponed</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-add-task">Submit</button>
                        </form>
                    </div>
                </div>

                <h3>Note</h3>

                <div class="card mb-3">
                    <div class="card-body">
                        <strong>Note from supervisor</strong>
                        <br>
                        {!! nl2br(e($taskDetail['Supervisor'] ?? 'No comment from supervisor.')) !!}
                        <br>
                        <br>
                        <br>
                        <strong>Note from trainee</strong>
                        <br>
                        {!! nl2br(e($taskDetail['Trainee'] ?? 'No comment from trainee.')) !!}
                    </div>
                </div>

                <button type="button" id="commentButton" class="btn btn-primary btn-add-task" style="padding: 7px; height: 40px;">Add or Edit Note</button>

                <div id="commentModal" class="modal modal-note">
                    <div class="modal-content modal-content-note">
                        <span class="close" id="closeCommentModal">&times;</span>
                        <h2>Add or Edit Note</h2>
                        <form id="commentForm" action="{{ route('task-timeline-daily-comment', ['date' => $date, 'taskID' => $taskID]) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="comment">Note:</label>
                                <textarea id="comment" name="comment">{{ $taskDetail['Trainee'] ?? '' }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-add-task">Submit</button>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </div>
</body>
<script>
        // Get the button and the modal
        const editTaskButton = document.getElementById("editTaskButton");
        const taskModal = document.getElementById("taskModal");
        const closeModal = document.getElementById("closeModal");

        const commentButton = document.getElementById("commentButton");
        const commentModal = document.getElementById("commentModal");
        const closeCommentModal = document.getElementById("closeCommentModal");

        // Show the modal when the button is clicked
        editTaskButton.addEventListener("click", () => {
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

                // Show the modal when the button is clicked
                commentButton.addEventListener("click", () => {
            commentModal.style.display = "block";
        });

        // Close the modal when the "x" button is clicked
        closeCommentModal.addEventListener("click", () => {
            commentModal.style.display = "none";
        });

        // Close the modal when the user clicks outside of it
        window.addEventListener("click", (event) => {
            if (event.target == commentModal) {
                commentModal.style.display = "none";
            }
        });
</script>
@endsection






