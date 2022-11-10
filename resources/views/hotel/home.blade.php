@extends('hotel.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content container-fluid">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="panel panel-bordered">
                        <header class="panel-heading">
                            <h2 class="panel-title">@lang("$string_file.service_statistics")</h2>
                        </header>
{{--                            <h1 class="h5">Earning : {{ isset($earings) ? $earings : '---' }}</h1>--}}
                        <div class="row">
                            <div class="col-xl-3 col-md-4 col-sm-6 info-panel">
                                <a href="{{ route('hotel.activeride') }}">
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
                            <div class="col-xl-3 col-md-4 col-sm-6 info-panel">
                                <a href="{{ route('hotel.cancelride') }}">
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
                            <div class="col-xl-3 col-md-4 col-sm-6 info-panel">
                                <a href="{{ route('hotel.completeride') }}">
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
                            <div class="col-xl-2 col-md-4 col-sm-6 info-panel">
                                <a href="#">
                                    <div class="card card-shadow" style="margin-bottom:0.243rem">
                                        <div class="card-block bg-white p-20">
                                            <button type="button" class="btn btn-floating btn-sm btn-info"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                <i class="icon fa-calculator"></i>
                                            </button>
                                            <span class="ml-10 font-weight-400">@lang("$string_file.total")</span>
                                            <div class="content-text text-center mb-0">
                                                <span class="font-size-18 font-weight-100">{{ $booking }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6 info-panel">
                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                    <div class="card-block bg-white p-20">
                                        <button type="button" class="btn btn-floating btn-sm btn-info"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                            <i class="icon fa-money"></i>
                                        </button>
                                        <span class="ml-10 font-weight-400">Earnings</span>
                                        <div class="content-text text-center mb-0">
                                            <span class="font-size-18 font-weight-100">{{ isset($earings) ? $earings : '---' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title">@lang("$string_file.recent_rides")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%" >
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.area")</th>
                            <th>@lang("$string_file.service_type")</th>
                            <th>@lang("$string_file.vehicle_type")</th>
                            <th>@lang("$string_file.pickup_location")</th>
                            <th>@lang("$string_file.drop_off_location")</th>
                            <th>@lang("$string_file.payment")</th>
                            <th>@lang("$string_file.current_status")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td><a target="_blank" class="address_link"
                                       href="#">#{{ $booking->id }}</a>
                                </td>
                                <td>
                                    {{ $booking->User->UserName }}
                                    <br>
                                    {{ $booking->User->UserPhone }}
                                    <br>
                                    {{ $booking->User->email }}
                                </td>
                                <td>
                                    @if($booking->Driver)
                                        {{ $booking->Driver->fullName }}
                                        <br>
                                        {{ $booking->Driver->phoneNumber }}
                                        <br>
                                        {{ $booking->Driver->email }}
                                    @else
                                        @lang("$string_file.not_assigned_yet")
                                    @endif
                                </td>
                                <td> {{ $booking->CountryArea->LanguageSingle == "" ? $booking->CountryArea->LanguageAny->AreaName : $booking->CountryArea->LanguageSingle->AreaName }}</td>
                                <td> {{ $booking->ServiceType->serviceName }}</td>
                                <td> {{ $booking->VehicleType->LanguageVehicleTypeSingle == "" ? $booking->VehicleType->LanguageVehicleTypeAny->vehicleTypeName : $booking->VehicleType->LanguageVehicleTypeSingle->vehicleTypeName }}</td>
                                <td><a title="{{ $booking->pickup_location }}"
                                       class="map_address address_link"
                                       target="_blank"
                                       href="https://www.google.com/maps/place/{{ $booking->pickup_location }}" class="btn btn-icon btn-success ml-40"><i class="icon wb-map"></i></a>
                                </td>
                                <td><a title="{{ $booking->drop_location }}"
                                       class="map_address address_link"
                                       target="_blank"
                                       href="https://www.google.com/maps/place/{{ $booking->drop_location }}" class="btn btn-icon btn-danger ml-40"><i class="icon fa-tint"></i></a>
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
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($booking->created_at, $booking->CountryArea->timezone,null,$booking->Merchant) !!}
                                </td>
                                <td>
                                    <a target="_blank" title="@lang("$string_file.requested_drivers")"
                                       href="{{ route('merchant.ride-requests',$booking->id) }}"
                                       class="btn menu-icon btn_detail action_btn"><span
                                                class="fa fa-list-alt"></span></a>
                                    <a target="_blank" title="@lang("$string_file.ride_details")"
                                       href="{{ route('merchant.booking.details',$booking->id) }}"
                                       class="btn menu-icon btn_money action_btn"><span
                                                class="fa fa-info-circle"
                                                title="Booking Details"></span></a>

                                    @if(Auth::user('merchant')->can('ride_cancel_dispatch'))
                                        <span data-target="#cancelbooking"
                                              data-toggle="modal" id="{{ $booking->id }}">
                                            <a
                                                data-original-title="Cancel Booking"
                                                data-toggle="tooltip"
                                                id="{{ $booking->id }}" data-placement="top"
                                                class="btn menu-icon btn_delete action_btn"> <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1 float-right">{{ $bookings->links() }}</div>
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
                        {{--                        @foreach($cancelreasons as $cancelreason )--}}
                        {{--                            <div class="form-group">--}}
                        {{--                                <label>--}}
                        {{--                                    <input type="radio" name="cancel_reason_id" value="{{ $cancelreason->id }}">--}}
                        {{--                                    {{ $cancelreason->reason }}--}}
                        {{--                                </label>--}}
                        {{--                            </div>--}}
                        {{--                        @endforeach--}}
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

