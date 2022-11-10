@extends('taxicompany.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">

                    </div>
                    <h3 class="panel-title"><i class="fa fa-taxi" aria-hidden="true"></i>
                        @lang("$string_file.all_vehicles")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>"",'url'=>route('taxicompany.driver.allvehicles'),'method'=>"GET"]) !!}
                    {{--                    <form action="{{ route('merchant.driver.allvehicles') }}" method="GET">--}}
                    {{--                        <div class="table_search row">--}}
                    {{--                            <div class="col-sm-2 col-xs-12 form-group active-margin-top">--}}
                    {{--                                @lang("$string_file.search_by"):--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    <div class="table_search row">
                        @php $vehicletype = NULL; $vehicle_number = "";$searched_param = NULL; $searched_area = NULL; $searched_text = ""; @endphp
                        @if(!empty($arr_search))
                            @php $vehicletype = isset($arr_search['vehicletype']) ? $arr_search['vehicletype'] : NULL ;
                             $searched_param = isset($arr_search['parameter']) ? $arr_search['parameter'] : NULL;
                             $searched_area = isset($arr_search['area_id']) ? $arr_search['area_id'] : NULL;
                             $searched_text = isset($arr_search['keyword']) ? $arr_search['keyword'] : "";
                             $vehicle_number = isset($arr_search['vehicleNumber']) ? $arr_search['vehicleNumber'] : ""; @endphp
                        @endif
                        <div class="col-md-2 col-xs-12 form-group active-margin-top">
                            <div class="">
                                <select class="form-control" name="parameter" id="parameter">
                                    <option value="">@lang("$string_file.select")</option>
                                    <option value="1" {{$searched_param == 1 ? "selected" : ""}}>@lang("$string_file.name")</option>
                                    <option value="2" {{$searched_param == 2 ? "selected" : ""}}>@lang("$string_file.email")</option>
                                    <option value="3" {{$searched_param == 3 ? "selected" : ""}}>@lang("$string_file.phone")</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-12 form-group active-margin-top">
                            <div class="input-group">
                                <input type="text" id="" name="keyword" value="{{$searched_text}}"
                                       placeholder="@lang("$string_file.enter_text")"
                                       class="form-control col-md-12 col-xs-12">
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-12 form-group active-margin-top">
                            <div class="">
                                {!! Form::select('area_id',add_blank_option($areas,trans("$string_file.area")),$searched_area,['class'=>'form-control select2','id'=>'area_id']) !!}
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-12 form-group active-margin-top">
                            <div class="">
                                <select class="form-control" name="vehicletype" id="vehicletype">
                                    <option value="">--@lang("$string_file.vehicle_type")--
                                    </option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}"
                                                @if($vehicletype == $vehicle->id) selected @endif>{{ $vehicle->VehicleTypeName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-12 form-group active-margin-top">
                            <div class="input-group">
                                <input type="text" id="" name="vehicleNumber" value="{{$vehicle_number}}"
                                       placeholder="@lang("$string_file.vehicle_number") "
                                       class="form-control col-md-12 col-xs-12">
                            </div>
                        </div>
                        <div class="col-sm-2  col-xs-12 form-group active-margin-top">
                            <button class="btn btn-primary" type="submit"><i
                                        class="fa fa-search" aria-hidden="true"></i>
                            </button>
                            <a href="{{route('merchant.driver.allvehicles')}}">
                                <button class="btn btn-success" type="button"><i class="fa fa-refresh"
                                                                                 aria-hidden="true"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                    {{--                    </form>--}}
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.vehicle_type") </th>
                            <th>@lang("$string_file.services")</th>
                            <th>@lang("$string_file.vehicle_number")</th>
                            <th>@lang("$string_file.vehicle_id") </th>
                            @if($vehicle_model_expire == 1)
                                <th>@lang("$string_file.vehicle_registered_date")</th>
                                <th>@lang("$string_file.vehicle_expire_date")</th>
                            @endif
                            <th>@lang("$string_file.color")</th>
                            <th>@lang("$string_file.image")</th>
                            <th>@lang("$string_file.number_plate")</th>
                            <th>@lang("$string_file.action")</th>
                            <th>@lang("$string_file.created_at") </th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $driver_vehicles->firstItem() @endphp
                        @foreach($driver_vehicles as $value)
                            @foreach($value->DriverVehicles as $vehicle)
                                <tr>
                                    <td>{{ $sr }}</td>
                                    @if(Auth::user()->demo == 1)
                                        <td><span class="long_text">
                                                            {{ "********".substr($value->last_name,-2) }}
                                                            <br>
                                                             {{ "********".substr($value->phoneNumber,-2) }}
                                                            <br>
                                                            {{ "********".substr($value->email,-2) }}
                                                            </span>
                                        </td>
                                    @else
                                        <td><span class="long_text">
                                                            {{ $value->first_name." ".$value->last_name }}
                                                            <br>
                                                            {{ $value->phoneNumber }}
                                                            <br>
                                                            {{ $value->email }}
                                                            </span>
                                        </td>
                                    @endif
                                    <td>
                                        {{ $vehicle->VehicleType->VehicleTypeName}}
                                    </td>
                                    {{--                                    <td> <span class="long_text">{{ implode(',',array_pluck($vehicle->ServiceTypes,'serviceName')) }}</span></td>--}}
                                    <?php $a = array(); ?>
                                    @foreach($vehicle->ServiceTypes as $servicetypes)
                                        <?php $a[] = $servicetypes->serviceName; ?>
                                    @endforeach
                                    <td>
                                        @foreach($a as $service)
                                            {{ $service }}<br>
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        {{ $vehicle->vehicle_number }}
                                    </td>
                                    <td class="text-center">
                                        {{ $vehicle->shareCode }}
                                    </td>
                                    @if($vehicle_model_expire == 1)
                                        <td>
                                            {!! convertTimeToUSERzone($vehicle->vehicle_register_date, $vehicle->Driver->CountryArea->timezone,null,$vehicle->Driver->Merchant, 2) !!}
                                        </td>
                                        <td>
                                            {!! convertTimeToUSERzone($vehicle->vehicle_expire_date, $vehicle->Driver->CountryArea->timezone,null,$vehicle->Driver->Merchant, 2) !!}
                                        </td>
                                    @endif
                                    <td class="text-center">
                                        {{ $vehicle->vehicle_color }}
                                    </td>
                                    <td class="text-center">
                                        <a target="_blank"
                                           href="{{ get_image($vehicle->vehicle_image,'vehicle_document', $vehicle->merchant_id) }}">
                                            <img src="{{ get_image($vehicle->vehicle_image,'vehicle_document', $vehicle->merchant_id) }}"
                                                 alt="avatar"
                                                 style="width: 80px;height: 80px;border-radius:10px;">
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a target="_blank"
                                           href="{{ get_image($vehicle->vehicle_number_plate_image,'vehicle_document', $vehicle->merchant_id) }}">
                                            <img src="{{ get_image($vehicle->vehicle_number_plate_image,'vehicle_document', $vehicle->merchant_id) }}"
                                                 alt="avatar"
                                                 style="width: 80px;height: 80px;border-radius:10px;">
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('taxicompany.driver-vehicledetails',$vehicle->id) }}"
                                           class="btn btn-sm btn-info menu-icon btn_detail action_btn"><span
                                                    class="fa fa-list-alt"
                                                    data-original-title="@lang("$string_file.vehicle")  @lang("$string_file.details")"
                                                    data-toggle="tooltip"></span></a>
                                        <a href="{{ route('taxicompany.driver.vehicle.create',[$vehicle->driver_id,$vehicle->id]) }}"
                                           data-original-title="@lang("$string_file.edit_vehicle") "
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                            <i class="fa fa-edit"></i> </a>
{{--                                        <button onclick="DeleteEvent({{ $vehicle->id }},{{count($value->DriverVehicles)}})"--}}
{{--                                                type="submit"--}}
{{--                                                data-original-title="@lang("$string_file.delete")"--}}
{{--                                                data-toggle="tooltip"--}}
{{--                                                data-placement="top"--}}
{{--                                                class="btn btn-sm btn-danger menu-icon btn_delete action_btn">--}}
{{--                                            <i class="fa fa-trash"></i></button>--}}
                                    </td>
                                    <td class="text-center">
                                        {!! convertTimeToUSERzone($vehicle->created_at, $vehicle->Driver->CountryArea->timezone, null, $vehicle->Driver->Merchant) !!}
                                    </td>
                                </tr>
                                @php $sr++  @endphp
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1 float-right">{{ $driver_vehicles->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        {{--function DeleteEvent(id, vehicle_count) {--}}
        {{--    var token = $('[name="_token"]').val();--}}
        {{--    if (vehicle_count > 1) {--}}
        {{--        swal({--}}
        {{--            title: "@lang("$string_file.are_you_sure")",--}}
        {{--            text: "@lang("$string_file.delete_warning")",--}}
        {{--            icon: "warning",--}}
        {{--            buttons: true,--}}
        {{--            dangerMode: true,--}}
        {{--        }).then((isConfirm) => {--}}
        {{--            if (isConfirm) {--}}
        {{--                $.ajax({--}}
        {{--                    headers: {--}}
        {{--                        'X-CSRF-TOKEN': token--}}
        {{--                    },--}}
        {{--                    type: "GET",--}}
        {{--                    url: "{{ route('driver.delete.pendingvehicle') }}" + "/" + id,--}}
        {{--                }).done(function (data) {--}}
        {{--                    swal({--}}
        {{--                        title: "DELETED!",--}}
        {{--                        text: data,--}}
        {{--                        type: "success",--}}
        {{--                    });--}}
        {{--                    window.location.href = "{{ route('taxicompany.driver.allvehicles') }}";--}}
        {{--                });--}}
        {{--            } else {--}}
        {{--                swal("@lang("$string_file.data_is_safe")");--}}
        {{--            }--}}
        {{--        });--}}
        {{--    } else {--}}
        {{--        swal({--}}
        {{--            text: "@lang("$string_file.denied_to_delete_vehicle")",--}}
        {{--        });--}}
        {{--    }--}}
        {{--}--}}
    </script>
@endsection