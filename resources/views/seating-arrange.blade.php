@extends('layouts.admin')

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seating Arrangement</title>
</head>
<body>
  <style>
    .alert {
        /* Adjust the width as needed */
        max-width: 1000px;
        margin: 0 auto; /* Center the alert horizontally */
    }

    .btn-primary-ex1{
      background: #275968;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
      color: #FFFFFF;
      width: auto;
      height: 35px;
      border-radius: 14px;
      border: none;
      text-align: center;
      margin-bottom: 20px;
      margin-top: 10px;
      transition: background-color 0.3s, color 0.3s;
    }

    .btn-primary-ex2{
      background: #275968;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
      color: #FFFFFF;
      width: 200px;
      height: 40px;
      border-radius: 14px;
      border: none;
      text-align: center;
      margin-bottom: 20px;
      margin-top: 10px;
      transition: background-color 0.3s, color 0.3s;
    }

    .homepage-container {
      margin-top: 20px;
      margin-left: 100px;
      background: linear-gradient(to bottom, #ADD8E6, #FFFFF7);
      padding: 20px; 
      border-radius: 14px; 
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    }

    table {
        border-collapse: collapse;
        width: 70%;
        height: 80%;
        margin: 20px auto;
    }

    table, th, td {
        border: 3px solid #000000;
        padding: 8px;
    }

    td{
        min-width: 50px;
        max-width: 70px;
        min-height: 50px;
        max-height: 70px;
    }

    .seating-arrange-wrapper{
        display: flex;
        flex-direction: row;
    }

    .assign-bar-wrapper{
        display: flex;
        flex-direction: column;
        width: 350px;
    }

    .card {
        display: none;
        position: absolute;
        background-color: #fff;
        border: 4px solid #ccc;
        padding: 10px;
        border-radius: 20px;
        height: auto;
        min-width: 306px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.6);
    }

    .seat-assign-trainee-list {
        max-height: 180px; 
        overflow-y: scroll; 
        border: 1px solid #ccc;
    }

    .manual-assign-ul {
        list-style-type: none;
        padding: 0;
    }

    .manual-assign-li {
        padding: 10px;
        border-bottom: 1px solid #ccc;
    }

    #carouselExample{
        width: 750px;
        align-self: center;
    }

    .carousel-control-prev-icon{
        background-color: #000000;
    }

    .carousel-control-next-icon{
        background-color: #000000;
    }

    .dropdown{
        margin-bottom: 10px;
    }

    .trainee-assign-button-group{
        margin-top: 100px;
        margin-left: 50px;
        display: flex;
        flex-direction: column;
    }

    .no-border-button {
        border: none;
        background: none;
        width: 100%;
        height: 100%;
        padding: 0;
        margin: 0;
        cursor: pointer;
    }

    /* Default styling for list items */
    #selectable-list li {
        cursor: pointer;
        padding: 8px;
        border: 1px solid #ccc;
        margin: 4px;
    }

    /* Styling for selected item */
    #selectable-list .selected {
        background-color: #337ab7; 
        color: #fff; 
    }

    .assign-popover{
        cursor: pointer;
    }

    .navbar {
        height: 50px;
    }
    
    #navbarDropdown {
        margin-bottom: -30px;
    }
