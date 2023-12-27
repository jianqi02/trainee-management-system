@extends('layouts.sv')
@section('pageTitle', 'Homepage')

@section('breadcrumbs', Breadcrumbs::render('sv-homepage'))

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Verdana', Geneva, Tahoma, sans-serif;
            background-color: #f8f9fa; /* Set a light background color */
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .homepage-container {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          margin-top: 50px;
          margin-left: 150px;
          margin-right: 150px; 
          padding: 40px; 
          height: 75vh;
          width: 80%;
          background-color: #e9eff3; 
          border-radius: 14px;
          box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
      }

      .welcome-word {
          color: #0E272F;
          font-size: 2.5em;
          margin-bottom: 20px;
          width: 75%; 
          text-align: left;
      }

        .button-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 20px;
        }

        .rectangular-button {
          display: flex;
          align-items: center;
          justify-content: center;
          width: 200px;
          height: 70px;
          background-color: rgba(255, 255, 255, 0.7); /* Button background color with 70% opacity */
          color: #000000; /* Button text color */
          border: 1px solid #000000; /* 1px solid black border */
          border-radius: 5px;
          box-shadow: 0px 4px 8px rgba(23, 213, 223, 0.1);
          transition: box-shadow 0.3s ease-in-out; /* Smooth transition for hover effect */
          font-size: 1.2em;
          text-decoration: none;
        }

        .rectangular-button:hover {
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.4);
            color: #d3d3d3;
        }
    </style>
</head>
<body>
  <div class="container homepage-container mx-auto">
    @if(session('error'))
      <div class="alert alert-warning">{{ session('error') }}</div>
    @endif 
    <div class="row">
        <div class="col-md-6">
            <p class="tagline">Welcome to</p>
            <h1 class="welcome-word">Trainee Management System</h1>
            <p class="tagline">Manage your trainee tasks and progress with ease.</p>
        </div>
        
        <div class="col-md-6">
          <div class="button-container text-center">
              <div class="row">
                  <div class="col-md-12">
                    <a href="/sv-trainee-assign" class="rectangular-button" style="text-decoration: none;">My Trainee</a>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-12 order-md-2" style="margin-left: 200px;">
                    <a href="/sv-profile" class="rectangular-button" style="text-decoration: none;">Profile</a>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-12">
                    <a href="/sv-view-seat-plan" class="rectangular-button" style="text-decoration: none;">View Seat Plan</a>
                  </div>
              </div>
          </div>
      </div>
      
    </div>
  </div>
</body>
</html>
@endsection