@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(Auth::user('merchant')->can('create_admin'))
                            @if(!empty($info_setting) && $info_setting->view_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                            <a href="{{route('subadmin.create')}}">
                                <button type="button" title="@lang('admin.addsub')"
                                        class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                            class="wb-plus"></i>
                                </button>
                            </a>
                            <a href="{{route('excel.subadmin')}}">
                                <button type="button" data-toggle="tooltip" data-original-title="@lang("$string_file.export")"
                                        class="btn btn-icon btn-primary mr-1 float-right" style="margin:10px">
                                    <i class="wb-download" title="@lang("$string_file.export_excel")"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title">
                        <i class="wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.sub")-@lang("$string_file.admin_management")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.sub_admin")</th>
{{--                            <th>@lang("$string_file.first_name")</th>--}}
{{--                            <th>@lang("$string_file.last_name")</th>--}}
{{--                            <th>@lang("$string_file.phone")</th>--}}
{{--                            <th>@lang("$string_file.email")</th>--}}
                            <th>@lang("$string_file.role")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $subadmins->firstItem() @endphp
                        @foreach($subadmins as $subadmin)
                            <tr>
                                <td>{{ $sr }}</td>
                                @if(Auth::user()->demo == 1)
                                    <td>
                                        <span class="long_text">
                                            {{ "********".substr($subadmin->merchantFirstName, -2) }}
                                            <br>
                                            {{ "********".substr($subadmin->merchantLastName, -2) }}
                                            <br>
                                            {{ "********".substr($subadmin->merchantPhone, -2) }}
                                            <br>
                                            {{ "********".substr($subadmin->email, -2) }}
                                        </span>
                                        </td>
                                @else
                                    <td>
                                        <span class="long_text">
                                            {{ $subadmin->merchantFirstName." ".$subadmin->merchantLastName }}
{{--                                            <br>--}}
{{--                                            {{ $subadmin->merchantLastName }}--}}
                                            <br>
                                            {{ $subadmin->merchantPhone }}
                                            <br>
                                            {{ $subadmin->email }}
                                        </span>
                                    </td>
                                @endif
                                <td>
                                    {!! $subadmin->roles->first()->display_name !!}
                                </td>
                                <td>
                                    @if($subadmin->merchantStatus == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($subadmin->created_at, null, $subadmin->parent_id,null, 2) !!}
                                </td>
                                <td>
                                    @if(Auth::user('merchant')->can('edit_admin'))
                                        @if($subadmin->merchantStatus == 1)
                                            <a href="{{ route('merchant.subadmin.active-deactive',['id'=>$subadmin->id,'status'=>2]) }}"
                                               data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn"> <i
                                                        class="fa fa-eye-slash"></i> </a>
                                        @else
                                            <a href="{{ route('merchant.subadmin.active-deactive',['id'=>$subadmin->id,'status'=>1]) }}"
                                               data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                        class="fa fa-eye"></i> </a>
                                        @endif
                                        @if(Auth::user()->demo != 1)
                                            <a href="{{ route('subadmin.edit',$subadmin->id) }}"
                                               data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                        class="fa fa-edit"></i> </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $subadmins, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
