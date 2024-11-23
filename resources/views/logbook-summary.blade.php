<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Summary Report</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-top: 20px;
        }
        .info-section p {
            margin: 5px 0;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .task-section {
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .daily-details {
            margin-top: 10px;
        }
        .daily-details table {
            width: 95%;
            margin: 10px auto;
            border: none;
        }
        .daily-details th, .daily-details td {
            border: none;
            padding: 4px;
        }
        .signature-section {
            width: 100%;
            margin-top: 40px;
        }
        .signature {
            width: 50%;
            text-align: center;
            display: inline-block;
        }
    </style>
</head>
<body>
    <h1>LOGBOOK SUMMARY REPORT</h1>

    <div class="info-section">
        <p><strong>Company:</strong> SARAWAK INFORMATION SYSTEM SDN. BHD.</p>
        <p><strong>Trainee Name:</strong> {{ $trainee->name }}</p>
        <p><strong>Position:</strong> TRAINEE</p>
        <p><strong>Company email:</strong> {{ $trainee->sains_email }}</p>
        <p><strong>Department/Expertise:</strong> {{ $trainee->expertise }}</p>
        <p><strong>Supervisor Name:</strong> {{ $supervisors->isEmpty() ? 'No supervisor assigned' : $supervisors->pluck('name')->implode(', ') }}</p>
        <p><strong>Date Generated:</strong> {{ $dateGenerated }}</p>
        <p><strong>Task Period (Month/Year):</strong>
            @if($isSingleMonth)
                {{ \Carbon\Carbon::parse($startMonth)->format('F Y') }}
            @else
                {{ \Carbon\Carbon::parse($startMonth)->format('F Y') }} - {{ \Carbon\Carbon::parse($endMonth)->format('F Y') }}
            @endif
        </p>
    </div>

    @foreach($tasks as $task)
    <div class="task-section">
        <table>
            <tr>
                <th>Task Name</th>
                <td>{{ $task->task_name }}</td>
            </tr>
            <tr>
                <th>Task Description</th>
                <!-- Display decoded task_detail data -->
                <td>{{ $task->task_detail_data['Description'] ?? 'No description available' }}</td>
            </tr>
            <tr>
                <th>Task Status</th>
                <td>{{ $task->task_status }}</td>
            </tr>
            <tr>
                <th>Start Date</th>
                <td>{{ \Carbon\Carbon::parse($task->task_start_date)->format('j F Y') }}</td>
            </tr>
            <tr>
                <th>End Date</th>
                <td>{{ \Carbon\Carbon::parse($task->task_end_date)->format('j F Y') }}</td>
            </tr>
            <tr>
                <th>Overall Note</th>
                <td>
                    <strong>Supervisor:</strong> {{ $task->task_overall_comment_data['Supervisor'] ?? 'No overall comment from supervisor' }} <br>
                    <strong>Trainee:</strong> {{ $task->task_overall_comment_data['Trainee'] ?? 'No overall comment from trainee' }}
                </td>
            </tr>
        </table>

        <div class="daily-details">
            <strong>Daily Report</strong>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>Note/Comment</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($task->timeline_data))
                        @foreach($task->timeline_data as $date => $details)
                            <tr>
                                <td>{{ $date }}</td>
                                <td>{{ $details['Description'] }}</td>
                                <td>{{ $details['Status'] }}</td>
                                <td>
                                    <strong>Supervisor:</strong> {{ $details['Supervisor'] ?? 'No comment from Supervisor' }} <br>
                                    <strong>Trainee:</strong> {{ $details['Trainee'] ?? 'No comment from Trainee' }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4">No daily details available.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <br><br>
    @endforeach
    <table style="width: 100%; border: none;">
        <tr>
            <td style="width: 50%; text-align: center;">
                <p>Reviewed by,</p><br>
                <p>______________________</p>
                <p>({{ $supervisors->isNotEmpty() ? $supervisors->first()->name : 'No supervisor assigned' }})</p>
            </td>
            
            @if($supervisors->count() > 1)
                @foreach($supervisors->slice(1) as $supervisor)
                    <td style="width: 50%; text-align: center;">
                        <p>Supported by,</p><br>
                        <p>______________________</p>
                        <p>({{ $supervisor->name }})</p>
                    </td>
                @endforeach
            @endif
        </tr>
    </table>
</body>
</html>
