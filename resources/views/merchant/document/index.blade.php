@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        @if(Auth::user('merchant')->can('create_documents'))
                            <a href="{{ url('merchant/admin/document/add') }}">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus" title="@lang("$string_file.add_document")"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="fa-files-o" aria-hidden="true"></i>
                        @lang("$string_file.document_management")
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn") </th>
                            <th>@lang("$string_file.name") </th>
                            <th>@lang("$string_file.expire_date")</th>
                            <th>@lang("$string_file.mandatory")</th>
                            <th>@lang("$string_file.document_number_required")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $documents->firstItem() @endphp
                        @foreach($documents as $document)
                        
                            <tr>
                                <td>{{ $sr }}</td>
                                <td><span class="long_text"> @if(empty($document->LanguageSingle))
                                            <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                            <span class="text-primary">( In {{ $document->LanguageAny->LanguageName->name }}
                                                            : {{ $document->LanguageAny->documentname }}
                                                            )</span>
                                        @else
                                            {{ $document->LanguageSingle->documentname }}
                                        @endif
                                        </span>
                                </td>
                                <td>
                                    {{$status[$document->expire_date]}}
                                </td>
                                <td>
                                    {{$status[$document->documentNeed]}}
                                </td>
                                <td>
                                    @if($document->document_number_required == 1)
                                        @lang("$string_file.enable")
                                    @else
                                        @lang("$string_file.disable")
                                    @endif
                                </td>
                                <td>
                                    @if($document->documentStatus == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td style="width: 100px;float: left">
                                    @if(Auth::user('merchant')->can('edit_documents'))
                                        <a href="{{ url('merchant/admin/document/add/'.$document->id) }}">
                                            <button type="button"
                                                    class="btn btn-sm btn-warning menu-icon btn_edit action_btn">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        </a>

                                        @if($document->documentStatus == 1)
                                            <a href="{{ route('merchant.document.active-deactive',['id'=>$document->id,'status'=>2]) }}"
                                               data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn"> <i
                                                        class="fa fa-eye-slash"></i> </a>
                                        @else
                                            <a href="{{ route('merchant.document.active-deactive',['id'=>$document->id,'status'=>1]) }}"
                                               data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                        class="icon fa-eye"></i> </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $documents, 'data' => []])
                    {{--                    <div class="pagination1" style="float:right;">{{$documents->links()}}</div>--}}
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection