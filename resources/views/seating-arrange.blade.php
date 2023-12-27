@extends('layouts.admin')
@section('pageTitle', 'Seating Arrangement')

@section('breadcrumbs', Breadcrumbs::render('seating-arrangement'))

@section('content') 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        display: flex;
        align-items: center;
        justify-content: center;
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

    label {
        font-size: 18px;
        margin-bottom: 10px;
    }

    input[type="week"] {
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 15px;
    }

    input[type="submit"] {
        background: #275968;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        color: #FFFFFF;
        width: 200px;
        height: 45px;
        border-radius: 14px;
        border: none;
        text-align: center;
        margin-bottom: 40px;
        margin-top: -10px;
        transition: background-color 0.3s, color 0.3s;
    }

    input[type="submit"]:hover {
        background-color: #45a049;
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
                  <div class="carousel-item">
                    @php
                        $seat = json_decode($seatingData, true);
                    @endphp
                    <p class="text-center">Ground Floor Map (Level 1)</p>
                        <table id="map_level1">
                            <tbody>
                                <tr>
                                    <td colspan="2" style="background-color: #D3D3D3;"> </td>
                                    <td rowspan="6" style="background-color: #D3D3D3;"> </td>
                                    <td colspan="2" style="text-align: right; background-color: #D3D3D3;"><strong>Exit>></strong></td>
                                </tr>
                                <tr>
                                    <td id="CSM11" class="assign-popover" style="background-color: {{ $seat['CSM11']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if( $seat['CSM11']['seat_status'] != 'Not Available')
                                            CSM11 <div style="font-size: 12px;">({{ $seat['CSM11']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM12" class="assign-popover" style="background-color: {{ $seat['CSM12']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM12']['seat_status'] != 'Not Available')
                                            CSM12 <div style="font-size: 12px;">({{$seat['CSM12']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM01" class="assign-popover" style="background-color: {{ $seat['CSM01']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM01']['seat_status'] != 'Not Available')
                                            CSM01 <div style="font-size: 12px;">({{$seat['CSM01']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM02" class="assign-popover" style="background-color: {{ $seat['CSM02']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM02']['seat_status'] != 'Not Available')
                                            CSM02 <div style="font-size: 12px;">({{$seat['CSM02']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td id="CSM13" class="assign-popover" style="background-color: {{ $seat['CSM13']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM13']['seat_status'] != 'Not Available')
                                            CSM13 <div style="font-size: 12px;">({{$seat['CSM13']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM14" class="assign-popover" style="background-color: {{ $seat['CSM14']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM14']['seat_status'] != 'Not Available')
                                            CSM14 <div style="font-size: 12px;">({{$seat['CSM14']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM03" class="assign-popover" style="background-color: {{ $seat['CSM03']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM03']['seat_status'] != 'Not Available')
                                            CSM03 <div style="font-size: 12px;">({{$seat['CSM03']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif 
                                    </td>
                                    <td id="CSM04" class="assign-popover" style="background-color: {{ $seat['CSM04']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM04']['seat_status'] != 'Not Available')
                                            CSM04 <div style="font-size: 12px;">({{$seat['CSM04']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td id="CSM15" class="assign-popover" style="background-color: {{ $seat['CSM15']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM15']['seat_status'] != 'Not Available')
                                            CSM15 <div style="font-size: 12px;">({{$seat['CSM15']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM16" class="assign-popover" style="background-color: {{ $seat['CSM16']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM16']['seat_status'] != 'Not Available')
                                            CSM16 <div style="font-size: 12px;">({{$seat['CSM16']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM05" class="assign-popover" style="background-color: {{ $seat['CSM05']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM05']['seat_status'] != 'Not Available')
                                            CSM05 <div style="font-size: 12px;">({{$seat['CSM05']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM06" class="assign-popover" style="background-color: {{ $seat['CSM06']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM06']['seat_status'] != 'Not Available')
                                            CSM06 <div style="font-size: 12px;">({{$seat['CSM06']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td id="CSM17" class="assign-popover" style="background-color: {{ $seat['CSM17']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM17']['seat_status'] != 'Not Available')
                                            CSM17 <div style="font-size: 12px;">({{$seat['CSM17']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM18" class="assign-popover" style="background-color: {{ $seat['CSM18']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM18']['seat_status'] != 'Not Available')
                                            CSM18 <div style="font-size: 12px;">({{$seat['CSM18']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM07" class="assign-popover" style="background-color: {{ $seat['CSM07']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM07']['seat_status'] != 'Not Available')
                                            CSM07 <div style="font-size: 12px;">({{$seat['CSM07']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM08" class="assign-popover" style="background-color: {{ $seat['CSM08']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM08']['seat_status'] != 'Not Available')
                                            CSM08 <div style="font-size: 12px;">({{$seat['CSM08']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td id="CSM19" class="assign-popover" style="background-color: {{ $seat['CSM19']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM19']['seat_status'] != 'Not Available')
                                            CSM19 <div style="font-size: 12px;">({{$seat['CSM19']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM20" class="assign-popover" style="background-color: #90EE90;">
                                        @if($seat['CSM20']['seat_status'] != 'Not Available')
                                            CSM20 <div style="font-size: 12px;">({{$seat['CSM20']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM09" class="assign-popover" style="background-color: {{ $seat['CSM09']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM09']['seat_status'] != 'Not Available')
                                            CSM09 <div style="font-size: 12px;">({{$seat['CSM09']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                    <td id="CSM10" class="assign-popover" style="background-color: {{ $seat['CSM10']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                        @if($seat['CSM10']['seat_status'] != 'Not Available')
                                            CSM10 <div style="font-size: 12px;">({{$seat['CSM10']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                  </div>
                  <div class="carousel-item active">
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
                                <td style="width: 15.9768%; background-color: {{ $seat['T01']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T01">
                                        @if($seat['T01']['seat_status'] != 'Not Available')
                                            T01 <div style="font-size: 12px;">({{$seat['T01']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 15.9768%; background-color: {{ $seat['T02']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T02">
                                        @if($seat['T02']['seat_status'] != 'Not Available')
                                            T02 <div style="font-size: 12px;">({{$seat['T02']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 15.9768%; background-color: {{ $seat['Round-Table']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="Round-Table">
                                        @if($seat['Round-Table']['seat_status'] != 'Not Available')
                                        Round Table <div style="font-size: 12px;">({{$seat['Round-Table']['trainee_id']}})</div>
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
                                            T03 <div style="font-size: 12px;">({{$seat['T03']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 9.9768%;background-color: {{ $seat['T04']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T04">
                                        @if($seat['T04']['seat_status'] != 'Not Available')
                                            T04 <div style="font-size: 12px;">({{$seat['T04']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 10.4408%; background-color: {{ $seat['T05']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T05">
                                        @if($seat['T05']['seat_status'] != 'Not Available')
                                            T05 <div style="font-size: 12px;">({{$seat['T05']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 9.9768%; background-color: {{ $seat['T06']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T06">
                                        @if($seat['T06']['seat_status'] != 'Not Available')
                                            T06 <div style="font-size: 12px;">({{$seat['T06']['trainee_id']}})</div>
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
                                            T07 <div style="font-size: 12px;">({{$seat['T07']['trainee_id']}})</div>
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
                                            T08 <div style="font-size: 12px;">({{$seat['T08']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 10.4408%;background-color: {{ $seat['T09']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T09">
                                        @if($seat['T09']['seat_status'] != 'Not Available')
                                            T09 <div style="font-size: 12px;">({{$seat['T09']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 9.9768%; background-color: {{ $seat['T10']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T10">
                                        @if($seat['T10']['seat_status'] != 'Not Available')
                                            T10 <div style="font-size: 12px;">({{$seat['T10']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 15.9768%; background-color: {{ $seat['T15']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T15">
                                        @if($seat['T15']['seat_status'] != 'Not Available')
                                            T15 <div style="font-size: 12px;">({{$seat['T15']['trainee_id']}})</div>
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
                                            T11 <div style="font-size: 12px;">({{$seat['T11']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 9.9768%; background-color: {{ $seat['T12']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;"class="assign-popover" id="T12">
                                        @if($seat['T12']['seat_status'] != 'Not Available')
                                            T12 <div style="font-size: 12px;">({{$seat['T12']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 10.0185%; background-color: {{ $seat['T16']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T16">
                                        @if($seat['T16']['seat_status'] != 'Not Available')
                                            T16 <div style="font-size: 12px;">({{$seat['T16']['trainee_id']}})</div>
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
                                            T13 <div style="font-size: 12px;">({{$seat['T13']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 9.9768%;background-color: {{ $seat['T14']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T14">
                                        @if($seat['T14']['seat_status'] != 'Not Available')
                                            T14 <div style="font-size: 12px;">({{$seat['T14']['trainee_id']}})</div>
                                        @else
                                            OTHER
                                        @endif
                                    </div>
                                </td>
                                <td style="width: 10.0185%; background-color: {{ $seat['T17']['trainee_id'] != 'Not Assigned' ? 'rgb(173, 216, 230)' : 'rgb(144, 238, 144)' }};">
                                    <div style="text-align: center;" class="assign-popover" id="T17">
                                        @if($seat['T17']['seat_status'] != 'Not Available')
                                            T17 <div style="font-size: 12px;">({{$seat['T17']['trainee_id']}})</div>
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
                <div class="card" id="popoverContent" style="max-width: 22rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <a href="#" id="close-popover" style="border: none; text-decoration: none;">
                                <img width="24" height="24" src="https://img.icons8.com/metro/26/808080/delete-sign.png" alt="cancel"/>
                            </a>
                        </div>
                
                        <h5 class="card-title mt-3"></h5>
                        <p class="card-text"></p>
                
                        <div class="seat-assign-trainee-list">
                            <h6 class="mt-3 mb-2">Available Trainees:</h6>
                            <ul class="manual-assign-ul" id="selectable-list">
                                @foreach ($trainees as $trainee)
                                    <li class="manual-assign-li">{{ $trainee->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                
                        <div class="mt-4 justify-content-between" style="display: flex; flex-direction: column;">
                            <a href="" class="btn btn-primary" id="change-ownership-btn" style="margin-bottom: 10px;">Change Seat Status</a>
                            <a href="" class="btn btn-danger" id="remove-trainee-btn" style="margin-bottom: 10px;">Remove Assigned Trainee</a>
                            <a href="" class="btn btn-success" id="assign-seat-btn">Assign Selected Trainee</a>
                        </div>
                    </div>
                </div>
                
                
                <div class="trainee-assign-button-group">
                    <form action="{{ route('seating-arrange') }}">
                        <label for="week">Select a week:</label>
                        <input type="week" id="week" name="week" value="{{ $week }}">
                        <input type="submit" value="Show Seating Plan">
                    </form>
                    <a href="#" class="btn btn-primary-ex2" data-toggle="modal" data-target="#confirmRandomAssignModal">Random Assign</a>

                    <div class="modal fade" id="confirmRandomAssignModal" tabindex="-1" role="dialog" aria-labelledby="confirmRandomAssignModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="confirmRandomAssignModalLabel">Confirm Random Assignment</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to perform a random assignment? This action cannot be undone.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <a href="/seating-arrange/random?week={{ $week }}" class="btn btn-primary">Confirm</a>
                                </div>
                            </div>
                        </div>
                    </div>


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

    // Add a click event listener to the "Change Seat Status" button
    changeOwnershipButton.addEventListener("click", function () {
        if (lastClickedButtonId) {
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