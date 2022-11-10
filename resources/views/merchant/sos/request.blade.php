@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        @if(Auth::user('merchant')->can('view_sos_request'))
                            <a href="{{route('excel.sosrequests')}}">
                                <button type="button" class="btn btn-icon btn-primary mr-1 float-right"
                                        style="margin:10px"
                                        data-original-title="@lang("$string_file.export_excel")"
                                        data-toggle="tooltip"><i
                                            class="fa fa-download"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.sos_request")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('merchant.sos.sreach') }}">
                        <div class="table_search">
                            <div class="row">
                                <div class="col-md-2 form-group ">
                                    <div class="input-group">
                                        <select class="form-control" name="application"
                                                id="application">
                                            <option value="">--@lang("$string_file.application")--</option>
                                            <option value="2">@lang("$string_file.driver")</option>
                                            <option value="1">@lang("$string_file.user")</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="rider"
                                               placeholder="@lang("$string_file.user_details")"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-md-2 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="driver"
                                               placeholder="@lang("$string_file.driver_details")"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-md-2 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="date"
                                               placeholder="@lang("$string_file.date")"
                                               class="form-control col-md-12 col-xs-12 datepickersearch"
                                               id="datepickersearch">
                                    </div>
                                </div>
                                <div class="col-sm-2 form-group ">
                                    <button class="btn btn-primary" type="submit" name="seabt12"><i
                                                class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.application")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.services")</th>
                            <th>@lang("$string_file.vehicle_type")</th>
                            <th>@lang("$string_file.number")</th>
                            <th>@lang("$string_file.sos_location")</th>
                            <th>@lang("$string_file.request_time")</th>
                            <th>@lang("$string_file.date")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $sosRequests->firstItem() @endphp
                        @foreach($sosRequests as $sosRequest)
                            <tr>
                                <td><a target="_blank" class="address_link"
                                       href="{{ route('merchant.booking.details',$sosRequest->booking_id) }}">#{{ $sr }}</a>
                                </td>
                                @switch($sosRequest->application)
                                    @case(1)
                                    <td>@lang("$string_file.user")</td>
                                    @break
                                    @case(2)
                                    <td>@lang("$string_file.driver")</td>
                                    @break
                                @endswitch

                                @if(Auth::user()->demo == 1)
                                    <td>
                                        {{ "********".substr($sosRequest->Booking->User->UserName, -2) }}
                                        <br>
                                        {{ "********".substr($sosRequest->Booking->User->UserPhone, -2) }}
                                        <br>
                                        {{ "********".substr($sosRequest->Booking->User->email, -2) }}
                                    </td>
                                    <td>
                                        @if($sosRequest->Booking->driver_id)
                                            {{ "********".substr($sosRequest->Booking->Driver->last_name, -2) }}
                                            <br>
                                            {{ "********".substr($sosRequest->Booking->Driver->phoneNumber, -2) }}
                                            <br>
                                            {{ "********".substr($sosRequest->Booking->Driver->email, -2) }}
                                        @else
                                            No Driver
                                        @endif
                                    </td>
                                @else
                                    <td>
                                        {{ $sosRequest->Booking->User->UserName }}
                                        <br>
                                        {{ $sosRequest->Booking->User->UserPhone }}
                                        <br>
                                        {{ $sosRequest->Booking->User->email }}
                                    </td>
                                    <td>
                                        @if($sosRequest->Booking->driver_id)
                                            {{ $sosRequest->Booking->Driver->first_name." ".$sosRequest->Booking->Driver->last_name }}
                                            <br>
                                            {{ $sosRequest->Booking->Driver->phoneNumber }}
                                            <br>
                                            {{ $sosRequest->Booking->Driver->email }}
                                        @else
                                            No Driver
                                        @endif
                                    </td>
                                @endif

                                <td> {{ $sosRequest->Booking->CountryArea->LanguageSingle == "" ? $sosRequest->Booking->CountryArea->LanguageAny->AreaName : $sosRequest->Booking->CountryArea->LanguageSingle->AreaName }}</td>
                                <td> {{ $sosRequest->Booking->ServiceType->serviceName }}</td>
                                <td> {{ $sosRequest->Booking->VehicleType->LanguageVehicleTypeSingle == "" ? $sosRequest->Booking->VehicleType->LanguageVehicleTypeAny->vehicleTypeName : $sosRequest->Booking->VehicleType->LanguageVehicleTypeSingle->vehicleTypeName }}</td>

                                <td>{{ $sosRequest->number }}</td>
                                <td><a class="map_address address_link" target="_blank"
                                       href="https://www.google.com/maps/place/{{ $sosRequest->latitude }},{{ $sosRequest->longitude }}">{{ $sosRequest->latitude }}
                                        ,{{ $sosRequest->longitude }}</a>
                                </td>
                                <td>{!! convertTimeToUSERzone($sosRequest->created_at, $sosRequest->CountryArea->timezone, null, $sosRequest->Merchant) !!}</td>
                                <td>{!! convertTimeToUSERzone($sosRequest->Booking->created_at, $sosRequest->CountryArea->timezone, null, $sosRequest->Merchant, 2) !!}</td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1 float-right">{{ $sosRequests->appends($data)->links() }}</div>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection