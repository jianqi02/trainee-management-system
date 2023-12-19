@extends('layouts.sv')
@section('pageTitle', 'Profile')

@section('breadcrumbs', Breadcrumbs::render('sv-profile'))

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .profile-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid #333;
            margin: 0 auto;
            display: block;
        }

        .profile-buttons {
            text-align: center;
            margin-top: 20px;
        }

        .profile-info {
            text-align: center;
            margin-top: 20px;
        }

        .profile-info h2 {
            margin: 0;
            color: #333;
        }

        .profile-info p {
            color: #777;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h3 class="text-center">Profile</h3>
        <div class="profile-info" style="text-align: left; margin-left: 30%;">
            <p>Name: {{ $supervisor->name }} </p>
            <p>SAINS Email: {{ $supervisor->sains_email }}</p>
            <p>Phone Number: {{ $supervisor->phone_number }}</p>
            <p>Section: {{ $supervisor->section }}</p>
            <p>Department: {{ $supervisor->department }}</p>
        </div>
        <div class="profile-buttons">
            <a href="/sv-edit-profile" class="btn btn-primary">Edit Profile</a>
        </div>
    </div>
</body>
</html>
@endsection