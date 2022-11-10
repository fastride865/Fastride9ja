@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
           @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(Auth::user('merchant')->can('create_taxi_company'))
                            <a href="{{route('excel.ridecancel',$arr_search)}}">
                                <button type="button" title="@lang("$string_file.export_excel")"
                                        class="btn btn-icon btn-primary float-right"  style="margin:10px"><i class="wb-download"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title">
                        <i class="fa-taxi" aria-hidden="true"></i>
                        @lang("$string_file.cancelled_rides")
                    </h3>
                </header>
                <div class="panel-body">
                    {!! $search_view !!}
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
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
                        @php $sr = $bookings->firstItem() @endphp
                        @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    {{ $sr }}
                                </td>
                                <td><a target="_blank" class="address_link"
                                       href="{{ route('merchant.booking.details',$booking->id) }}">{{ $booking->merchant_booking_id }}</a>
                                </td>
                                <td>
                                    @if($booking->booking_type == 1)
                                        @lang("$string_file.ride_now")
                                    @else
                                        @lang("$string_file.ride_later") <br>(
                                        {!! convertTimeToUSERzone($booking->later_booking_date, $booking->CountryArea->timezone,null,$booking->Merchant, 2) !!}<br>
                                        {{--                                                {{ $booking->later_booking_date }}--}}
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

                                <td>
                                    <a title="{{ $booking->pickup_location }}"
                                       href="https://www.google.com/maps/place/{{ $booking->pickup_location }}" class="btn btn-icon btn-success ml-20"><i class="icon wb-map"></i></a>
                                    <a title="{{ $booking->drop_location }}"
                                       href="https://www.google.com/maps/place/{{ $booking->drop_location }}" class="btn btn-icon btn-danger ml-20"><i class="icon fa-tint"></i></a>
                                </td>
                                <td>
                                    <span>
                                        {{ !empty($booking->cancel_reason_id) ? $booking->CancelReason->ReasonName  : ""}}
                                    </span>
                                </td>
                                <td>
                                    @if(!empty($arr_booking_status))
                                        {!! isset($arr_booking_status[$booking->booking_status]) ? $arr_booking_status[$booking->booking_status] : ""  !!}
                                    @endif
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
                                </td>
                            </tr>
                            @php $sr++ @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $bookings, 'data' => $arr_search])
                </div>
            </div>
        </div>
    </div>
@endsection

