@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.navigation_drawer_configuration")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.name")</th> {{-- Name --}}
                            <th>@lang("$string_file.description")</th> {{-- Description --}}
                            <th>@lang("$string_file.icon")</th> {{-- Image --}}
                            <th>@lang("$string_file.sequence")</th> {{-- Sequence --}}
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $index_list->firstItem() @endphp
                        @foreach($index_list as $list)
                            <tr>
                                <td>{{ $sr  }}</td>
                                <td>@if(empty($list->LanguageAppNavigationDrawersOneViews))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary">( In {{ $list->LanguageAppNavigationDrawersAnyViews->LanguageName->name }}
                                                            : {{ $list->LanguageAppNavigationDrawersAnyViews->name }}
                                                            )</span>
                                    @else
                                        {{ $list->LanguageAppNavigationDrawersOneViews->name }}
                                    @endif
                                </td>

                                <td>{{$list->AppNavigationDrawer->name}}</td>

                                <td>
                                    @php
                                        $image = !empty($list->image) ? get_image($list->image,'drawericons') :
                                         get_image($list->AppNavigationDrawer->image,'drawer_icon',null,false);
                                    @endphp

                                    <img src="{{$image }}" class="img-responsive" height="80" width="80">

                                </td>
                                <td>{{$list->sequence}}</td>
                                <td>
                                    @if($list->status == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                        <a href="{{ route('navigation-drawer.edit',$list->id) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i> </a>
                                    @if($list->status == 1)
                                        <a href="{{ route('merchant.navigations.active-deactive',['id'=>$list->id,'status'=>0]) }}"
                                           data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                            <i
                                                    class="fa fa-eye-slash"></i> </a>
                                    @else
                                        <a href="{{ route('merchant.navigations.active-deactive',['id'=>$list->id,'status'=>1]) }}"
                                           data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                    class="fa fa-eye"></i> </a>
                                    @endif
                                </td>

                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $index_list, 'data' => []])
                    {{--                    <div class="pagination1 float-right">{{$index_list->links()}}</div>--}}
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection