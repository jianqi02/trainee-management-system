@extends('layouts.app')
@section('pageTitle', 'Trainee Resume')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        h1 {
            font-family: 'Roboto', sans-serif;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .resume-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .small-text {
            font-size: 12px;
            color: #808080;
        }

        .resume-info {
            display: flex;
            flex-direction: row;
            flex-grow: 1;
        }

        .resume-cards {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .resume-card {
            flex: 0 0 calc(50% - 10px); 
            margin-bottom: 20px;
        }

        .card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
        }

        .card-body {
            padding: 10px;
            max-height: 180px;
        }

        .card-title a {
            text-decoration: none;
            color: #333; 
        }
        .resume-wrapper{
            display: flex;
            flex-direction: column;
        }

        .delete-resume-button {
            background: none;
            border: none;
            cursor: pointer;
            bottom: 10px;
            right: 10px;
        }

        .delete-resume-button i {
            color: #f44336;
        }

        .upload-resume-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        .custom-upload-btn {
            background-color: #337ab7;
            color: #fff;
            border-color: #337ab7;
        }

        .custom-upload-btn:hover {
            background-color: #286090;
            border-color: #204d74;
        }

        .file-input {
            display: none; 
        }

        .custom-file-upload {
            display: inline-block;
            padding: 10px 20px;
            background: #337ab7;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s, color 0.3s;
            margin-right: 10px;
        }

        .custom-file-upload:hover {
            background: #235a9b; 
        }

        .upload-button {
            background: #4caf50;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s, color 0.3s;
        }

        .upload-button:hover {
            background: #45a049; 
        }

        .modal-content {
            border-radius: 8px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: none;
            background-color: #f8f9fa;
            border-radius: 8px 8px 0 0;
        }

        .modal-footer {
            border-top: none;
            background-color: #f8f9fa;
            border-radius: 0 0 8px 8px;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="resume-container">
            <h1>{{ $trainee->name }} 's Resume</h1>
            <ul>
                @if ($trainee->resume_path == null)
                    <p>This trainee has not upload any resume yet.</p>
                @else
                <ul class="resume-cards">
                    <li class="resume-card">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="{{ asset($trainee->resume_path) }}" target="_blank" class="resume-link" style="color: blue;">
                                        {{ pathinfo($trainee->resume_path, PATHINFO_BASENAME) }}
                                    </a>
                                </h5>
                            </div>
                        </div>
                    </li>
                </ul>          
                @endif
            </ul>
        </div>
    </div>
</body>
<script>
    function updateFileName() {
        const fileInput = document.getElementById('resume');
        const fileName = document.getElementById('file-name');

        if (fileInput.files.length > 0) {
            fileName.textContent = fileInput.files[0].name;
        } else {
            fileName.textContent = 'No file selected';
        }
    }
</script>
@endsection