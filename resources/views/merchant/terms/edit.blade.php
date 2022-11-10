@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('cmsupdate'))
                <div class="alert dark alert-icon alert-success alert-dismissible">
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.terms_condition_updated')
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span>
                            </button>
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('terms.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        @lang('admin.message371') (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('terms.update',$cmspage->id) }}">
                        {{method_field('PUT')}}
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.page_title")<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="title"
                                           name="title"
                                           value="@if(!empty($cmspage->LanguageSingle)){{ $cmspage->LanguageSingle->title }} @endif"
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
                                        @lang("$string_file.description")<span
                                                class="text-danger">*</span>
                                    </label>
                                    <textarea id="summernote" class="form-control"
                                              name="description" rows="5"
                                              placeholder="@lang("$string_file.description")" data-plugin="summernote">
                                        @if(!empty($cmspage->LanguageSingle)){{ $cmspage->LanguageSingle->description }} @endif</textarea>
                                    @if ($errors->has('description'))
                                        <label class="danger">{{ $errors->first('description') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
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
