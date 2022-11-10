@extends('hotel.layouts.main')
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
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title"><i class="m-1 fa fa-car" aria-hidden="true"></i>
                        @lang("$string_file.ongoing_rides")</h3>
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
                                    <i class="icon fa-clock-o"></i>@lang("$string_file.ride")  @lang("$string_file.later") </a></li>
                        </ul>
                        <div class="tab-content pt-20">
                            <div class="tab-pane active" id="exampleTabsLineTopOne" role="tabpanel">
                                <form action="{{ route('hotel.activeride.serach') }}" method="post">
                                    @csrf
                                    <div class="table_search row p-3 ">
                                        <div class="col-md-2 active-margin-top">
                                            @lang("$string_file.search_by") :
                                        </div>
                                        <div class="col-md-1 col-xs-12 form-group active-margin-top">
                                            <div class="">
                                                <input id="" name="booking_id"
                                                       placeholder="@lang("$string_file.ride_id")"
                                                       class="form-control"
                                                       type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                            <div class="">
                                                <input id="" name="rider"
                                                       placeholder="@lang("$string_file.user_details")"
                                                       class="form-control"
                                                       type="text">
{{--                                                <p>@lang('admin.searchhint')</p>--}}
                                            </div>

                                        </div>
                                        <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                            <div class="">
                                                <input id="" name="driver"
                                                       placeholder="@lang("$string_file.driver_details")"
                                                       class="form-control"
                                                       type="text">
{{--                                                <p>@lang('admin.searchhint')</p>--}}
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-12 form-group active-margin-top">
                                            <div class="">
                                                <select class="form-control" name="booking_status"
                                                        id="booking_status"
                                                >
                                                    <option value="">{{trans("$string_file.ride_status")}}</option>
                                                    <option value="1001">{{trans("$string_file.new_ride")}}</option>
                                                    <option value="1002"> {{trans("$string_file.accepted_by_driver")}}</option>
                                                    <option value="1012">{{trans("$string_file.partial_accepted")}}</option>
                                                    <option value="1003"> {{trans("$string_file.arrived_at_pickup")}}</option>
                                                    <option value="1004">{{trans("$string_file.ride_started")}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-xs-12 form-group active-margin-top">
                                            <button class="btn btn-primary" type="submit"
                                                    name="seabt12"><i class="fa fa-search"
                                                                      aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <table class="display nowrap table table-hover table-stripedw-full" id="customDataTable" style="width:100%">
                                    <div class="p-1">
                                        <thead>
                                        <tr>
                                            <th>@lang("$string_file.sn")</th>
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
{{--                                            <th>@lang("$string_file.pickup_location")</th>--}}
{{--                                            <th>@lang("$string_file.drop_off_location")</th>--}}
                                            <th>@lang("$string_file.estimated")</th>
                                            <th>@lang("$string_file.payment_method")</th>
                                            <th>@lang("$string_file.date")</th>
                                            <th>@lang("$string_file.action")</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php $sr = $bookings->firstItem() @endphp
                                        @foreach($bookings as $booking)
                                            <tr>
                                                <td>{{ $sr }}</td>
                                                <td><a target="_blank"
                                                       class="address_link hyperLink"
                                                       href="{{ route('taxicompany.booking.details',$booking->id) }}">{{ $booking->merchant_booking_id }}</a>
                                                </td>
                                                @if($bookingConfig->ride_otp == 1)
                                                    <td>{{$booking->ride_otp}}</td>
                                                @endif
                                                <td style="text-align: center">
                                                    @switch($booking->booking_status)
                                                        @case(1001)
                                                        @lang('admin.new_booking')
                                                        <br>

                                                        {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3) !!}
                                                        @break
                                                        @case(1012)
                                                        @lang('admin.message291')
                                                        <br>

                                                        {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3) !!}
                                                        @break
                                                        @case(1002)
                                                        @lang('admin.driver_accepted')

                                                        <br>
                                                        {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3) !!}
                                                        @break
                                                        @case(1003)
                                                        @lang('admin.driver_arrived')
                                                        <br>

                                                        {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3) !!}
                                                        @break
                                                        @case(1004)
                                                        @lang('admin.begin')
                                                        <br>

                                                        {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3) !!}
                                                        @break
                                                    @endswitch
                                                </td>
                                                @if(Auth::user()->demo == 1)
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
                                                    {!! convertTimeToUSERzone($booking->created_at, $booking->CountryArea->timezone,null,$booking->Merchant) !!}
                                                </td>
                                                <td>
                                                    <a target="_blank" title="@lang("$string_file.requested_drivers")"
                                                       href="{{ route('hotel.ride-requests',$booking->id) }}"
                                                       class="btn menu-icon btn_detail action_btn"><span
                                                                class="fa fa-list-alt"></span></a>
                                                    <a target="_blank" title="@lang("$string_file.ride_details")"
                                                       href="{{ route('hotel.booking.details',$booking->id) }}"
                                                       class="btn menu-icon btn_money action_btn"><span
                                                                class="fa fa-info-circle"
                                                                title="Booking Details"></span></a>
                                                    <span data-target="#cancelbooking"
                                                          data-toggle="modal"
                                                          id="{{ $booking->id }}"><a
                                                                data-original-title="Cancel Booking"
                                                                data-toggle="tooltip"
                                                                id="{{ $booking->id }}"
                                                                data-placement="top"
                                                                class="btn btn-sm btn-danger menu-icon btn_delete action_btn"> <i
                                                                    class="fa fa-times"></i> </a></span>
                                                </td>
                                            </tr>
                                            @php $sr++ @endphp
                                        @endforeach
                                        </tbody>
                                    </div>
                                </table>
                                <div class="pagination1 float-right">{{ $bookings->links() }}</div>
                            </div>
                            <div class="tab-pane" id="exampleTabsLineTopTwo" role="tabpanel">
                                <form method="post"
                                      action="{{ route('hotel.activeride.later.serach') }}">
                                    @csrf
                                    <div class="table_search row p-3">
                                        <div class="col-sm-2 active-margin-top">
                                            @lang("$string_file.search_by") :
                                        </div>
                                        <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                            <div class="">
                                                <input id="" name="booking_id"
                                                       placeholder="@lang("$string_file.ride_id")"
                                                       class="form-control"
                                                       type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                            <div class="">
                                                <input id="" name="rider"
                                                       placeholder="@lang("$string_file.user_details")"
                                                       class="form-control"
                                                       type="text">
{{--                                                <p> @lang('admin.searchhint')</p>--}}
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                            <div class="">
                                                <input id="" name="driver"
                                                       placeholder="@lang("$string_file.driver_details")"
                                                       class="form-control"
                                                       type="text">
{{--                                                <p> @lang('admin.searchhint')</p>--}}
                                            </div>
                                        </div>
                                        <div class="col-sm-2  col-xs-12 form-group active-margin-top">
                                            <button class="btn btn-primary" type="submit"
                                                    name="seabt12"><i class="fa fa-search" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        {{--                                                            <div class="col-sm-2 active-margin-top">--}}
                                        {{--                                                                <a href="{{route('excel.ridelater')}}">--}}
                                        {{--                                                                    <button type="button"--}}
                                        {{--                                                                            class="float-right btn btn-icon btn-primary mr-1">--}}
                                        {{--                                                                        <i--}}
                                        {{--                                                                                class="fa fa-download"--}}
                                        {{--                                                                                title="@lang("$string_file.export_excel")"> </i>--}}
                                        {{--                                                                    </button>--}}
                                        {{--                                                                </a>--}}
                                        {{--                                                            </div>--}}
                                    </div>
                                </form>
                                <table class="display nowrap table table-hover table-stripedw-full" id="customDataTable2" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>@lang("$string_file.sn")</th>
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
                                        <th>@lang("$string_file.ride_time")</th>
                                        <th>@lang("$string_file.estimated")</th>
                                        <th>@lang("$string_file.payment_method")</th>
                                        <th>@lang("$string_file.date")</th>
                                        <th>@lang("$string_file.action")</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php $sr = $later_bookings->firstItem() @endphp
                                    @foreach($later_bookings as $booking)
                                        <tr>
                                            <td>
                                                {{ $sr }}
                                            </td>
                                            <td>
                                                <a class="address_link"
                                                   href="{{ route('taxicompany.booking.details',$booking->id) }}">{{ $booking->merchant_booking_id }} </a>
                                            </td>
                                            @if($bookingConfig->ride_otp == 1)
                                                <td>{{$booking->ride_otp}}</td>
                                            @endif
                                            <td style="text-align: center">
                                                @switch($booking->booking_status)
                                                    @case(1001)
                                                    @lang('admin.new_booking')

                                                    <br>
                                                    {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3) !!}
                                                    @break
                                                    @case(1012)
                                                    @lang('admin.message291')
                                                    <br>

                                                    {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3) !!}
                                                    @break
                                                    @case(1002)
                                                    @lang('admin.driver_accepted')

                                                    <br>
                                                    {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3) !!}
                                                    @break
                                                    @case(1003)
                                                    @lang('admin.driver_arrived')

                                                    <br>
                                                    {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3) !!}
                                                    @break
                                                    @case(1004)
                                                    @lang('admin.begin')

                                                    <br>
                                                    {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3) !!}
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @if(Auth::user()->demo == 1)
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
                                                                         @if(Auth::user()->demo == 1)
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
                                                {{$booking->later_booking_time }}<br>
                                                {!! convertTimeToUSERzone($booking->later_booking_date, $booking->CountryArea->timezone,null,$booking->Merchant, 2) !!}
                                            </td>
                                            <td>
                                                {{ $booking->estimate_distance }}<br>
                                                {{$booking->CountryArea->Country->isoCode . $booking->estimate_bill }}
                                            </td>
                                            <td>
                                                {{ $booking->PaymentMethod->payment_method }}
                                            </td>
                                            <td>
                                                {!! convertTimeToUSERzone($booking->created_at, $booking->CountryArea->timezone,null,$booking->Merchant) !!}
                                            </td>
                                            <td>
                                                <a target="_blank" title="@lang("$string_file.ride_details")"
                                                   href="{{ route('hotel.booking.details',$booking->id) }}"
                                                   class="btn menu-icon btn_money action_btn"><span
                                                            class="fa fa-info-circle"
                                                            title="Booking Details"></span></a>
                                                <span data-target="#cancelbooking"
                                                      data-toggle="modal"
                                                      id="{{ $booking->id }}"><a
                                                            data-original-title="Cancel Booking"
                                                            data-toggle="tooltip"
                                                            id="{{ $booking->id }}"
                                                            data-placement="top"
                                                            class="btn btn-sm btn-danger menu-icon btn_delete action_btn"> <i
                                                                class="fa fa-times"></i> </a></span>
                                            </td>
                                        </tr>
                                        @php $sr++ @endphp
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
                <form action="{{ route('hotel.cancelbooking') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <select class="form-control" name="cancel_reason_id" required>
                                <option value="">@lang('admin.select_cancel_reason')</option>
                                @foreach($cancelreasons as $cancelreason )
                                    <option value="{{ $cancelreason->id }}">{{ $cancelreason->ReasonName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <label>@lang("$string_file.additional_notes"): </label>
                        <div class="form-group">
                            <textarea class="form-control" id="title1" rows="3" name="description"
                                      placeholder="@lang("$string_file.description")"></textarea>
                        </div>
                        <input type="hidden" name="booking_id" id="booking_id" value="">

                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-primary" value="Cancel Booking">
                    </div>
                </form>
            </div>
        </div>
    </div>
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

