@extends('layouts.sv')
@section('pageTitle', 'My Trainee')

@section('breadcrumbs', Breadcrumbs::render('my-trainee'))

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
  <style>
    h1{
      margin-left: 325px;
    }
    .sv-trainee-assign-container {
      margin-left: 150px;
      width: auto;
      max-width: 80%;
      padding: 20px; 
      border-radius: 14px; 
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    }

    .sv-current-trainee-info-container{
      padding: 20px;
      margin-top: 20px;
      margin-left: 250px;
      margin-right: 250px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    }

    .delete-one {
      background: none;
      border: none;
      margin-top: -120px;
      font-size: 30px; /* Adjust the size as needed */
      cursor: pointer;
    }

    .delete-all {
      background: none;
      border: none;
      margin-top: -20px;
      margin-bottom: -20px;
      font-size: 32px; /* Adjust the size as needed */
      cursor: pointer;
    }

    .add-new-trainee{
      background: none;
      border: none;
      margin-top: -20px;
      margin-bottom: -20px;
      margin-left: 250px;
      font-size: 32px; /* Adjust the size as needed */
      cursor: pointer;
    }

    .image-placeholder {
    width: 120px;
    height: 120px;
    margin-top: 20px;
    border-radius: 50%;
    max-width: 120px;
    max-height: 120px;
    background-color: #ccc;
    align-self: center;
    flex-shrink: 0; 
    }

    .horizontal-wrapper{
      display: flex;
      flex-direction: row;
    }

    .vertical-wrapper{
      display: flex;
      flex-direction: column;
      padding-top: 30px;
      padding-left: 10px;
    }

    .card{
      margin-right: 10px;
    }
  </style>
<div class="container sv-trainee-assign-container">
  <h4 class="text-center">My Trainee</h4>
  <div class="row">
      @foreach($traineeBasicDatas as $traineeData)
          @if($traineeData != null)
              <div class="col-md-6 mb-4">
                  <div class="card">
                      <img class="image-placeholder" src="{{ asset('storage/' . str_replace('public/', '', $traineeData->profile_image)) }}" alt="Profile Picture">
                      <div class="card-body">
                          <h5 class="card-title text-center">{{ $traineeData->name }}</h5><br>
                          <p><strong>Expertise:</strong> {{ $traineeData->expertise }}</p>
                          <p><strong>H/P No.:</strong> {{ $traineeData->phone_number }}</p>
                          <p><strong>Personal Email: </strong>{{ $traineeData->personal_email }}</p>
                          <p><strong>SAINS Email:</strong> {{ $traineeData->sains_email }}</p>
                          @if($traineeData->sains_email != "")
                              <a href="{{ route('go-profile', ['traineeName' => $traineeData->name]) }}" class="btn btn-primary">Go To Profile</a>
                          @endif
                          @if($traineeData->sains_email != "")
                              <a href="{{ route('go-task-timeline', ['traineeName' => $traineeData->name]) }}" class="btn btn-primary">Task Timeline</a>
                          @endif
                      </div>
                  </div>
              </div>
          @endif
      @endforeach
  </div>
</div>

</body>
</html>
@endsection