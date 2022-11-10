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
                        <a href="{{route('merchant.driver.allvehicles')}}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.all_vehicles") "></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-taxi" aria-hidden="true"></i>
                        @lang("$string_file.rejected_vehicle") </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th> @lang("$string_file.id")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.vehicle_number")</th>
                            <th>@lang("$string_file.vehicle_type") </th>
                            <th>@lang("$string_file.services")</th>
                            <th>@lang("$string_file.reject_reason")</th>
                            <th>@lang("$string_file.image")</th>
                            <th>@lang("$string_file.number_plate")</th>
                            <th>@lang("$string_file.registered_date")</th>
                            <th>@lang("$string_file.update")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sn = 1; @endphp
                        @if(count($vehicles) > 0)
                            @foreach($vehicles as $vehicle)
                                <tr>
                                    <td>
                                        <a href="{{ route('driver.show',$vehicle->Driver->id) }}"
                                           class="address_link">{{ $sn }}</a>
                                    </td>
                                    <td>
                                        @if(Auth::user()->demo == 1)
                                            {{ "********".substr($vehicle->Driver->last_name, -2) }}<br>
                                            {{ "********".substr($vehicle->Driver->phoneNumber, -2) }}
                                            <br>
                                            {{ "********".substr($vehicle->Driver->email, -2) }}
                                        @else
                                            {{ $vehicle->Driver->first_name." ".$vehicle->Driver->last_name }}<br>
                                            {{ $vehicle->Driver->email }}<br>
                                            {{ $vehicle->Driver->phoneNumber }}
                                        @endif
                                    </td>
                                    <td>{{ $vehicle->vehicle_number }}</td>
                                    <td>
                                        {{ $vehicle->VehicleType->VehicleTypeName}}
                                    </td>
                                    <td class="text-center"> <span class="long_text">
                                                {{ implode(',',array_pluck($vehicle->ServiceTypes,'serviceName')) }}
                                            </span></td>
                                    <td>{{ $vehicle->Driver->admin_msg }}</td>
                                    <td class="text-center">
                                        <a target="_blank"
                                           href="{{ get_image($vehicle->vehicle_image,'vehicle_document') }}">
                                            <img src="{{ get_image($vehicle->vehicle_image,'vehicle_document') }}"
                                                 alt="avatar"
                                                 style="width: 80px;height: 80px;border-radius:10px;">
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a target="_blank"
                                           href="{{ get_image($vehicle->vehicle_number_plate_image,'vehicle_document') }}">
                                            <img src="{{ get_image($vehicle->vehicle_number_plate_image,'vehicle_document') }}"
                                                 alt="avatar"
                                                 style="width: 80px;height: 80px;border-radius:10px;">
                                        </a>
                                    </td>
                                    <td>
                                        {!! convertTimeToUSERzone($vehicle->created_at, $vehicle->Driver->CountryArea->timezone, null, $vehicle->Driver->Merchant) !!}
                                    </td>
                                    <td>
                                        {!! convertTimeToUSERzone($vehicle->updated_at, $vehicle->Driver->CountryArea->timezone, null, $vehicle->Driver->Merchant) !!}
                                    </td>
                                </tr>
                                @php $sn++; @endphp
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $vehicles, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
