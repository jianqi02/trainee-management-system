@extends('layouts.app')
@section('pageTitle', 'Edit Profile')

@section('breadcrumbs', Breadcrumbs::render('editprofile'))

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

        .trainee-edit-profile-container {
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
        <div class="trainee-edit-profile-container">
            <div class="container mt-5">
                <h1>Edit Profile</h1>
                @if (session('alert'))
                    <div class="alert alert-warning">
                        {{ session('alert') }}
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
                <form method="POST" action="{{ route('update-profile') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="phoneNum" class="form-label">Phone Number*</label>
                        <input type="text" class="form-control" id="phoneNum" name="phoneNum" placeholder="Example: 0171234567 / 60171234567 / +60171234567" value="{{ $trainee->phone_number }} " required>
                    </div>
                    <div class="mb-3">
                        <label for="expertise" class="form-label">Expertise</label>
                        <select class="form-select" id="expertise" name="expertise">
                            <option value="Not Specified" {{ $trainee->expertise === 'Not Specified' ? 'selected' : '' }}>Not Specified</option>
                            <option value="Programming" {{ $trainee->expertise === 'Programming' ? 'selected' : '' }}>Programming</option>
                            <option value="Networking" {{ $trainee->expertise === 'Networking' ? 'selected' : '' }}>Networking</option>
                            <option value="Multimedia Design" {{ $trainee->expertise === 'Multimedia Design' ? 'selected' : '' }}>Multimedia Design</option>
                            <option value="Computer Security" {{ $trainee->expertise === 'Computer Security' ? 'selected' : '' }}>Computer Security</option>
                            <option value="Others" {{ $trainee->expertise === 'Others' ? 'selected' : '' }}>Others</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="personalEmail" class="form-label">Personal Email*</label>
                        <input type="email" class="form-control" id="personalEmail" name="personalEmail" value="{{ $trainee->personal_email }} " required>
                    </div>
                    <div class="mb-3">
                        <label for="graduateDate" class="form-label">Graduation Date</label>
                        <input type="date" class="form-control" id="graduateDate" name="graduateDate" value="{{ trim($trainee->graduate_date) }}">
                    </div>
                    <div class="mb-3">
                        <label for="profilePicture" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profilePicture" name="profilePicture" accept=".jpg, .jpeg, .png">
                    </div>                    
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
@endsection