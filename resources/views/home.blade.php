@extends('layouts.basicpage')
@section('pageTitle', 'Landing Page')

@section('content')
<div class="container">
    <div class="jumbotron text-center bg-light" style="padding: 80px 40px;">
        <h1 class="display-4 font-weight-bold">Welcome to the Trainee Management System (TMS)</h1>
        <p class="lead">Efficiently manage your trainees, tasks, and progress with our easy-to-use platform.</p>
        <div class="mt-4">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg mr-3">Register</a>
            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">Login</a>
        </div>
    </div>

    <div class="row text-center mb-5">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h3 class="font-weight-bold">For Supervisors</h3>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-user fa-lg mr-2 text-primary"></i> Track trainee information.</li>
                        <li class="mb-3"><i class="fas fa-tasks fa-lg mr-2 text-primary"></i> Assign tasks with specific durations.</li>
                        <li class="mb-3"><i class="fas fa-chart-line fa-lg mr-2 text-primary"></i> Monitor task progress and timelines.</li>
                        <li class="mb-3"><i class="fas fa-map-marked-alt fa-lg mr-2 text-primary"></i> View seating plans.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h3 class="font-weight-bold">For Trainees</h3>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-edit fa-lg mr-2 text-primary"></i> Record and update task progress.</li>
                        <li class="mb-3"><i class="fas fa-file-upload fa-lg mr-2 text-primary"></i> Upload logbooks for signature.</li>
                        <li class="mb-3"><i class="fas fa-chair fa-lg mr-2 text-primary"></i> View seating plans for easy location tracking.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="text-center py-4 bg-dark text-light">
    <p>&copy; 2024 Trainee Management System.</p>
</footer>

@endsection
