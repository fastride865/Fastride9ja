@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        @if(Auth::user('merchant')->can('create_role'))
                            <a href="{{route('role.create')}}">
                                <button type="button" title="@lang('admin.tax_company')"
                                        class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                            class="wb-plus"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.role_management")
                    </h3>
                </div>
                <div class="panel-body">
                    <table class="display nowrap table table-hover table-stripedw-full" id="customDataTable"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.role")</th>
                            <th>@lang("$string_file.description")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $roles->firstItem() @endphp
                        @foreach($roles as $role)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $role->display_name }}</td>
                                <td>{{ $role->description }}</td>
                                <td>
                                    {{--                                    @if(Auth::user('merchant')->can('view_admin'))--}}
                                    {{--                                        <a href="{{ route('role.show',$role->id) }}"--}}
                                    {{--                                           class="btn btn-sm btn-info menu-icon btn_delete action_btn"--}}
                                    {{--                                           data-original-title="@lang("$string_file.details")"--}}
                                    {{--                                           data-toggle="tooltip"--}}
                                    {{--                                           data-placement="top"><span--}}
                                    {{--                                                    class="fa fa-list"></span></a>--}}
                                    {{--                                    @endif--}}

                                    @if(Auth::user('merchant')->can('edit_admin'))
                                        <a href="{{ route('role.edit',$role->id) }}"
                                           data-original-title="View & Edit" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i> </a>
                                    @endif
                                </td>
                                @php $sr++ @endphp
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1" style="float:right;">{{$roles->links()}}</div>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