</style>
    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
  <div class="container homepage-container">
    <div class="row">
        <div class="col-md-12 text-center">
            <h1><strong>Seating Arrangement</strong></h1>
            <h3 class="text-center">{{ $startDate }} - {{ $endDate }}</h3>
            <p class="text-center">There are {{ $emptySeatCount }} empty seat(s) available</p>
        </div>
        <div class="seating-arrange-wrapper" id="seating-plan">
            <div id="carouselExample" class="carousel slide">
                <div class="carousel-inner">
                  <div class="carousel-item active">
                    @php
                        $seat = json_decode($seatingData, true);
                    @endphp
                    <p class="text-center">Ground Floor Map (Level 1)</p>
                        <table id="map_level1">
                            <tbody>
                                <!-- Entrance / Exit -->
                                <tr>
                                    <td colspan="2" style="background-color: #D3D3D3;"> </td>
                                    <td rowspan="6" style="background-color: #D3D3D3;"> </td>
                                    <td colspan="2" style="text-align: right; background-color: #D3D3D3;"><strong>Exit>></strong></td>
                                </tr>
                                <!-- Row 1 -->
                                <tr>
                                    <td id="CSM11" class="assign-popover" style="background-color: {{ $seat['CSM11']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if( $seat['CSM11']['seat_status'] != 'Not Available')
                                            CSM11 ({{ $seat['CSM11']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM12" class="assign-popover" style="background-color: {{ $seat['CSM12']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM12']['seat_status'] != 'Not Available')
                                            CSM12 ({{$seat['CSM12']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM01" class="assign-popover" style="background-color: {{ $seat['CSM01']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM01']['seat_status'] != 'Not Available')
                                            CSM01 ({{$seat['CSM01']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM02" class="assign-popover" style="background-color: {{ $seat['CSM02']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM02']['seat_status'] != 'Not Available')
                                            CSM02 ({{$seat['CSM02']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                </tr>
                                <!-- Row 2 -->
                                <tr>
                                    <td id="CSM13" class="assign-popover" style="background-color: {{ $seat['CSM13']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM13']['seat_status'] != 'Not Available')
                                            CSM13 ({{$seat['CSM13']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM14" class="assign-popover" style="background-color: {{ $seat['CSM14']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM14']['seat_status'] != 'Not Available')
                                            CSM14 ({{$seat['CSM14']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM03" class="assign-popover" style="background-color: {{ $seat['CSM03']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM03']['seat_status'] != 'Not Available')
                                            CSM03 ({{$seat['CSM03']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif 
                                    </td>
                                    <td id="CSM04" class="assign-popover" style="background-color: {{ $seat['CSM04']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM04']['seat_status'] != 'Not Available')
                                            CSM04 ({{$seat['CSM04']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                </tr>
                                <!-- Row 3 -->
                                <tr>
                                    <td id="CSM15" class="assign-popover" style="background-color: {{ $seat['CSM15']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM15']['seat_status'] != 'Not Available')
                                            CSM15 ({{$seat['CSM15']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM16" class="assign-popover" style="background-color: {{ $seat['CSM16']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM16']['seat_status'] != 'Not Available')
                                            CSM16 ({{$seat['CSM16']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM05" class="assign-popover" style="background-color: {{ $seat['CSM05']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM05']['seat_status'] != 'Not Available')
                                            CSM05 ({{$seat['CSM05']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM06" class="assign-popover" style="background-color: {{ $seat['CSM06']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM06']['seat_status'] != 'Not Available')
                                            CSM06 ({{$seat['CSM06']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                </tr>
                                <!-- Row 4 -->
                                <tr>
                                    <td id="CSM17" class="assign-popover" style="background-color: {{ $seat['CSM17']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM17']['seat_status'] != 'Not Available')
                                            CSM17 ({{$seat['CSM17']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM18" class="assign-popover" style="background-color: {{ $seat['CSM18']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM18']['seat_status'] != 'Not Available')
                                            CSM18 ({{$seat['CSM18']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM07" class="assign-popover" style="background-color: {{ $seat['CSM07']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM07']['seat_status'] != 'Not Available')
                                            CSM07 ({{$seat['CSM07']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM08" class="assign-popover" style="background-color: {{ $seat['CSM08']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM08']['seat_status'] != 'Not Available')
                                            CSM08 ({{$seat['CSM08']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                </tr>
                                <!-- Row 5 -->
                                <tr>
                                    <td id="CSM19" class="assign-popover" style="background-color: {{ $seat['CSM19']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM19']['seat_status'] != 'Not Available')
                                            CSM19 ({{$seat['CSM19']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM20" class="assign-popover" style="background-color: {{ $seat['CSM20']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM20']['seat_status'] != 'Not Available')
                                            CSM20 ({{$seat['CSM20']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM09" class="assign-popover" style="background-color: {{ $seat['CSM09']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM09']['seat_status'] != 'Not Available')
                                            CSM09 ({{$seat['CSM09']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM10" class="assign-popover" style="background-color: {{ $seat['CSM10']['seat_status'] !== 'Not Available' ? '#90EE90' : 'none' }};">
                                        @if($seat['CSM10']['seat_status'] != 'Not Available')
                                            CSM10 ({{$seat['CSM10']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                  </div>
                  <div class="carousel-item">
                    <p class="text-center">Second Floor Map (Level 3)</p>
                    <table style="width: 80%;">
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
                                        @if($seat['T01']['seat_status'] != 'Not Available')
                                            T01 ({{$seat['T01']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 15.9768%; background-color: rgb(144, 238, 144);">
                                    <div style="text-align: center;" class="assign-popover" id="T02">
                                        @if($seat['T02']['seat_status'] != 'Not Available')
                                            T02 ({{$seat['T02']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 15.9768%; background-color: rgb(144, 238, 144);">
                                    <div style="text-align: center;" class="assign-popover" id="Round-Table">
                                        @if($seat['Round-Table']['seat_status'] != 'Not Available')
                                        Round Table ({{$seat['Round-Table']['trainee_id']}})
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
                                        @if($seat['T03']['seat_status'] != 'Not Available')
                                            T03 ({{$seat['T03']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 9.9768%; background-color: rgb(144, 238, 144);">
                                    <div style="text-align: center;" class="assign-popover" id="T04">
                                        @if($seat['T04']['seat_status'] != 'Not Available')
                                            T04 ({{$seat['T04']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 10.4408%; background-color: rgb(144, 238, 144);">
                                    <div style="text-align: center;" class="assign-popover" id="T05">
                                        @if($seat['T05']['seat_status'] != 'Not Available')
                                            T05 ({{$seat['T05']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 9.9768%; background-color: rgb(144, 238, 144);">
                                    <div style="text-align: center;" class="assign-popover" id="T06">
                                        @if($seat['T06']['seat_status'] != 'Not Available')
                                            T06 ({{$seat['T06']['trainee_id']}})
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
                                        @if($seat['T07']['seat_status'] != 'Not Available')
                                            T07 ({{$seat['T07']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 9.9768%; background-color: rgb(144, 238, 144);">
                                    <div style="text-align: center;" class="assign-popover" id="T08">
                                        @if($seat['T08']['seat_status'] != 'Not Available')
                                            T08 ({{$seat['T08']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 10.4408%; background-color: rgb(144, 238, 144);">
                                    <div style="text-align: center;" class="assign-popover" id="T09">
                                        @if($seat['T09']['seat_status'] != 'Not Available')
                                            T09 ({{$seat['T09']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 9.9768%; background-color: rgb(144, 238, 144);">
                                    <div style="text-align: center;" class="assign-popover" id="T10">
                                        @if($seat['T10']['seat_status'] != 'Not Available')
                                            T10 ({{$seat['T10']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 15.9768%; background-color: rgb(144, 238, 144);">
                                    <div style="text-align: center;" class="assign-popover" id="T15">
                                        @if($seat['T15']['seat_status'] != 'Not Available')
                                            T15 ({{$seat['T15']['trainee_id']}})
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
                                        @if($seat['T11']['seat_status'] != 'Not Available')
                                            T11 ({{$seat['T11']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 9.9768%; background-color: rgb(144, 238, 144);">
                                    <div style="text-align: center;"class="assign-popover" id="T12">
                                        @if($seat['T12']['seat_status'] != 'Not Available')
                                            T12 ({{$seat['T12']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 10.0185%; background-color: rgb(144, 238, 144);">
                                    <div style="text-align: center;" class="assign-popover" id="T16">
                                        @if($seat['T16']['seat_status'] != 'Not Available')
                                            T16 ({{$seat['T16']['trainee_id']}})
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
                                        @if($seat['T13']['seat_status'] != 'Not Available')
                                            T13 ({{$seat['T13']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 9.9768%; background-color: rgb(144, 238, 144);">
                                    <div style="text-align: center;" class="assign-popover" id="T14">
                                        @if($seat['T14']['seat_status'] != 'Not Available')
                                            T14 ({{$seat['T14']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 10.0185%; background-color: rgb(144, 238, 144);">
                                    <div style="text-align: center;" class="assign-popover" id="T17">
                                        @if($seat['T17']['seat_status'] != 'Not Available')
                                            T17 ({{$seat['T17']['trainee_id']}})
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 10.0185%; background-color: rgb(148, 148, 148);"><br></td>
                            </tr>
                        </tbody>
                    </table>
                  </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                </button>
              </div>
            <div class="assign-bar-wrapper">
                <div class="card" id="popoverContent" style="width: 18rem;">
                    <div class="card-body">
                      <h5 class="card-title"></h5>
                      <p class="card-text"></p>
                      <div class="col-md-8">
                    </div>
                        <div class="seat-assign-trainee-list">
                            <ul class="manual-assign-ul" id="selectable-list">
                                @foreach ($trainees as $trainee)
                                    <li class="manual-assign-li">{{ $trainee->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <p class="btn btn-primary-ex1" id="close-popover">Cancel</p>
                        <a href="" class="btn btn-primary-ex1" id="change-ownership-btn">Change Seat Ownership</a>
                        <a href="" class="btn btn-primary-ex1" id="remove-trainee-btn">Remove Assigned Trainee</a>
                        <a href="" class="btn btn-primary-ex1" id="assign-seat-btn">Assign Selected Trainee</a>
                    </div>
                </div>
                <div class="trainee-assign-button-group">
                    <form action="{{ route('seating-arrange') }}">
                        <label for="week">Select a week:</label>
                        <input type="week" id="week" name="week" value="{{ $week }}">
                        <input type="submit" value="Show Seating Plan">
                    </form>
                    <a href="/seating-arrange/random?week={{ $week }}" class="btn btn-primary-ex2">Random Assign</a>
                    <a href="" class="btn btn-primary-ex2">Refresh</a>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="weekInfo" value="{{ $week }}">

</div>
<script>
 document.addEventListener("DOMContentLoaded", function () {
    var popoverButtons = document.querySelectorAll(".assign-popover");
    var popoverContent = document.getElementById("popoverContent");
    var closePopover = document.getElementById("close-popover");
    var removeBtn = document.getElementById("remove-trainee-btn");
    var assignBtn = document.getElementById("assign-seat-btn");
    var changeOwnershipButton = document.getElementById("change-ownership-btn");
    var week = document.getElementById("weekInfo").value;
    var lastClickedButtonId = null;

    // Add click event listeners to all td elements with class "assign-popover"
    popoverButtons.forEach(function (popoverButton) {
        popoverButton.addEventListener("click", function () {
            var seat = popoverButton.getAttribute("id");
            var seat_name = popoverButton.textContent;
            var extractedSeatName = seat_name.split(' (')[0];

            // If a button is already selected, deselect it
            if (lastClickedButtonId) {
                document.getElementById(lastClickedButtonId).classList.remove("selected");
            }

            document.querySelector(".card-title").textContent = "Selected: " + extractedSeatName;

            // Select the clicked button
            popoverButton.classList.add("selected");
            lastClickedButtonId = seat;

            // Create a new XMLHttpRequest for fetching seat data
            var req = new XMLHttpRequest();
            req.open("GET", "/get-seat-data/" + seat + "?week=" + week, true);
            req.send();

            req.onreadystatechange = function () {
                if (req.readyState === 4 && req.status === 200) {
                    // Parse the JSON response
                    var data = JSON.parse(req.responseText);

                    // Update the popover content with the retrieved data
                    document.querySelector(".card-text").textContent = "Trainee Assigned: " + data.trainee_id;
                    popoverContent.style.display = "block";
                } else if (req.readyState === 4 && req.status !== 200) {
                    console.error("Error fetching seat data. Status code: " + req.status);
                }
            };
        });
    });

    // Add a click event listener to the "Remove" button
    removeBtn.addEventListener("click", function () {
        if (lastClickedButtonId) {
            // Implement your remove logic here for the selected seat
            var req = new XMLHttpRequest();
            req.open("GET", "/remove-seat/" + lastClickedButtonId + "?week=" + week, true);
            req.send();
        }
    });

    // Add a click event listener to the "Assign" button
    assignBtn.addEventListener("click", function () {
        var trainee_selected = getSelectedListItemContent();
        if (trainee_selected) {
            var req1 = new XMLHttpRequest();
            req1.open("GET", "/assign-seat-for-trainee/" + trainee_selected + "/" + lastClickedButtonId + "?week=" + week, true);
            req1.send();
        } else {
            // Handle the case where no item is selected
        }
    });

    // Add a click event listener to the "Change Ownership" button
    changeOwnershipButton.addEventListener("click", function () {
        if (lastClickedButtonId) {
            // Send the selectedCellId to your controller using an HTTP request (e.g., AJAX)
            var req2= new XMLHttpRequest();
            req2.open("GET", "/change-ownership/" + lastClickedButtonId + "?week=" + week, true);
            req2.send();
        }
    });

    // Close the popover when the "Close" button is clicked
    closePopover.addEventListener("click", function () {
        popoverContent.style.display = "none";

        // Deselect the last clicked button
        if (lastClickedButtonId) {
            document.getElementById(lastClickedButtonId).classList.remove("selected");
            lastClickedButtonId = null;
        }
    });

    // Add click event listeners to all list items to make the list selectable
    const list = document.getElementById("selectable-list");
    const listItems = list.querySelectorAll("li");

    // Add click event listeners to list items
    listItems.forEach(function (item) {
        item.addEventListener("click", function () {
            // Clear the selected class from all items
            listItems.forEach(function (li) {
                li.classList.remove("selected");
            });

            // Add the selected class to the clicked item
            item.classList.add("selected");
        });
    });

    function getSelectedListItemContent() {
        const selectedTraineeListItem = document.querySelector("#selectable-list .selected");
        if (selectedTraineeListItem) {
            const selectedTrainee = selectedTraineeListItem.textContent;
            return selectedTrainee;
        } else {
            return null;
        }
    }
});


</script>
    


</body>
</html>
@endsection