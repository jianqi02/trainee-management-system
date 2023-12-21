@extends('layouts.sv')
@section('pageTitle', 'Trainee Logbook')

@section('breadcrumbs', Breadcrumbs::render('view-trainee-logbook', $name))

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

        .logbook-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .logbook-created-time {
            font-size: 12px;
            color: #808080;
        }

        .logbook-info {
            display: flex;
            flex-direction: row;
            flex-grow: 1;
        }

        .logbook-cards {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .logbook-card {
            flex: 0 0 calc(50% - 10px); /* Two cards per row with some spacing */
            margin-bottom: 20px;
        }

        .card {
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 10px;
            height: 180px;
            max-height: 180px;
        }

        .card-title a {
            text-decoration: none;
            color: #333; /* Link color */
        }
        .logbook-wrapper{
            display: flex;
            flex-direction: column;
        }

        .delete-logbook-button {
        background: none;
        border: none;
        cursor: pointer;
        }

        .delete-logbook-button i {
            color: #f44336; /* Red color for the bin icon */
        }

        .upload-logbook-form {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 20px;
        }

        /* Style for the "Choose a Logbook" input */
        .file-input {
            display: none; /* Hide the default input element */
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
        }

        .custom-file-upload:hover {
            background: #235a9b; 
        }

        /* Style for the "Upload Logbook" button */
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

        .status-unsigned {
            background-color: red;
            color: white;
            padding: 4px 8px;
            border-radius: 50px;
        }

        .status-signed {
            background-color: #82eb82;
            color: white;
            padding: 4px 8px;
            border-radius: 50px;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="logbook-container">
            <h2>{{ $name }}'s Logbooks</h2>
            <p class="logbook-created-time">Supported file types are .pdf, .doc, .docx. Maximum size is 2MB.</p>
            <p class="logbook-created-time"  style="margin-bottom: 30px;">Click on the filename to download.</p>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-warning">{{ session('error') }}</div>
            @endif
            @error('logbook')
                <div class="alert alert-warning">{{ $message }}</div>
            @enderror
            <ul>
                @if ($logbooks->isEmpty())
                    <p>No logbooks uploaded yet.</p>
                @else
                <ul class="logbook-cards">
                    @foreach ($logbooks as $logbook)
                    <li class="logbook-card">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="{{ asset($logbook->logbook_path) }}" target="_blank" class="logbook-link" style="color: blue;">
                                        {{ pathinfo($logbook->logbook_path, PATHINFO_BASENAME) }}
                                    </a>
                                </h5>
                                <p class="card-text logbook-created-time">Uploaded at: {{ $logbook->created_at }}</p>
                                <p class="card-text logbook-created-time">
                                    Status:
                                    <span class="{{ $logbook->status === 'Unsigned' ? 'status-unsigned' : 'status-signed' }}">
                                        {{ $logbook->status }}
                                    </span>
                                </p>
                
                                <button type="submit" class="delete-logbook-button" data-toggle="modal" data-target="#confirmationModal-{{ $logbook->id }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>

                                <div class="modal" tabindex="-1" role="dialog" id="confirmationModal-{{ $logbook->id }}">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmation</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this logbook?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <form action="{{ route('remove-logbooks-sv.destroy', ['logbook' => $logbook, 'name' => $name]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Confirm Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>          
                @endif
                <form action={{ route('sv-upload-logbook', ['name' => $name]) }} method="POST" enctype="multipart/form-data" class="upload-logbook-form">
                    @csrf
                    <input type="file" name="logbook" id="logbook" accept=".pdf, .doc, .docx" class="file-input" onchange="updateFileName()">
                    <div class="logbook-info">
                        <label for="logbook" class="custom-file-upload" style="margin-right: 30px;">Choose a Logbook</label>
                        <span id="file-name" class="file-name" style="margin-top: 10px;">No file selected</span>
                    </div>
                    <button type="submit" class="upload-button" style="margin-top: 30px;">Upload Logbook</button>
                </form>
            </ul>
        </div>
    </div>
</body>
<script>
    function updateFileName() {
        const fileInput = document.getElementById('logbook');
        const fileName = document.getElementById('file-name');

        if (fileInput.files.length > 0) {
            fileName.textContent = fileInput.files[0].name;
        } else {
            fileName.textContent = 'No file selected';
        }
    }
</script>
@endsection