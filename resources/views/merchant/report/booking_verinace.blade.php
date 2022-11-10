@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                        <div class="panel-actions">
                            @if(Auth::user()->demo == 1)
                                <a href="">
                                    <button type="button" class="btn btn-icon btn-primary mr-1 float-right" style="margin:10px" data-original-title="@lang("$string_file.export_excel")" data-toggle="tooltip"><i
                                                class="fa fa-download"></i>
                                    </button>
                                </a>
                            @else
                                <a href="{{route('excel.bookingvariancereport',$data)}}">
                                    <button type="button" class="btn btn-icon btn-primary mr-1 float-right" style="margin:10px" data-original-title="@lang("$string_file.export_excel")" data-toggle="tooltip"><i
                                                class="fa fa-download"></i>
                                    </button>
                                </a>
                            @endif
                        </div>
                    <h3 class="panel-title">
                        <i class=" wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.ride_variance_report")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('report.bookingVariance.search') }}">
                        <div class="table_search">
                            <div class="row">
                                <div class="col-md-2 col-xs-12 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="booking_id" @if(!empty($data) && isset($data['booking_id'])) value="{{$data['booking_id']}}" @endif
                                               placeholder="@lang("$string_file.ride_id")"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-md-3 col-xs-12 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="rider" @if(!empty($data) && isset($data['rider'])) value="{{$data['rider']}}" @endif
                                               placeholder="@lang("$string_file.user_details")"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-md-3 col-xs-12 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="driver" @if(!empty($data) && isset($data['driver'])) value="{{$data['driver']}}" @endif
                                               placeholder="@lang("$string_file.driver_details")"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>

                                <div class="col-md-2 col-xs-12 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="date" @if(!empty($data) && isset($data['date'])) value="{{$data['date']}}" @endif
                                               placeholder="@lang("$string_file.ride")  @lang("$string_file.date")" readonly
                                               class="form-control col-md-12 col-xs-12 datepickersearch bg-this-color"
                                               id="datepickersearch">
                                    </div>
                                </div>
                                <div class="col-sm-2  col-xs-12 form-group ">
                                    <button class="btn btn-primary" type="submit" name="seabt12"><i
                                                class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang("$string_file.ride_id")</th>
                                <th>@lang("$string_file.user_details")</th>
                                <th>@lang("$string_file.driver_details")</th>
                                <th>@lang("$string_file.pickup_drop")</th>
                                <th>@lang("$string_file.ride_time")</th>
                                <th>@lang("$string_file.estimate_time")</th>
                                <th>@lang("$string_file.actual_time")</th>
                                <th>@lang("$string_file.estimate_distance")</th>
                                <th>@lang("$string_file.actual_distance")</th>
                                <th>@lang("$string_file.estimate_amount")</th>
                                <th>@lang("$string_file.actual_amount")</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->merchant_booking_id }}</td>
                                    @if(Auth::user()->demo == 1)
                                        <td>
                                            {{ "********".substr($booking->User->UserName, -2) }}
                                            <br>
                                            {{"********".substr( $booking->User->UserPhone, -2) }}
                                            <br>
                                            {{ "********".substr($booking->User->email, -2) }}
                                        </td>
                                        <td>
                                            {{ "********".substr($booking->Driver->fullName, -2) }}
                                            <br>
                                            {{ "********".substr($booking->Driver->phoneNumber, -2) }}
                                            <br>
                                            {{ "********".substr($booking->Driver->email, -2) }}
                                        </td>
                                    @else
                                        <td>
                                            {{ $booking->User->UserName }}
                                            <br>
                                            {{ $booking->User->UserPhone }}
                                            <br>
                                            {{ $booking->User->email }}
                                        </td>
                                        <td>
                                            {{ $booking->Driver->fullName }}
                                            <br>
                                            {{ $booking->Driver->phoneNumber }}
                                            <br>
                                            {{ $booking->Driver->email }}
                                        </td>
                                    @endif
                                    <td >
{{--                                        <a href="#" class="map_address">--}}
{{--                                            {{ $booking->BookingDetail->start_location }}--}}
{{--                                            <br>--}}
{{--                                            To--}}
{{--                                            <br>--}}
{{--                                            {{ $booking->BookingDetail->end_location }}--}}
{{--                                        </a>--}}

                                        <a title="{{ $booking->BookingDetail->start_location }}"
                                           href="https://www.google.com/maps/place/{{ $booking->BookingDetail->start_location }}" class="btn btn-icon btn-success ml-20"><i class="icon wb-map"></i>
                                        </a>
                                        {{--                                        <br>--}}
                                        {{--                                        To--}}
                                        {{--                                        <br>--}}
                                        <a title="{{ $booking->BookingDetail->end_location }}"
                                           href="https://www.google.com/maps/place/{{ $booking->BookingDetail->end_location }}" class="btn btn-icon btn-danger ml-40"><i class="icon fa-tint"></i>
                                        </a>

                                    </td>
                                    <td>{{ $booking->created_at->toDateString() }}
                                    <br>
                                    {{ $booking->created_at->toTimeString() }}</td>
                                    <td>
                                        {{ $booking->estimate_time }}
                                    </td>
                                    <td>
                                        {{ $booking->travel_time_min .trans("$string_file.min") }}
                                    </td>
                                    <td>
                                        {{ $booking->estimate_distance }}
                                    </td>
                                    <td>
                                        {{ $booking->travel_distance }}
                                    </td>
                                    <td>
                                        {{ $booking->CountryArea->Country->isoCode." ".$booking->estimate_bill }}
                                    </td>
                                    <td>
                                        {{ $booking->CountryArea->Country->isoCode." ".$booking->final_amount_paid }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @include('merchant.shared.table-footer', ['table_data' => $bookings, 'data' => $data])
                </div>
            </div>
        </div>
    </div>
@endsection

