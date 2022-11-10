@extends('merchant.layouts.main')
@section('content')
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
                    <h3 class="panel-title"><i class="fa-info-circle" aria-hidden="true"></i>
                        @lang("$string_file.ride_details")
                        #{{$booking->merchant_booking_id}}
                    </h3>
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
                                        <div class="col-md-4 col-sm-4" style="float:left;">
                                            <img height="80" width="80" class="rounded-circle"
                                                 src="@if ($booking->User->UserProfileImage) {{ get_image($booking->User->UserProfileImage,'user') }} @else {{ get_image(null,'user') }} @endif"
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

                                            <img height="80" width="80" class="rounded-circle"
                                                 src="@if ($booking->Driver) {{ get_image($booking->Driver->profile_image,'driver') }} @else {{ get_image(null,'driver') }} @endif">

                                        </div>
                                        <div class="overlay-box">
                                            <div class="user-content " style="text-align:center">
                                                <!-- <a href="javascript:void(0)"> -->
                                            <!-- <img src="@if ($booking->Driver) {{ asset($booking->Driver->profile_image) }} @else {{ asset("user.png") }} @endif" -->
                                                <!-- class="thumb-lg img-circle" alt="img"> -->
                                                <!-- </a> -->
                                                @if(!empty($booking->Driver->id))
                                                    @if(Auth::user()->demo == 1)
                                                        <h5 class="user-name mt-5 mb-5">@if ($booking->Driver) {{ "********".substr($booking->Driver->fullName, -2) }} @else  @endif</h5>
                                                        <p class="user-job mb-1 ">@if ($booking->Driver) {{ "********".substr($booking->Driver->email, 2) }} @else  @endif</p>
                                                        <p class="user-location mb-2">@if ($booking->Driver) {{ "********".substr($booking->Driver->phoneNumber, -2) }} @else  @endif</p>
                                                    @else
                                                        <h5 class="user-name mt-5 mb-1">@if ($booking->Driver) {{ $booking->Driver->fullName }} @else  @endif</h5>
                                                        <p class="user-job mb-1">@if ($booking->Driver) {{ $booking->Driver->email }} @else  @endif</p>
                                                        <p class="user-location mb-2">@if ($booking->Driver) {{ $booking->Driver->phoneNumber }} @else  @endif</p>
                                                    @endif
                                                @else
                                                    @lang("$string_file.not_accepted")
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
                                                    $package_name = ($booking->service_type_id == 2) && !empty($booking->service_package_id) ? ' ('.$booking->ServicePackage->PackageName.')' : '';
                                                    $service_text = ($booking->ServiceType) ? $booking->ServiceType->serviceName.$package_name : ($booking->deliveryType ? $booking->deliveryType->name : '---' );
                                                @endphp
                                                <h5 class="user-name">{{ $service_text }}</h5>
                                                <h6 class="user-job">{{ $booking->VehicleType->VehicleTypeName }}</h6>
                                                <h6 class="user-location">@if ($booking->DriverVehicle) {{ $booking->DriverVehicle->VehicleMake->VehicleMakeName }}
                                                    :{{  $booking->DriverVehicle->VehicleModel->VehicleModelName }}
                                                    -{{ $booking->DriverVehicle->vehicle_number }} @else  @endif</h6>
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
                                                    @lang("$string_file.drop_location")</div>
                                                <div class="mb-0">{{ $booking->drop_location }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(isset($booking->BookingDetail) && !empty($booking->BookingDetail->start_location) && !empty($booking->BookingDetail->end_location))
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 mb-5 py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-500 text-success text-uppercase mb-1">
                                                        <i class="icon fa-map-marker fa-2x text-gray-300"></i>
                                                        @lang("$string_file.start_location")</div>
                                                    <div class="mb-0">{{ $booking->BookingDetail->start_location }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12 mb-5 py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-500 text-danger text-uppercase mb-1">
                                                        <i class="icon fa-tint fa-2x text-gray-300"></i>
                                                        @lang("$string_file.end_location")</div>
                                                    <div class="mb-0">{{ $booking->BookingDetail->end_location }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
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
                                                    @lang("$string_file.current_status")</div>
                                                <div class="mb-0">
                                                    @if(!empty($arr_booking_status))
                                                        {!! isset($arr_booking_status[$booking->booking_status]) ? $arr_booking_status[$booking->booking_status] : ""  !!}
                                                    @endif
                                                </div>
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
                                                    @lang("$string_file.date")</div>
                                                <div class="mb-0">
                                                    {!! convertTimeToUSERzone($booking->created_at, $booking->CountryArea->timezone,null,$booking->Merchant) !!}
                                                    {{--                                                    {{ $booking->created_at }}--}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($booking->booking_status == 1005)
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-500 text-success text-uppercase mb-1">
                                                        <i class="icon fa-road fa-2x text-gray-300"></i>
                                                        @lang("$string_file.final_distance_time")</div>
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
                                    <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="font-weight-400 text-success text-uppercase mb-1">
                                                    <i class="icon fa-calendar fa-2x text-gray-300"></i> @lang("$string_file.ride_type")
                                                </div>
                                                <div class="mb-0">
                                                    @if($booking->booking_type == 1)
                                                        @lang("$string_file.ride_now")
                                                    @else
                                                        @lang("$string_file.ride_later")<br>(
                                                        {!! date(getDateTimeFormat($booking->Merchant->datetime_format,2),strtotime($booking->later_booking_date)) !!}
                                                        {{--                                                {!! convertTimeToUSERzone($booking->later_booking_date, $booking->CountryArea->timezone,null,$booking->Merchant, 2) !!}--}}
                                                        <br>
                                                        {{--                                                {{ $booking->later_booking_date }}--}}
                                                        {{$booking->later_booking_time }} )
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(isset($booking->Merchant->Configuration->no_of_children) && $booking->Merchant->Configuration->no_of_children == 1)
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-400 text-success text-uppercase mb-1">
                                                        <i class="icon fa-calendar fa-2x text-gray-300"></i> @lang("$string_file.no_of_children")
                                                    </div>
                                                    <div class="mb-0">@if($booking->no_of_children > 0) {{ $booking->no_of_children }} @else
                                                            0 @endif</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($booking->service_type_id == 2 || $booking->service_type_id == 4)
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2" style="float:left;">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-400 text-success text-uppercase mb-1">
                                                        <i class="icon fa-tachometer fa-2x text-gray-300"></i> @lang("$string_file.start_meter_image")
                                                    </div>
                                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                        @if(!empty($booking->BookingDetail->start_meter_image))
                                                            <a href="{{get_image($booking->BookingDetail->start_meter_image,'send_meter_image')}}"
                                                               target="_blank"><img width="100" height="100"
                                                                                    style="border-radius: 50%"
                                                                                    src="{{get_image($booking->BookingDetail->start_meter_image,'send_meter_image')}}"></a>
                                                            <h6>@lang("$string_file.start_meter_reading")
                                                                : {{$booking->BookingDetail->start_meter_value}}</h6>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2" style="float:left;">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-400 text-danger text-uppercase mb-1">
                                                        <i class="icon fa-tachometer fa-2x text-gray-300"></i> @lang("$string_file.end_meter_image")
                                                    </div>
                                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                        @if(!empty($booking->BookingDetail->end_meter_image))
                                                            <a href="{{get_image($booking->BookingDetail->end_meter_image,'send_meter_image')}}"
                                                               target="_blank"><img width="100" height="100"
                                                                                    style="border-radius: 50%"
                                                                                    src="{{get_image($booking->BookingDetail->end_meter_image,'send_meter_image')}}"></a>
                                                            <h6>@lang("$string_file.end_meter_reading")
                                                                : {{$booking->BookingDetail->end_meter_value}}</h6>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($booking->service_type_id == 4)
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2" style="float:left;">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-400 text-danger text-uppercase mb-1">
                                                        <i class="icon fa-road fa-2x text-gray-300"></i> @lang("$string_file.ride_details")
                                                    </div>
                                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                        @if(isset($booking->return_date) && isset($booking->return_time))
                                                            <h6>@lang("$string_file.round_trip_only")</h6>
                                                        @else
                                                            <h6>@lang("$string_file.one_way")</h6>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($booking->family_member_id) && $booking->family_member_id != '')
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-500 text-success text-uppercase mb-1">
                                                        <i class="icon fa-child fa-2x text-gray-300"></i>
                                                        @lang('admin.family_member')</div>
                                                    @if(Auth::user()->demo == 1)
                                                        <div class="mb-0 font-weight-400">@lang("$string_file.name")
                                                            : {{ "********".substr($booking->FamilyMember->name, -2)  }}</div>
                                                        <div class="mb-0 font-weight-400">@lang("$string_file.age")
                                                            : {{ $booking->FamilyMember->age  }}</div>
                                                        <div class="mb-0">@lang("$string_file.gender")
                                                            : {{ ($booking->FamilyMember->gender == 1) ? 'Male' : 'Female'  }}</div>
                                                    @else
                                                        <div class="mb-0 font-weight-400">@lang("$string_file.name")
                                                            : {{ $booking->FamilyMember->name  }}</div>
                                                        <div class="mb-0 font-weight-400">@lang("$string_file.age")
                                                            : {{ $booking->FamilyMember->age  }}</div>
                                                        <div class="mb-0 font-weight-400">@lang("$string_file.gender")
                                                            : {{ ($booking->FamilyMember->gender == 1) ? 'Male' : 'Female'  }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2" style="float:left;">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="font-weight-400 text-danger text-uppercase mb-1">
                                                            <i class="icon fa-road fa-2x text-gray-300"></i> @lang("$string_file.note"):
                                                        </div>
                                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                            @if($final_bill_calculation == 1)
                                                                <h6>@lang("$string_file.final_equal_actual")</h6>
                                                            @else
                                                                <h6>@lang("$string_file.final_equal_estimate")</h6>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @if($final_bill_calculation == 2)
                                            <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2" style="float:left;">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="font-weight-400 text-success text-uppercase mb-1">
                                                            <i class="icon fa-list text-gray-300"></i> @lang("$string_file.estimate_distance_time"):
                                                        </div>
                                                        <div class="h6 mb-0 text-gray-800">
                                                           {{$booking->estimate_time}} & {{$booking->estimate_distance}}
                                                            <br>
                                                            @lang("$string_file.estimate_bill") : {{$booking->estimate_bill}}
                                                        </div>
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
                                                        <td>{{ convertTimeToUSERzone(date("Y-m-d H:i:s",$booking->BookingDetail->accept_timestamp), $booking->CountryArea->timezone,null,$booking->Merchant,3) }}</td>
                                                        {{--                                                        <td>{{ date("H:i:s",$booking->BookingDetail->accept_timestamp) }}</td>--}}
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
                                                            <td>{{ convertTimeToUSERzone(date("Y-m-d H:i:s",$booking->BookingDetail->arrive_timestamp), $booking->CountryArea->timezone,null,$booking->Merchant,3) }}</td>
                                                            {{--                                                            <td>{{ date("H:i:s",$booking->BookingDetail->arrive_timestamp) }}</td>--}}
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
                                                            {{--                                                            <td>{{ date("H:i:s",$booking->BookingDetail->start_timestamp) }}</td>--}}
                                                            <td>{{ convertTimeToUSERzone(date("Y-m-d H:i:s",$booking->BookingDetail->start_timestamp), $booking->CountryArea->timezone,null,$booking->Merchant,3) }}</td>
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
                                                            {{--                                                            <td>{{ date("H:i:s",$booking->BookingDetail->end_timestamp) }}</td>--}}
                                                            <td>{{ convertTimeToUSERzone(date("Y-m-d H:i:s",$booking->BookingDetail->end_timestamp), $booking->CountryArea->timezone,null,$booking->Merchant,3) }}</td>
                                                            <td>{{ $booking->BookingDetail->end_latitude }}
                                                                ,{{  $booking->BookingDetail->end_longitude }}</td>
                                                            <td><a target="_blank"
                                                                   href="https://www.google.com/maps/place/{{ $booking->BookingDetail->end_latitude }},{{  $booking->BookingDetail->end_longitude }}">
                                                                    <button type="button"
                                                                            class="btn btn-primary btn-min-width mr-1 mb-1">
                                                                        @lang("$string_file.map")
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
                        @if(!empty($booking->BookingDeliveryDetail) && $booking->BookingDeliveryDetail->count() > 0)
                            <br>
                            <h4 class="form-section" style="color: black"><i
                                        class="fa fa-microchip"></i> @lang("$string_file.delivery_drop_details")
                                <hr>
                            </h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="dataTable">
                                            <thead>
                                            <tr>
                                                <th width="8%">@lang("$string_file.stop_no")</th>
                                                <th>@lang("$string_file.map")</th>
                                                <th width="15%">@lang("$string_file.location")</th>
                                                <th>@lang("$string_file.receiver_details")</th>
                                                <th>@lang("$string_file.products_images")</th>
                                                <th>@lang("$string_file.additional_notes")</th>
                                                @if($booking->Merchant->BookingConfiguration->delivery_drop_otp == 1)
                                                    <th>@lang("$string_file.otp")</th>
                                                @endif
                                                <th>@lang("$string_file.drop_status")</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($booking->BookingDeliveryDetail as $booking_delivery_detail)
                                                <tr>
                                                    <td>{{$booking_delivery_detail->stop_no}}</td>
                                                    <td><a target="_blank"
                                                           href="https://www.google.com/maps/place/{{ $booking_delivery_detail->drop_latitude }},{{  $booking_delivery_detail->drop_longitude }}">
                                                            <button type="button"
                                                                    class="btn btn-primary btn-min-width mr-1 mb-1">
                                                                Map
                                                            </button>
                                                        </a></td>
                                                    <td>{{ $booking_delivery_detail->drop_location }}</td>
                                                    <td>{{ "Name : ". ($booking_delivery_detail->receiver_name) ? $booking_delivery_detail->receiver_name : "---" }}
                                                        <br>{{ "Phone : ".($booking_delivery_detail->receiver_phone) ? $booking_delivery_detail->receiver_phone : "---" }}
                                                        <br>
                                                        @if($booking_delivery_detail->receiver_image != '')
                                                            <img height="80" width="80" class="rounded-circle"
                                                                 src="@if ($booking_delivery_detail->receiver_image) {{ get_image($booking_delivery_detail->receiver_image,'booking_images') }} @endif"
                                                                 alt="img">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($booking_delivery_detail->product_image_one != '')
                                                            <img height="80" width="80" class="rounded-circle"
                                                                 src="@if ($booking_delivery_detail->product_image_one) {{ get_image($booking_delivery_detail->product_image_one,'product_image') }} @endif"
                                                                 alt="img">
                                                        @endif
                                                        @if($booking_delivery_detail->product_image_two != '')
                                                            <img height="80" width="80" class="rounded-circle"
                                                                 src="@if ($booking_delivery_detail->product_image_two) {{ get_image($booking_delivery_detail->product_image_two,'product_image') }} @endif"
                                                                 alt="img">
                                                        @endif
                                                    </td>
                                                    <td>{{ !empty($booking_delivery_detail->additional_notes) ? $booking_delivery_detail->additional_notes : '--' }}</td>
                                                    @if($booking->Merchant->BookingConfiguration->delivery_drop_otp == 1)
                                                        <td>{{ $booking_delivery_detail->opt_for_verify }}</td>
                                                    @endif
                                                    <td>{{ ($booking_delivery_detail->drop_status == 1) ? "Delivered" : "Not Delivered" }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection