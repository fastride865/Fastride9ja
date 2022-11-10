@extends('merchant.layouts.main')
@section('content')
    <style>
    </style>
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
                    <h3 class="panel-title">
                        <i class="fa fa-cog fa-spin" aria-hidden="true"></i>
                        @lang("$string_file.services")</h3>
                </header>
                <div id="exampleTransition" class="page-content container-fluid" data-plugin="animateList">
                    <ul class="blocks-sm-100 blocks-xxl-3">
                        @foreach($segment_services as $services)
                            <li>
                                <div class="panel panel-bordered" style="border: 1px solid #e4eaec;">
                                    <div class="panel-heading">
                                        <a href="">
                                            <h3 class="panel-title segment_class">
                                                {!! $services['slag'] !!}
                                            </h3>
                                        </a>
                                        <div class="panel-actions">
                                            <img class="img-responsive" height="50px"
                                                 src="{!! $services['segment_icon'] !!}">
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <span style="font-size:20px;">{!! $services['name'] !!}</span>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
{{--                                            @if(Auth::user('merchant')->can('edit_segment'))--}}
                                            <a href="{{ route('merchant.segment.edit',$services['segment_id']) }}"
                                               class="panel-action" data-toggle="panel-close" aria-hidden="true"
                                               title="@lang("$string_file.edit")"><i class="fa-pencil"></i> </a>
{{--                                            @endif--}}
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        @if($services['segment_group_id'] == 2)
                                            <a href="{{ route('merchant.serviceType.edit',$services['segment_id']) }}"
                                               class="panel-action float-right" data-toggle="panel-close"
                                               aria-hidden="true" title="@lang('admin.add_service')"><i
                                                        class="fa-plus"></i> </a>
                                        @endif
                                        <div class="example table-responsive">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>@lang("$string_file.type")</th>
                                                    <th>@lang("$string_file.service_type")</th>
                                                    <th>@lang("$string_file.description")</th>
                                                    <th>@lang("$string_file.sequence")</th>
                                                    <th>@lang("$string_file.icon")</th>
                                                    @if(Auth::user('merchant')->can('edit_service_types'))
                                                        <th>@lang("$string_file.action")</th>
                                                    @endif
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php $i = 1; @endphp
                                                @foreach($services['arr_services'] as $service)
                                                    <tr>
                                                        <td>{{$i}}</td>
                                                        <td>{!! $service['serviceName'] !!}</td>
                                                        <td>{!! $service['locale_service_name'] !!}</td>
                                                        <td>{!! $service['locale_service_description'] !!}</td>
                                                          <td>{!! $service['service_sequence'] !!}</td>
                                                        <td>
                                                          <img class="img-responsive" height="50px" width="50px"
                                                               src="{!! $service['service_icon'] !!}">
                                                        </td>
                                                        <td>
                                                            @if(Auth::user('merchant')->can('edit_service_types'))
                                                            <a href="{{ route('merchant.serviceType.edit',[$service['segment_id'],$service['id']]) }}"
                                                               class="panel-action" data-toggle="panel-close"
                                                               aria-hidden="true" title="@lang("$string_file.edit")"><i
                                                                        class="fa-pencil"
                                                                        style="padding-left: 19%;"></i> </a>
                                                            @endif
                                                        </td>
                                                        @php $i++; @endphp
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
