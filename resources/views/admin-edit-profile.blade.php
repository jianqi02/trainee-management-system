@extends('layouts.admin')
@section('pageTitle', 'Edit Profile')



@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .card {
            margin-left: 150px;
            margin-right: 150px;
        }
    </style>
</head>
<body>
    <div class="content-edit-profile">
        <!-- Display trainee-specific fields if applicable -->
        @if($user->role_id === 3)
        @section('breadcrumbs', Breadcrumbs::render('admin-edit-trainee-profile', $user->name))
        <div class="card card-edit-profile">
            <div class="card-header">{{ __('Edit profile for') }} {{ $user->name }}</div>
            <div class="card-body">
            @if (session('error'))
                    <div class="alert alert-warning">
                        {{ session('error') }}
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
            <div class="container mt-5">
                <form method="POST" action="{{ route('admin-update-profile', ['selected' => $user->name]) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3" style="margin-top: -50px;">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" name="fullName" value="{{ $trainee->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="phoneNum" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phoneNum" name="phoneNum" placeholder="Example: 0171234567 / 60171234567 / +60171234567" value="{{ $trainee->phone_number }}">
                    </div>
                    <div class="mb-3">
                        <label for="expertise" class="form-label">Expertise</label>
                        <select class="form-select" id="expertise" name="expertise">
                            <option value="Not Specified" {{ $trainee->expertise === 'Not Specified' ? 'selected' : '' }}>Not Specified</option>

                            @foreach($expertises as $expertise)
                                <option value="{{ $expertise }}" {{ $trainee->expertise === $expertise ? 'selected' : '' }}>
                                    {{ $expertise }}
                                </option>
                            @endforeach
                    
                            <option value="Others" {{ $trainee->expertise === 'Others' ? 'selected' : '' }}>Others</option>
                        </select>
                        <a href="/settings" style="font-size: 14px;">add or remove expertises...</a>
                    </div>
                    
                    <div class="mb-3">
                        <label for="personalEmail" class="form-label">Personal Email</label>
                        <input type="email" class="form-control" id="personalEmail" name="personalEmail" value="{{ $trainee->personal_email }}">
                    </div>
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Internship Date (Start)</label>
                        <input type="date" class="form-control" id="startDate" name="startDate" value="{{ $internship_date->internship_start ?? null }}">
                    </div>
                    <div class="mb-3">
                        <label for="endDate" class="form-label">Internship Date (End)</label>
                        <input type="date" class="form-control" id="endDate" name="endDate" value="{{ $internship_date->internship_end ?? null }}">
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
        @section('breadcrumbs', Breadcrumbs::render('admin-edit-sv-profile', $user->name))
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
                        <label for="expertise" class="form-label">Expertise</label>
                        <select class="form-select" id="expertise" name="expertise">
                            <option value="Not Specified" {{ $supervisor->expertise === 'Not Specified' ? 'selected' : '' }}>Not Specified</option>

                            @foreach($expertises as $expertise)
                                <option value="{{ $expertise }}" {{ $supervisor->expertise === $expertise ? 'selected' : '' }}>
                                    {{ $expertise }}
                                </option>
                            @endforeach
                    
                            <option value="Others" {{ $supervisor->expertise === 'Others' ? 'selected' : '' }}>Others</option>
                        </select>
                        <a href="/settings" style="font-size: 14px;">add or remove expertises...</a>
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <select class="form-select" id="department" name="department">
                            @foreach($departments as $department)
                                <option value="{{ $department }}" {{ $supervisor->department === $department ? 'selected' : '' }}>
                                    {{ $department }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="section" class="form-label">Section</label>
                        <select class="form-select" id="section" name="section">
                            @foreach($sections as $section)
                                <option value="{{ $section }}" {{ $supervisor->section === $section ? 'selected' : '' }}>
                                    {{ $section }}
                                </option>
                            @endforeach
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