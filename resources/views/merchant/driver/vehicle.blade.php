@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('driver.index') }}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.all_drivers")"></i>
                            </button>
                        </a>
                        <a href="{{ route('merchant.driver.vehicle.create', [$driver->id]) }}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-plus" title="@lang("$string_file.add_vehicle")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        {{ $driver->first_name." ".$driver->last_name }}'s @lang("$string_file.vehicle")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <!-- Task List table -->
                    <!-- <table id="users-contacts" -->
                    <!-- class="table table-responsive table-white-space table-bordered row-grouping display no-wrap icheck table-middle"> -->
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.vehicle_type") </th>
                            <th>@lang("$string_file.services")</th>
                            <th>@lang("$string_file.vehicle_number")</th>
                            <th>@lang("$string_file.color")</th>
                            <th>@lang("$string_file.image")</th>
                            <th>@lang("$string_file.number_plate")</th>
                            @if($vehicle_model_expire == 1)
                            <th>@lang("$string_file.registered_date")</th>
                            <th>@lang("$string_file.expire_date")</th>
                            @endif
                            <th>@lang("$string_file.action")</th>
                            <th>@lang("$string_file.created_at") </th>`
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1 @endphp
                        @foreach($driver->DriverVehicles as $vehicle)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td class="text-center">
                                    @if($vehicle->VehicleType->LanguageVehicleTypeSingle) {{ $vehicle->VehicleType->LanguageVehicleTypeSingle->vehicleTypeName }} @else  {{ $vehicle->VehicleType->LanguageVehicleTypeAny->vehicleTypeName }} @endif
                                </td>
                                <?php $a = array() ?>
                                @foreach($vehicle->ServiceTypes as $serviceType)
                                    <?php $a[] = $serviceType->serviceName; ?>
                                @endforeach
                                <td class="text-center">
                                    {{ implode(',',$a) }}
                                </td>
                                <td class="text-center">
                                    {{ $vehicle->vehicle_number }}
                                </td>
                                <td class="text-center">
                                    {{ $vehicle->vehicle_color }}
                                </td>
                                <td class="text-center">
                                    <img src="{{ get_image($vehicle->vehicle_image,'vehicle_document') }}"
                                         alt="avatar" style="width: 100px;height: 100px;">
                                </td>
                                <td class="text-center">
                                    <img src="{{ get_image($vehicle->vehicle_number_plate_image,'vehicle_document') }}"
                                         alt="avatar" style="width: 100px;height: 100px;">
                                </td>
                                @if($vehicle_model_expire == 1)
                                    <td>
                                        {!! convertTimeToUSERzone($vehicle->vehicle_register_date, $driver->CountryArea->timezone,null,$driver->Merchant, 2) !!}
                                    </td>
                                    <td>
                                        {!! convertTimeToUSERzone($vehicle->vehicle_expire_date, $driver->CountryArea->timezone,null,$driver->Merchant, 2) !!}
                                    </td>
                                @endif
                                <td class="text-center">
                                    <a href="{{ route('merchant.driver-vehicledetails',$vehicle->id) }}"
                                       class="btn btn-sm btn-info menu-icon btn_detail action_btn"><span
                                                class="fa fa-list-alt"
                                                data-original-title="@lang("$string_file.vehicle_details")"
                                                data-toggle="tooltip"></span>
                                    </a>
                                    @if(Auth::user('merchant')->can('edit_vehicle'))
                                        <a href="{{ route('merchant.driver.vehicle.create',[$vehicle->driver_id,$vehicle->id]) }}"
                                           data-original-title="@lang("$string_file.edit_vehicle") "
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                            <i class="fa fa-edit"></i> </a>
                                    @endif

                                </td>
                                <td class="text-center">
                                    {!! convertTimeToUSERzone($vehicle->created_at, $driver->CountryArea->timezone,null,$driver->Merchant) !!}
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection