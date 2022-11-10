@extends('merchant.layouts.main')
@section('content')
    <div class="app-content content">
        <div class="container-fluid ">
            <div class="content-wrapper">
                @if($errors->all())
                    @foreach($errors->all() as $message)
                        <div class="box no-border">
                            <div class="box-tools">
                                <p class="alert alert-warning alert-dismissible">
                                    {{ $message }}
                                    <button type="button" class="close" data-dismiss="alert"
                                            aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                </p>
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="content-body">
                    <section id="horizontal">
                        <div class="row">
                            <div class="col-12" style="height:100vh">
                                <div class="card shadow ">
                                    <div class="card-header py-3">
                                        <div class="content-header row">
                                            <div class="content-header-left col-md-4 col-12 mb-2">
                                                <h3 class="content-header-title mb-0 d-inline-block">
                                                    <i class=" fa fa-ban" aria-hidden="true"></i>
                                                    @lang("$string_file.reject_reason") </h3>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                @if(session('reject'))
                                                    <div class="col-md-6 alert alert-icon-right alert-info alert-dismissible mb-2"
                                                         role="alert">
                                                        <span class="alert-icon"><i class="fa fa-info"></i></span>
                                                        <button type="button" class="close" data-dismiss="alert"
                                                                aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                        <strong>{{ session('reject') }}</strong>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="content-header-right col-md-2 col-12">
                                                <div class="btn-group float-md-right">
                                                    <div class="heading-elements">
                                                        <button type="button" class="btn btn-icon btn-success mr-1"
                                                                title="@lang('admin.message702')" data-toggle="modal"
                                                                data-target="#inlineForm">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table display nowrap table-bordered table-striped"
                                                   id="dataTable" width="100%" cellspacing="0">
                                                <thead>
                                                <tr>
                                                    <th>@lang("$string_file.sn")</th>
                                                    <th>@lang("$string_file.title")</th>
                                                    <th>@lang('admin.message703')</th>
                                                    <th>@lang("$string_file.status")</th>
                                                    <th>@lang("$string_file.action")</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php $sr = $rejectreasons->firstItem() @endphp
                                                @foreach($rejectreasons as $reject)
                                                    <tr>
                                                        <td>{{ $sr }}</td>
                                                        <td>@if(empty($reject->LanguageSingle))
                                                                <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                                                <span class="text-primary">( In {{ $reject->LanguageAny->LanguageName->name }}
                                                                : {{ $reject->LanguageAny->title }}
                                                                )</span>
                                                            @else
                                                                {{ $reject->LanguageSingle->title }}
                                                            @endif
                                                        </td>
                                                        <td>@if(empty($reject->LanguageSingle))
                                                                <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                                                <span class="text-primary">( In {{ $reject->LanguageAny->LanguageName->name }}
                                                                : {{ $reject->LanguageAny->action }}
                                                                )</span>
                                                            @else
                                                                {{ $reject->LanguageSingle->action }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($reject->status == 1)
                                                                <label class="label_success">@lang("$string_file.active")</label>
                                                            @else
                                                                <label class="label_danger">@lang("$string_file.inactive")</label>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('rejectreason.edit',$reject->id) }}"
                                                               data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                                               data-placement="top"
                                                               class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                                                <i
                                                                        class="fa fa-edit"></i> </a>
                                                            @if($reject->status == 1)
                                                                <a href="{{ route('merchant.reject.active-deactive',['id'=>$reject->id,'status'=>2]) }}"
                                                                   data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                                                   data-placement="top"
                                                                   class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                                                    <i
                                                                            class="fa fa-eye-slash"></i> </a>
                                                            @else
                                                                <a href="{{ route('merchant.reject.active-deactive',['id'=>$reject->id,'status'=>1]) }}"
                                                                   data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                                                   data-placement="top"
                                                                   class="btn btn-success menu-icon btn_eye action_btn">
                                                                    <i
                                                                            class="fa fa-eye"></i> </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @php $sr++  @endphp
                                                @endforeach
                                                </tbody>
                                            </table>
                                            @include('merchant.shared.table-footer', ['table_data' => $rejectreasons, 'data' => []])
                                        </div>
                                    </div>
{{--                                    <div class="col-sm-12">--}}
{{--                                        <div class="pagination1">{{ $rejectreasons->links() }}</div>--}}
{{--                                    </div>--}}
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <label class="modal-title text-text-bold-600"
                               id="myModalLabel33"><b>@lang('admin.message702')
                                (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</b></label>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" enctype="multipart/form-data" action="{{ route('rejectreason.store') }}">
                        @csrf
                        <div class="modal-body">

                            <label>@lang("$string_file.title") <span class="text-danger">*</span></label>
                            <div class="form-group">
                                <input type="text" class="form-control" id="title" name="title"
                                       placeholder="@lang('admin.message627')"/>
                            </div>

                            <label> @lang('admin.message703')
                                <span class="text-danger">*</span> </label>
                            <div class="form-group">
                            <textarea class="form-control" id="action" name="action" rows="3"
                                      placeholder="@lang('admin.message704')"></textarea>
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
@endsection
