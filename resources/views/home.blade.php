@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="mr--10 ml--10">
                <div class="row" style="margin-right: 0rem;margin-left: 0rem">
                    <!-- First Row -->
                    @if(Auth::user('merchant')->can('view_drivers') && Auth::user('merchant')->can('view_rider') && Auth::user('merchant')->can('active_ride'))
                        <div class="col-6 col-md-6 col-sm-12">
                            <!-- Example Panel With Heading -->
                            <div class="panel panel-bordered">
                                <div class="panel-heading">
                                    <div class="panel-actions"></div>
                                    <h3 class="panel-title">@lang("$string_file.site_statistics")  </h3>
                                </div>
                                <div class="panel-body">
                                    <div class="row" >
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="@if(Auth::user('merchant')->can('view_drivers')) {{ route('users.index') }} @else # @endif">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-success"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="icon fa-cab"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$string_file.active_users")</span>
                                                        <div class="content-text text-center mb-0">
                                                            <span class="font-size-18 font-weight-100">{{ $merchant->User->where('taxi_company_id','=',NULL)->count() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="@if(Auth::user('merchant')->can('view_countries')){{ route('country.index') }} @else # @endif">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-warning"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="icon fa-flag"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$sting_file.service_countries")</span>
                                                        <div class="content-text text-center mb-0">
                                                            <span class="font-size-18 font-weight-100">{{ $totalCountry }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="@if(Auth::user('merchant')->can('view_drivers')) {{ route('driver.index') }} @else # @endif">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-primary"
                                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="icon wb-users"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$string_file.active_drivers")</span>
                                                        <div class="content-text text-center mb-0">
                                                            <span class="font-size-18 font-weight-100">{{ $merchant->Driver->where('taxi_company_id','=',NULL)->count() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="{{ route('merchant.driver.goingtoexpiredocuments') }}">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-danger"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="icon wb-file"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$string_file.docs_going_expire")</span>
                                                        <div class="content-text text-center mb-0">
                                                            <span class="font-size-18 font-weight-100">{{ $expire_driver_doc }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Example Panel With Heading -->
                        <div class="col-6 col-md-6 col-sm-12">
                            <!-- Example Panel With Heading -->
                            <div class="panel panel-bordered">
                                <div class="panel-heading">
                                    <div class="panel-actions"></div>
                                    <h3 class="panel-title">@lang("$string_file.service_statistics")</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="@if(Auth::user('merchant')->can('completed_ride')) {{ route('merchant.all.ride') }} @else # @endif">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-info"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="icon fa-calculator"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$string_file.total")</span>
                                                        <div class="content-text text-center mb-0">
                                                            <span class="font-size-18 font-weight-100">{{ $merchant->Booking->where('taxi_company_id','=',NULL)->count() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="@if(Auth::user('merchant')->can('active_ride')) {{ route('merchant.activeride') }} @else # @endif">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-warning"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="icon fa-road"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$string_file.on_going")</span>
                                                        <div class="content-text text-center mb-0">
                                                            <span class="font-size-18 font-weight-100">{{ $activebookings }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="@if(Auth::user('merchant')->can('view_corporate')) {{ route('merchant.cancelride') }} @else # @endif">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-danger"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="icon fa-times"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$string_file.cancelled")</span>
                                                        <div class="content-text text-center mb-0">
                                                            <span class="font-size-18 font-weight-100">{{ $cancelbookings }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="@if(Auth::user('merchant')->can('completed_ride')) {{ route('merchant.completeride') }} @else # @endif">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-success"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="icon wb-check"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$string_file.completed")</span>
                                                        <div class="content-text text-center mb-0">
                                                            <span class="font-size-18 font-weight-100">{{ $complete }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--                @else--}}
                        {{--                    <h1 class="text-center">@lang('admin.welcome')</h1>--}}
                    @endif
                </div>

                <div class="row"  style="margin-right: 0;margin-left: 0">
                    @if(Auth::user('merchant')->can('view_corporate') && isset($merchant->Configuration->corporate_admin) && $merchant->Configuration->corporate_admin == 1)
                        <div class="col-xl-3 col-md-6 col-sm-6 info-panel">
                            <a href="@if(Auth::user('merchant')->can('view_corporate')) {{ route('corporate.index') }} @else # @endif">
                                <div class="card card-shadow">
                                    <div class="card-block bg-white ml-20 p-20">
                                        <button type="button" class="btn btn-floating btn-sm btn-primary" style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                            <i class="icon fa-industry"></i>
                                        </button>
                                        <span class="ml-10 font-weight-400"> @lang("$string_file.corporate_user")</span>
                                        <div class="content-text text-center mb-0">
                                            <span class="font-size-18 font-weight-100">{{ $merchant->Corporate->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                    <div class="col-xl-3 col-md-6 col-sm-6 info-panel">
                        <a href="{{ route('countryareas.index') }}">
                            <div class="card card-shadow">
                                <div class="card-block bg-white ml-20  p-20">
                                    <button type="button" class="btn btn-floating btn-sm btn-warning"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                        <i class="icon fa-area-chart"></i>
                                    </button>
                                    <span class="ml-10 font-weight-400">@lang("$string.service_area")</span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-18 font-weight-100">{{ $merchant->GetCountryArea->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6 info-panel">
                        <div class="card card-shadow">
                            <div class="card-block bg-white ml-20 p-20">
                                <button type="button" class="btn btn-floating btn-sm btn-success"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                    <i class="icon wb-file"></i>
                                </button>
                                <span class="ml-10 font-weight-400">Coming Soon</span>

                                <div class="content-text text-center mb-0">
                                    <span class="font-size-18 font-weight-100">--</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-6 info-panel">
                        <div class="card card-shadow">
                            <div class="card-block bg-white ml-20 p-20">
                                <button type="button" class="btn btn-floating btn-sm btn-primary"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                    <i class="icon wb-user"></i>
                                </button>
                                <span class="ml-10 font-weight-400">Coming Soon</span>
                                <div class="content-text text-center mb-0">
                                    <span class="font-size-18 font-weight-100">--</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <header class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title">@lang("$string_file.recent_rides")</h3>
                </header>
                <div class="panel-body">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.ride_id")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.ride_details")</th>
                            <th>@lang("$string_file.pickup_location")</th>
                            <th>@lang("$string_file.drop_off_location")</th>
                            <th>@lang("$string_file.payment")</th>
                            <th>@lang("$string_file.current_status")</th>
                            <th>@lang("$string_file.ride")  @lang("$string_file.date")</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1 @endphp
                        @foreach($bookings as $booking)
                            <tr>
                                <td><a target="_blank" class="hyperLink"
                                       href="{{ route('merchant.booking.details',$booking->id) }}">{{ $booking->merchant_booking_id }}</a>
                                </td>
                                @if(Auth::user()->demo == 1)
                                    <td>
                                                 <span class="long_text">
                                                {{ "********".substr($booking->User->UserName,-2) }}
                                                <br>
                                                {{ "********".substr($booking->User->UserPhone,-2) }}
                                                <br>
                                                {{ "********".substr($booking->User->email,-2) }}
                                                </span>--}}
                                    </td>
                                    <td>
                                                <span class="long_text">
                                                @if($booking->Driver)
                                                        {{ "********".substr($booking->Driver->fullName,-2) }}
                                                        <br>
                                                        {{ "********".substr($booking->Driver->phoneNumber,-2) }}
                                                        <br>
                                                        {{ "********".substr($booking->Driver->email,-2) }}
                                                    @else
                                                        @lang("$string_file.not_assigned_yet")
                                                    @endif
                                                </span>
                                    </td>
                                @else
                                    <td>
                                                <span class="long_text">
                                                {{ $booking->User->UserName }}
                                                <br>
                                                {{ $booking->User->UserPhone }}
                                                <br>
                                                {{ $booking->User->email }}
                                                </span>
                                    </td>
                                    <td>
                                                 <span class="long_text">
                                                @if($booking->Driver)
                                                         {{ $booking->Driver->fullName }}
                                                         <br>
                                                         {{ $booking->Driver->phoneNumber }}
                                                         <br>
                                                         {{ $booking->Driver->email }}
                                                     @else
                                                         @lang("$string_file.not_assigned_yet")
                                                     @endif
                                                </span>
                                    </td>
                                @endif
                                @php
                                    $service_type = ($booking->ServiceType) ? $booking->ServiceType->serviceName : ($booking->deliveryType ? $booking->deliveryType->name : '---' );
                                @endphp
                                <td>
                                    {!! nl2br($booking->CountryArea->CountryAreaName."\n".$service_type."\n".$booking->VehicleType->VehicleTypeName) !!}
                                </td>
                                <td><a title="{{ $booking->pickup_location }}"
                                       class="map_address hyperLink long_text"
                                       target="_blank"
                                       href="https://www.google.com/maps/place/{{ $booking->pickup_location }}">{{ $booking->pickup_location }}</a>
                                </td>
                                <td><a title="{{ $booking->drop_location }}"
                                       class="map_address hyperLink long_text"
                                       target="_blank"
                                       href="https://www.google.com/maps/place/{{ $booking->drop_location }}">{{ $booking->drop_location }}</a>
                                </td>
                                <td>
                                    Cash
                                </td>
                                <td>
                                    @switch($booking->booking_status)
                                        @case(1001)
                                        @lang('admin.newbooking')
                                        @break
                                        @case(1002)
                                        @lang('admin.driverAccepted')
                                        @break
                                        @case(1003)
                                        @lang('admin.driverArrived')
                                        @break
                                        @case(1004)
                                        @lang('admin.begin')
                                        @break
                                    @endswitch
                                    <br>
                                    @lang("$string_file.at")
                                    <br>
                                    {{ $booking->updated_at->toTimeString() }}
                                </td>
                                <td>
                                    {{ $booking->created_at->toformatteddatestring() }}
                                </td>
                                <td>
                                    <a target="_blank" title="@lang("$string_file.requested_drivers")"
                                       href="{{ route('merchant.ride-requests',$booking->id) }}"
                                       class="btn btn-sm btn-warning menu-icon btn_detail action_btn"><i
                                                class="fa fa-list-alt"></i></a>
                                    <a target="_blank" title="@lang("$string_file.ride_details")"
                                       href="{{ route('merchant.booking.details',$booking->id) }}"
                                       class="btn btn-sm btn-info menu-icon btn_money action_btn"><i
                                                class="fa fa-info-circle"
                                                title="Booking Details"></i></a>
                                    @if(Auth::user('merchant')->can('ride_cancel_dispatch'))
                                        <a data-target="#cancelbooking"
                                           data-toggle="modal" id="{{ $booking->id }}"
                                           data-original-title="Cancel Booking"
                                           data-toggle="tooltip"
                                           id="{{ $booking->id }}" data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_delete action_btn"> <i
                                                    class="fa fa-times"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="cancelbooking" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33">@lang('admin.message56')</label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('merchant.cancelbooking') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        @foreach($cancelreasons as $cancelreason )
                            <div class="form-group">
                                <label>
                                    <input type="radio" name="cancel_reason_id" value="{{ $cancelreason->id }}">
                                    {{ $cancelreason->ReasonName }}
                                </label>
                            </div>
                        @endforeach

                        <label>@lang("$string_file.additional_notes"): </label>
                        <div class="form-group">
                                    <textarea class="form-control" id="title1" rows="3" name="description"
                                              placeholder="@lang("$string_file.additional_notes")"></textarea>
                        </div>
                        <input type="hidden" name="booking_id" id="booking_id" value="">

                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-outline-primary btn-lg" value="Cancel Booking">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    {{--    <script src="{{ asset('global/vendor/chartist/chartist.js') }}"></script>--}}
    {{--    <script src="{{ asset('global/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.js') }}"></script>--}}
    {{--    <script src="{{ asset('theme/examples/js/dashboard/ecommerce.js') }}"></script>--}}
@endsection