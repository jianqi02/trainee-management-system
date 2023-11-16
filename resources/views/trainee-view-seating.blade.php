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
        @foreach ($seatingData as $seat)
        <p class="text-center" style="margin-top: 20px; margin-bottom: -65px; margin-right: 20px;">{{ $seat['start_date']}} - {{ $seat['end_date']}}</p>
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
                            <td id="CSM11" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM11']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if( $seat['seating_plan']['CSM11']['seat_status'] != 'Not Available')
                                    CSM11 ({{ $seat['seating_plan']['CSM11']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM12" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM12']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM12']['seat_status'] != 'Not Available')
                                    CSM12 ({{$seat['seating_plan']['CSM12']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM01" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM01']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM01']['seat_status'] != 'Not Available')
                                    CSM01 ({{$seat['seating_plan']['CSM01']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM02" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM02']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM02']['seat_status'] != 'Not Available')
                                    CSM02 ({{$seat['seating_plan']['CSM02']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                        </tr>
                        <!-- Row 2 -->
                        <tr>
                            <td id="CSM13" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM13']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM13']['seat_status'] != 'Not Available')
                                    CSM13 ({{$seat['seating_plan']['CSM13']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM14" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM14']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM14']['seat_status'] != 'Not Available')
                                    CSM14 ({{$seat['seating_plan']['CSM14']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM03" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM03']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM03']['seat_status'] != 'Not Available')
                                    CSM03 ({{$seat['seating_plan']['CSM03']['trainee_id']}})
                                @else
                                    OTHER
                                @endif 
                            </td>
                            <td id="CSM04" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM04']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM04']['seat_status'] != 'Not Available')
                                    CSM04 ({{$seat['seating_plan']['CSM04']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                        </tr>
                        <!-- Row 3 -->
                        <tr>
                            <td id="CSM15" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM15']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM15']['seat_status'] != 'Not Available')
                                    CSM15 ({{$seat['seating_plan']['CSM15']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM16" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM16']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM16']['seat_status'] != 'Not Available')
                                    CSM16 ({{$seat['seating_plan']['CSM16']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM05" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM05']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM05']['seat_status'] != 'Not Available')
                                    CSM05 ({{$seat['seating_plan']['CSM05']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM06" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM06']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM06']['seat_status'] != 'Not Available')
                                    CSM06 ({{$seat['seating_plan']['CSM06']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                        </tr>
                        <!-- Row 4 -->
                        <tr>
                            <td id="CSM17" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM17']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM17']['seat_status'] != 'Not Available')
                                    CSM17 ({{$seat['seating_plan']['CSM17']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM18" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM18']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM18']['seat_status'] != 'Not Available')
                                    CSM18 ({{$seat['seating_plan']['CSM18']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM07" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM07']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM07']['seat_status'] != 'Not Available')
                                    CSM07 ({{$seat['seating_plan']['CSM07']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM08" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM08']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM08']['seat_status'] != 'Not Available')
                                    CSM08 ({{$seat['seating_plan']['CSM08']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                        </tr>
                        <!-- Row 5 -->
                        <tr>
                            <td id="CSM19" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM19']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM19']['seat_status'] != 'Not Available')
                                    CSM19 ({{$seat['seating_plan']['CSM19']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM20" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM20']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM20']['seat_status'] != 'Not Available')
                                    CSM20 ({{$seat['seating_plan']['CSM20']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM09" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM09']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM09']['seat_status'] != 'Not Available')
                                    CSM09 ({{$seat['seating_plan']['CSM09']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                            </td>
                            <td id="CSM10" class="assign-popover" style="background-color: {{ $seat['seating_plan']['CSM10']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                @if($seat['seating_plan']['CSM10']['seat_status'] != 'Not Available')
                                    CSM10 ({{$seat['seating_plan']['CSM10']['trainee_id']}})
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
                <table style="width: 80%;" class="map-level-3" id="map_level3">
                    <tbody>
                        <tr>
                            <td style="width: 30.3944%; background-color: rgb(148, 148, 148);" colspan="3">Director Room</td>
                            <td style="width: 69.3736%; background-color: rgb(211, 211, 211);" rowspan="2" colspan="5">
                                <div style="text-align: right;">Exit&gt;&gt;</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 15.9768%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T01">
                                    @if($seat['seating_plan']['T01']['seat_status'] != 'Not Available')
                                        T01 ({{$seat['seating_plan']['T01']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                            <td style="width: 15.9768%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T02">
                                    @if($seat['seating_plan']['T02']['seat_status'] != 'Not Available')
                                        T02 ({{$seat['seating_plan']['T02']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                            <td style="width: 15.9768%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="Round-Table">
                                    @if($seat['seating_plan']['Round-Table']['seat_status'] != 'Not Available')
                                    Round Table ({{$seat['seating_plan']['Round-Table']['trainee_id']}})
                                @else
                                    OTHER
                                @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 9.9768%; background-color: rgb(211, 211, 211);" rowspan="4"><br></td>
                            <td style="width: 40.3712%; background-color: rgb(211, 211, 211);" colspan="4"><br></td>
                            <td style="width: 49.42%; background-color: rgb(211, 211, 211);" colspan="3" rowspan="8"><br></td>
                        </tr>
                        <tr>
                            <td style="width: 9.9768%; background-color: rgb(148, 148, 148);"><br></td>
                            <td style="width: 10.4408%; background-color: rgb(148, 148, 148);"><br></td>
                            <td style="width: 15.9768%; background-color: rgb(148, 148, 148);"><br></td>
                            <td style="width: 15.9768%; background-color: rgb(148, 148, 148);"><br></td>
                        </tr>
                        <tr>
                            <td style="width: 9.9768%; background-color: rgb(148, 148, 148);"><br></td>
                            <td style="width: 10.4408%; background-color: rgb(148, 148, 148);"><br></td>
                            <td style="width: 9.9768%; background-color: rgb(148, 148, 148);"><br></td>
                            <td style="width: 10%; background-color: rgb(148, 148, 148);"><br></td>
                        </tr>
                        <tr>
                            <td style="width: 40.3712%; background-color: rgb(211, 211, 211);" colspan="4"><br></td>
                        </tr>
                        <tr>
                            <td style="width: 9.9768%; background-color: rgb(148, 148, 148);"><br></td>
                            <td style="width: 9.9768%; background-color: rgb(148, 148, 148);"><br></td>
                            <td style="width: 10.4408%; background-color: rgb(148, 148, 148);"><br></td>
                            <td style="width: 9.9768%; background-color: rgb(148, 148, 148);"><br></td>
                            <td style="width: 10.0186%; background-color: rgb(148, 148, 148);"><br></td>
                        </tr>
                        <tr>
                            <td style="width: 9.9768%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T03">
                                    @if($seat['seating_plan']['T03']['seat_status'] != 'Not Available')
                                        T03 ({{$seat['seating_plan']['T03']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                            <td style="width: 9.9768%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T04">
                                    @if($seat['seating_plan']['T04']['seat_status'] != 'Not Available')
                                        T04 ({{$seat['seating_plan']['T04']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                            <td style="width: 10.4408%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T05">
                                    @if($seat['seating_plan']['T05']['seat_status'] != 'Not Available')
                                        T05 ({{$seat['seating_plan']['T05']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                            <td style="width: 9.9768%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T06">
                                    @if($seat['seating_plan']['T06']['seat_status'] != 'Not Available')
                                        T06 ({{$seat['seating_plan']['T06']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                            <td style="width: 10.0186%; background-color: rgb(148, 148, 148);"><br></td>
                        </tr>
                        <tr>
                            <td style="width: 10%; background-color: rgb(211, 211, 211);"><br></td>
                            <td style="width: 9.9768%; background-color: rgb(211, 211, 211);" rowspan="5"><br></td>
                            <td style="width: 20.4176%; background-color: rgb(211, 211, 211);" colspan="2" rowspan="2"><br></td>
                            <td style="width: 9.9768%; background-color: rgb(211, 211, 211);" rowspan="5"><br></td>
                        </tr>
                        <tr>
                            <td style="width: 9.9768%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T07">
                                    @if($seat['seating_plan']['T07']['seat_status'] != 'Not Available')
                                        T07 ({{$seat['seating_plan']['T07']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 9.9768%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T08">
                                    @if($seat['seating_plan']['T08']['seat_status'] != 'Not Available')
                                        T08 ({{$seat['seating_plan']['T08']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                            <td style="width: 10.4408%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T09">
                                    @if($seat['seating_plan']['T09']['seat_status'] != 'Not Available')
                                        T09 ({{$seat['seating_plan']['T09']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                            <td style="width: 9.9768%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T10">
                                    @if($seat['seating_plan']['T10']['seat_status'] != 'Not Available')
                                        T10 ({{$seat['seating_plan']['T10']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                            <td style="width: 15.9768%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T15">
                                    @if($seat['seating_plan']['T15']['seat_status'] != 'Not Available')
                                        T15 ({{$seat['seating_plan']['T15']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                            <td style="width: 15.9768%; background-color: rgb(148, 148, 148);"><br></td>
                            <td style="width: 29.8701%; background-color: rgb(211, 211, 211);" rowspan="3"><br></td>
                        </tr>
                        <tr>
                            <td style="width: 9.9768%; background-color: rgb(211, 211, 211);" rowspan="2"><br></td>
                            <td style="width: 10.4408%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T11">
                                    @if($seat['seating_plan']['T11']['seat_status'] != 'Not Available')
                                        T11 ({{$seat['seating_plan']['T11']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                            <td style="width: 9.9768%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;"class="assign-popover" id="T12">
                                    @if($seat['seating_plan']['T12']['seat_status'] != 'Not Available')
                                        T12 ({{$seat['seating_plan']['T12']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                            <td style="width: 10.0185%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T16">
                                    @if($seat['seating_plan']['T16']['seat_status'] != 'Not Available')
                                        T16 ({{$seat['seating_plan']['T16']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                            <td style="width: 10.0185%; background-color: rgb(148, 148, 148);"><br></td>
                        </tr>
                        <tr>
                            <td style="width: 10.4408%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T13">
                                    @if($seat['seating_plan']['T13']['seat_status'] != 'Not Available')
                                        T13 ({{$seat['seating_plan']['T13']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                            <td style="width: 9.9768%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T14">
                                    @if($seat['seating_plan']['T14']['seat_status'] != 'Not Available')
                                        T14 ({{$seat['seating_plan']['T14']['trainee_id']}})
                                    @else
                                        OTHER
                                    @endif
                                </div>
                            </td>
                            <td style="width: 10.0185%; background-color: rgb(144, 238, 144);">
                                <div style="text-align: center;" class="assign-popover" id="T17">
                                    @if($seat['seating_plan']['T17']['seat_status'] != 'Not Available')
                                        T17 ({{$seat['seating_plan']['T17']['trainee_id']}})
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
        @endforeach
    </div>  
</body>
</html>
@endsection 