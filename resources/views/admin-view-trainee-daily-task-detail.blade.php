@extends('layouts.admin')

@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Detail</title>
    <style> 

        
        .task-container{
            margin-left: 200px;
            width: 100%;
            overflow-x: hidden;
        }

        .btn-primary {
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
        .btn-primary:hover {
            background-color: #d3d3d3; /* Change to your preferred color on hover */
        }

        /* Focus effect (when the button is selected) */
        .btn-primary:focus {
            outline: none; /* Remove the default outline */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Add a subtle shadow on focus */
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin-left: 20%;
            margin-top: 50px;
            margin-bottom: 50px;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 5px;
            width: 60%;
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

        .btn-add-task {
            width: 100%;
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-add-task:hover {
            background-color: #0056b3;
        }

        /* Remove underline from links */
        .task-card-link {
            text-decoration: none;
        }

        /* Add a hover animation to links */
        .task-card-link:hover {
            color: #007bff; /* Change the link color on hover to your preferred color */
            transition: color 0.2s ease; /* Add a smooth color transition effect */
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
                background-color: #f0f0f0; /* Change to your preferred background color on hover */
                cursor: pointer; /* Change cursor to pointer on hover to indicate interactivity */
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
            <h3>Detail</h3>
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        @if ($taskDetail)
                        <h5 class="card-title">Date: {{ $date }} ({{ $dayOfWeek }})</h5>
                        <br>
                        <h5 class="card-title">{{ $taskDetail['Name'] }}</h5>
                        <p class="card-text">
                            {!! nl2br(e($taskDetail['Description'])) !!}
                            <br>
                            <br>
                            <br>
                            <strong>Status: </strong>{{ $taskDetail['Status'] }}
                            <br>
                        </p>
                        @else
                            <h5 class="card-title">Date: {{ $date }} ({{ $dayOfWeek }})</h5>
                            <br>
                            <p>No task detail available for this day.</p>
                        @endif
                    </div>
                </div>
                <button type="button" id="editTaskButton" class="btn btn-primary" style="padding: 7px; height: 40px;">Edit Task</button>

                
                <div id="taskModal" class="modal">
                    <div class="modal-content">
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

                <h3>Comment</h3>

                <div class="card mb-3">
                    <div class="card-body">
                        <strong>Comment from supervisor</strong>
                        <br>
                        {!! nl2br(e($taskDetail['Supervisor'] ?? 'No comment from supervisor.')) !!}
                        <br>
                        <br>
                        <br>
                        <strong>Comment from trainee</strong>
                        <br>
                        {!! nl2br(e($taskDetail['Trainee'] ?? 'No comment from trainee.')) !!}
                    </div>
                </div>

                <button type="button" id="commentButton" class="btn btn-primary" style="padding: 7px; height: 40px;">Comment</button>

                <div id="commentModal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="closeCommentModal">&times;</span>
                        <h2>Add or Edit Comment</h2>
                        <form id="commentForm" action="{{ route('task-timeline-daily-comment', ['date' => $date, 'taskID' => $taskID]) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="commentSV">Comment from supervisor:</label>
                                <textarea id="commentSV" name="commentSV">{!! nl2br(e($taskDetail['Supervisor'] ?? '')) !!}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="commentTR">Comment from trainee:</label>
                                <textarea id="commentTR" name="commentTR">{!! nl2br(e($taskDetail['Trainee'] ?? '')) !!}</textarea>
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






