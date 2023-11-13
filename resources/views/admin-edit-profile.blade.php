@extends('layouts.admin')

@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>

    <style>
        .content {
            margin-left: 250px;
            padding: 20px;
        }

        h1 {
            font-family: 'Roboto', sans-serif;
        }

        .card{
            width: 80%;
        }


    </style>
</head>
<body>
    <div class='content'>
        <!-- Display trainee-specific fields if applicable -->
        @if($user->role_id === 3)
        <div class="card">
            <div class="card-header">{{ __('Edit profile for') }} {{ $user->name }}</div>
            <div class="card-body">
            <div class="container mt-5">
                <form method="POST" action="{{ route('admin-update-profile', ['selected' => $user->name]) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3" style="margin-top: -50px;">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" name="fullName" value="{{ $trainee->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="phoneNum" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phoneNum" name="phoneNum" value="{{ $trainee->phone_number }}">
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
                        <label for="personalEmail" class="form-label">Personal Email</label>
                        <input type="email" class="form-control" id="personalEmail" name="personalEmail" value="{{ $trainee->personal_email }}">
                    </div>
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Internship Date (Start)</label>
                        <input type="date" class="form-control" id="startDate" name="startDate" value="{{ $internship_date->internship_start }}">
                    </div>
                    <div class="mb-3">
                        <label for="endDate" class="form-label">Internship Date (End)</label>
                        <input type="date" class="form-control" id="endDate" name="endDate" value="{{ $internship_date->internship_end }}">
                    </div>
                    <div class="mb-3">
                        <label for="graduateDate" class="form-label">Graduation Date</label>
                        <input type="date" class="form-control" id="graduateDate" name="graduateDate" value="{{ $trainee->graduate_date }}">
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
        </div>
        @endif

        <!-- Display supervisor-specific fields if applicable -->
        @if($user->role_id === 2)
        <div class="card">
            <div class="card-header">{{ __('Edit profile for') }} {{ $user->name }}</div>
            <div class="card-body">
            <div class="container mt-5">
                <form method="POST" action="{{ route('admin-update-profile', ['selected' => $user->name]) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3" style="margin-top: -50px;">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" name="fullName" value="{{ $supervisor->name }}">
                    </div>
                    <div class="mb-3">
                        <label for="phoneNum" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phoneNum" name="phoneNum" value="{{ $supervisor->phone_number }}">
                    </div>
                    <div class="mb-3">
                        <label for="personalEmail" class="form-label">Personal Email</label>
                        <input type="email" class="form-control" id="personalEmail" name="personalEmail" value="{{ $supervisor->personal_email }}">
                    </div>
                    <div class="mb-3">
                        <label for="expertise" class="form-label">Section</label>
                        <select class="form-select" id="section" name="section">
                            <option value="PSS" {{ $supervisor->section === 'PSS' ? 'selected' : '' }}>Professional Security Services (PSS)</option>
                            <option value="MSS" {{ $supervisor->section === 'MSS' ? 'selected' : '' }}>Managed Security Services (MSS)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
        </div>
        @endif
    </div>
</body>
@endsection