@extends('layouts.admin')
@section('pageTitle', 'Trainee Task Detail')

@section('breadcrumbs', Breadcrumbs::render('admin-task-detail', $traineeName, $trainee_id, $task->id))

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

        .modal-note,
        .modal-edit {
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
            background-color: #f5f5f5; 
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
                background-color: #f0f0f0;
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
        $taskDetail = json_decode($task->task_detail);
        $timeline = (array)json_decode($task->timeline);
    @endphp
    <div class="task-container">
        <div class="row">
            <h3>Detail</h3>
            @if(session('warning'))
                <div class="alert alert-warning" style="width: 64.3%; margin-left: 15px;">{{ session('warning') }}</div>
            @endif
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ $task->task_name }}</h5>
                        <p class="card-text">
                            <strong>Description: </strong><br>
                            {!! nl2br(e($taskDetail->Description)) !!}
                            <br>
                            <br>
                            <br>
                            <strong>Status: </strong>{{ $task->task_status }}
                            <br>
                            <strong>Priority: </strong>{{ $task->task_priority }}
                            <br>
                            <strong>Start Date: </strong> {{ $task->task_start_date }}
                            <br>
                            <strong>End Date: </strong>{{ $task->task_end_date }}
                        </p>
                    </div>
                </div>
                <button type="button" id="editTaskButton" class="btn btn-primar btn-add-task" style="padding: 7px; height: 40px;">Edit Task</button>

                <div id="taskModal" class="modal modal-edit">
                    <div class="modal-content modal-content-edit">
                        <span class="close" id="closeModal">&times;</span>
                        <h2>Edit This Task</h2>
                        <form id="taskForm" action="{{ route('trainee-edit-task', ['taskID' => $task->id]) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="taskName">Task Name:</label>
                                <input type="text" id="taskName" name="taskName" value="{{ $task->task_name }}" required>
                            </div>
                            <div class="form-group">
                                <label for="taskDescription">Description:</label>
                                <textarea id="taskDescription" name="taskDescription">{{ $taskDetail->Description }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="startDate">Start Date:</label>
                                <input type="date" id="startDate" name="startDate" value="{{ $task->task_start_date }}" required>
                            </div>
                            <div class="form-group">
                                <label for="endDate">End Date:</label>
                                <input type="date" id="endDate" name="endDate" value="{{ $task->task_end_date }}" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Status:</label>
                                <select id="status" name="status" required>
                                    <option value="Not Started" {{ $task->task_status === 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                    <option value="Ongoing" {{ $task->task_status === 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="Completed" {{ $task->task_status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="Postponed" {{ $task->task_status === 'Postponed' ? 'selected' : '' }}>Postponed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="priority">Priority:</label>
                                <select id="priority" name="priority" required>
                                    <option value="High" {{ $task->task_priority === 'High' ? 'selected' : '' }}>High</option>
                                    <option value="Medium" {{ $task->task_priority === 'Medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="Low" {{ $task->task_priority === 'Low' ? 'selected' : '' }}>Low</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-add-task">Submit</button>
                        </form>
                    </div>
                </div>

                <h3>Timeline</h3>

                <div class="timeline">
                    @foreach ($dateRange as $date)
                        @php
                           $strDate = $date->format('Y-m-d');
                        @endphp
                        @if ($loop->iteration % 2 == 0)
                            <div class="timeline__component">
                                <div class="timeline__date timeline__date--right">{{ $date->format('F d, Y') }}</div>
                            </div>
                            <div class="timeline__middle">
                                <div class="timeline__point"></div>
                            </div>
                            <a href="{{ route('trainee-daily-task-detail', ['date' => $date->format('Y-m-d'), 'taskID' => $task->id]) }}" class="timeline__component timeline__component--bg" data-date="{{ $date->format('Y-m-d') }}" style="text-decoration:none; color: inherit;">
                                <h2 class="timeline__title">
                                    @if(!empty($timeline[$strDate]))
                                        {{ $timeline[$strDate]->Name ?? 'Nothing' }}
                                        <p style="font-size: 15px; margin-bottom: -10px; font-weight: normal;">Status: {{ $timeline[$strDate]->Status ?? '-'}}</p>
                                    @else
                                        Nothing
                                    @endif
                                </h2>
                            </a>
                        @else
                            <a href="{{ route('trainee-daily-task-detail', ['date' => $date->format('Y-m-d'), 'taskID' => $task->id]) }}" class="timeline__component timeline__component--bg" data-date="{{ $date->format('Y-m-d') }}" style="text-decoration:none; color: inherit;">
                                <h2 class="timeline__title">
                                    @if(!empty($timeline[$strDate]))
                                        {{ $timeline[$strDate]->Name ?? 'Nothing' }}
                                        <p style="font-size: 15px; margin-bottom: -10px; font-weight: normal;">Status: {{ $timeline[$strDate]->Status ?? '-'}}</p>
                                    @else
                                        Nothing
                                    @endif
                                </h2>
                            </a>
                            <div class="timeline__middle">
                                <div class="timeline__point"></div>
                            </div>
                            <div class="timeline__component">
                                <div class="timeline__date">{{ $date->format('F d, Y') }}</div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <h3>Overall Note</h3>

                <div class="card mb-3">
                    <div class="card-body">
                        <strong>Note from supervisor</strong>
                        <br>
                        {!! nl2br(e($comments['Supervisor'] ?? 'No comment from supervisor.')) !!}
                        <br>
                        <br>
                        <br>
                        <strong>Note from trainee</strong>
                        <br>
                        {!! nl2br(e($comments['Trainee'] ?? 'No comment from trainee.')) !!}
                    </div>
                </div>

                <button type="button" id="commentButton" class="btn btn-primary btn-add-task" style="padding: 7px; height: 40px;">Add or Edit Note</button>

                <div id="commentModal" class="modal modal-note">
                    <div class="modal-content modal-content-note">
                        <span class="close" id="closeCommentModal">&times;</span>
                        <h2>Add or Edit Note</h2>
                        <form id="commentForm" action="{{ route('task-timeline-overall-comment', ['taskID' => $task->id]) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="commentSV">Note from supervisor:</label>
                                <textarea id="commentSV" name="commentSV">{{ $comments['Supervisor'] ?? '' }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="comment">Note from trainee:</label>
                                <textarea id="commentTR" name="commentTR">{{ $comments['Trainee'] ?? '' }}</textarea>
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

    // Show the modal when the button is clicked
    commentButton.addEventListener("click", () => {
        commentModal.style.display = "block";
    });

    // Close the modal when the "x" button is clicked
    closeModal.addEventListener("click", () => {
        taskModal.style.display = "none";
    });

    // Close the modal when the "x" button is clicked
    closeCommentModal.addEventListener("click", () => {
        commentModal.style.display = "none";
    });

    // Close the modal when the user clicks outside of it
    window.addEventListener("click", (event) => {
        if (event.target == taskModal) {
            taskModal.style.display = "none";
        }
    });

    // Close the modal when the user clicks outside of it
    window.addEventListener("click", (event) => {
        if (event.target == commentModal) {
            commentModal.style.display = "none";
        }
    });
</script>
@endsection






