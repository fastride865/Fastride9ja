@extends('hotel.layouts.main')
@section('content')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #section-to-print, #section-to-print * {
                visibility: visible;
            }
        }
    </style>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <button class="btn btn-icon btn-warning float-right print_invoice" style="margin:10px;width:115px;" ><i class="icon wb-print" aria-hidden="true"></i>
                            @lang("$string_file.print")
                        </button>
                    </div>
                    <h3 class="panel-title">
                        <i class="wb-flag" aria-hidden="true"></i>
                       @lang("$string_file.invoice")
                    </h3>
                </header>
                @if(Auth::user()->tax)
                    <div class="panel-heading"> @php $a = json_decode(Auth::user()->tax,true);echo $a['name'] @endphp
                        <strong>@php $a = json_decode(Auth::user()->tax,true);echo $a['tax_number'] @endphp </strong>
                    </div>
                @endif
                <div id="section-to-print" class="panel">
                    <div class="panel-body container-fluid printableArea">
                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <img class="mr-5" src="{{ get_image(Auth::user()->BusinessLogo,'business_logo') }}" title="{{ (Auth::user()->BusinessName) }}"
                                     width="40" height="40" alt="..."><br><h4>{{ (Auth::user()->BusinessName) }}</h4>
                                @if(Auth::user()->demo == 1)
                                    <address>
                                        {{ "********".substr($booking->Merchant->BusinessName, -2) }}
                                        <br>{{ "********".substr($booking->Merchant->merchantAddress, -2) }}<br>
                                        {{ $booking->Merchant->merchantFirstName }} {{ $booking->Merchant->merchantLastName }}
                                        <br>
                                        <abbr title="Mail">@lang("$string_file.email")</abbr>{{ "********".substr($booking->Merchant->email, -2) }}
                                        <br>
                                        <abbr title="Phone">@lang("$string_file.phone")</abbr>{{ "********".substr($booking->Merchant->merchantPhone, -2) }}
                                        <br>
                                    </address>
                                @else
                                    <address>
                                        {{ $booking->Merchant->merchantFirstName }} {{ $booking->Merchant->merchantLastName }}<br>
                                        {{ $booking->Merchant->merchantAddress }}
                                        <br>
                                        <abbr title="Mail">@lang("$string_file.email"): </abbr>{{ $booking->Merchant->email}}
                                        <br>
                                        <abbr title="Phone">@lang("$string_file.phone"): </abbr>{{$booking->Merchant->merchantPhone}}
                                        <br>
                                    </address>
                                @endif
                            </div>
                            <div class="col-lg-3 offset-lg-6 text-right">
                                <a class="font-size-18" href="javascript:void(0)">@lang("$string_file.ride_id")#{{ $booking->merchant_booking_id }}</a>
                                <br><b>@lang("$string_file.f_cap_to"):</b>
                                <br>
                                <span class="font-size-16">{{ $booking->User->UserName }}</span>
                                @if(Auth::user()->demo == 1)
                                    <address>
                                        {{ "********".substr($booking->User->UserName, -2) }}
                                        <br>
                                        <abbr title="Mail">@lang("$string_file.email")</abbr>{{ "********".substr($booking->User->email, -2) }}
                                        <br>
                                        <abbr title="Phone">@lang("$string_file.phone"):</abbr>{{ "********".substr($booking->User->UserPhone, -2) }}
                                        <br>
                                    </address>
                                @else
                                    <address>
                                        <abbr title="Mail">@lang("$string_file.email"): </abbr>{{ $booking->User->email }}
                                        <br>
                                        <abbr title="Phone">@lang("$string_file.phone"): </abbr>{{$booking->User->UserPhone}}
                                        <br>
                                    </address>
                                @endif
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <h4 class="font-size-16">@lang("$string_file.ride_details")</h4>
                                <hr>
                                <div class="row mt-40 mb-10">
                                    <div class="col-xl-6 col-md-6 col-sm-6">
                                        <p><img class="location_marker"
                                                src="{{ view_config_image('static-images/pinup.png') }}"
                                                width="30">
                                            {{ $booking->BookingDetail->start_location }}</p>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-sm-6">
                                        <p><img class="location_marker"
                                                src="{{ view_config_image('static-images/pindown.png') }}"
                                                width="30">
                                            {{ $booking->BookingDetail->end_location }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <h4 class="font-size-16">@lang("$string_file.driver_and_symbol_vehicle_details")</h4>
                                <hr>
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 col-sm-6 text-center">
                                        <img class="profile_img" style="border-radius: 100%;"
                                             src="@if ($booking->Driver->profile_image) {{ get_image($booking->Driver->profile_image,'driver') }} @else {{ get_image(null, 'driver') }} @endif"
                                             width="100" height="100">
                                        @if(Auth::user()->demo == 1)
                                            <h5 class="profile_name">{{ "********".substr($booking->Driver->fullName, -2) }}</h5>>
                                            {{ "********".substr($booking->Driver->email, -2) }}
                                            {{ "********".substr($booking->Driver->phoneNumber, -2) }}
                                        @else
                                            <h5 class="profile_name">{{ $booking->Driver->fullName }}</h5>
                                            {{ $booking->Driver->email }}
                                            {{ $booking->Driver->phoneNumber }}
                                        @endif
                                        @if ($booking->Driver->rating == "0.0")
                                            @lang("$string_file.not_rated_yet")
                                        @else
                                            @while($booking->Driver->rating >0)
                                                @if($booking->Driver->rating >0.5)
                                                    <img src="{{ view_config_image("static-images/star.png") }}"
                                                         alt='Whole Star'>
                                                @else
                                                    <img src="{{ view_config_image('static-images/halfstar.png') }}"
                                                         alt='Half Star'>
                                                @endif
                                                @php $booking->Driver->rating--; @endphp
                                            @endwhile
                                        @endif
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-sm-6 text-center">
                                        <img src="{{ get_image($booking->VehicleType->vehicleTypeImage,'vehicle') }}"
                                             width="100" height="100">
                                        <h5  class="vehicle_name">{{ $booking->VehicleType->LanguageVehicleTypeSingle == "" ?
                                         $booking->VehicleType->LanguageVehicleTypeAny->vehicleTypeName : $booking->VehicleType->LanguageVehicleTypeSingle->vehicleTypeName }}</h5>
                                        <td align="center"
                                            class="vehicle_number">{{ $booking->DriverVehicle->vehicle_number }}
                                        </td>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-20 mb-20">
                            <div class="col-lg-6 col-md-6 col-sm-12 mt-25">
                                <img class="map_img" style="border-radius: 20px;"
                                     src="{{ str_replace('https:maps.','https://maps.',$booking->map_image) }}"
                                     width="100%" height="300"/>
{{--                                <div class="mb-3">--}}
{{--                                    <div class="table-responsive">--}}
{{--                                        <table class="table table-default" id="dataTable" >--}}
{{--                                            <tfoot>--}}
{{--                                            <tr>--}}
{{--                                                <th width="20%"><b>@lang("$string_file.travelled_distance")</b></th>--}}
{{--                                                <th width="30%" class="address">--}}
{{--                                                    <b> {{ $booking->travel_distance }}</b>--}}
{{--                                                </th>--}}
{{--                                                <th width="20%"><b>@lang("$string_file.total_time")</b></th>--}}
{{--                                                <th width="30%"--}}
{{--                                                    class="address"> <b>{{ $booking->travel_time }}</b>--}}
{{--                                                </th>--}}
{{--                                            </tr>--}}
{{--                                            </tfoot>--}}
{{--                                        </table>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 mt-5">
                                <div class="table-responsive">
                                    <table class="table table-default table-hover" id="dataTable" >
                                        <thead>
                                        <tr>
                                            <th class="left">@lang("$string_file.description")</th>
                                            <th class="right">@lang("$string_file.price")</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($booking->holder as $b)
                                            <tr>
                                                <td class="left">{{ $b['highlighted_text'] }}</td>
                                                <td class="right">{{ $b['value_text'] }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th class="left">@lang("$string_file.total")</th>
                                            <th class="right">{{ $booking->CountryArea->Country->isoCode.' '.$booking->final_amount_paid }}</th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/jquery.PrintArea.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            $(".print_invoice").click(function(){
                var mode = 'popup'; //popup
                var close = mode == "popup";
                var options = { mode : mode, popClose : true, popHt : 900, popWd: 900, };
                $(".printableArea").printArea( options );
            });
        });
    </script>
@endsection