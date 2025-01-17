@extends('layouts.admin')
@section('pageTitle', 'Seating Arrangement')

@section('breadcrumbs', Breadcrumbs::render('seating-arrangement'))

@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <style>
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
            max-width: 800px; 
            margin: 0 auto;  
            overflow-x: auto; 
        }
        .table-card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .table th, .table td {
            padding: 0.75rem;
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
        .btn-modern {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }
        .image-toggle-btn,
        .image-toggle-btn2 {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .image-toggle-btn:hover,
        .image-toggle-btn2:hover {
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
        <p class="text-center">Current Week: 
            {{ 
                $currentStartDate
            }} to 
            {{ 
                $currentEndDate
            }}</p>

        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
        @endif

        @if ($errors->any()) 
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error) 
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if (is_null($currentSeatingPlan))
            <div class="alert alert-warning text-center">No seating plan is created for this week.</div>
        @else
            <!-- Current Week's Seating Plan Table -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Current Week Seating Plan</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Seat Code</th>
                                    <th>Assigned To</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($currentSeatingDetails as $seatCode => $assignedTo)
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
    
        <!-- Buttons Section -->
        <div class="text-end mt-3">
            <!-- Create Seating Plan for This Week Button if the Selected Week does not have a Plan-->
            @if (is_null($currentSeatingPlan))
                <a href="{{ route('seating-plan.create', ['week' => now()->format('Y-\WW')]) }}" class="btn btn-success">Create New Seating Plan for This Week</a>
            @endif
            @if (!is_null($currentSeatingPlan))
                <!-- Edit Seating Plan and Remove Seating Plan for Current Week Button -->
                <a href="{{ route('seating-plan.edit') }}" class="btn btn-primary">Edit Current Week Seating Plan</a>
                <form id="remove-seating-plan-form" action="{{ route('seating-plan.remove', ['week' => now()->format('Y-\WW')]) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
        
                    <button type="button" class="btn btn-danger" onclick="confirmRemove()">Remove Current Week Seating Plan</button>
                </form>
            @endif
        </div>

        <br>

        @if ($currentSeatingImages)
            <!-- Image container, initially hidden -->
            <div class="image-container" id="seatingImageContainer" style="display: none;">
                @foreach($currentSeatingImages as $seatingImage)
                    <img src="{{ asset('storage/' . $seatingImage) }}" alt="Seating Plan Image" class="seating-image">
                @endforeach
            </div>      
            <br>
            <div class="text-center">
                <!-- Updated toggle button -->
                <button class="image-toggle-btn" onclick="toggleImage()">Show Picture</button>
            </div>      
        @endif
    
        <!-- Divider -->
        <hr class="my-5">
    
        <!-- View Other Week Seating Plans -->
        <h3 class="text-center">View Other Week Seating Plans</h3>

        <!-- Week Selection Form -->
        <form method="GET" action="{{ route('seating-plan.view-other-weeks') }}" class="text-center mb-4">
            <div class="form-group">
                <label for="week">Select a Week:</label>
                <input type="week" name="week" id="week" class="form-control d-inline-block w-auto mx-2" 
                    value="{{ request('week', now()->format('Y-\WW')) }}" onchange="this.form.submit()">
            </div>
        </form>

    
        <!-- Display Selected Week's Seating Plan -->
        @if (!is_null($selectedSeatingPlan))
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Seating Plan for Week:
                        {{ $selectedStartDate }} to
                        {{ $selectedEndDate }}
                    </h5>
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
            <div class="text-end mt-3">
                <a href="{{ route('seating-plan.edit', ['week' => request('week')]) }}" class="btn btn-primary" style="margin-top: 15px;">Edit Selected Seating Plan</a>
                <form id="remove-seating-plan-form" action="{{ route('seating-plan.remove', ['week' => request('week')]) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
        
                    <button type="button" class="btn btn-danger" onclick="confirmRemove()" style="margin-top: 15px;">Remove Current Week Seating Plan</button>
                </form>
            </div>
        @elseif (request('week'))
            <!-- If no seating plan exists for the selected week -->
            <div class="alert alert-warning text-center mt-4">No seating plan is available for the selected week.</div>
            <div class="text-end mt-3">
                <a href="{{ route('seating-plan.create', ['week' => request('week')]) }}" class="btn btn-success">Create New Seating Plan for This Week</a>
            </div>
        @endif
    </div>

    @if ($selectedSeatingImages)
        <!-- Image container, initially hidden -->
        <div class="image-container" id="seatingImageContainer2" style="display: none;">
            @foreach($selectedSeatingImages as $seatingImage)
                <img src="{{ asset('storage/' . $seatingImage) }}" alt="Seating Plan Image" class="seating-image">
            @endforeach
        </div>     
        <br>
        <div class="text-center">
            <!-- Updated toggle button -->
            <button class="image-toggle-btn2" onclick="toggleImage2()">Show Picture</button>
        </div>       
    @endif

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

        function toggleImage2() {
            var imageContainer = document.getElementById('seatingImageContainer2');
            var toggleButton = document.querySelector('.image-toggle-btn2');
            
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
    <script>
        function confirmRemove() {
            if (confirm('Are you sure you want to remove the seating plan for the current week? This action cannot be undone.')) {
                // Submit the form if the user confirms
                document.getElementById('remove-seating-plan-form').submit();
            }
        }
    </script>
    </body>
    </html>
    @endsection
