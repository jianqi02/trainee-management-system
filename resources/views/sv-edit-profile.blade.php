@extends('layouts.sv')

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" name="fullName" value="{{ $supervisor->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="phoneNum" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phoneNum" name="phoneNum" value="{{ $supervisor->phone_number }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="personalEmail" class="form-label">Personal Email</label>
                        <input type="email" class="form-control" id="personalEmail" name="personalEmail" value="{{ $supervisor->personal_email }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="expertise" class="form-label">Section</label>
                        <select class="form-select" id="section" name="section">
                            <option value="PSS" {{ $supervisor->section === 'PSS' ? 'selected' : '' }}>Professional Security Services (PSS)</option>
                            <option value="MSS" {{ $supervisor->section === 'MSS' ? 'selected' : '' }}>Managed Security Services (MSS)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="profilePicture" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profilePicture" name="profilePicture">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
@endsection