@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
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
                        @lang("$string_file.pending_vehicle_approval")</h3>
                </header>
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>"",'url'=>route('merchant.driver.pending.vehicles'),'method'=>"GET"]) !!}
                    {{--                    <form action="{{ route('merchant.driver.allvehicles') }}" method="GET">--}}
                    <div class="table_search row">
                        <div class="col-sm-2 col-xs-12 form-group active-margin-top">
                            @lang("$string_file.search_by"):
                        </div>
                    </div>
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
                                <select class="form-control" name="parameter" id="parameter" required>
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
                                    <option value="">--@lang("$string_file.vehicle_type") --</option>
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
                                <button class="btn btn-success" type="button"><i class="fa fa-refresh" aria-hidden="true"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.vehicle_type") </th>
                            <th>@lang("$string_file.services")</th>
                            <th>@lang("$string_file.vehicle_number")</th>
                            <th>@lang("$string_file.color")</th>
                            <th>@lang("$string_file.image")</th>
                            <th>@lang("$string_file.number_plate")</th>
                            <th>@lang("$string_file.action")</th>
                            <th>@lang("$string_file.created_at") </th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1; @endphp
                        @foreach($driver_vehicles as $value)
                            @foreach($value->DriverVehicles as $vehicle)
                                <tr>
                                    <td>{{ $sr }}</td>

                                    @if(Auth::user()->demo == 1)
                                        <td>

                                            {{ "********".substr($value->last_name,-2) }}
                                            <br>
                                            {{ "********".substr($value->phoneNumber,-2) }}
                                            <br>
                                            {{ "********".substr($value->email,-2) }}

                                        </td>
                                    @else
                                        <td>
                                            {{ $value->first_name." ".$value->last_name }}
                                            <br>
                                            {{ $value->phoneNumber }}
                                            <br>
                                            {{ $value->email }}
                                        </td>

                                    @endif
                                    <td>
                                        {{ $vehicle->VehicleType->VehicleTypeName }}
                                    </td>
                                    <?php $a = array() ?>
                                    @foreach($vehicle->ServiceTypes as $serviceType)
                                        <?php $a[] = $serviceType->serviceName; ?>
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
                                        {{ $vehicle->vehicle_color }}
                                    </td>
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
                                    <td class="text-center">
                                        @if(Auth::user('merchant')->can('view_pending_vehicle_apporvels'))
                                            <a href="{{ route('merchant.driver-vehicledetails',$vehicle->id) }}"
                                               class="btn btn-sm btn-info menu-icon btn_detail action_btn"><span
                                                        class="fa fa-list-alt"
                                                        data-original-title="@lang("$string_file.vehicle")  @lang("$string_file.details")"
                                                        data-toggle="tooltip"></span></a>
                                        @endif

                                        @if(Auth::user('merchant')->can('edit_vehicle'))
                                            <a href="{{ route('merchant.driver.vehicle.create',[$vehicle->driver_id,$vehicle->id]) }}"
                                               data-original-title="@lang("$string_file.edit_vehicle") "
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                                <i class="fa fa-edit"></i> </a>
                                        @endif

                                        @if(Auth::user('merchant')->can('delete_vehicle'))
                                            <a  role="button"
                                                onclick="DeleteEvent({{ $vehicle->id }})"
                                                class="btn btn-sm btn-danger menu-icon btn_delete action_btn"
                                                data-original-title="Delete Vehicle"
                                                data-toggle="tooltip"><i class="fa fa-trash"></i>
                                            </a>
                                        @endif
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
                    @include('merchant.shared.table-footer', ['table_data' => $driver_vehicles, 'data' => $arr_search])
{{--                    <div class="pagination1 float-right">{{$driver_vehicles->appends($data)->links()}}</div>--}}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="cancelbooking" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">@lang('admin.auth_required')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-group" method="post" action="{{route('merchant.driver.move-to-pending')}}">
                    @csrf
                    <div class="modal-body">
                        <h1 class="text-danger text-center" style="font-size:60px"><i class="fa fa-exclamation-circle"></i></h1>
                        <h5 class="text-danger text-center">@lang('admin.confirmation_to_move')</h5><br>
                        <input type="hidden" id="docId" name="driver_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">@lang("$string_file.close")</button>
                        <button type="submit" class="btn btn-success">@lang('admin.submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
@section('js')
    <script>
        function DeleteEvent(id) {
            var token = $('[name="_token"]').val();
            swal({
                title: "@lang("$string_file.are_you_sure")",
                text: "@lang("$string_file.delete_warning"),
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        type: "GET",
                        url: "{{ route('driver.delete.pendingvehicle') }}"+"/"+id,
                    }).done(function (data) {
                        swal({
                            title: "DELETED!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "{{ route('merchant.driver.pending.vehicles') }}";
                    });
                } else {
                    swal("@lang('admin.vehicle_safe')");
                }
            });
        }
    </script>
@endsection
