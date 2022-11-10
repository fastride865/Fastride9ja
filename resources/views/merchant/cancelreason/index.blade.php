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
                        <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"
                                data-toggle="modal" data-target="#inlineForm">
                            <i class="wb-plus" title="@lang("$string_file.cancel_reason") "></i>
                        </button>
                    </div>
                    <h3 class="panel-title"><i class=" wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.cancel_reason_management")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="post" action="{{ route('cancelreason.search') }}">
                        @csrf
                        {{--                        <div class="table_search row ">--}}
                        {{--                            <div class="col-md-2 col-xs-12 form-group ">--}}
                        {{--                                <div class="input-group">--}}
                        {{--                                    <input type="text" id="" name="reason"--}}
                        {{--                                           placeholder="@lang('admin.message376')"--}}
                        {{--                                           class="form-control col-md-12 col-xs-12">--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                            <div class="col-md-2 col-xs-12 form-group ">--}}
                        {{--                                <div class="form-group">--}}
                        {{--                                    <select class="form-control" name="reason_for"--}}
                        {{--                                            id="reason_for">--}}
                        {{--                                        <option value="">--@lang("$string_file.select") --</option>--}}
                        {{--                                        <option value="1">@lang("$string_file.user") </option>--}}
                        {{--                                        <option value="2">@lang("$string_file.driver")</option>--}}
                        {{--                                        <option value="3">@lang("$string_file.dispatcher") </option>--}}
                        {{--                                    </select>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                            <div class="col-sm-2  col-xs-12 form-group ">--}}
                        {{--                                <button class="btn btn-primary" type="submit" name="seabt12"><i--}}
                        {{--                                            class="fa fa-search" aria-hidden="true"></i>--}}
                        {{--                                </button>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.cancel_reason")</th>
                            <th>@lang("$string_file.reason_for")</th>
                            <th>@lang("$string_file.segment") </th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $cancelreasons->firstItem() @endphp
                        @foreach($cancelreasons as $cancelreason)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    {{ $cancelreason->ReasonName}}
                                   
                                </td>
                                @switch($cancelreason->reason_type)
                                    @case(1)
                                    <td>@lang("$string_file.user") </td>
                                    @break
                                    @case(2)
                                    <td>@lang("$string_file.driver")</td>
                                    @break
                                    @case(3)
                                    <td>@lang("$string_file.dispatcher") </td>
                                    @break
                                @endswitch
                                <td>{{ array_key_exists($cancelreason->segment_id,$merchant_segments) ? $merchant_segments[$cancelreason->segment_id] : '--'}}</td>
                                <td>
                                    @if($cancelreason->reason_status == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>

                                <td style="width:100px;float:left">
                                    <a href="{{ route('cancelreason.edit',$cancelreason->id) }}"
                                       data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                        <i
                                                class="fa fa-edit"></i> </a>
                                    @if($cancelreason->reason_status == 1)
                                        <a href="{{ route('merchant.cancelreason.active-deactive',['id'=>$cancelreason->id,'status'=>2]) }}"
                                           data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                            <i
                                                    class="fa fa-eye-slash"></i> </a>
                                    @else
                                        <a href="{{ route('merchant.cancelreason.active-deactive',['id'=>$cancelreason->id,'status'=>1]) }}"
                                           data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                            <i
                                                    class="fa fa-eye"></i> </a>
                                    @endif
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $cancelreasons, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b>@lang("$string_file.cancel_reason")
                            (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{ route('cancelreason.store') }}">
                    @csrf
                    <div class="modal-body">
                        <label>@lang("$string_file.reason_for") <span class="text-danger">*</span></label>
                        <div class="form-group">
                            <select class="form-control" name="reason_for"
                                    id="reason_for" required>
                                <option value="">--@lang("$string_file.select") --</option>
                                <option value="1">@lang("$string_file.user") </option>
                                <option value="2">@lang("$string_file.driver")</option>
                                <option value="3">@lang("$string_file.dispatcher") </option>
{{--                                will change condition later --}}
                                @if(in_array(3,$merchant_segments) || in_array(4,$merchant_segments))
                                <option value="4">@lang("$string_file.business_segment") </option>
                                @endif
                            </select>
                        </div>
                        <label>@lang("$string_file.segment")  <span class="text-danger">*</span></label>
                        <div class="form-group">
                            <select class="form-control" name="segment_id" id="segment_id"
                                    required>
                                <option value="">@lang("$string_file.select") </option>
                                @foreach($merchant_segments  as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <label> @lang("$string_file.reason")
                            <span class="text-danger">*</span> </label>
                        <div class="form-group">
                            <textarea class="form-control" id="reason" name="reason" rows="3"
                                      placeholder=""></textarea>
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
