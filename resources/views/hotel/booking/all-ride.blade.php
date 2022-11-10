@extends('hotel.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <h3 class="panel-title"><i class="fa-taxi" aria-hidden="true"></i>
                        @lang("$string_file.all_rides")</h3>
                    </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('hotel.allrides.search') }}" method="get">
                        <div class="table_search row">
                            <div class="col-md-1 col-xs-12 form-group active-margin-top">
                                @lang("$string_file.search_by"):
                            </div>
                            <div class="col-md-1 col-xs-12 form-group active-margin-top">
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
                                    <input type="text" id="" name="driver"
                                           placeholder="@lang("$string_file.driver_details")"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
{{--                                <p>@lang('admin.searchhint')</p>--}}
                            </div>

                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="input-group">
                                    <input type="text" id="" name="date"
                                           placeholder="@lang("$string_file.ride")  @lang("$string_file.date")"
                                           class="form-control col-md-12 col-xs-12 customDatePicker2"
                                           id="datepickersearch" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="">
                                    {!! Form::select("booking_status",add_blank_option($arr_status,trans("$string_file.ride_status")),NULL,['class'=>'form-control']) !!}
{{--                                    <select class="form-control" name="booking_status" id="booking_status">--}}
{{--                                        <option value="">@lang('admin.serachBy')</option>--}}
{{--                                        <option value="1001">@lang('admin.new_booking')</option>--}}
{{--                                        <option value="1002"> @lang('admin.message291')</option>--}}
{{--                                        <option value="1012">@lang('admin.driver_accepted')</option>--}}
{{--                                        <option value="1003">@lang('admin.driver_arrived')</option>--}}
{{--                                        <option value="1004">@lang("$string_file.phone")</option>--}}
{{--                                        <option value="1005">@lang('admin.completedBooking')</option>--}}
{{--                                        <option value="1006">@lang('admin.message48')</option>--}}
{{--                                        <option value="1007">@lang('admin.message49')</option>--}}
{{--                                        <option value="1008">@lang('admin.message50')</option>--}}
{{--                                        <option value="1016">@lang('admin.autoCancel')</option>--}}
{{--                                    </select>--}}
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
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.ride_id")</th>
                            <th>@lang("$string_file.ride_type")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.service_detail")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.pickup_drop")</th>
{{--                            <th>@lang("$string_file.pickup_location")</th>--}}
{{--                            <th>@lang("$string_file.drop_off_location")</th>--}}
                            <th>@lang("$string_file.current_status")</th>
                            <th>@lang("$string_file.payment")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $bookings->firstItem() @endphp
                        @foreach($bookings as $booking)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $booking->merchant_booking_id }}</td>
                                <td>
                                    @if($booking->booking_type == 1)
                                        @lang("$string_file.ride_now")
                                    @else
                                        @lang("$string_file.ride_later")(
                                        {!! convertTimeToUSERzone($booking->later_booking_date, $booking->CountryArea->timezone,null,$booking->Merchant, 2) !!}<br>
                                        {{$booking->later_booking_time }} )
                                    @endif
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
                                                                 {{ '********'.substr($booking->Driver->phoneNumber,-2) }}
                                                                 <br>
                                                                 {{ '********'.substr($booking->Driver->email,-2) }}
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
                                    <br>
                                    @php
                                        $service_text = ($booking->ServiceType) ? $booking->ServiceType->serviceName : $booking->deliveryType->name ;
                                    @endphp
                                    {{ $service_text }} <br>
                                    {{ $booking->VehicleType->VehicleTypeName }}
                                </td>
                                <td> {{ $booking->CountryArea->CountryAreaName }}</td>
                                <td>
                                    <a title="{{ $booking->pickup_location }}"
                                       href="https://www.google.com/maps/place/{{ $booking->pickup_location }}" class="btn btn-icon btn-success ml-20"><i class="icon wb-map"></i></a>
                                    <a title="{{ $booking->drop_location }}"
                                       href="https://www.google.com/maps/place/{{ $booking->drop_location }}" class="btn btn-icon btn-danger ml-20"><i class="icon fa-tint"></i></a>
                                </td>
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
                                        @lang("$string_file.ride_accepted")

                                        <br>
                                        {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3) !!}
                                        @break
                                        @case(1003)
                                        @lang('admin.driverArrived')
                                        <br>
                                        {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3) !!}
                                        @break
                                        @case(1004)
                                        @lang('admin.begin')
                                        <br>
                                        {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3) !!}
                                        @break
                                        @case(1005)
                                        @lang('admin.completedBooking')
                                        <br>
                                        {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3) !!}
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
                                        @case(1016)
                                        @lang('admin.autoCancel')
                                        <br>
                                        {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant,3) !!}
                                        @break
                                        @case(1018)
                                        @lang('admin.driver-no-show')
                                        @break
                                    @endswitch
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
                                       class="btn btn-sm btn-success menu-icon btn_detail action_btn"><span
                                                class="fa fa-list-alt"></span></a>

                                    <a target="_blank" title="@lang("$string_file.ride_details")"
                                       href="{{ route('hotel.booking.details',$booking->id) }}"
                                       class="btn btn-sm btn-info menu-icon btn_money action_btn"><span
                                                class="fa fa-info-circle"
                                                title="Booking Details"></span></a>

                                    <a target="_blank" title="@lang('"$string_file.invoice"')"
                                       href="{{ route('hotel.booking.invoice',$booking->id) }}"
                                       class="btn btn-sm btn-warning menu-icon btn_eye action_btn"><span
                                                class="fa fa-print"></span></a>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1 float-right">{{ $bookings->appends($data)->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection

