@extends('layouts.app')
@section('pageTitle', 'Homepage')

@section('breadcrumbs', Breadcrumbs::render('homepage'))

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css\trainee.css">
    <style>
        .rectangular-button{
            height: 50px;
        }
    </style>
</head>
<body class="homepage-view">
  <div class="container homepage-container mx-auto">
    @if(session('error'))
        <div class="alert alert-warning">{{ session('error') }}</div>
    @endif 
    <div class="row">
        <div class="col-md-6" style="margin-top: 75px;">
            <p class="tagline">Welcome to</p>
            <h1 class="welcome-word">Trainee Management System</h1>
            <p class="tagline">Manage your tasks and progress with ease.</p>
        </div>
        
        <div class="col-md-6">
          <div class="button-container text-center">
              <div class="row">
                  <div class="col-md-12">
                      <a href="/trainee-profile" class="rectangular-button" style="text-decoration: none;">Profile</a>
                  </div>
              </div>
              <div class="row">
                <div class="col-md-12 order-md-2" style="margin-left: 200px;">
                    <a href="/my-supervisor" class="rectangular-button" style="text-decoration: none;">My Supervisor</a>
                </div>
              </div>
              <div class="row">
                  <div class="col-md-12">
                      <a href="/view-seat-plan" class="rectangular-button" style="text-decoration: none;">View Seat Plan</a>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-12 order-md-2" style="margin-left: 200px;">
                      <a href="/trainee-upload-resume" class="rectangular-button" style="text-decoration: none;">Upload Resume</a>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-12">
                      <a href="/trainee-upload-logbook" class="rectangular-button" style="text-decoration: none;">Upload Logbook</a>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-12 order-md-2"  style="margin-left: 200px;">
                      <a href="/trainee-task-timeline" class="rectangular-button" style="text-decoration: none;">Task Timeline</a>
                  </div>
              </div>
          </div>
      </div>
      
    </div>
  </div>
</body>
</html>
@endsection