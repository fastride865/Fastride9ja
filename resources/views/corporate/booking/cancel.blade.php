@extends('corporate.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('noridecancelexport'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message449')
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">

                    </div>
                    <h3 class="panel-title"><i class="fa fa-car" aria-hidden="true"></i>
                        @lang("$string_file.cancelled_rides")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('corporate.cancelRide.search') }}" method="get">
                        @csrf
                        <div class="table_search row">
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                @lang("$string_file.search_by"):
                            </div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="input-group">
                                    <input type="text" id="" name="booking_id"
                                           placeholder="@lang("$string_file.ride_id")"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="input-group">
                                    <input type="text" id="" name="rider"
                                           placeholder="@lang("$string_file.user_details")"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
{{--                                <p>@lang('admin.searchhint')</p>--}}
                            </div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="input-group">
                                    <input type="text" id="" name="date"
                                           placeholder="@lang("$string_file.date")"
                                           class="form-control col-md-12 col-xs-12 datepickersearch"
                                           id="datepickersearch" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-2  col-xs-12 form-group active-margin-top">
                                <button class="btn btn-primary" type="submit"
                                        name="seabt12"><i
                                            class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.ride_id")</th>
                            <th>@lang("$string_file.ride_type")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.ride_details")</th>
                            <th>@lang("$string_file.pickup_drop")</th>
                            <th>@lang("$string_file.cancel_reason")</th>
                            <th>@lang("$string_file.current_status")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td><a target="_blank" class="address_link"
                                       href="{{ route('corporate.booking.details',$booking->id) }}">{{ $booking->merchant_booking_id }}</a>
                                </td>
                                <td>
                                    @if($booking->booking_type == 1)
                                        @lang("$string_file.ride_now")
                                    @else
                                        @lang("$string_file.ride")  @lang("$string_file.later")
                                    @endif
                                </td>
                                @if(Auth::user()->Merchant->demo == 1)
                                    <td>
                                                         <span class="long_text">
                                                        {{ "********".substr($booking->User->UserName,-2) }}
                                                        <br>
                                                        {{ "********".substr($booking->User->UserPhone,-2) }}
                                                        <br>
                                                        {{ "********".substr($booking->User->email,-2) }}
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
                                @endif

                                @php
                                    $service_text = ($booking->ServiceType) ? $booking->ServiceType->serviceName : $booking->deliveryType->name ;
                                @endphp

                                <td>{!! nl2br($booking->CountryArea->CountryAreaName ."\n". $service_text."\n".$booking->VehicleType->VehicleTypeName) !!}</td>

                                <td><a class="long_text hyperLink" target="_blank"
                                       href="https://www.google.com/maps/place/{{ $booking->pickup_location }}">{{ $booking->pickup_location }}</a>
                                    <a class="long_text hyperLink" target="_blank"
                                       href="https://www.google.com/maps/place/{{ $booking->drop_location }}">{{ $booking->drop_location }}</a>
                                </td>
                                <td>
                                                        <span >
                                                            {{ $booking->CancelReason->ReasonName }}
                                                        </span>
                                </td>
                                <td>
                                    @switch($booking->booking_status)
                                        @case(1006)
                                        @lang('admin.message48')
                                        @break
                                        @case(1007)
                                        @lang('admin.message49')
                                        @break
                                        @case(1008)
                                        @lang('admin.message50')
                                        @break
                                    @endswitch
                                </td>
                                <td>
                                    {{ $booking->created_at->toDateString() }}
                                    <br>
                                    {{ $booking->created_at->toTimeString() }}
                                </td>
                                <td>
                                    <a target="_blank" title="@lang("$string_file.requested_drivers")"
                                       href="{{ route('corporate.ride-requests',$booking->id) }}"
                                       class="btn btn-sm btn-success menu-icon btn_detail action_btn"><span
                                                class="fa fa-list-alt"></span></a>
                                    <a target="_blank" title="@lang("$string_file.ride_details")"
                                       href="{{ route('corporate.booking.details',$booking->id) }}"
                                       class="btn btn-sm btn-info menu-icon btn_money action_btn"><span
                                                class="fa fa-info-circle"
                                                title="Booking Details"></span></a>
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
@endsection
