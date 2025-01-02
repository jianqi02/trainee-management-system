@extends('layouts.app')
@section('pageTitle', 'Upload Resume')

@section('breadcrumbs', Breadcrumbs::render('resume'))

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
            border-radius: 10px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: none;
            background-color: #f8f9fa;
            border-radius: 10px 10px 0 0;
        }

        .modal-body iframe {
            border: none;
            border-radius: 10px;
        }

        .modal-footer {
            border-top: none;
            background-color: #f8f9fa;
            border-radius: 0 0 8px 8px;
            justify-content: flex-end;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="resume-container">
            <h1>Your Resume</h1>
            <p class="small-text"  style="margin-bottom: 30px;">Upload your resume here. Supported file type is .pdf.</p>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
            @endif
            @error('resume')
                <div class="alert alert-warning">{{ $message }}</div>
            @enderror

            <!-- Upload Resume Button -->
            <div class="button-container">
                <button type="button" class="btn custom-upload-btn" data-bs-toggle="modal" data-bs-target="#uploadResumeModal">
                    Upload Resume
                </button>
            </div>
           
            <!-- Upload Resume Modal -->
            <div class="modal fade" id="uploadResumeModal" tabindex="-1" aria-labelledby="uploadResumeModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadResumeModalLabel">Upload Resume</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="/upload" method="POST" enctype="multipart/form-data" class="upload-resume-form">
                                @csrf
                                <input type="file" name="resume" id="resume" accept=".pdf" class="file-input" onchange="updateFileName()">
                                <div class="resume-info">
                                    <label for="resume" class="custom-file-upload" style="margin-right: 30px;">Choose a resume</label>
                                    <span id="file-name" class="file-name" style="margin-top: 10px;">No file selected</span>
                                </div>
                                <button type="submit" class="upload-button" style="margin-top: 30px;">Upload resume</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <ul>
                @if ($trainee->resume_path == null)
                    <p>No resume uploaded yet.</p>
                @else
                <ul class="resume-cards">
                    <li class="resume-card">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="#" class="preview-resume-link" data-toggle="modal" data-target="#previewModal" style="color: blue; text-decoration: none;">
                                        {{ pathinfo($trainee->resume_path, PATHINFO_BASENAME) }}
                                    </a>
                                </h5>
                                <button type="submit" class="delete-resume-button" data-toggle="modal" data-target="#confirmationModal">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
            
                                <!-- Preview Modal -->
                                <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="previewModalLabel">Resume Preview</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <iframe src="{{ asset($trainee->resume_path) }}" frameborder="0" width="100%" height="500px"></iframe>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
            
                                <!-- Delete Confirmation Modal -->
                                <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this resume?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <form action="{{ route('resumes.destroyResume', $trainee) }}" method="POST">
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