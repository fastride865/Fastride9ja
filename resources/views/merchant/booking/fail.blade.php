@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
           @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="{{route('excel.ridefailed')}}" data-toggle="tooltip">
                                <button type="button" class="btn btn-icon btn-primary float-right" style="margin: 10px;">
                                    <i class="wb-download" title="@lang("$string_file.export_excel")"></i>
                                </button>
                            </a>
                        </div>
                    <h3 class="panel-title"><i class="fa-taxi" aria-hidden="true"></i>
                        @lang("$string_file.failed_rides")</h3>
                    </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('merchant.failride.search',['slug' => $url_slug]) }}" method="get">
                        <div class="table_search row ">
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
                                <th>@lang("$string_file.ride_details")</th>
                                <th>@lang("$string_file.pickup_location")</th>
                                <th>@lang("$string_file.failed_reason")</th>
                                <th>@lang("$string_file.date")</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $sr = $bookings->firstItem() @endphp
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>
                                        {{ $sr }}
                                    </td>
                                    <td>{{ $booking->id }}
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

                                    <td>
                                        {{ $booking->CountryArea->LanguageSingle == "" ? $booking->CountryArea->LanguageAny->AreaName : $booking->CountryArea->LanguageSingle->AreaName }}
                                        <br>
                                        @php
                                            $service_text = ($booking->ServiceType) ? $booking->ServiceType->serviceName : ($booking->deliveryType ? $booking->deliveryType->name : '---' );
                                        @endphp
                                        {{ $service_text }}<br>
                                        @if($booking->VehicleType) {{ $booking->VehicleType->VehicleTypeName }} @else
                                            ------- @endif</td>
                                    <td>@if(!empty($booking->pickup_location))
                                            <a title="{{ $booking->pickup_location }}"
                                               target="_blank"
                                               href="https://www.google.com/maps/place/{{ $booking->pickup_location }}"class="btn btn-icon btn-success ml-40"><i class="icon wb-map"></i></a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($booking->failreason == 1)
                                            @lang("$string_file.configuration_not_found")
                                        @else
                                            @lang("$string_file.driver_not_found")
                                        @endif
                                    </td>
                                    <td>
                                        {!! convertTimeToUSERzone($booking->created_at, $booking->CountryArea->timezone,null,$booking->Merchant) !!}
{{--                                        {{ $booking->created_at->toDateString() }}--}}
{{--                                        <br>--}}
{{--                                        {{ $booking->created_at->toTimeString() }}--}}
                                    </td>
                                </tr>
                                @php $sr++ @endphp
                            @endforeach
                            </tbody>
                        </table>
                    @include('merchant.shared.table-footer', ['table_data' => $bookings, 'data' => $data])
{{--                    <div class="pagination1 float-right">{{ $bookings->appends($data)->links() }}</div>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection