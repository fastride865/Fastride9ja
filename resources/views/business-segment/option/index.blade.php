@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("business-segment.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('business-segment.option.add')}}">
                            <button type="button", class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i title="@lang("$string_file.add_option")" class="wb-plus"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-list" aria-hidden="true"></i>
                        @lang("$string_file.options_management")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.type")</th>
{{--                            <th>@lang("$string_file.sequence")</th>--}}
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1 @endphp
                        @foreach($data as $option)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $option->Name($option->business_segment_id)}}</td>
                                <td>{{$option->OptionType->Type($merchant_id)}}</td>
{{--                                <td>{{$option->sequence}}</td>--}}
                                <td>
                                    @if($option->status==1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td style="width:100px; float:left">
                                    <a href="{{ route('business-segment.option.add',$option->id) }}"
                                       data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                class="fa fa-edit"></i>
                                    </a>
                                    @if($option->status==1)
                                        <a href="{{route('business-segment.option.active-deactive',['id'=>$option->id,'status'=>2])}}">
                                        <button type="button" data-original-title="@lang("$string_file.inactive")" data-toggle="tool-tip"
                                            data-placement="top"
                                            class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn mr-1"> <i
                                                    class="fa fa-eye-slash"></i>
                                        </button></a>
                                    @else
                                        <a href="{{route('business-segment.option.active-deactive',['id'=>$option->id,'status'=>1])}}">
                                        <button type="button" data-original-title="@lang("$string_file.active")" data-toggle="tool-tip"
                                                data-placement="top"
                                                class="btn btn-sm btn-success menu-icon btn_eye action_button">
                                            <i class="fa fa-eye"></i>
                                        </button></a>
                                    @endif
                                    <a href="{{ route('business-segment.option.delete',$option->id) }}"
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
@endsection
