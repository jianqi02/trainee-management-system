@extends('layouts.admin')
@section('pageTitle', 'Seating Arrangement')

@section('breadcrumbs', Breadcrumbs::render('seating-arrangement'))

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
</head>
<body>

    <div class="container my-5">
        <!-- Title -->
        <h1 class="text-center mb-4">Seating Arrangement</h1>
    
        <!-- Current Week Display -->
        <p class="text-center">Current Week: 
            {{ 
                \Carbon\Carbon::parse($currentSeatingPlan->start_date ?? now())->format('d/m/Y') 
            }} to 
            {{ 
                \Carbon\Carbon::parse($currentSeatingPlan->end_date ?? now())->format('d/m/Y') 
            }}</p>

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
                <!-- Edit Seating Plan for Current Week Button -->
                <a href="{{ route('seating-plan.edit') }}" class="btn btn-primary">Edit Current Week Seating Plan</a>
            @endif
        </div>

        <!-- Seating Plan Image Toggle Button -->
        @if ($currentSeatingImage)
            <div class="text-center">
                <span class="image-toggle" onclick="toggleImage()">+</span>
            </div>
            <div class="image-container" id="seatingImageContainer">
                <img src="{{ asset('storage/' . $currentSeatingImage->image_path) }}" alt="Seating Plan Image">
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
                        {{ \Carbon\Carbon::createFromFormat('d/m/Y', $selectedSeatingPlan->start_date ?? now()->format('d/m/Y'))->format('d/m/Y') }} to
                        {{ \Carbon\Carbon::createFromFormat('d/m/Y', $selectedSeatingPlan->end_date ?? now()->format('d/m/Y'))->format('d/m/Y') }}
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
            <a href="{{ route('seating-plan.edit', ['week' => request('week')]) }}" class="btn btn-primary" style="margin-top: 15px;">Edit Selected Seating Plan</a>
        @elseif (request('week'))
            <!-- If no seating plan exists for the selected week -->
            <div class="alert alert-warning text-center mt-4">No seating plan is available for the selected week.</div>
            <a href="{{ route('seating-plan.create', ['week' => request('week')]) }}" class="btn btn-success">Create New Seating Plan for This Week</a>
        @endif
    </div>

            <!-- Seating Plan Image Toggle Button -->
        @if ($selectedSeatingImage)
            <div class="text-center">
                <span class="image-toggle" onclick="toggleImage()">+</span>
            </div>
            <div class="image-container" id="seatingImageContainer">
                <img src="{{ asset('storage/' . $selectedSeatingImage->image_path) }}" alt="Seating Plan Image">
            </div>
        @endif

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle image visibility
        function toggleImage() {
            var imageContainer = document.getElementById('seatingImageContainer');
            var toggleButton = document.querySelector('.image-toggle');
            if (imageContainer.style.display === 'none') {
                imageContainer.style.display = 'block';
                toggleButton.textContent = '-';
            } else {
                imageContainer.style.display = 'none';
                toggleButton.textContent = '+';
            }
        }
    </script>
    </body>
    </html>
    @endsection
