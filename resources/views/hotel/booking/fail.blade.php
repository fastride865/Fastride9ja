@extends('hotel.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('noridefailedexport'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message452')
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('excel.ridefailed')}}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-primary float-right" style="margin: 10px;">
                                <i class="wb-download" title="@lang("$string_file.export_excel")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        @lang('admin.message55')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="post" action="{{ route('merchant.failride.search') }}">
                        @csrf
                        <div class="table_search">
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="booking_id"
                                           placeholder="@lang("$string_file.ride_id")"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="rider"
                                           placeholder="@lang("$string_file.enter_text")"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>
                            <div class="col-sm-2  col-xs-12 form-group ">
                                <button class="btn btn-primary" type="submit" name="seabt12"><i
                                            class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <table class="display nowrap table table-hover table-stripedw-full" id="customDataTable" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.ride_type")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.service_type")</th>
                            <th>@lang("$string_file.vehicle_type")</th>
                            <th>@lang("$string_file.pickup_location")</th>
                            <th>@lang("$string_file.drop_off_location")</th>
                            <th>@lang("$string_file.failed_reason")</th>
                            <th>@lang("$string_file.created_at")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td><a target="_blank" class="address_link"
                                       href="{{ route('merchant.booking.details',$booking->id) }}">#{{ $booking->id }}</a>
                                </td>
                                <td>
                                    @if($booking->booking_type == 1)
                                        @lang("$string_file.ride_now")
                                    @else
                                        @lang("$string_file.ride_later")
                                    @endif
                                </td>
                                <td>
                                    {{ $booking->User->UserName }}
                                    <br>
                                    {{ $booking->User->UserPhone }}
                                    <br>
                                    {{ $booking->User->email }}
                                </td>

                                <td> {{ $booking->CountryArea->LanguageSingle == "" ? $booking->CountryArea->LanguageAny->AreaName : $booking->CountryArea->LanguageSingle->AreaName }}</td>
                                <td> {{ $booking->ServiceType->serviceName }}</td>
                                <td> {{ $booking->VehicleType->LanguageVehicleTypeSingle == "" ? $booking->VehicleType->LanguageVehicleTypeAny->vehicleTypeName : $booking->VehicleType->LanguageVehicleTypeSingle->vehicleTypeName }}</td>
                                <td><a class="map_address address_link" target="_blank"
                                       href="https://www.google.com/maps/place/{{ $booking->pickup_location }}">{{ $booking->pickup_location }}</a>
                                </td>
                                <td><a class="map_address address_link" target="_blank"
                                       href="https://www.google.com/maps/place/{{ $booking->drop_location }}">{{ $booking->drop_location }}</a>
                                </td>
                                <td>
                                    @if($booking->failreason == 1)
                                        @lang("$string_file.configuration_not_found")
                                    @else
                                        @lang("$string_file.driver_not_found")
                                    @endif
                                </td>
                                <td>
                                    {{ $booking->created_at->toformatteddatestring() }}
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

