@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("business-segment.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <a href="{{route('merchant.option-type.add')}}">
                            <button type="button" , class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i title="@lang("$string_file.add_option")" class="wb-plus"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-list" aria-hidden="true"></i>
                        @lang("$string_file.option_type_management")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.type")</th>
                            {{--                            <th>@lang("$string_file.sequence")</th>--}}
                            <th>@lang("$string_file.charges_type") </th>
                            <th>@lang("$string_file.type") </th>
                            <th>@lang("$string_file.maximum_options_on_app")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1 @endphp
                        @foreach($data as $option)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    @if(empty($option->LanguageOptionTypeSingle))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary">( In {{ $option->LanguageOptionTypeAny->LanguageName->name }}
                                                                : {{ $option->LanguageOptionTypeAny->type }}
                                                                )</span>
                                    @else
{{--                                        {{$option->Type($option->merchant_id)}}--}}
                                        {{ $option->LanguageOptionTypeSingle->type }}
                                    @endif
                                </td>
                                {{--                                <td>{{$option->sequence}}</td>--}}
                                <td>{{$option->charges_type == 1 ? trans("$string_file.free") : trans("$string_file.paid")}}</td>
                                <td>{{$option->select_type == 1 ? trans("$string_file.optional") : trans("$string_file.mandatory")}}</td>
                                <td>{{$option->max_option_on_app}}</td>
                                <td>
                                    @if($option->status==1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td style="width:100px; float:left">
                                    <a href="{{ route('merchant.option-type.add',$option->id) }}"
                                       data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                class="fa fa-edit"></i>
                                    </a>
                                    @if($option->status==1)
                                        <a href="{{route('merchant.option-type.active-deactive',['id'=>$option->id,'status'=>2])}}">
                                            <button type="button" data-original-title="@lang("$string_file.inactive")"
                                                    data-toggle="tool-tip"
                                                    data-placement="top"
                                                    class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn mr-1">
                                                <i
                                                        class="fa fa-eye-slash"></i>
                                            </button>
                                        </a>
                                    @else
                                        <a href="{{route('merchant.option-type.active-deactive',['id'=>$option->id,'status'=>1])}}">
                                            <button type="button" data-original-title="@lang("$string_file.active")"
                                                    data-toggle="tool-tip"
                                                    data-placement="top"
                                                    class="btn btn-sm btn-success menu-icon btn_eye action_button">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </a>
                                    @endif
                                    <a href="{{ route('merchant.option-type.delete',$option->id) }}"
                                       data-original-title="@lang("$string_file.delete")"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-danger menu-icon btn_delete action_btn"> <i
                                                class="fa fa-trash"></i> </a>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    {{--                    <div class="pagination1" style="float:right;">{{$option->links()}}</div>--}}
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
