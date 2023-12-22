@extends('layouts.basicpage')
@section('pageTitle', 'Landing Page')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                @if(session('error'))
                    <div class="alert alert-warning">{{ session('error') }}</div>
                @endif 
                <div class="card-header">
                    <h2>{{ __('Welcome to the Trainee Management System!') }}</h2>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p class="lead">Manage your trainees or your tasks efficiently with our Trainee Management System (TMS)!</p>

                    <p><ins>Features for supervisors:</ins></p>
                    <ul>
                        <li>Track trainee information.</li>
                        <li>Assign a specific duration task to trainee.</li>
                        <li>Monitor trainee task timeline and progress.</li>
                        <li>View all trainee seat plans for the month.</li>
                    </ul>
                    <br>
                    <p><ins>Features for trainees:</ins></p>
                    <ul>
                        <li>Record and update your task progress.</li>
                        <li>Upload a logbook to be signed.</li>
                        <li>View all the seat plans for the month and locate your seat for the week easily.</li>
                    </ul>

                    <p>Get started today! <a href="{{ route('register') }}">Register</a> or <a href="{{ route('login') }}">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
