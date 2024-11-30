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

        .button-container {
            display: flex;
            justify-content: flex-end;
            gap: 10px; /* Space between buttons */
            margin-top: 10px;
            margin-bottom: 20px;
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

        .custom-upload-btn {
            background-color: #337ab7;
            color: #fff;
            border-color: #337ab7;
        }

        .custom-upload-btn:hover {
            background-color: #286090;
            border-color: #204d74;
        }

        .custom-generate-btn {
            background-color: #4caf50;
            color: #fff;
            border-color: #4caf50;
        }

        .custom-generate-btn:hover {
            background-color: #45a049;
            border-color: #3e8e41;
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
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
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
            bottom: 10px;
            right: 10px;
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
            margin-right: 10px;
        }

        .custom-file-upload:hover {
            background: #235a9b; 
        }

        .form-row {
            display: flex;
            align-items: center;
        }

        .file-name {
            margin-left: 10px;
            color: #555;
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

         /* General styling for tabs */
        .nav-tabs {
            border-bottom: 2px solid #dee2e6; 
            margin-bottom: 20px;
        }

        .nav-tabs .nav-link {
            border: none;
            background-color: #f8f9fa;
            color: #337ab7;
            font-weight: bold;
            margin-right: 5px;
            padding: 10px 20px;
            border-radius: 5px 5px 0 0;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            color: #337ab7;
            background-color: #eef3ff; /* Light blue */
            border-color: #337ab7;
            text-decoration: none;
        }

        .nav-tabs .nav-link:hover {
            box-shadow: 0 3px 5px rgba(0, 123, 255, 0.2);
        }

        .nav-tabs .nav-link.active {
            background-color: #337ab7;
            color: #ffffff;
            border: none;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Tab content styling */
        .tab-content {
            border: 1px solid #dee2e6;
            border-radius: 0 0 5px 5px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Modal customization */
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
        <div class="logbook-container">
            <h2>{{ $name }}'s Logbooks</h2>
            <p class="logbook-created-time">Supported file types are .pdf, .doc, .docx. Maximum size is 2MB. Click on the filename to download.</p>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-warning">{{ session('error') }}</div>
            @endif
            @error('logbook')
                <div class="alert alert-warning">{{ $message }}</div>
            @enderror

            <!-- Button to Open "Upload", "Generate" Logbook Modal -->
            <div class="button-container">
                <!-- Upload Logbook Button -->
                <button type="button" class="btn custom-upload-btn" data-bs-toggle="modal" data-bs-target="#uploadLogbookModal">
                    Upload Logbook
                </button>
                <!-- Generate E-Logbook Button -->
                <button type="button" class="btn custom-generate-btn" data-bs-toggle="modal" data-bs-target="#dateModal">
                    Generate e-Logbook
                </button>  
            </div>

            <!-- Upload Logbook Modal -->
            <div class="modal fade" id="uploadLogbookModal" tabindex="-1" aria-labelledby="uploadLogbookModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadLogbookModalLabel">Upload Logbook</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action={{ route('sv-upload-logbook', ['name' => $name]) }} method="POST" enctype="multipart/form-data" class="upload-logbook-form">
                                @csrf
                                <div class="form-row d-flex">
                                    <div class="mb-3 d-flex align-items-center">
                                        <label for="logbook-name" class="form-label me-2">Logbook Name</label>
                                        <input type="text" name="logbook_name" id="logbook-name" class="form-control" required>
                                    </div>
                                    <div class="logbook-info">
                                        <label for="logbook" class="custom-file-upload">Choose a Logbook</label>
                                        <input type="file" name="logbook" id="logbook" accept=".pdf, .doc, .docx" class="file-input" onchange="updateFileName()" aria-describedby="file-name">
                                        <span id="file-name" class="file-name">No file selected</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success mt-3">Upload Logbook</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="dateModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="dateModalLabel">Select Period</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="dateSelectionForm" action="{{ route('sv-generate-logbook', ['traineeName' => $name]) }}" method="GET" target="_blank" onsubmit="openReportInNewTab(event)">
                                @csrf
                                <div class="mb-3">
                                    <label for="startMonth" class="form-label">Start Month and Year</label>
                                    <input type="month" id="startMonth" name="startMonth" class="form-control" required>
                                    @error('startMonth')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="endMonth" class="form-label">End Month and Year</label>
                                    <input type="month" id="endMonth" name="endMonth" class="form-control" required>
                                    @error('endMonth')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="error-container text-danger d-none"></div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" form="dateSelectionForm" class="btn btn-primary">Preview Report</button>
                        </div>
                    </div>
                </div>
            </div>
            

            <!-- Tabs Navigation -->
             <ul class="nav nav-tabs" id="logbookTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="unsigned-tab" data-bs-toggle="tab" data-bs-target="#unsigned" type="button" role="tab" aria-controls="unsigned" aria-selected="true">
                        Unsigned
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="signed-tab" data-bs-toggle="tab" data-bs-target="#signed" type="button" role="tab" aria-controls="signed" aria-selected="false">
                        Signed
                    </button>
                </li>
            </ul>
        
            <!-- Tabs Content -->
            <div class="tab-content" id="logbookTabsContent">
                
                <!-- Unsigned Tab -->
                <div class="tab-pane fade show active" id="unsigned" role="tabpanel" aria-labelledby="unsigned-tab">
                    @if ($logbooks->where('status', 'Unsigned')->isEmpty())
                        <p>No unsigned logbooks uploaded yet.</p>
                    @else
                        <ul class="logbook-cards">
                            @foreach ($logbooks->where('status', 'Unsigned') as $logbook)
                            <li class="logbook-card">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="#" class="logbook-link" style="color: blue;" data-toggle="modal" data-target="#previewModalUnsigned-{{ $logbook->id }}">
                                                {{ pathinfo($logbook->logbook_path, PATHINFO_BASENAME) }}
                                            </a>
                                        </h5>
                                        <p class="card-text logbook-created-time">File name: {{ $logbook->name }}</p>
                                        <p class="card-text logbook-created-time">Uploaded at: {{ $logbook->created_at }}</p>
                                        <p class="card-text logbook-created-time">
                                            Status:
                                            <span class="status-unsigned">
                                                {{ $logbook->status }}
                                            </span>
                                        </p>
                                        
                                        <!-- Unsigned Delete button -->
                                        <button type="submit" class="delete-logbook-button" data-toggle="modal" data-target="#confirmationModal-{{ $logbook->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                        <!-- Unsigned Preview modal -->
                                        <div class="modal fade" id="previewModalUnsigned-{{ $logbook->id }}" tabindex="-1" role="dialog" aria-labelledby="previewModalLabelUnsigned-{{ $logbook->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="previewModalLabelUnsigned-{{ $logbook->id }}">Logbook Preview</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <iframe src="{{ asset($logbook->logbook_path) }}" frameborder="0" width="100%" height="500px"></iframe>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
        
                                        <!-- Unsigned Delete Confirmation modal -->
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
                </div>
        
                <!-- Signed Tab -->
                <div class="tab-pane fade" id="signed" role="tabpanel" aria-labelledby="signed-tab">
                    @if ($logbooks->where('status', 'Signed')->isEmpty())
                        <p>No signed logbooks uploaded yet.</p>
                    @else
                        <ul class="logbook-cards">
                            @foreach ($logbooks->where('status', 'Signed') as $logbook)
                            <li class="logbook-card">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="#" class="logbook-link" style="color: blue;" data-toggle="modal" data-target="#previewModalSigned-{{ $logbook->id }}">
                                                {{ pathinfo($logbook->logbook_path, PATHINFO_BASENAME) }}
                                            </a>
                                        </h5>
                                        <p class="card-text logbook-created-time">File name: {{ $logbook->name }}</p>
                                        <p class="card-text logbook-created-time">Uploaded at: {{ $logbook->created_at }}</p>
                                        <p class="card-text logbook-created-time">
                                            Status:
                                            <span class="status-signed">
                                                {{ $logbook->status }}
                                            </span>
                                        </p>
                                        
                                        <!-- Signed Delete button -->
                                        <button type="submit" class="delete-logbook-button" data-toggle="modal" data-target="#confirmationModal-{{ $logbook->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        
                                        <!-- Signed Preview Modal -->
                                        <div class="modal fade" id="previewModalSigned-{{ $logbook->id }}" tabindex="-1" role="dialog" aria-labelledby="previewModalLabelSigned-{{ $logbook->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="previewModalLabelUnsigned-{{ $logbook->id }}">Logbook Preview</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <iframe src="{{ asset($logbook->logbook_path) }}" frameborder="0" width="100%" height="500px"></iframe>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
        
                                        <!-- Signed Delete Confirmation Modal -->
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
                </div>
            </div>
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

    function openReportInNewTab(event) {
    event.preventDefault();

    const form = event.target;
    const startMonthInput = form.querySelector('#startMonth');
    const endMonthInput = form.querySelector('#endMonth');
    const errorContainer = form.querySelector('.error-container');

    errorContainer.textContent = '';
    errorContainer.classList.add('d-none');

    const startMonth = new Date(startMonthInput.value);
    const endMonth = new Date(endMonthInput.value);

    if (startMonth > endMonth) {
        errorContainer.innerHTML = "<span style='font-weight: bold; color: red;'>The start date must be earlier than or equal to the end date.</span>";
        errorContainer.classList.remove('d-none');
        return;
    }

    const formData = new FormData(form);
    const url = form.action + '?' + new URLSearchParams(formData).toString();
    window.open(url, '_blank');
}


</script>
@endsection