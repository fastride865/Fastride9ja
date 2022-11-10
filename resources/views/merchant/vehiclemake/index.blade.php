@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('vehiclemakeadded'))
                <div class="alert dark alert-icon alert-info alert-dismissible"
                     role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang("$string_file.vehicle_make_added")
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        @if(Auth::user('merchant')->can('create_vehicle_make'))
                            <a href="{{route('excel.vehicle.make',$arr_vehicle_make['arr_search'])}}" data-toggle="tooltip">
                                <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                    <i class="wb-download" title="@lang("$string_file.export_excel")"></i>
                                </button>
                            </a>
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"
                                    title="@lang("$string_file.vehicle_make")" data-toggle="modal"
                                    data-target="#examplePositionCenter">
                                <i class="fa fa-plus"></i>
                            </button>
                        @endif
                    </div>
                    <h3 class="panel-title">
                        <i class="fa-car" aria-hidden="true"></i>
                        @lang("$string_file.vehicle_make") </h3>
                </header>
                <div class="panel-body container-fluid">

                    @php
                        $vehicle_make = isset($arr_vehicle_make['vehicle_type']) ? $arr_vehicle_make['vehicle_type'] : "";
                    @endphp
                    {!! Form::open(['name'=>'','url'=>$arr_vehicle_make['search_route'],'method'=>'GET']) !!}
                    <div class="table_search row">
                        <div class="col-md-3 col-xs-12 form-group active-margin-top">
                            <div class="input-group">
                                <input type="text" id="" name="vehicle_make" value="{{$vehicle_make}}" placeholder="@lang("$string_file.vehicle_make")" class="form-control col-md-12 col-xs-12">
                            </div>
                        </div>
                        <div class="col-sm-2  col-xs-12 form-group active-margin-top">
                            <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
                            <a href="{{$arr_vehicle_make['search_route']}}" ><button class="btn btn-success" type="button"><i class="fa fa-refresh" aria-hidden="true"></i></button></a>
                        </div>
                    </div>
                    {!! Form::close() !!}

                    <table class="display nowrap table table-hover table-stripedw-full" id="customDataTable" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.vehicle_make") </th>
                            <th>@lang("$string_file.logo")</th>
                            <th>@lang("$string_file.description")</th>
                            <th>@lang("$string_file.status")</th>
                            @if(Auth::user('merchant')->can('edit_vehicle_make'))
                                <th>@lang("$string_file.action")</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $vehiclemakes->firstItem() @endphp
                        @foreach($vehiclemakes as $vehiclemake)
                            <tr>
                                <td>{{ $sr  }}</td>
                                <td>@if(empty($vehiclemake->LanguageVehicleMakeSingle))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary">( In {{ $vehiclemake->LanguageVehicleMakeAny->LanguageName->name }}
                                                                : {{ $vehiclemake->LanguageVehicleMakeAny->vehicleMakeName }}
                                                                )</span>
                                    @else
                                        {{ $vehiclemake->VehicleMakeName }}
                                    @endif
                                </td>

                                <td><img src="{!! get_image($vehiclemake->vehicleMakeLogo,'vehicle') !!}"
                                         align="center" width="100px" class="img-radius"
                                         alt="User-Profile-Image"></td>
                                <td>@if(empty($vehiclemake->LanguageVehicleMakeSingle))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary long_text">( In {{ $vehiclemake->LanguageVehicleMakeAny->LanguageName->name }}
                                                                : {{ $vehiclemake->LanguageVehicleMakeAny->vehicleMakeDescription }}
                                                                )</span>
                                    @else
                                        <span class="map_address long_text">{{ $vehiclemake->LanguageVehicleMakeSingle->vehicleMakeDescription  }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($vehiclemake->vehicleMakeStatus == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                @if(Auth::user('merchant')->can('edit_vehicle_make'))
                                <td>
                                    <a href="{{ route('vehiclemake.edit',$vehiclemake->id) }}"
                                       data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                class="fa fa-edit"></i> </a>
                                    <button onclick="DeleteEvent({{ $vehiclemake->id }})"
                                            type="submit"
                                            data-original-title="@lang("$string_file.delete")"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                                @endif
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $vehiclemakes, 'data' => []])
{{--                    <div class="pagination1" style="float:right;">{{$vehiclemakes->links()}}</div>--}}
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="examplePositionCenter" aria-hidden="true" aria-labelledby="examplePositionCenter"
         role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple modal-center">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b> @lang("$string_file.vehicle_make")
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{ route('vehiclemake.store') }}">
                    @csrf
                    <div class="modal-body">
                        <label>@lang("$string_file.vehicle_make")  <span class="text-danger">*</span></label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="vehicle_make" name="vehicle_make"
                                   placeholder="" required>
                        </div>

                        <label> @lang("$string_file.description")
                            <span class="text-danger">*</span> </label>
                        <div class="form-group">
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder=""></textarea>
                        </div>

                        <label>  @lang("$string_file.logo")
                            <span class="text-danger">*</span> </label><span style="color: blue">(@lang("$string_file.logo_size"))</span><i
                                class="fa fa-info-circle fa-1"
                                data-toggle="tooltip"
                                data-placement="top"
                                title=""></i>
                        <div class="form-group">
                            <input type="file" class="form-control" id="vehicle_make_logo" name="vehicle_make_logo"
                                   placeholder="" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn" data-dismiss="modal"
                               value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-outline-primary btn" value="@lang("$string_file.add")">
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
<script type="">
    function DeleteEvent(id) {
        var token = $('[name="_token"]').val();
        swal({
            title: "@lang("$string_file.are_you_sure")",
            text: "@lang("$string_file.delete_warning")",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    type: "POST",
                    data: {
                        id: id,
                    },
                    url: "{{ route('merchant.vehiclemake.delete') }}",
                }).done(function (data) {
                    swal({
                        title: "@lang("$string_file.deleted")",
                        text: data,
                        type: "success",
                    });
                    window.location.href = "{{ route('vehiclemake.index') }}";
                });
            } else {
                swal("@lang("$string_file.data_is_safe")");
            }
        });
    }
</script>