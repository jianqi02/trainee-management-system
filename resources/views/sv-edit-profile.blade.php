@extends('layouts.sv')
@section('pageTitle', 'Edit Profile')

@section('breadcrumbs', Breadcrumbs::render('sv-edit-profile'))

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        h1{
            margin-top: -50px;
        }

        .content{
            margin-left: 250px;
        }

        .supervisor-edit-profile-container {
            width: auto;
            max-width: 80%;
            padding: 20px; 
            border-radius: 14px; 
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="supervisor-edit-profile-container">
            <div class="container mt-5">
                <h1>Edit Profile</h1>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('update-profile-sv') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="phoneNum" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phoneNum" name="phoneNum" placeholder="Example: 0171234567 / 60171234567 / +60171234567" value="{{ $supervisor->phone_number }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
@endsection