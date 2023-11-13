@extends('layouts.app')

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Seat Plan</title>
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
</head>
<body>
    <div class="content">
        <h1>Monthly Seat Plan</h1>
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
</html>
@endsection 