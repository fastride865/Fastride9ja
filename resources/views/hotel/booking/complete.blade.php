@extends('hotel.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title"><i class="fa fa-car" aria-hidden="true"></i>
                        @lang("$string_file.completed_rides") </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="post" action="{{ route('hotel.completeride.search') }}">
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
                                           class="form-control col-md-12 col-xs-12 datepickersearch"
                                           id="datepickersearch" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-2  col-xs-12 form-group active-margin-top">
                                <button class="btn btn-primary" type="submit" name="seabt12"><i
                                            class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.ride_id")</th>
                            <th>@lang("$string_file.ride_type")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.request_from")</th>
                            <th>@lang("$string_file.ride_details")</th>
                            <th>@lang("$string_file.pickup_drop")</th>
{{--                            <th>@lang("$string_file.pickup_location")</th>--}}
{{--                            <th>@lang("$string_file.drop_off_location")</th>--}}
                            <th>@lang("$string_file.payment_method")</th>
                            <th>@lang("$string_file.bill_amount")</th>
                            <th>@lang("$string_file.date")</th>
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
                                        @lang("$string_file.ride")  @lang("$string_file.later") (
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
                                                         {{ "********".substr($booking->Driver->last_name,-2) }}
                                                        <br>
                                                       {{ "********".substr($booking->Driver->phoneNumber,-2) }}
                                                        <br>
                                                        {{ "********".substr($booking->Driver->email,-2) }}
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
                                                         {{ $booking->Driver->first_name.' '.$booking->Driver->last_name }}
                                                        <br>
                                                        {{ $booking->Driver->phoneNumber }}
                                                        <br>
                                                        {{ $booking->Driver->email }}
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

                                <td>
                                    @if(!empty($booking->BookingDetail->start_location))
                                        <a title="{{ $booking->BookingDetail->start_location }}"
                                           target="_blank"
                                           href="https://www.google.com/maps/place/{{ $booking->BookingDetail->start_location }}"class="btn btn-icon btn-success ml-20"><i class="icon wb-map"></i></a>
                                    @endif
                                    @if(!empty($booking->BookingDetail->end_location))
                                        <a title="{{ $booking->BookingDetail->end_location }}"
                                            target="_blank"
                                           href="https://www.google.com/maps/place/{{ $booking->BookingDetail->end_location }}"class="btn btn-icon btn-danger ml-20"><i class="icon fa-tint"></i></a>
                                    @endif
                                </td>
                                <td>
                                    {{ $booking->PaymentMethod->payment_method }}
                                </td>
                                <td>
                                    {{ $booking->CountryArea->Country->isoCode . $booking->final_amount_paid }}
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($booking->created_at, $booking->CountryArea->timezone,null,$booking->Merchant) !!}
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

                                    <a target="_blank" title="@lang('"$string_file.invoice"')"
                                       href="{{ route('merchant.booking.invoice',$booking->id) }}"
                                       class="btn btn-sm btn-warning menu-icon btn_eye action_btn"><span
                                                class="fa fa-print"></span></a>
                                </td>
                            </tr>
                            @php $sr++ @endphp
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1 float-right">{{ $bookings->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection

