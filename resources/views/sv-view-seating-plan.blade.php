@extends('layouts.sv')
@section('pageTitle', 'View Seating Plan')

@section('breadcrumbs', Breadcrumbs::render('sv-view-seating-plan'))

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <style>
        /* Adjust table and card to make it more compact and modern */
        .seating-container {
            width: 1000px;
            display: flex;
            justify-content: space-between;
            margin-left: 100px;
        }
        .seating-table {
            flex: 1;
            margin-right: 20px;
        }
        .seating-buttons {
            flex-shrink: 0;
            text-align: right;
        }
        .table-container {
            width: 550px;
            max-width: 800px; /* Set a maximum width */
            margin: 0 auto;   /* Center the table */
            overflow-x: auto; /* Enable horizontal scrolling on small screens */
        }
        .table-card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .table th, .table td {
            padding: 0.75rem; /* Adjust table padding */
        }
        .table th {
            background-color: #f8f9fa;
            text-align: left;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
        .table-bordered {
            border: 1px solid #dee2e6;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6;
        }
        /* Make buttons smaller and well-aligned */
        .btn-modern {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }
        .image-toggle-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .image-toggle-btn:hover {
            background-color: #45a049;
        }
        .image-container {
            display: none; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            gap: 15px; 
            min-height: 400px; 
        }
        .seating-image {
            max-width: 100%;
            max-height: 500px;
            width: auto;
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        @media (max-width: 768px) {
            .seating-image {
                max-height: 300px; 
            }
        }
    </style>
</head>
<body>

    <div class="container my-5">
        <!-- Title -->
        <h1 class="text-center mb-4">Seating Arrangement</h1>
    
        <!-- Current Week Display -->
        <p class="text-center">Selected Week: 
            {{ 
                $selectedStartDate
            }} to 
            {{ 
                $selectedEndDate
            }}</p>

            <!-- Week Selection Form -->
            <form method="GET" action="{{ route('seating-plan.view-other-weeks') }}" class="text-center mb-4">
                <div class="form-group">
                    <label for="week">Select a Week:</label>
                    <input type="week" name="week" id="week" class="form-control d-inline-block w-auto mx-2" 
                        value="{{ request('week', now()->format('Y-\WW')) }}" onchange="this.form.submit()">
                </div>
            </form>

        @if (is_null($selectedSeatingPlan))
            <div class="alert alert-warning text-center">No seating plan is created for this week.</div>
        @else
            <!-- Current Week's Seating Plan Table -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Selected Week Seating Plan</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Seat Code</th>
                                    <th>Assigned To</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($selectedSeatingDetails as $seatCode => $assignedTo)
                                    <tr>
                                        <td>{{ $seatCode }}</td>
                                        <td>{{ $assignedTo }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if ($selectedSeatingImages)
            <!-- Image container, initially hidden -->
            <div class="image-container" id="seatingImageContainer" style="display: none;">
                @foreach($selectedSeatingImages as $seatingImage)
                    <img src="{{ asset('storage/' . $seatingImage) }}" alt="Seating Plan Image" class="seating-image">
                @endforeach
            </div>     
            <br>
            <div class="text-center">
                <!-- Updated toggle button -->
                <button class="image-toggle-btn" onclick="toggleImage()">Show Picture</button>
            </div>       
        @endif
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle image visibility
        function toggleImage() {
            var imageContainer = document.getElementById('seatingImageContainer');
            var toggleButton = document.querySelector('.image-toggle-btn');
            
            // Toggle the visibility and button text
            if (imageContainer.style.display === 'none') {
                imageContainer.style.display = 'flex'; // Use flex to vertically center
                toggleButton.textContent = 'Hide Picture';
            } else {
                imageContainer.style.display = 'none';
                toggleButton.textContent = 'Show Picture';
            }
        }
    </script>
    </body>
    </html>
@endsection