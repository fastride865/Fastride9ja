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
                        @if(Auth::user('merchant')->can('create_sos_number'))
                            <div class="btn-group float-md-right">
                                <div class="heading-elements">
                                    <button type="button" class="btn btn-icon btn-success mr-1 float-right"
                                            style="margin:10px"
                                            title="@lang("$string_file.add_sos")" data-toggle="modal"
                                            data-target="#inlineForm">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.sos_management") </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="post" action="{{ route('merchant.sos.search') }}">
                        @csrf
                        <div class="table_search">
                            <div class="row">
                                <div class="col-md-3 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="name"
                                               placeholder="@lang("$string_file.sos_number")"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>

                                <div class="col-md-3 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="number"
                                               placeholder="@lang("$string_file.name")"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-sm-2 form-group ">
                                    <button class="btn btn-primary" type="submit" name="seabt12"><i
                                                class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.sos_number")</th>
                            <th>@lang("$string_file.application")</th>
                            <th>@lang("$string_file.added_by")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $Sos->firstItem() @endphp
                        @foreach($Sos as $sos)
                            <tr>
                                <td>{{ $sr  }}</td>
                                <td>
                                    @if(Auth::user()->demo == 1)
                                        @if(empty($sos->LanguageSingle))
                                            <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                            <span class="text-primary">( In {{ "********".substr($sos->LanguageAny->LanguageName->name, -2) }}
                                                                    : {{ "********".substr($sos->LanguageAny->name, -2) }}
                                                                    )</span>
                                        @else
                                            {{ "********".substr($sos->LanguageSingle->name, -2) }}
                                        @endif
                                    @else
                                        @if(empty($sos->LanguageSingle))
                                            <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                            <span class="text-primary">( In {{ $sos->LanguageAny->LanguageName->name }}
                                                                    : {{ $sos->LanguageAny->name }}
                                                                    )</span>
                                        @else
                                            {{ $sos->LanguageSingle->name }}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user()->demo == 1)
                                        {{ "********".substr($sos->number, -2) }}
                                    @else
                                        {{ $sos->number }}
                                    @endif
                                </td>
                                <td>
                                    @if($sos->application == 1)
                                        @lang("$string_file.user")
                                    @elseif($sos->application == 2)
                                        @lang("$string_file.driver")
                                    @else
                                        - - -
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user()->demo == 1)
                                        @if($sos->application == 1)
                                            {{ ($sos->User) ? "********".substr($sos->User->first_name, -2) : '- - -' }}
                                        @elseif($sos->application == 2)
                                            {{ ($sos->Driver) ? "********".substr($sos->Driver->first_name, -2) : '- - -' }}
                                        @else
                                            - - -
                                        @endif
                                    @else
                                        @if($sos->application == 1)
                                            {{ ($sos->User) ? $sos->User->first_name.' ('.$sos->User->UserPhone.')': '- - -' }}
                                        @elseif($sos->application == 2)
                                            {{ ($sos->Driver) ? $sos->Driver->first_name.' ('.$sos->Driver->phoneNumber.')' : '- - -' }}
                                        @else
                                            - - -
                                        @endif
                                    @endif

                                </td>

                                <td>
                                    @if($sos->sosStatus == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user('merchant')->can('edit_sos_number'))
                                        <a href="{{ route('sos.edit',$sos->id) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-warning btn-sm menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i> </a>

                                        @if($sos->sosStatus == 1)
                                            <a href="{{ route('merchant.sos.active-deactive',['id'=>$sos->id,'status'=>2]) }}"
                                               data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-danger btn-sm menu-icon btn_eye_dis action_btn"> <i
                                                        class="fa fa-eye-slash"></i> </a>
                                        @else
                                            <a href="{{ route('merchant.sos.active-deactive',['id'=>$sos->id,'status'=>1]) }}"
                                               data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-success btn-sm menu-icon btn_eye action_btn"> <i
                                                        class="fa fa-eye"></i> </a>
                                        @endif
                                    @endif

                                    @if(Auth::user('merchant')->can('delete_sos_number'))
                                        <a href="{{ route('merchant.sos.delete',$sos->id) }}"
                                           data-original-title="@lang("$string_file.delete")"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-danger btn-sm menu-icon btn_delete action_btn"> <i
                                                    class="fa fa-trash"></i> </a>
                                    @endif
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $Sos, 'data' => []])
                    {{--                    <div class="pagination1 float-right">{{ $Sos->links() }}</div>--}}
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
                           id="myModalLabel33"><b>@lang("$string_file.add_sos")
                            (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{ route('sos.store') }}">
                    @csrf
                    <div class="modal-body">

                        <label>@lang("$string_file.name")<span class="text-danger">*</span></label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="name"
                                   name="name" placeholder=""
                                   required>
                        </div>


                        <label>@lang("$string_file.application") <span class=" text-danger">*</span></label>
                        <div class="form-group">
                            <select class="form-control" name="application" id="application" required>
                                <option value="1">@lang("$string_file.user")</option>
                                <option value="2">@lang("$string_file.driver")</option>
                            </select>
                        </div>


                        <label> @lang("$string_file.sos_number")<span class="text-danger">*</span></label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="number"
                                   name="number" placeholder="@lang("$string_file.phone")"
                                   required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary" data-dismiss="modal"
                               value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-outline-primary" value="@lang("$string_file.add")">
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection

