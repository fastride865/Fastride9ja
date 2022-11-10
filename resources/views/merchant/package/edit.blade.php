@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('package'))
                <div class="alert dark alert-icon alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.packagedetial')
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                        <div class="panel-actions">
                            <a href="{{ route('packages.index') }}" data-toggle="tooltip">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-reply" title="@lang('admin.message98')"></i>
                                </button>
                            </a>
                        </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang('admin.message233') (@lang('admin.message460') {{ strtoupper(Config::get('app.locale')) }})</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification" enctype="multipart/form-data"
                          action="{{route('packages.update', $package->id)}}"> {{method_field('PUT')}}
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="emailAddress5">@lang('admin.message99')
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="@if($package->LanguagePackageSingle) {{ $package->LanguagePackageSingle->name }} @endif"
                                           placeholder="@lang('admin.message658')" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="lastName3">@lang('admin.description')
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="description" rows="3" name="description"
                                        placeholder="@lang('admin.message648')">@if($package->LanguagePackageSingle) {{ $package->LanguagePackageSingle->description }} @endif</textarea>
                                    @if ($errors->has('description'))
                                        <label class="text-danger">{{ $errors->first('description') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="lastName3">@lang('admin.message101')
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="terms_conditions" rows="3" name="terms_conditions"
                                              placeholder="@lang('admin.message659')">@if($package->LanguagePackageSingle) {{ $package->LanguagePackageSingle->terms_conditions }} @endif</textarea>
                                    @if ($errors->has('terms_conditions'))
                                        <label class="text-danger">{{ $errors->first('terms_conditions') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions float-right" style="margin-bottom: 1%">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i> @lang('admin.update')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection