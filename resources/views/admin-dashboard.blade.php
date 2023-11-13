@extends('layouts.admin')

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            font-family: 'Roboto', sans-serif;
        }

        .card {
            margin-top: 20px;
            width: 100%;
            height: 100%;
        }

        .card-title {
            color:#555;
            font-size: 1rem;
        }

        .card-text {
            font-size: 2.1rem;
            color:#555;
        }

        .trainee-list-container{
            margin-top: 60px;
        }

        .content {
            margin-left: 120px;
            padding: 20px;
        }

        .trainee-list-table{
            max-height: 250px; /* Set the maximum height for the container */
            overflow-y: auto; /* Enable vertical scrolling when the content overflows */
            border: 1px solid #ccc; /* Optional: Add a border for clarity */
        }

        .map-level-1,
        .map-level-3 {
            border-collapse: collapse;
            width: 70%;
            height: 80%;
            margin-left: auto;
            margin-right: auto;
            margin-top: 70px;
        }

        .map-level-1 table, 
        .map-level-1 th, 
        .map-level-1 td,
        .map-level-3 table,
        .map-level-3 th,
        .map-level-3 td {
            border: 3px solid #000000;
            padding: 8px;
        }


        .map-level-1 td, 
        .map-level-3 td {
            min-width: 90px;
            max-width: 90px;
            min-height: 50px;
            max-height: 70px;
        }

        .seating-card {
            margin-top: 40px;
        }
        
        .table-wrapper-horizontal{
            display: flex;
            flex-direction: row;
            width: auto;
        }

        .table-wrapper-vertical{
            display: flex;
            flex-direction: column;
            margin-left: 20px;
            margin-right: 40px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0-beta2/css/all.min.css" integrity="sha384-4ByBMk1MxXrdS6JEIo0DDXkBC32b4V4or9jG7r1B4mXs6wM4Xf+OBo0IfkCFC73J4" crossorigin="anonymous">

</head>
<body>
    <div class="content">
        
        <h1>Dashboard</h1>
        
        
        <form action="{{ route('admin-dashboard') }}" style="margin-top: 20px;">
            <div style="display: flex; align-items: center;">
                <label for="week" style="margin-right: 10px; font-weight: bold; color: #555;">Select a week:</label>
                <input type="week" id="week" name="week" value="{{ $weekRequired }}" style="padding: 5px; border: 1px solid #ccc; border-radius: 3px; font-size: 16px;">
                <button type="submit" style="background-color: #007BFF; color: #fff; border: none; border-radius: 3px; padding: 5px 10px; font-size: 16px; cursor: pointer; margin-left: 20px;">Display Information</button>
            </div>
        </form>
        <div class="container mt-5">
            <div class="row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 22px;">Current Number of Trainee(s)</h5>
                            <p class="card-text">{{ $count }}</p>
                        </div>
                    </div>
                </div>
        
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 22px;">Total Trainee(s)</h5>
                            <p class="card-text">{{ $totalTrainee }}</p>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card seating-card">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 22px;">Empty Seat(s) Available</h5>
                            <h6 class="card-title" style="font-size: 14px;">from {{ $start_date }} to {{ $end_date }}</h6>
                            <p class="card-text">{{ $weeklyData['empty_seat_count'] }}</p>
                        </div>
                    </div>
                </div>
    
                <div class="col-md-4 mb-3">
                    <div class="card seating-card">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 22px;">Seat(s) Occupied</h5>
                            <h6 class="card-title" style="font-size: 14px;">from {{ $start_date }} to {{ $end_date }}</h6>
                            <p class="card-text">{{ $weeklyData['occupied_seat_count'] }}</p>
                        </div>
                    </div>
                </div>
    
                <div class="col-md-4 mb-3">
                    <div class="card seating-card">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 22px;">Total Seat(s)</h5>
                            <h6 class="card-title" style="font-size: 14px;">from {{ $start_date }} to {{ $end_date }}</h6>
                            <p class="card-text">{{ $weeklyData['total_seat_count'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        <div class="container trainee-list-container">
            <div class="row">
                <div class="col-md-4">
                    <!-- Search Bar -->
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Search trainees" id="search-input">
                        <button class="btn btn-outline-secondary" type="button" id="search-button">Search</button>
                    </div>
                </div>
                <div class="col-md-8">
                </div>
            </div>
        </div>
            
        <div class="container mt-4 trainee-list-table">
            <table class="table table-striped" id="trainee-table">
                <thead>
                    <tr>
                        <th>Name
                            <button class="sort-button-trainee" data-column="0" style="border: none; background: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                    <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                </svg>
                            </button>
                        </th>
                        <th>Internship Date (Start)
                            <button class="sort-button-trainee" data-column="1" style="border: none; background: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                    <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                </svg>
                            </button>
                        </th>
                        <th>Internship Date (End)
                            <button class="sort-button-trainee" data-column="2" style="border: none; background: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                    <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                </svg>
                            </button>
                        </th>
                        <th>Graduation Date
                            <button class="sort-button-trainee" data-column="3" style="border: none; background: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                    <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                </svg>
                            </button>
                        </th>
                        <th>Logbook Submitted
                            <button class="sort-button-trainee" data-column="4" style="border: none; background: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                    <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                </svg>
                            </button>
                        </th>
                        <th>Expertise
                            <button class="sort-button-trainee" data-column="5" style="border: none; background: none;">
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                                    <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                                </svg>
                            </button>
                        </th>
                    </tr> 
                </thead>
                <tbody>
                    @foreach ($trainees as $trainee)
                        <tr id="trainee-{{ $trainee->name }}">
                            <td>{{ $trainee->name }}</td>
                            <td>{{ $trainee->internship_start }}</td>
                            <td>{{ $trainee->internship_end }}</td>
                            <td>{{ $trainee->graduate_date }}</td>
                            <td>
                                @if($trainee->logbooks->isNotEmpty())
                                    <a href="{{ route('view-and-upload-logbook', ['traineeName' => $trainee->name]) }}">Yes</a>
                                @else
                                    No
                                @endif
                            </td>
                            <td>{{ $trainee->expertise }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @foreach ($seatingArray as $seatInfo)
        <p class="text-center" style="margin-top: 20px; margin-bottom: -65px; margin-right: 20px;">{{ $seatInfo[0]['start_date']}} - {{ $seatInfo[0]['end_date']}}</p>
        <div class="table-wrapper-horizontal">
            <div class="table-wrapper-vertical">
                <table class="map-level-1" id="map_level1">
                    <tbody>
                        <!-- Entrance / Exit -->
                        <tr>
                            <td colspan="2" style="background-color: #D3D3D3;"> </td>
                            <td rowspan="6" style="background-color: #D3D3D3;"> </td>
                            <td colspan="2" style="text-align: right; background-color: #D3D3D3;"><strong>Exit>></strong></td>
                        </tr>
                        <!-- Row 1 -->
                        <tr>
                            <td id="{{ $seatInfo[10]['seat_name'] }}" class="assign-popover" style="background-color: {{ $seatInfo[10]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[10]['seat_status'] != 'Not Available')
                                    {{ $seatInfo[10]['seat_name'] }} ({{ $seatInfo[10]['trainee_name'] }})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM12" class="assign-popover" style="background-color: {{ $seatInfo[11]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[11]['seat_status'] != 'Not Available')
                                    {{$seatInfo[11]['seat_name']}} ({{$seatInfo[11]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM01" class="assign-popover" style="background-color: {{ $seatInfo[0]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[0]['seat_status'] != 'Not Available')
                                    {{$seatInfo[0]['seat_name']}} ({{$seatInfo[0]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM02" class="assign-popover" style="background-color: {{ $seatInfo[1]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[1]['seat_status'] != 'Not Available')
                                    {{$seatInfo[1]['seat_name']}} ({{$seatInfo[1]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                        </tr>
                        <!-- Row 2 -->
                        <tr>
                            <td id="CSM13" class="assign-popover" style="background-color: {{ $seatInfo[12]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[12]['seat_status'] != 'Not Available')
                                    {{$seatInfo[12]['seat_name']}} ({{$seatInfo[12]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM14" class="assign-popover" style="background-color: {{ $seatInfo[13]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[13]['seat_status'] != 'Not Available')
                                    {{$seatInfo[13]['seat_name']}} ({{$seatInfo[13]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM03" class="assign-popover" style="background-color: {{ $seatInfo[2]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[2]['seat_status'] != 'Not Available')
                                    {{$seatInfo[2]['seat_name']}} ({{$seatInfo[2]['trainee_name']}})
                                @else
                                    OTHER
                                @endif 
                            </td>
                            <td id="CSM04" class="assign-popover" style="background-color: {{ $seatInfo[3]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[3]['seat_status'] != 'Not Available')
                                    {{$seatInfo[3]['seat_name']}} ({{$seatInfo[3]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                        </tr>
                        <!-- Row 3 -->
                        <tr>
                            <td id="CSM15" class="assign-popover" style="background-color: {{ $seatInfo[14]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[14]['seat_status'] != 'Not Available')
                                    {{$seatInfo[14]['seat_name']}} ({{$seatInfo[14]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM16" class="assign-popover" style="background-color: {{ $seatInfo[15]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[15]['seat_status'] != 'Not Available')
                                    {{$seatInfo[15]['seat_name']}} ({{$seatInfo[15]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM05" class="assign-popover" style="background-color: {{ $seatInfo[4]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[4]['seat_status'] != 'Not Available')
                                    {{$seatInfo[4]['seat_name']}} ({{$seatInfo[4]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM06" class="assign-popover" style="background-color: {{ $seatInfo[5]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[5]['seat_status'] != 'Not Available')
                                    {{$seatInfo[5]['seat_name']}} ({{$seatInfo[5]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                        </tr>
                        <!-- Row 4 -->
                        <tr>
                            <td id="CSM17" class="assign-popover" style="background-color: {{ $seatInfo[16]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[16]['seat_status'] != 'Not Available')
                                    {{$seatInfo[16]['seat_name']}} ({{$seatInfo[16]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM18" class="assign-popover" style="background-color: {{ $seatInfo[17]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[17]['seat_status'] != 'Not Available')
                                    {{$seatInfo[17]['seat_name']}} ({{$seatInfo[17]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM07" class="assign-popover" style="background-color: {{ $seatInfo[6]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[6]['seat_status'] != 'Not Available')
                                    {{$seatInfo[6]['seat_name']}} ({{$seatInfo[6]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM08" class="assign-popover" style="background-color: {{ $seatInfo[7]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[7]['seat_status'] != 'Not Available')
                                    {{$seatInfo[7]['seat_name']}} ({{$seatInfo[7]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                        </tr>
                        <!-- Row 5 -->
                        <tr>
                            <td id="CSM19" class="assign-popover" style="background-color: {{ $seatInfo[18]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[18]['seat_status'] != 'Not Available')
                                    {{$seatInfo[18]['seat_name']}} ({{$seatInfo[18]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM20" class="assign-popover" style="background-color: {{ $seatInfo[19]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[19]['seat_status'] != 'Not Available')
                                    {{$seatInfo[19]['seat_name']}} ({{$seatInfo[19]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM09" class="assign-popover" style="background-color: {{ $seatInfo[8]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[8]['seat_status'] != 'Not Available')
                                    {{$seatInfo[8]['seat_name']}} ({{$seatInfo[8]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM10" class="assign-popover" style="background-color: {{ $seatInfo[9]['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seatInfo[9]['seat_status'] != 'Not Available')
                                    {{$seatInfo[9]['seat_name']}} ({{$seatInfo[9]['trainee_name']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="text-center">Ground Floor Map (Level 1)</p>
            </div>
            <div class="table-wrapper-vertical">
                <table class="map-level-3" id="map_level3">
                    <tbody>
                        <tr>
                            <td rowspan="2" colspan="3" style="background-color: #D3D3D3;">Director's Room</td>
                            <td rowspan="2" colspan="9" style="background-color: #D3D3D3;"></td>
                        </tr>
                        <tr>
                        </tr>
                        <!-- Row 1 -->
                        <tr>
                            <td id="T1" class="assign-popover" style="background-color: #90EE90;">T1 ({{$seatInfo[20]['trainee_name']}})</td>
                            <td id="T2" class="assign-popover" style="background-color: #90EE90;">T2 ({{$seatInfo[21]['trainee_name']}})</td>
                            <td id="Round-Table" class="assign-popover" style="background-color: #90EE90;">Round Table ({{$seatInfo[22]['trainee_name']}}) </td>
                            <td colspan="9" style="background-color: #D3D3D3; text-align: right;"><strong>Exit>></strong></td>
                        </tr>
                        <!-- Row 2 -->
                        <tr>
                            <td rowspan="7" style="background-color: #D3D3D3;"></td>
                            <td colspan="9" style="background-color: #D3D3D3;"></td>
                            <td rowspan="7" colspan="3" style="background-color: #D3D3D3;"></td>
                        </tr>
                        <!-- Row 3 -->
                        <tr>
                            <td style="background-color: #90EE90;"> </td>
                            <td style="background-color: #90EE90;"> </td>
                            <td style="background-color: #90EE90;"> </td>
                            <td style="background-color: #90EE90;"> </td>
                        </tr>
                        <!-- Row 4 -->
                        <tr>
                            <td style="background-color: #90EE90;"> </td>
                            <td style="background-color: #90EE90;"> </td>
                            <td style="background-color: #90EE90;"> </td>
                            <td style="background-color: #90EE90;"> </td>
                        </tr>
                        <!-- Row 5 -->
                        <tr>
                            <td colspan="10" style="background-color: #D3D3D3;"></td>
                        </tr>
                        <tr>
                            <td style="background-color: #90EE90;"> </td>
                            <td style="background-color: #90EE90;"> </td>
                            <td style="background-color: #90EE90;"> </td>
                            <td style="background-color: #90EE90;"> </td>
                        </tr>
                        <tr>
                            <td style="background-color: #90EE90;"> </td>
                            <td style="background-color: #90EE90;"> </td>
                            <td style="background-color: #90EE90;"> </td>
                            <td style="background-color: #90EE90;"> </td>
                        </tr>
                        <tr>
                            <td colspan="8" style="background-color: #D3D3D3;"></td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
                <p class="text-center">Second Floor Map (Level 3)</p>
            </div>
        </div>
        @endforeach
    </div>
</body>
<script>
    const traineeFilterButtons = document.querySelectorAll('.sort-button-trainee');
    let columnToSort = -1; // Track the currently sorted column
    let ascending = true; // Track the sorting order

    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("search-input");
        const traineeTable = document.getElementById("trainee-table");

        searchInput.addEventListener("keyup", function () {
            const searchValue = searchInput.value.toLowerCase();

            for (let i = 1; i < traineeTable.rows.length; i++) {
                const row = traineeTable.rows[i];
                const name = row.cells[0].textContent.toLowerCase();
                
                if (name.includes(searchValue)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        });
    });

    traineeFilterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const column = button.dataset.column;
            if (column === columnToSort) {
                ascending = !ascending; // Toggle sorting order if the same column is clicked
            } else {
                columnToSort = column;
                ascending = true; // Default to ascending order for the clicked column
            }

            // Call the function to sort the table
            sortTableTrainee(column, ascending);
        });
    });

    

    function sortTableTrainee(column, ascending) {
        const table = document.getElementById('trainee-table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        rows.sort((a, b) => {
            const cellA = a.querySelectorAll('td')[column].textContent;
            const cellB = b.querySelectorAll('td')[column].textContent;
            return ascending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
        });

        tbody.innerHTML = '';
        rows.forEach((row) => {
            tbody.appendChild(row);
        });
    }
</script>
</html>
@endsection