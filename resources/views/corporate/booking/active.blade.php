@extends('corporate.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('ridecancel'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message57')
                </div>
            @endif
            @if(session('noridenowexport'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message440')
                </div>
            @endif
            @if(session('noridelaterexport'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message447')
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.on_going_rides")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <div class="nav-tabs-horizontal" data-plugin="tabs">
                        <ul class="nav nav-tabs nav-tabs-line tabs-line-top" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="base-tab11" data-toggle="tab" href="#exampleTabsLineTopOne"
                                   aria-controls="#exampleTabsLineTopOne" role="tab">
                                    <i class="icon fa-cab"></i>@lang("$string_file.ride_now")</a></li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="base-tab12" data-toggle="tab" href="#exampleTabsLineTopTwo"
                                   aria-controls="#exampleTabsLineTopTwo" role="tab">
                                    <i class="icon fa-clock-o"></i>@lang("$string_file.ride_later")</a></li>
                        </ul>
                        <div class="tab-content pt-20">
                            <div class="tab-pane active" id="exampleTabsLineTopOne" role="tabpanel">
                                <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>@lang("$string_file.ride_id")</th>
                                        @if($bookingConfig->ride_otp == 1)
                                            <th>@lang("$string_file.otp")</th>
                                        @endif
                                        <th>@lang("$string_file.current_status")</th>
                                        <th>@lang("$string_file.user_details")</th>
                                        <th>@lang("$string_file.driver_details")</th>
                                        <th>@lang("$string_file.request_from")</th>
                                        <th>@lang("$string_file.ride_details")</th>
                                        <th>@lang("$string_file.pickup_drop")</th>
                                        <th>@lang("$string_file.estimated")</th>
                                        <th>@lang("$string_file.payment_method")</th>
                                        <th>@lang("$string_file.date")</th>
                                        <th>@lang("$string_file.action")</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td><a target="_blank"
                                                   class="address_link hyperLink"
                                                   href="{{ route('merchant.booking.details',$booking->id) }}">{{ $booking->merchant_booking_id }}</a>
                                            </td>
                                            @if($bookingConfig->ride_otp == 1)
                                                <td>{{$booking->ride_otp}}</td>
                                            @endif
                                            <td style="text-align: center">
                                                @switch($booking->booking_status)
                                                    @case(1001)
                                                    @lang('admin.newbooking')
                                                    <br>

                                                    {{ $booking->updated_at->toTimeString() }}
                                                    @break
                                                    @case(1012)
                                                    @lang('admin.message291')
                                                    <br>

                                                    {{ $booking->updated_at->toTimeString() }}
                                                    @break
                                                    @case(1002)
                                                    @lang('admin.driverAccepted')

                                                    <br>
                                                    {{ $booking->updated_at->toTimeString() }}
                                                    @break
                                                    @case(1003)
                                                    @lang('admin.driverArrived')
                                                    <br>

                                                    {{ $booking->updated_at->toTimeString() }}
                                                    @break
                                                    @case(1004)
                                                    @lang('admin.begin')
                                                    <br>

                                                    {{ $booking->updated_at->toTimeString() }}
                                                    @break
                                                @endswitch
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
                                                <td>
                                                                 <span class="long_text">
                                                                @if($booking->Driver)
                                                                         {{ '********'.substr($booking->Driver->last_name,-2) }}
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
                                                                         {{ $booking->Driver->first_name.' '.$booking->Driver->last_name }}
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
                                            <td>
                                                @switch($booking->platform)
                                                    @case(1)
                                                    @lang("$string_file.application")
                                                    @break
                                                    @case(2)
                                                    @lang("$string_file.admin")
                                                    @break
                                                    @case(3)
                                                    @lang("$string_file.web")
                                                    @break
                                                @endswitch
                                            </td>

                                            @php
                                                $service_text = ($booking->ServiceType) ? $booking->ServiceType->serviceName : $booking->deliveryType->name ;
                                            @endphp

                                            <td>{!! nl2br($booking->CountryArea->CountryAreaName ."\n". $service_text."\n".$booking->VehicleType->VehicleTypeName) !!}</td>

                                            <td><a class="long_text hyperLink" target="_blank"
                                                   href="https://www.google.com/maps/place/{{ $booking->pickup_location }}">{{ $booking->pickup_location }}</a></span>
                                                <a class="long_text hyperLink" target="_blank"
                                                   href="https://www.google.com/maps/place/{{ $booking->drop_location }}">{{ $booking->drop_location }}</a>
                                            </td>
                                            <td>
                                                {{ $booking->estimate_distance }}<br>
                                                {{ $booking->CountryArea->Country->isoCode .  $booking->estimate_bill }}
                                            </td>
                                            <td>
                                                {{ $booking->PaymentMethod->payment_method }}
                                            </td>
                                            <td>
                                                {{ $booking->created_at->toDateString() }}
                                                <br>
                                                {{ $booking->created_at->toTimeString() }}
                                            </td>
                                            <td>

                                                <a target="_blank"
                                                   title="@lang("$string_file.requested_drivers")"
                                                   href="{{ route('corporate.ride-requests',$booking->id) }}"
                                                   class="btn btn-sm btn-primary menu-icon btn_detail action_btn"><span
                                                            class="fa fa-list-alt"
                                                            data-original-title="@lang('admin.message184')"
                                                            data-toggle="tooltip"
                                                            data-placement="top"></span></a>
                                                <a target="_blank"
                                                   title="@lang("$string_file.ride_details")"
                                                   href="{{ route('corporate.booking.details',$booking->id) }}"
                                                   class="btn btn-sm btn-success menu-icon btn_money action_btn"><span
                                                            class="fa fa-info-circle"
                                                            data-original-title="@lang("$string_file.service_detail")"
                                                            data-toggle="tooltip"
                                                            data-placement="top"></span></a>
                                                @if(Auth::user('merchant')->can('ride_cancel_dispatch'))
                                                    <span data-target="#cancelbooking"
                                                          data-toggle="modal"
                                                          id="{{ $booking->id }}"><a
                                                                data-original-title="Cancel Booking"
                                                                data-toggle="tooltip"
                                                                id="{{ $booking->id }}"
                                                                data-placement="top"
                                                                class="btn btn-sm btn-warning menu-icon btn_delete action_btn"> <i
                                                                    class="fa fa-times"></i> </a></span>
                                                @endif
                                                @if($booking->booking_status != 1001)
                                                    <a target="_blank"
                                                       title="@lang("$string_file.ride_details")"
                                                       href="{{ route('corporate.activeride.track',$booking->id) }}"
                                                       class="btn btn-sm btn-success menu-icon btn_money action_btn"><span
                                                                class="fa fa-map-marker"
                                                                data-original-title="@lang('admin.trackRide')"
                                                                data-toggle="tooltip"
                                                                data-placement="top"></span></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="pagination1 float-right">{{ $bookings->links() }}</div>
                            </div>
                            <div class="tab-pane" id="exampleTabsLineTopTwo" role="tabpanel">
                                <table id="customDataTable2" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>@lang("$string_file.ride_id")</th>
                                        @if($bookingConfig->ride_otp == 1)
                                            <th>@lang("$string_file.otp")</th>
                                        @endif
                                        <th>@lang("$string_file.current_status")</th>
                                        <th>@lang("$string_file.user_details")</th>
                                        <th>@lang("$string_file.driver_details")</th>
                                        <th>@lang("$string_file.request_from")</th>
                                        <th>@lang("$string_file.ride_details")</th>
                                        <th>@lang("$string_file.pickup_drop")</th>
{{--                                        <th>@lang("$string_file.pickup_location")</th>--}}
{{--                                        <th>@lang("$string_file.drop_off_location")</th>--}}
                                        <th>@lang("$string_file.estimated")</th>
                                        <th>@lang("$string_file.payment_method")</th>
                                        <th>@lang("$string_file.date")</th>
                                        <th>@lang("$string_file.action")</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($later_bookings as $booking)
                                        <tr>
                                            <td>
                                                <a class="address_link"
                                                   href="{{ route('corporate.booking.details',$booking->id) }}">{{ $booking->merchant_booking_id }} </a>
                                            </td>
                                            @if($bookingConfig->ride_otp == 1)
                                                <td>{{$booking->ride_otp}}</td>
                                            @endif
                                            <td style="text-align: center">
                                                @switch($booking->booking_status)
                                                    @case(1001)
                                                    @lang('admin.newbooking')

                                                    <br>
                                                    {{ $booking->updated_at->toTimeString() }}
                                                    @break
                                                    @case(1012)
                                                    @lang('admin.message291')
                                                    <br>

                                                    {{ $booking->updated_at->toTimeString() }}
                                                    @break
                                                    @case(1002)
                                                    @lang('admin.message582')

                                                    <br>
                                                    {{ $booking->updated_at->toTimeString() }}
                                                    @break
                                                    @case(1003)
                                                    @lang('admin.driver_arrived')

                                                    <br>
                                                    {{ $booking->updated_at->toTimeString() }}
                                                    @break
                                                    @case(1004)
                                                    @lang('admin.begin')

                                                    <br>
                                                    {{ $booking->updated_at->toTimeString() }}
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @if(Auth::user()->Merchant->demo == 1)
                                                    <span class="long_text">
                                                                            {{ "********".substr($booking->User->UserName, -2) }}
                                                                            <br>
                                                                            {{ "********".substr($booking->User->UserPhone, -2) }}
                                                                            <br>
                                                                            {{ "********".substr($booking->User->email, -2) }}
                                                                            </span>
                                                @else
                                                    <span class="long_text">
                                                                            {{ $booking->User->UserName }}
                                                                            <br>
                                                                            {{ $booking->User->UserPhone }}
                                                                            <br>
                                                                            {{ $booking->User->email }}
                                                                            </span>
                                                @endif
                                            </td>
                                            <td>
                                                                 <span class="long_text">
                                                                    @if($booking->Driver)
                                                                         @if(Auth::user()->Merchant->demo == 1)
                                                                             {{ "********".substr($booking->Driver->first_name.' '.$booking->Driver->last_name, -2) }}
                                                                             <br>
                                                                             {{ "********".substr($booking->Driver->phoneNumber, -2) }}
                                                                             <br>
                                                                             {{ "********".substr($booking->Driver->email, -2) }}
                                                                         @else
                                                                             {{ $booking->Driver->first_name.' '.$booking->Driver->last_name }}
                                                                             <br>
                                                                             {{ $booking->Driver->phoneNumber }}
                                                                             <br>
                                                                             {{ $booking->Driver->email }}
                                                                         @endif
                                                                     @else
                                                                         @lang("$string_file.not_assigned_yet")
                                                                     @endif
                                                                </span>
                                            </td>
                                            <td>
                                                @switch($booking->platform)
                                                    @case(1)
                                                    @lang("$string_file.application")
                                                    @break
                                                    @case(2)
                                                    @lang("$string_file.admin")
                                                    @break
                                                    @case(3)
                                                    @lang("$string_file.web")
                                                    @break
                                                @endswitch
                                            </td>
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
                                                {{ $booking->later_booking_date }}<br>
                                                {{$booking->later_booking_time }}
                                            </td>
                                            <td>
                                                {{ $booking->estimate_distance }}<br>
                                                {{$booking->CountryArea->Country->isoCode . $booking->estimate_bill }}
                                            </td>
                                            <td>
                                                {{ $booking->PaymentMethod->payment_method }}
                                            </td>
                                            <td>
                                                {{ $booking->created_at->toDateString() }}
                                                <br>
                                                {{ $booking->created_at->toTimeString() }}
                                            </td>
                                            <td>
                                                <a target="_blank"
                                                   title="@lang("$string_file.ride_details")"
                                                   href="{{ route('corporate.booking.details',$booking->id) }}"
                                                   class="btn btn-sm btn-info menu-icon btn_money action_btn"><span
                                                            class="fa fa-info-circle"
                                                            data-original-title="@lang("$string_file.service_detail")"
                                                            data-toggle="tooltip"
                                                            data-placement="top"></span></a>
                                                @if(Auth::user('merchant')->can('ride_cancel_dispatch'))
                                                    <span data-target="#cancelbooking"
                                                          data-toggle="modal"
                                                          id="{{ $booking->id }}"><a
                                                                data-original-title="Cancel Booking"
                                                                data-toggle="tooltip"
                                                                id="{{ $booking->id }}"
                                                                data-placement="top"
                                                                class="btn btn-sm btn-danger menu-icon btn_delete action_btn"> <i
                                                                    class="fa fa-times"></i> </a></span>

                                                @endif
                                                @if(!in_array($booking->booking_status,[1001,1012]))
                                                    <a target="_blank"
                                                       title="@lang("$string_file.ride_details")"
                                                       href="{{ route('corporate.activeride.track',$booking->id) }}"
                                                       class="btn btn-sm btn-success menu-icon btn_money action_btn"><span
                                                                class="fa fa-map-marker"
                                                                data-original-title="@lang('admin.trackRide')"
                                                                data-toggle="tooltip"
                                                                data-placement="top"></span></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="pagination1 float-right">{{ $later_bookings->links() }}</div>
                            </div>
                        </div>
                    </div>
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
    <br>


@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $('#dataTable2').DataTable({
                searching: false,
                paging: false,
                info: false,
                "bSort": false,
            });
        });
    </script>
@endsection
