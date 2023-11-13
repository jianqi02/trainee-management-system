@extends('layouts.app')

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
</head>
<body>
  <style>
    .welcome-word {
      color: #0E272F;
      font-family: Verdana, Geneva, Tahoma, sans-serif;
      margin-top: 50px;
    }

    .btn-primary-ex1{
      background: #275968;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
      color: #FFFFFF;
      margin-right: 4.5rem;
      width: 9rem;
      height: 9rem;
      border-radius: 14px;
      border: none;
      line-height: 8rem;
      text-align: center;
      transition: background-color 0.3s, color 0.3s;
    }

    a{
      margin-right: 50px;
    }

    .btn-primary-ex2{
      background: #275968;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
      color: #FFFFFF;
      width: 9rem;
      height: 9rem;
      border-radius: 14px;
      justify-content: center;
      border: none;
      line-height: 8rem;
      text-align: center;
      transition: background-color 0.3s, color 0.3s;
    }

    .homepage-container {
      margin-top: 10px;
      margin-left: 130px;
      width: 80%;
      background: linear-gradient(to bottom, #ADD8E6, #FFFFF7);
      padding: 20px; 
      border-radius: 14px; 
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    }

  </style>
   
  <div class="container homepage-container">
    <div class="row">
      <div class="col-md-12 text-center">
        <h1 class="welcome-word">Welcome to Trainee Management System</h1>
      </div>
      <div class="col-md-6">
        <!-- Add more content here -->
      </div>
    </div>
  <div class="row justify-content-center align-items-center" style="min-height: 40vh;">
    <div class="col-md-10 text-center">
      <!-- Add your buttons here -->
      <a href="/trainee-profile" class="btn btn-primary-ex2">Profile</a>
      <a href="/view-seat-plan" class="btn btn-primary-ex2">View Seat Plan</a>
      <a href="/trainee-upload-resume" class="btn btn-primary-ex2">Upload Resume</a>
      <a href="/trainee-upload-logbook" class="btn btn-primary-ex2">Upload Logbook</a>
      <a href="/trainee-task-timeline" class="btn btn-primary-ex2">Task Timeline</a>
    </div>
  </div>
</div>
</body>
</html>
@endsection