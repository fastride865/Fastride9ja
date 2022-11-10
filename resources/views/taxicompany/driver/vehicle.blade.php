@extends('taxicompany.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('taxicompany.driver.index') }}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.all_drivers")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        {{ $driver->first_name." ".$driver->last_name }}'s @lang("$string_file.vehicle") </h3>
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
                            <th>@lang("$string_file.service_type")</th>
                            <th>@lang("$string_file.vehicle_number")</th>
                            <th>@lang("$string_file.color")</th>
                            <th>@lang("$string_file.image")</th>
                            <th>@lang("$string_file.number_plate")</th>
                            {{--<th>@lang("$string_file.vehicle_details")</th>
                            <th>@lang("$string_file.action")</th>--}}
                            <th>@lang("$string_file.created_at")</th>
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
                                    <img src="{{ get_image($vehicle->vehicle_image,'vehicle_document', $vehicle->merchant_id) }}"
                                         alt="avatar" style="width: 100px;height: 100px;">
                                </td>
                                <td class="text-center">
                                    <img src="{{ get_image($vehicle->vehicle_number_plate_image,'vehicle_document', $vehicle->merchant_id) }}"
                                         alt="avatar" style="width: 100px;height: 100px;">
                                </td>
                                {{--<td class="text-center">
                                    <a href="{{ route('merchant.driver-vehicledetails',$vehicle->id) }}"
                                       type="button" class="btn btn-icon btn-primary mr-1"><i
                                                class="fa fa-file-text-o"></i></a>
                                </td>
                                <td class="text-center">
                                    @switch($vehicle->vehicle_verification_status)
                                        @case(1)
                                        @lang("$string_file.verified")
                                        @break
                                        @case(2)
                                        <a type="button" class="btn btn-sm btn-icon btn-success mr-1"
                                           href="{{ route('merchant.driver-vehicle-verify',[$vehicle->id,1]) }}"><i
                                                    class="fa fa-check"></i></a>
                                        <a type="button" class="btn btn-sm btn-icon btn-danger mr-1"
                                           href="{{ route('merchant.driver-vehicle-verify',[$vehicle->id,3]) }}"><i
                                                    class="fa fa-eye"></i></a>
                                        @break
                                        @case(3)
                                        @lang("$string_file.rejected")
                                        @break
                                    @endswitch
                                </td>--}}
                                <td class="text-center">
                                    {{ $vehicle->created_at }}
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