@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->edit_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <a href="{{ route('packages.index') }}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.view_package")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang("$string_file.edit_package") {{ strtoupper(Config::get('app.locale')) }})</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification" enctype="multipart/form-data"
                          action="{{route('packages.update', $package->id)}}"> {{method_field('PUT')}}
                        @csrf
                        {!! Form::hidden('service_type_id',$package->service_type_id) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="emailAddress5">@lang("$string_file.package_name")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="@if($package->LanguagePackageSingle) {{ $package->LanguagePackageSingle->name }} @endif"
                                           placeholder="" required/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="lastName3">@lang("$string_file.description")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="description" rows="3" name="description"
                                              placeholder="">@if($package->LanguagePackageSingle) {{ $package->LanguagePackageSingle->description }} @endif</textarea>
                                    @if ($errors->has('description'))
                                        <label class="text-danger">{{ $errors->first('description') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="lastName3">@lang("$string_file.terms_conditions")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="terms_conditions" rows="3"
                                              name="terms_conditions"
                                              placeholder="">@if($package->LanguagePackageSingle) {{ $package->LanguagePackageSingle->terms_conditions }} @endif</textarea>
                                    @if ($errors->has('terms_conditions'))
                                        <label class="text-danger">{{ $errors->first('terms_conditions') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions float-right" style="margin-bottom: 1%">
                            @if(!$is_demo)
                            <button type="submit" class="btn btn-primary"><i
                                        class="fa fa-check-circle"></i> @lang("$string_file.save") </button>
                            @else
                                <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'])
@endsection