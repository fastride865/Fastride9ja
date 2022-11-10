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
                        @if(Auth::user('merchant')->can('create_area'))
                            <a href="{{route('countryareas.add')}}">
                                <button type="button"
                                        title="@lang("$string_file.add_restricted_area")"
                                        class="btn btn-icon btn-success mr-1 float-right" style="margin:10px"><i
                                            class="fa fa-plus"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title">
                        <i class="wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.restricted_area_management")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.geofence_area")</th>
                            <th>@lang("$string_file.restricted_area_for")</th>
                            <th>@lang("$string_file.restricted_type")</th>
                            <th>@lang("$string_file.restricted_base_area")</th>
                            <th>@lang("$string_file.queue_management") </th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $areas->firstItem() @endphp
                        @foreach($areas as $area)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    @if(empty($area->LanguageSingle))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary">( In {{ $area->LanguageAny->LanguageName->name }}
                                                            : {{ $area->LanguageAny->AreaName }}
                                                            )</span>
                                    @else
                                        {{ $area->LanguageSingle->AreaName }}
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($area->RestrictedArea))
                                        @switch($area->RestrictedArea->restrict_area)
                                            @case(1)@lang("$string_file.pickup")
                                            {{--                                                    @lang('admin.restrict_pickup')--}}
                                            @break
                                            @case(2)
                                            @lang("$string_file.drop_off")
                                            {{--                                                    @lang('admin.restrict_drop')--}}
                                            @break
                                            @case(3)
                                            @lang("$string_file.both")
                                            {{--                                                    @lang('admin.restrict_both')--}}
                                            @break
                                            @default
                                            <span>---</span>
                                        @endswitch
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($area->RestrictedArea))
                                        @if(!empty($area->RestrictedArea->restrict_type) && $area->RestrictedArea->restrict_type == 1)
                                            @lang("$string_file.allowed")
                                            {{--                                                @lang('admin.restrict_allowed')--}}
                                        @else
                                            @lang("$string_file.not")  @lang("$string_file.allowed")
                                            {{--                                                @lang('admin.restrict_not_allowed')--}}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @php $base_areas = isset($area->RestrictedArea->base_areas) ? explode(',', $area->RestrictedArea->base_areas) : ''; @endphp
                                    @if(!empty($base_areas))
                                        @foreach($base_areas as $base_area)
                                            {{ isset($area_list[$base_area]) ? $area_list[$base_area].', ' : '--' }}
                                        @endforeach
                                    @else
                                        ---
                                    @endif
                                </td>

                                <td class="text-center">

                                    <a href="{{route('geofence.restrict.viewgeofencequeue',[$area->id])}}"
                                       class="btn btn-icon btn-info btn_eye action_btn"><i class="icon fa-sign-in"></i></a>
                                </td>

                                <td>
                                    @php $base_areas = isset($area->RestrictedArea->queue_system) ? $area->RestrictedArea->queue_system : ''; @endphp
                                    @if($base_areas == 1)
                                        <span class="badge badge-success"> @lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger"> @lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('geofence.restrict.edit',$area->id) }}"
                                       data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                class="fa fa-edit"></i> </a>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $areas, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection