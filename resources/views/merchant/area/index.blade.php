@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading"><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        @if(Auth::user('merchant')->can('create_area'))
                            <a href="{{route('excel.serviceareamanagement')}}">
                                <button type="button" title="@lang("$string_file.export_excel")"
                                        class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="fa fa-download"></i>
                                </button>
                            </a>
                            <a href="{{route('countryareas.add')}}">
                                <button type="button" title="@lang("$string_file.add_service_area")"
                                        class="btn btn-icon btn-success mr-1 float-right" style="margin:10px"><i
                                            class="fa fa-plus"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title">
                        <i class=" icon wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.service_area_management")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form class="form-group" action="{{route('countryArea.Search')}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <select name="area_id" class="form-control">
                                    <option value="">-- @lang("$string_file.select") --</option>
                                    @foreach($arr_areas as $area)
                                        <option value="{{$area->id}}" {{$area_id == $area->id ? "selected" : NULL}}>
                                            {{$area->CountryAreaName}}

                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button class="btn btn-small btn-primary" type="submit"><i class="fa fa-search"></i></button>
                            </div>
                            <div class="col-md-1 ml--25">
                                <a href="{{ route('countryareas.index') }}">
                                <button class="btn btn-small btn-success" type="button"><i class="fa fa-refresh"></i></button>
                                </a>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.country")</th>
                            <th>@lang("$string_file.segment")</th>
                            <th>@lang("$string_file.type")</th>
                            <th>@lang("$string_file.personal_document")</th>
                            <th>@lang("$string_file.timezone")</th>
                            @if($config->driver_wallet_status == 1)
                                <th title="">@lang("$string_file.wallet_money")</th>
                            @endif
                            @if($config->no_driver_availabe_enable == 1)
                                <th>@lang("$string_file.auto_upgradation")</th>
                            @endif
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tfoot></tfoot>
                        <tbody>
                        @php $sr = $areas->firstItem() @endphp
                        @foreach($areas as $area)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    {{$area->CountryAreaName}}
                                </td>
                                <td>{{ $area->country->CountryName }}</td>

                                @php $arr_segment = ""@endphp
                                @foreach($area->Segment as $segment)
                                    @php $arr_segment .= $segment->Name($area->merchant_id).', ';
                                    @endphp
                                @endforeach
                                @php $arr_segment = substr($arr_segment, 0, -2) @endphp
{{--                                @php $arr_segment = implode(',',array_pluck($area->Segment,'slag')) @endphp--}}
                                <td title="{{ $arr_segment }}">
                                    <span class="">
                                        @if (strlen($arr_segment) > 20)
                                            @php $trimstring = substr($arr_segment, 0, 20). ' ....etc'; @endphp
                                        @else
                                            @php $trimstring = $arr_segment; @endphp
                                        @endif
                                        {{$trimstring}}
                                    </span>
                                </td>
                                <td>
                                    @if($area->is_geofence == 1)
                                        @lang("$string_file.geofence_area")
                                    @else
                                        @lang("$string_file.service_area")
                                    @endif
                                </td>
                                <?php $a = array() ?>
                                @foreach($area->documents as $document)
                                    @php $a[] = $document->DocumentName; $arr_doc = implode(',',$a) @endphp
                                @endforeach
                                <td title="{{ $arr_doc }}">
                                    <span class="">
                                        @if (strlen($arr_doc) > 20)
                                        @php $trimstring = substr($arr_doc, 0, 20). ' ....etc'; @endphp
                                        @else
                                        @php $trimstring = $arr_doc; @endphp
                                        @endif
                                    {{$trimstring}}
                                    </span>
                                </td>
                                <td>{{ $area->timezone }}</td>
                                @if($config->driver_wallet_status == 1)
                                    <td>
                                        @if(!empty($area->minimum_wallet_amount))
                                            {{ $area->Country->isoCode.' '.$area->minimum_wallet_amount }}
                                        @else
                                            0
                                        @endif
                                    </td>
                                @endif
                                @if($config->no_driver_availabe_enable == 1)
                                    <td>
                                        @if($area->auto_upgradetion)
                                            @if($area->auto_upgradetion == 1) Enable @elseif($area->auto_upgradetion == 2) Disable @endif
                                        @else
                                            ------
                                        @endif
                                    </td>
                                @endif
                                <td>
                                    @if($area->status == 1)
                                        <span class="badge badge-success font-size-14">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user('merchant')->can('edit_area'))
                                        <a href="{{ route('countryareas.add',$area->id) }}"
                                           data-original-title="@lang("$string_file.edit_area_config")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                            <img src="{{asset('basic-images/basic-edit.png')}}" height="20" width="20">
                                        </a>

                                        @if($segment_group_vehicle == true)
                                        <a href="{{ route('countryareas.add.step2',$area->id) }}"
                                           data-original-title="@lang("$string_file.vehicle_configuration")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn">

                                            <img src="{{asset('basic-images/taxi.png')}}" height="20" width="20">
                                        </a>
                                        @endif
                                        @if($segment_group_handyman == true)
                                        <a href="{{ route('countryareas.add.step3',$area->id) }}"
                                           data-original-title="@lang("$string_file.handyman_configuration")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary btn_edit action_btn">
                                            <img src="{{asset('basic-images/handyman.png')}}" height="20" width="20">
                                        </a>
                                        @endif
                                        @if($category_vehicle_type_module == 1 && in_array('TAXI',array_pluck($area->Segment,'slag')))
                                            <a href="{{ route('country-area.category.vehicle.type',$area->id) }}"
                                               data-original-title="@lang("$string_file.vehicle_type_categorization")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-primary btn_edit action_btn">
                                                <i class="fa fa-list-alt" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                    @endif
{{--                                    @if(Auth::user('merchant')->can('view_area'))--}}
{{--                                        <a href="{{ route('countryareas.show',$area->id) }}"--}}
{{--                                           title="@lang("$string_file.details")"--}}
{{--                                           class="btn btn-sm btn-danger menu-icon btn_delete action_btn"><span--}}
{{--                                                    class="fa fa-list-alt"></span></a>--}}
{{--                                    @endif--}}
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $areas, 'data' => []])
{{--                    <div class="pagination1 float-right">{{$areas->links()}}</div>--}}
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection

