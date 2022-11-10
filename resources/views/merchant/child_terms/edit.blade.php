@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('success'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{session('success')}}
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                        <div class="panel-actions">
                            <div class="btn-group float-right" style="margin:10px">
                                <a href="{{ route('child-terms-conditions.index') }}">
                                    <button type="button" class="btn btn-icon btn-success float-right"><i class="wb-reply"></i>
                                    </button>
                                </a>
                            </div>
                        </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        @lang('admin.edit_child_terms') (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('child-terms-conditions.update',$page_data->id) }}">
                        {{method_field('PUT')}}
                        @csrf
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            @lang("$string_file.page_title")<span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="title"
                                               name="title" value="{{$page_data->LangTermsConditionSingle['name']}}"
                                               placeholder="@lang("$string_file.page_title")"
                                               required>
                                        @if ($errors->has('title'))
                                            <label class="danger">{{ $errors->first('title') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang("$string_file.description")<span class="text-danger">*</span>
                                        </label>
                                        <textarea id="summernote" class="form-control"
                                                  name="description" rows="5"
                                                  placeholder="@lang("$string_file.description")" data-plugin="summernote">
                                       {{$page_data->LangTermsConditionSingle['field_three']}}</textarea>
                                        @if ($errors->has('description'))
                                            <label class="danger">{{ $errors->first('description') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="row">
                            </div>
                        </fieldset>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="fa fa-check-circle"></i> @lang("$string_file.update")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection