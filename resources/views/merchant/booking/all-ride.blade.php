@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                            <a href="{{route('excel.allrides',$arr_search)}}" >
                                <button type="button" data-toggle="tooltip" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                    <i class="wb-download" title="@lang("$string_file.export_excel")"></i>
                                </button>
                            </a>
                        </div>
                    <h3 class="panel-title"><i class="fa-taxi" aria-hidden="true"></i>
                        @lang("$string_file.all_rides")</h3>
                    </header>
                <div class="panel-body container-fluid">
                    {!! $search_view !!}
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
                            <th>@lang("$string_file.current_status")</th>
                            <th>@lang("$string_file.payment")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $bookings->firstItem() ;
                        $status_keys = array_keys($arr_booking_status);
                        @endphp
                        @foreach($bookings as $booking)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $booking->merchant_booking_id }}</td>
                                <td>
                                    @if($booking->booking_type == 1)
                                        @lang("$string_file.ride_now")
                                    @else
                                        @lang("$string_file.ride_later")<br>(
                                        {!! date(getDateTimeFormat($booking->Merchant->datetime_format,2),strtotime($booking->later_booking_date)) !!}
{{--                                        {!! convertTimeToUSERzone($booking->later_booking_date, $booking->CountryArea->timezone,null,$booking->Merchant, 2) !!}--}}<br>
                                        <br>
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
                                    @if(!empty($arr_booking_status))
                                        {!! isset($arr_booking_status[$booking->booking_status]) ? $arr_booking_status[$booking->booking_status] : ""  !!}
                                        <br>
                                        @lang("$string_file.at") {!! convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant, 3) !!}
                                    @endif
                                </td>
                                <td>
                                    {{ $booking->PaymentMethod->payment_method }}
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($booking->created_at, $booking->CountryArea->timezone,null,$booking->Merchant) !!}
{{--                                    {{ $booking->created_at->toDateString() }}--}}
{{--                                    <br>--}}
{{--                                    {{ $booking->created_at->toTimeString() }}--}}
                                </td>
                                <td>
                                    <a target="_blank" title="@lang("$string_file.requested_drivers")"
                                       href="{{ route('merchant.ride-requests',$booking->id) }}"
                                       class="btn btn-sm btn-success menu-icon btn_detail action_btn"><span
                                                class="fa fa-list-alt"></span></a>

                                    <a target="_blank" title="@lang("$string_file.ride_details")"
                                       href="{{ route('merchant.booking.details',$booking->id) }}"
                                       class="btn btn-sm btn-info menu-icon btn_money action_btn"><span
                                                class="fa fa-info-circle"
                                                title="Booking Details"></span></a>

                                    @if($booking->booking_status == 1002 || $booking->booking_status == 1003 || $booking->booking_status == 1004 || $booking->booking_status == 1005)
                                    <a target="_blank" title="@lang("$string_file.invoice")"
                                       href="{{ route('merchant.booking.invoice',$booking->id) }}"
                                       class="btn btn-sm btn-warning menu-icon btn_eye action_btn"><span
                                                class="fa fa-print"></span></a>
                                    @endif
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $bookings, 'data' => $arr_search])
{{--                    <div class="pagination1 float-right">{{ $bookings->appends($data)->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection

