@extends('merchant.layouts.main')
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
    <div class="app-content content" id="section-to-print">
        <div class="container-fluid content-wrapper">
            @if(session('success') || session('error'))
                <div aria-live="polite" aria-atomic="true">
                    <div class="toast" style="position: absolute; top: 5px; right: 5px; z-index: 999 !important;" data-delay="3000" data-autohide="true">
                        <div class="toast-header">
                            <i class="fa fa-cog fa-spin"></i>
                            <strong class="mr-auto">@lang('"$string_file.invoice"')</strong>
                            <small>@lang('admin.just_now')</small>
                            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="toast-body @if(session('success')) text-success @else text-danger @endif bg-white">
                            @if(session('success'))
                                <h6>{{ session('success') }}</h6>
                            @else
                                <h6>{{ session('error') }}</h6>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            <div class="content-body">
                <section class="card shadow h-100">
                    <div id="section-to-print" class="card">
                        <div class="card-header py-3">
                            <div class="content-header row">
                                <div class="content-header-left col-md-8 col-12 mb-2 breadcrumb-new">
                                    <h3 class="content-header-title mb-0 d-inline-block">@lang('"$string_file.invoice"')</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-header"> @lang("$string_file.ride_id") <strong>#{{ $booking->merchant_booking_id }}</strong>
                            <a href="#" class="btn btn-sm btn-danger float-right" onClick="javascript:window.print();">
                                <i class="fa fa-print"></i> @lang("$string_file.print")</a>

                            <a href="{{route('admin.sendinvoice',$booking->id)}}"
                               class="btn btn-sm btn-warning float-right mr-2"><i class="fas fa-mail-bulk"></i>Send
                                invoice</a>
                        </div>
                        @if(Auth::user()->tax)
                            <div class="card-header"> @php $a = json_decode(Auth::user()->tax,true);echo $a['name'] @endphp
                                <strong>@php $a = json_decode(Auth::user()->tax,true);echo $a['tax_number'] @endphp </strong>
                            </div>
                        @endif

                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-sm-4">
                                    <h6 class="mb-1">@lang('admin.message307'):</h6>
                                    @if(Auth::user()->demo == 1)
                                        <div><strong>{{ "********".substr($booking->Merchant->BusinessName, -2) }}</strong></div>
                                        <div>{{ "********".substr($booking->Merchant->merchantFirstName, -2) }} {{ $booking->Merchant->merchantLastName }}</div>
                                        <div>{{ "********".substr($booking->Merchant->merchantAddress, -2) }}</div>
                                        <div>@lang("$string_file.email"):{{ "********".substr($booking->Merchant->email, -2) }}</div>
                                        <div>@lang("$string_file.phone"):{{ "********".substr($booking->Merchant->merchantPhone, -2) }}</div>
                                    @else
                                        <div><strong>{{ $booking->Merchant->BusinessName }}</strong></div>
                                        <div>{{ $booking->Merchant->merchantFirstName }} {{ $booking->Merchant->merchantLastName }}</div>
                                        <div>{{ $booking->Merchant->merchantAddress }}</div>
                                        <div>@lang("$string_file.email"):{{ $booking->Merchant->email }}</div>
                                        <div>@lang("$string_file.phone"):{{ $booking->Merchant->merchantPhone }}</div>
                                    @endif
                                </div>
                                <div class="col-sm-3">
                                    <h6 class="mb-1">@lang("$string_file.f_cap_to"):</h6>
                                    @if(Auth::user()->demo == 1)
                                        <div><strong>{{ "********".substr($booking->User->UserName, -2) }}</strong></div>
                                        <div>@lang("$string_file.email"):{{ "********".substr($booking->User->email, -2) }}</div>
                                        <div>@lang("$string_file.phone"):{{ "********".substr($booking->User->UserPhone, -2) }}</div>
                                    @else
                                        <div><strong>{{ $booking->User->UserName }}</strong></div>
                                        <div>@lang("$string_file.email"):{{ $booking->User->email }}</div>
                                        <div>@lang("$string_file.phone"):{{ $booking->User->UserPhone }}</div>
                                    @endif
                                </div>
                                <div class="col-sm-5">
                                    <table width="100%" border="0">
                                        <tbody>
                                        <tr>
                                            <td width="50%">
                                                <table align="center" width="100%" border="0">
                                                    <tbody>
                                                    <tr><td align="center"><img class="profile_img"
                                                                                style="border-radius: 100%;"
                                                                                src="@if ($booking->Driver->profile_image) {{ get_image($booking->Driver->profile_image,'driver') }} @else {{ get_image(null, 'driver') }} @endif"
                                                                                width="100" height="100"></td>
                                                    </tr>
                                                    <tr><td height="4px;"></td></tr>
                                                    @if(Auth::user()->demo == 1)
                                                        <tr><td align="center"><strong class="profile_name">{{ "********".substr($booking->Driver->fullName, -2) }}</strong></td></tr>
                                                        <tr><td height="4px;"></td></tr>
                                                        <tr><td align="center">{{ "********".substr($booking->Driver->email, -2) }}</td></tr>
                                                        <tr><td align="center">{{ "********".substr($booking->Driver->phoneNumber, -2) }}</td></tr>
                                                    @else
                                                        <tr><td align="center"><strong class="profile_name">{{ $booking->Driver->fullName }}</strong></td></tr>
                                                        <tr><td height="4px;"></td></tr>
                                                        <tr><td align="center">{{ $booking->Driver->email }}</td></tr>
                                                        <tr><td align="center">{{ $booking->Driver->phoneNumber }}</td></tr>
                                                    @endif
                                                    <tr>
                                                        <td align="center">
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
                                                            @endif</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td width="50%">
                                                <table width="100%" border="0">
                                                    <tbody>
                                                    <tr>
                                                        <td align="center"><img
                                                                    src="{{ get_image($booking->VehicleType->vehicleTypeImage,'vehicle') }}"
                                                                    width="100" height="100"></td>
                                                    </tr>
                                                    <tr>
                                                        <td height="4px;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center"><strong
                                                                    class="vehicle_name">{{ $booking->VehicleType->LanguageVehicleTypeSingle == "" ? $booking->VehicleType->LanguageVehicleTypeAny->vehicleTypeName : $booking->VehicleType->LanguageVehicleTypeSingle->vehicleTypeName }}</strong>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td height="4px;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center"
                                                            class="vehicle_number">{{ $booking->DriverVehicle->vehicle_number }}</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-7 col-sm-7">

                                    <div class="mb-3">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tbody>
                                            <tr>
                                                <td width="8%"><img class="location_marker"
                                                                    src="{{ view_config_image('static-images/pinup.png') }}"
                                                                    width="40"></td>
                                                <td width="38%" class="address">
                                                    {{ $booking->BookingDetail->start_location }}
                                                </td>
                                                <td width="8%">&nbsp;</td>
                                                <td width="8%"><img class="location_marker"
                                                                    src="{{ view_config_image('static-images/pindown.png') }}"
                                                                    width="40"></td>
                                                <td width="38%"
                                                    class="address"> {{ $booking->BookingDetail->end_location }}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="">
                                        <img class="map_img" style="border-radius: 20px;"
                                             src="{{ str_replace('https:maps.','https://maps.',$booking->map_image) }}"
                                             width="100%" height="300"/>
                                        <div class="mb-3">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tbody>
                                                <tr>
                                                    <td width="20%"><b>@lang("$string_file.travelled_distance")</b></td>
                                                    <td width="30%" class="address">
                                                        {{ $booking->travel_distance }}
                                                    </td>
                                                    <td width="20%"><b>@lang("$string_file.total_time")</b></td>
                                                    <td width="30%"
                                                        class="address"> {{ $booking->travel_time }}
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>


                                </div>
                                <div class="col-lg-5 col-sm-5">
                                    <table class="table table-clear">
                                        <tbody>
                                        <tr>
                                            <td class="left"><strong>@lang("$string_file.description")</strong></td>
                                            <td class="right"><strong>@lang("$string_file.price")</strong></td>
                                        </tr>
                                        @foreach($booking->holder as $b)
                                            <tr>
                                                <td class="left">{{ $b['highlighted_text'] }}</td>
                                                <td class="right">{{ $b['value_text'] }}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td class="left"><strong
                                                        style="font-size:20px;">@lang("$string_file.total")</strong></td>
                                            <td class="right"><strong
                                                        style="font-size:20px;">{{ $booking->final_amount_paid }}</strong>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $('.toast').toast('show');
    </script>
@endsection