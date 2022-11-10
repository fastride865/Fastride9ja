@extends('merchant.layouts.main')
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">

            <div class="content-header row">
                <div class="content-header-left col-md-10 col-12 mb-2">
                    <h3 class="content-header-title mb-0 d-inline-block">@lang('admin.message233')
                        (@lang('admin.message460') {{ strtoupper(Config::get('app.locale')) }})</h3>
                </div>
                <div class="content-header-right col-md-2 col-12">
                    <div class="btn-group float-md-right">
                        <a href="{{ route('transferpackage.index') }}">
                            <button type="button" class="btn btn-icon btn-success mr-1"><i class="fa fa-reply"></i>
                            </button>
                        </a>
                    </div>
                </div>
            </div>

            @if(session('package'))
                <div class="box no-border">
                    <div class="box-tools">
                        <p class="alert alert-info alert-dismissible">
                            @lang('admin.packagedetial')
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                        </p>
                    </div>
                </div>
            @endif
            <div class="content-body">
                <section id="validation">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                            <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form method="POST" class="steps-validation wizard-notification"
                                              enctype="multipart/form-data"
                                              action="{{route('transferpackage.update', $package->id)}}">
                                            {{method_field('PUT')}}
                                            @csrf
                                            <fieldset>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="firstName3">
                                                                @lang('admin.message99') :
                                                                <span class="danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control" id="name"
                                                                   name="name"
                                                                   value="@if($package->LanguagePackageSingle) {{ $package->LanguagePackageSingle->name }} @endif"
                                                                   placeholder="@lang('admin.message658')" required>
                                                            @if ($errors->has('name'))
                                                                <label class="text-danger">{{ $errors->first('name') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="lastName3">
                                                                @lang('admin.description') :
                                                                <span class="danger">*</span>
                                                            </label>
                                                            <textarea class="form-control" id="description"
                                                                      name="description" rows="3"
                                                                      placeholder="@lang('admin.message648')">@if($package->LanguagePackageSingle) {{ $package->LanguagePackageSingle->description }} @endif</textarea>
                                                            @if ($errors->has('description'))
                                                                <label class="text-danger">{{ $errors->first('description') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="emailAddress5">
                                                                @lang('admin.message101') :
                                                                <span class="danger">*</span>
                                                            </label>
                                                            <textarea class="form-control" id="terms_conditions"
                                                                      name="terms_conditions" rows="3"
                                                                      placeholder="@lang('admin.message659')">@if($package->LanguagePackageSingle) {{ $package->LanguagePackageSingle->terms_conditions }} @endif</textarea>
                                                            @if ($errors->has('terms_conditions'))
                                                                <label class="text-danger">{{ $errors->first('terms_conditions') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                            </fieldset>
                                            <div class="form-actions right">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa fa-check-circle"></i> Save
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
@endsection