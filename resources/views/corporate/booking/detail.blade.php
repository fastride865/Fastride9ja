@extends('corporate.layouts.main')
@section('content')
{{--    @php date_default_timezone_set($booking->CountryArea->timezone); @endphp--}}
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ URL::previous() }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="fa fa-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-car" aria-hidden="true"></i>
                        @lang('admin.message259')</h3>
                </header>
                <div class="panel-body">
                    <div id="user-profile">
                        <div class="row">
                            <div class="col-md-4 col-xs-12">
                                <div class="card my-2 shadow">
                                    <div class="">
                                        <img class="card-img-top" width="100%" style="height:22vh"
                                             alt="google image" src="{!! $booking->map_image !!}">
                                    </div>
                                    <div class="card-body ">
                                        <div class="col-md-4 col-sm-4"  style="float:left;">
                                            <img  height="80" width="80"  class="rounded-circle" src="@if ($booking->Driver) {{ get_image($booking->Driver->profile_image,'driver') }} @else {{ get_image(null,'driver') }} @endif"
                                                  alt="img">

                                        </div>
                                        <div class="card-text col-md-8 col-sm-8 py-2" style="float:left;">
                                            @if(Auth::user()->demo == 1)
                                                <h4 class="user-name">{{ "********".substr($booking->User->UserName, -2) }}</h4>
                                                <p class="user-job">{{ "********".substr($booking->User->UserPhone, -2) }}</p>
                                                <p class="user-location">{{ "********".substr($booking->User->email, -2) }}</p>
                                            @else
                                                <h5 class="user-name">{{ $booking->User->UserName }}</h5>
                                                <h6 class="user-job">{{ $booking->User->UserPhone }}</h6>
                                                <h6 class="user-location">{{ $booking->User->email }}</h6>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card my-2 shadow bg-white h-280">
                                    <div class="justify-content-center p-3">
                                        <div class="col-md-12 col-xs-12 mt-10"
                                             style="text-align:center;justify-content:center">

                                            <img height="80" width="80" class="rounded-circle" src="@if ($booking->Driver) {{ get_image($booking->Driver->profile_image,'driver') }} @else {{ get_image(null,'driver') }} @endif">

                                        </div>
                                        <div class="overlay-box">
                                            <div class="user-content " style="text-align:center">
                                                <!-- <a href="javascript:void(0)"> -->
                                            <!-- <img src="@if ($booking->Driver) {{ asset($booking->Driver->profile_image) }} @else {{ asset("user.png") }} @endif" -->
                                                <!-- class="thumb-lg img-circle" alt="img"> -->
                                                <!-- </a> -->
                                                @if(Auth::user()->demo == 1)
                                                    <h5 class="user-name mt-5 mb-5">@if ($booking->Driver) {{ "********".substr($booking->Driver->fullName, -2) }} @else @lang('admin.message253') @endif</h5>
                                                    <p class="user-job mb-1 ">@if ($booking->Driver) {{ "********".substr($booking->Driver->email, 2) }} @else @lang('admin.message253') @endif</p>
                                                    <p class="user-location mb-2">@if ($booking->Driver) {{ "********".substr($booking->Driver->phoneNumber, -2) }} @else @lang('admin.message253') @endif</p>
                                                @else
                                                    <h5 class="user-name mt-5 mb-1">@if ($booking->Driver) {{ $booking->Driver->fullName }} @else @lang('admin.message253') @endif</h5>
                                                    <p class="user-job mb-1">@if ($booking->Driver) {{ $booking->Driver->email }} @else @lang('admin.message253') @endif</p>
                                                    <p class="user-location mb-2">@if ($booking->Driver) {{ $booking->Driver->phoneNumber }} @else @lang('admin.message253') @endif</p>
                                                @endif
                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="clear"></div>
                                        <div class="user-btm-box">
                                            <div class="col-md-4 col-sm-4" style="float:left;">
                                                <a class="avatar img-bordered avatar-100" href="javascript:void(0)">
                                                    <img src="@if ($booking->VehicleType) {{ get_image($booking->VehicleType->vehicleTypeImage,'vehicle') }}@endif"
                                                    /></a>
                                            </div>
                                            <div class="col-md-8 col-sm-8" style="float:left;">
                                                @php
                                                    $service_text = ($booking->ServiceType) ? $booking->ServiceType->serviceName : $booking->deliveryType->name ;
                                                @endphp
                                                <h5 class="user-name">{{ $service_text }}</h5>
                                                <h6 class="user-job">{{ $booking->VehicleType->VehicleTypeName }}</h6>
                                                <h6 class="user-location">@if ($booking->DriverVehicle) {{ $booking->DriverVehicle->VehicleMake->VehicleMakeName }}
                                                    :{{  $booking->DriverVehicle->VehicleModel->VehicleModelName }}
                                                    -{{ $booking->DriverVehicle->vehicle_number }} @else @lang('admin.message253') @endif</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-xs-12 mt-20">
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12 mb-5 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="font-weight-500 text-success text-uppercase mb-1">
                                                    <i class="icon fa-map-marker fa-2x text-gray-300"></i>
                                                    @lang("$string_file.pickup_location")</div>
                                                <div class="mb-0">{{ $booking->pickup_location }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 mb-5 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="font-weight-500 text-danger text-uppercase mb-1">
                                                    <i class="icon fa-tint fa-2x text-gray-300"></i>
                                                    @lang("$string_file.drop_off_location")</div>
                                                <div class="mb-0">{{ $booking->drop_location }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12 mb-5 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="font-weight-500 text-info text-uppercase mb-1">
                                                    <i class="icon fa-money"></i>
                                                    @lang("$string_file.payment")</div>
                                                <div class="mb-0">
                                                    {{ $booking->PaymentMethod->payment_method  }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 mb-5 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="font-weight-500 text-warning text-uppercase mb-1">
                                                    <i class="icon fa-comments fa-2x text-gray-300"></i>
                                                    @lang('admin.currentstatus')</div>
                                                <div class="mb-0">
                                                    @switch($booking->booking_status)
                                                        @case(1001)
                                                        @lang('admin.new_booking')
                                                        @break
                                                        @case(1002)
                                                        @lang('admin.driver_accepted')
                                                        @break
                                                        @case(1003)
                                                        @lang('admin.driver_arrived')
                                                        @break
                                                        @case(1004)
                                                        @lang('admin.begin')
                                                        @break
                                                        @case(1005)
                                                        @lang("$string_file.completed")
                                                        @break
                                                        @case(1006)
                                                        @lang('admin.message48')
                                                        @break
                                                        @case(1007)
                                                        @lang('admin.message49')
                                                        @break
                                                        @case(1008)
                                                        @lang('admin.message50')
                                                        @break
                                                    @endswitch</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="font-weight-500 text-success text-uppercase mb-1">
                                                    <i class="icon fa-calendar fa-2x text-gray-300"></i>
                                                    @lang("$string_file.created_at")</div>
                                                <div class="mb-0">
                                                    {{ $booking->created_at }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($booking->booking_status == 1005)
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-500 text-success text-uppercase mb-1">
                                                        <i class="icon fa-road fa-2x text-gray-300"></i>
                                                        @lang("$string_file.distance")</div>
                                                    <div class="mb-0">
                                                        {{ $booking->travel_distance ." ".$booking->travel_time }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row">
                                    @if($booking->insurnce == 1)
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class=" font-weight-400 text-success text-uppercase mb-1">
                                                        <i class="icon fa-calendar-alt fa-2x text-gray-300"></i>
                                                        @lang("$string_file.insurance")</div>
                                                    <div class="mb-0">
                                                        @lang("$string_file.yes")</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($booking->booking_type == 2)
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-400 text-success text-uppercase mb-1">
                                                        <i class="icon fa-calendar fa-2x text-gray-300"></i>@lang("$string_file.ride_time")</div>
                                                    <div class="mb-0">{{ $booking->later_booking_date." ". $booking->later_booking_time }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row mt-50">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="dataTable">
                                                <thead>
                                                <tr>
                                                    <th>@lang("$string_file.action")</th>
                                                    <th>@lang("$string_file.time")</th>
                                                    <th>@lang("$string_file.coordinates")</th>
                                                    <th>@lang("$string_file.map")</th>
                                                    <th>@lang("$string_file.accuracy")</th>
                                                    <th>@lang("$string_file.time_difference")</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if($booking->BookingDetail)
                                                    <tr>
                                                        <td>@lang("$string_file.accepted")</td>
                                                        <td>{{ date("H:i:s",$booking->BookingDetail->accept_timestamp) }}</td>
                                                        <td>{{ $booking->BookingDetail->accept_latitude }}
                                                            ,{{  $booking->BookingDetail->accept_longitude }}</td>
                                                        <td><a target="_blank"
                                                               href="https://www.google.com/maps/place/{{ $booking->BookingDetail->accept_latitude }},{{  $booking->BookingDetail->accept_longitude }}">
                                                                <button type="button"
                                                                        class="btn btn-primary btn-min-width mr-1 mb-1">
                                                                    Map
                                                                </button>
                                                            </a></td>
                                                        <td>{{ $booking->BookingDetail->accuracy_at_accept }}</td>
                                                        <td>{{ round(abs($booking->booking_timestamp - $booking->BookingDetail->accept_timestamp) / 60, 2) }}</td>
                                                    </tr>
                                                    @if($booking->BookingDetail->arrive_timestamp)
                                                        <tr>
                                                            <td>@lang("$string_file.arrived")</td>
                                                            <td>{{ date("H:i:s",$booking->BookingDetail->arrive_timestamp) }}</td>
                                                            <td>{{ $booking->BookingDetail->arrive_latitude }}
                                                                ,{{  $booking->BookingDetail->arrive_longitude }}</td>
                                                            <td><a target="_blank"
                                                                   href="https://www.google.com/maps/place/{{ $booking->BookingDetail->arrive_latitude }},{{  $booking->BookingDetail->arrive_longitude }}">
                                                                    <button type="button"
                                                                            class="btn btn-primary btn-min-width mr-1 mb-1">
                                                                        Map
                                                                    </button>
                                                                </a></td>
                                                            <td>{{ $booking->BookingDetail->accuracy_at_arrive }}</td>
                                                            <td>{{ round(abs($booking->BookingDetail->arrive_timestamp - $booking->BookingDetail->accept_timestamp) / 60, 2) }}</td>
                                                        </tr>
                                                    @endif
                                                    @if($booking->BookingDetail->start_timestamp)
                                                        <tr>
                                                            <td>@lang("$string_file.started")</td>
                                                            <td>{{ date("H:i:s",$booking->BookingDetail->start_timestamp) }}</td>
                                                            <td>{{ $booking->BookingDetail->start_latitude }}
                                                                ,{{  $booking->BookingDetail->start_longitude }}</td>
                                                            <td><a target="_blank"
                                                                   href="https://www.google.com/maps/place/{{ $booking->BookingDetail->start_latitude }},{{  $booking->BookingDetail->start_longitude }}">
                                                                    <button type="button"
                                                                            class="btn btn-primary btn-min-width mr-1 mb-1">
                                                                        Map
                                                                    </button>
                                                                </a></td>
                                                            <td>{{ $booking->BookingDetail->accuracy_at_start }}</td>
                                                            <td>{{ round(abs($booking->BookingDetail->arrive_timestamp - $booking->BookingDetail->start_timestamp) / 60, 2) }}</td>
                                                        </tr>
                                                    @endif
                                                    @if($booking->BookingDetail->end_timestamp)
                                                        <tr>
                                                            <td>@lang("$string_file.completed")</td>
                                                            <td>{{ date("H:i:s",$booking->BookingDetail->end_timestamp) }}</td>
                                                            <td>{{ $booking->BookingDetail->end_latitude }}
                                                                ,{{  $booking->BookingDetail->end_longitude }}</td>
                                                            <td><a target="_blank"
                                                                   href="https://www.google.com/maps/place/{{ $booking->BookingDetail->end_latitude }},{{  $booking->BookingDetail->end_longitude }}">
                                                                    <button type="button"
                                                                            class="btn btn-primary btn-min-width mr-1 mb-1">
                                                                        Map
                                                                    </button>
                                                                </a></td>
                                                            <td>{{ $booking->BookingDetail->accuracy_at_end }}</td>
                                                            <td>{{ round(abs($booking->BookingDetail->end_timestamp - $booking->BookingDetail->start_timestamp) / 60, 2) }}</td>
                                                        </tr>
                                                    @endif
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
