@extends('layouts.admin')
@section('pageTitle', 'Admin Dashboard')

@section('breadcrumbs', Breadcrumbs::render('dashboard'))

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0-beta2/css/all.min.css" integrity="sha384-4ByBMk1MxXrdS6JEIo0DDXkBC32b4V4or9jG7r1B4mXs6wM4Xf+OBo0IfkCFC73J4" crossorigin="anonymous">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="content">
        <div class="container mt-5">
            <h1 style="margin-top: -50px;">Dashboard</h1>     
            <form action="{{ route('admin-dashboard') }}" style="margin-top: 20px;">
                <div style="display: flex; align-items: center;">
                    <label for="week" style="margin-right: 10px; font-weight: bold; color: #555;">Select a week:</label>
                    <input type="week" id="week" name="week" value="{{ $weekRequired }}" style="padding: 5px; border: 1px solid #ccc; border-radius: 3px; font-size: 16px;">
                    <button type="submit" style="background-color: #007BFF; color: #fff; border: none; border-radius: 3px; padding: 5px 10px; font-size: 16px; cursor: pointer; margin-left: 20px;">Display Information</button>
                </div>
            </form>
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
                    <div class="input-group mb-3" style="width: 1075px;">
                        <input type="text" class="form-control" placeholder="Search trainees..." id="search-input">
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

        @if($exist != false)
            @php
                $seat = json_decode($seatingData, true);
            @endphp
            <p class="text-center" style="margin-top: 20px; margin-bottom: -65px; margin-right: 20px;">{{ $start_date }} to {{ $end_date }}</p>
            <div class="table-wrapper-horizontal">
                <div class="table-wrapper-vertical" style="width: 400px; height: 400px;">
                    <table class="map-level-1" id="map_level1" style="width: 350px; table-layout: fixed;">
                        <tbody>
                            <tr>
                                <td colspan="2" style="background-color: #D3D3D3;"> </td>
                                <td rowspan="6" style="background-color: #D3D3D3;"> </td>
                                <td colspan="2" style="text-align: right; background-color: #D3D3D3;"><strong>Exit>></strong></td>
                            </tr>
                            <tr>
                                <td id="CSM11" class="assign-popover" style="background-color: {{ $seat['CSM11']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if( $seat['CSM11']['seat_status'] != 'Not Available')
                                        CSM11 <div style="font-size: 10px;">({{ $seat['CSM11']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                                <td id="CSM12" class="assign-popover" style="background-color: {{ $seat['CSM12']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM12']['seat_status'] != 'Not Available')
                                        CSM12 <div style="font-size: 10px;">({{$seat['CSM12']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                                <td id="CSM01" class="assign-popover" style="background-color: {{ $seat['CSM01']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM01']['seat_status'] != 'Not Available')
                                        CSM01 <div style="font-size: 10px;">({{$seat['CSM01']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                                <td id="CSM02" class="assign-popover" style="background-color: {{ $seat['CSM02']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM02']['seat_status'] != 'Not Available')
                                        CSM02 <div style="font-size: 10px;">({{$seat['CSM02']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td id="CSM13" class="assign-popover" style="background-color: {{ $seat['CSM13']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM13']['seat_status'] != 'Not Available')
                                        CSM13 <div style="font-size: 10px;">({{$seat['CSM13']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                                <td id="CSM14" class="assign-popover" style="background-color: {{ $seat['CSM14']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM14']['seat_status'] != 'Not Available')
                                        CSM14 <div style="font-size: 10px;">({{$seat['CSM14']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                                <td id="CSM03" class="assign-popover" style="background-color: {{ $seat['CSM03']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM03']['seat_status'] != 'Not Available')
                                        CSM03 <div style="font-size: 10px;">({{$seat['CSM03']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif 
                                </td>
                                <td id="CSM04" class="assign-popover" style="background-color: {{ $seat['CSM04']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM04']['seat_status'] != 'Not Available')
                                        CSM04 <div style="font-size: 10px;">({{$seat['CSM04']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td id="CSM15" class="assign-popover" style="background-color: {{ $seat['CSM15']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM15']['seat_status'] != 'Not Available')
                                        CSM15 <div style="font-size: 10px;">({{$seat['CSM15']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                                <td id="CSM16" class="assign-popover" style="background-color: {{ $seat['CSM16']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM16']['seat_status'] != 'Not Available')
                                        CSM16 <div style="font-size: 10px;">({{$seat['CSM16']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                                <td id="CSM05" class="assign-popover" style="background-color: {{ $seat['CSM05']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM05']['seat_status'] != 'Not Available')
                                        CSM05 <div style="font-size: 10px;">({{$seat['CSM05']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                                <td id="CSM06" class="assign-popover" style="background-color: {{ $seat['CSM06']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM06']['seat_status'] != 'Not Available')
                                        CSM06 <div style="font-size: 10px;">({{$seat['CSM06']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td id="CSM17" class="assign-popover" style="background-color: {{ $seat['CSM17']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM17']['seat_status'] != 'Not Available')
                                        CSM17 <div style="font-size: 10px;">({{$seat['CSM17']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                                <td id="CSM18" class="assign-popover" style="background-color: {{ $seat['CSM18']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM18']['seat_status'] != 'Not Available')
                                        CSM18 <div style="font-size: 10px;">({{$seat['CSM18']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                                <td id="CSM07" class="assign-popover" style="background-color: {{ $seat['CSM07']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM07']['seat_status'] != 'Not Available')
                                        CSM07 <div style="font-size: 10px;">({{$seat['CSM07']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                                <td id="CSM08" class="assign-popover" style="background-color: {{ $seat['CSM08']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM08']['seat_status'] != 'Not Available')
                                        CSM08 <div style="font-size: 10px;">({{$seat['CSM08']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td id="CSM19" class="assign-popover" style="background-color: {{ $seat['CSM19']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM19']['seat_status'] != 'Not Available')
                                        CSM19 <div style="font-size: 10px;">({{$seat['CSM19']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                                <td id="CSM20" class="assign-popover" style="background-color: {{ $seat['CSM20']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM20']['seat_status'] != 'Not Available')
                                        CSM20 <div style="font-size: 10px;">({{$seat['CSM20']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                                <td id="CSM09" class="assign-popover" style="background-color: {{ $seat['CSM09']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM09']['seat_status'] != 'Not Available')
                                        CSM09 <div style="font-size: 10px;">({{$seat['CSM09']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                                <td id="CSM10" class="assign-popover" style="background-color: {{ $seat['CSM10']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    @if($seat['CSM10']['seat_status'] != 'Not Available')
                                        CSM10 <div style="font-size: 10px;">({{$seat['CSM10']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-center">Ground Floor Map (Level 1)</p>
                </div>
                <div class="table-wrapper-vertical" style="width: 950px;">
                    <table style="width: 100%; table-layout: fixed;" class="map-level-3" id="map_level3">
                        <tbody>
                            <tr>
                                <td style="width: 30.3944%; background-color: rgb(148, 148, 148);" colspan="3">Director Room</td>
                                <td style="width: 69.3736%; background-color: rgb(211, 211, 211);" rowspan="2" colspan="5">
                                    <div style="text-align: right;">Exit&gt;&gt;</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 15.9768%; background-color: {{ $seat['T01']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T01">
                                        @if($seat['T01']['seat_status'] != 'Not Available')
                                            T01 <div style="font-size: 10px;">({{$seat['T01']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 15.9768%; background-color: {{ $seat['T02']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T02">
                                        @if($seat['T02']['seat_status'] != 'Not Available')
                                            T02 <div style="font-size: 10px;">({{$seat['T02']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 15.9768%; background-color: {{ $seat['Round-Table']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="Round-Table">
                                        @if($seat['Round-Table']['seat_status'] != 'Not Available')
                                        Round Table <div style="font-size: 10px;">({{$seat['Round-Table']['trainee_id']}})</div>
                                    @else
                                        OTHER
                                    @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 9.9768%; background-color: rgb(211, 211, 211);" rowspan="4"><br></td>
                                <td style="width: 40.3712%; background-color: rgb(211, 211, 211);" colspan="6"><br></td>
                                <td style="width: 49.42%; background-color: rgb(211, 211, 211);" rowspan="11"><br></td>
                            </tr>
                            <tr>
                                <td style="width: 9.9768%; background-color: rgb(148, 148, 148);"><br></td>
                                <td style="width: 10.4408%; background-color: rgb(148, 148, 148);"><br></td>
                                <td style="width: 15.9768%; background-color: rgb(148, 148, 148);" colspan="2"><br></td>
                                <td style="width: 15.9768%; background-color: rgb(148, 148, 148);"><br></td>
                                <td style="background-color: rgb(211,211,211);" rowspan="2"><br></td>
                            </tr>
                            <tr>
                                <td style="width: 9.9768%; background-color: rgb(148, 148, 148);"><br></td>
                                <td style="width: 10.4408%; background-color: rgb(148, 148, 148);"><br></td>
                                <td style="width: 9.9768%; background-color: rgb(148, 148, 148);" colspan="2"><br></td>
                                <td style="width: 10%; background-color: rgb(148, 148, 148);"><br></td>
                            </tr>
                            <tr>
                                <td style="width: 40.3712%; background-color: rgb(211, 211, 211);" colspan="6"><br></td>
                            </tr>
                            <tr>
                                <td style="width: 9.9768%; background-color: rgb(148, 148, 148);"><br></td>
                                <td style="width: 9.9768%; background-color: rgb(148, 148, 148);"><br></td>
                                <td style="width: 10.4408%; background-color: rgb(148, 148, 148);"><br></td>
                                <td style="width: 9.9768%; background-color: rgb(148, 148, 148);"><br></td>
                                <td style="width: 10.0186%; background-color: rgb(148, 148, 148);" colspan="2"><br></td>
                                <td style="width: 9.9768%; background-color: rgb(211, 211, 211);" rowspan="2"><br></td>
                            </tr>
                            <tr>
                                <td style="width: 9.9768%; background-color: {{ $seat['T03']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T03">
                                        @if($seat['T03']['seat_status'] != 'Not Available')
                                            T03 <div style="font-size: 10px;">({{$seat['T03']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 9.9768%; background-color: {{ $seat['T04']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T04">
                                        @if($seat['T04']['seat_status'] != 'Not Available')
                                            T04 <div style="font-size: 10px;">({{$seat['T04']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 10.4408%; background-color: {{ $seat['T05']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T05">
                                        @if($seat['T05']['seat_status'] != 'Not Available')
                                            T05 <div style="font-size: 10px;">({{$seat['T05']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 9.9768%; background-color: {{ $seat['T06']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T06">
                                        @if($seat['T06']['seat_status'] != 'Not Available')
                                            T06 <div style="font-size: 10px;">({{$seat['T06']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td colspan="2" style="width: 10.0186%; background-color: rgb(148, 148, 148);"><br></td>
                            </tr>
                            <tr>
                                <td style="width: 10%; background-color: rgb(211, 211, 211);"><br></td>
                                <td style="width: 9.9768%; background-color: rgb(211, 211, 211);" rowspan="5"><br></td>
                                <td style="width: 20.4176%; background-color: rgb(211, 211, 211);" colspan="2" rowspan="2"><br></td>
                                <td style="width: 9.9768%; background-color: rgb(211, 211, 211);" rowspan="5"><br></td>
                                <td style="width: 20%; background-color: rgb(211, 211, 211);" colspan="2" rowspan="2"><br></td>
                            </tr>
                            <tr>
                                <td style="width: 9.9768%; background-color: {{ $seat['T07']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T07">
                                        @if($seat['T07']['seat_status'] != 'Not Available')
                                            T07 <div style="font-size: 10px;">({{$seat['T07']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 9.9768%; background-color: {{ $seat['T08']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T08">
                                        @if($seat['T08']['seat_status'] != 'Not Available')
                                            T08 <div style="font-size: 10px;">({{$seat['T08']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 10.4408%; background-color: {{ $seat['T09']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T09">
                                        @if($seat['T09']['seat_status'] != 'Not Available')
                                            T09 <div style="font-size: 10px;">({{$seat['T09']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 9.9768%; background-color: {{ $seat['T10']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T10">
                                        @if($seat['T10']['seat_status'] != 'Not Available')
                                            T10 <div style="font-size: 10px;">({{$seat['T10']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 15.9768%; background-color: {{ $seat['T15']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T15">
                                        @if($seat['T15']['seat_status'] != 'Not Available')
                                            T15 <div style="font-size: 10px;">({{$seat['T15']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 15.9768%; background-color: rgb(148, 148, 148);"><br></td>
                            </tr>
                            <tr>
                                <td style="width: 9.9768%; background-color: rgb(211, 211, 211);" rowspan="2"><br></td>
                                <td style="width: 10.4408%; background-color: {{ $seat['T11']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T11">
                                        @if($seat['T11']['seat_status'] != 'Not Available')
                                            T11 <div style="font-size: 10px;">({{$seat['T11']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 9.9768%; background-color: {{ $seat['T12']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;"class="assign-popover" id="T12">
                                        @if($seat['T12']['seat_status'] != 'Not Available')
                                            T12 <div style="font-size: 10px;">({{$seat['T12']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 10.0185%; background-color: {{ $seat['T16']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T16">
                                        @if($seat['T16']['seat_status'] != 'Not Available')
                                            T16 <div style="font-size: 10px;">({{$seat['T16']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 10.0185%; background-color: rgb(148, 148, 148);"><br></td>
                            </tr>
                            <tr>
                                <td style="width: 10.4408%; background-color: {{ $seat['T13']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T13">
                                        @if($seat['T13']['seat_status'] != 'Not Available')
                                            T13 <div style="font-size: 10px;">({{$seat['T13']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 9.9768%; background-color: {{ $seat['T14']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T14">
                                        @if($seat['T14']['seat_status'] != 'Not Available')
                                            T14 <div style="font-size: 10px;">({{$seat['T14']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 10.0185%; background-color: {{ $seat['T17']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T17">
                                        @if($seat['T17']['seat_status'] != 'Not Available')
                                            T17 <div style="font-size: 10px;">({{$seat['T17']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 10.0185%; background-color: rgb(148, 148, 148);"><br></td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-center">Second Floor Map (Level 3)</p>
                </div>
            </div>
        @endif
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