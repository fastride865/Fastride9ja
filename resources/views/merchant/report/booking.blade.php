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
                                <a href="{{route('excel.bookingreport',$data)}}">
                                    <button type="button" class="btn btn-icon btn-primary mr-1 float-right" style="margin:10px" data-original-title="@lang("$string_file.export_excel")" data-toggle="tooltip"><i
                                                class="fa fa-download"></i>
                                    </button>
                                </a>
                            @endif
                        </div>
                    <h3 class="panel-title">
                        <i class=" wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.ride_time_report")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('report.booking.search') }}" method="get">
                        <div class="table_search">
                            <div class="row">
                                <div class="col-md-2 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="booking_id"  @if(!empty($data) && isset($data['booking_id'])) value="{{$data['booking_id']}}" @endif
                                               placeholder="@lang("$string_file.ride_id")"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-md-3 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="rider"  @if(!empty($data) && isset($data['rider'])) value="{{$data['rider']}}"  @endif
                                               placeholder="@lang("$string_file.user_details")"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-md-3 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="driver"  @if(!empty($data) && isset($data['driver'])) value="{{$data['driver']}}"  @endif
                                               placeholder="@lang("$string_file.driver_details")"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-md-2 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="date"  @if(!empty($data) && isset($data['date'])) value="{{$data['date']}}"  @endif
                                               placeholder="@lang("$string_file.ride")  @lang("$string_file.date")" readonly
                                               class="form-control col-md-12 col-xs-12 datepickersearch bg-this-color"
                                               id="datepickersearch">
                                    </div>
                                </div>
                                <div class="col-sm-2 form-group ">
                                    <button class="btn btn-primary" type="submit" name="seabt12"><i
                                                class="wb-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.ride_id")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.pickup_drop")</th>
                            <th>@lang("$string_file.ride_time")</th>
                            <th>@lang("$string_file.accepted_at")</th>
                            <th>@lang("$string_file.arrived_at")</th>
                            <th>@lang("$string_file.started_at")</th>
                            <th>@lang("$string_file.completed_at")</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bookings as $booking)
                            @php ($booking->CountryArea['timezone']); @endphp
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
                                <td>
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
                                    {{ date("Y-m-d H:i:s",$booking->BookingDetail->accept_timestamp) }}
                                    <br>
                                    @lang("$string_file.at")
                                    <br>
                                    <a class="map_address" target="_blank"
                                       href="https://www.google.com/maps/place/{{ $booking->BookingDetail->accept_latitude }},{{ $booking->BookingDetail->accept_longitude }}">{{ $booking->BookingDetail->accept_latitude }}
                                        ,<br>{{ $booking->BookingDetail->accept_longitude }}</a>
                                </td>
                                <td>
                                    {{ date("Y-m-d H:i:s",$booking->BookingDetail->arrive_timestamp) }}
                                    <br>
                                    @lang("$string_file.at")
                                    <br>
                                    <a class="map_address" target="_blank"
                                       href="https://www.google.com/maps/place/{{ $booking->BookingDetail->arrive_latitude }},{{ $booking->BookingDetail->arrive_longitude }}">{{ $booking->BookingDetail->arrive_latitude }}
                                        ,<br>{{ $booking->BookingDetail->arrive_longitude }}</a>
                                </td>
                                <td>
                                    {{ date("Y-m-d H:i:s",$booking->BookingDetail->start_timestamp) }}
                                    <br>
                                    @lang("$string_file.at")
                                    <br>
                                    <a class="map_address" target="_blank"
                                       href="https://www.google.com/maps/place/{{ $booking->BookingDetail->start_latitude }},{{ $booking->BookingDetail->start_longitude }}">{{ $booking->BookingDetail->start_latitude }}
                                        ,<br>{{ $booking->BookingDetail->start_longitude }}</a>
                                </td>
                                <td>
                                    {{ date("Y-m-d H:i:s",$booking->BookingDetail->end_timestamp) }}
                                    <br>
                                    @lang("$string_file.at")
                                    <br>
                                    <a class="map_address" target="_blank"
                                       href="https://www.google.com/maps/place/{{ $booking->BookingDetail->end_latitude }},{{ $booking->BookingDetail->end_longitude }}">{{ $booking->BookingDetail->end_latitude }}
                                        ,<br>{{ $booking->BookingDetail->end_longitude }}</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $bookings, 'data' => $data])
{{--                    <div class="pagination1 float-right">{{ $bookings->appends($data)->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection

