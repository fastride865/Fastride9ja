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
                    </div>
                    <h3 class="panel-title"><i class="wb-user" aria-hidden="true"></i>
                        @lang("$string_file.application")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              action="{{ route('merchant.application.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.ios_user_app_url")<span class="text-danger">*</span>
                                        </label>
                                        <input type="url" class="form-control"
                                               id="ios_user_link" name="ios_user_link"
                                               placeholder="@lang("$string_file.ios_user_app_url")"
                                               value="@if(!empty($application)) {{ $application->ios_user_link }} @endif"
                                               required>
                                        @if ($errors->has('ios_user_link'))
                                            <label class="danger">{{ $errors->first('ios_user_link') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.ios_driver_app_url")<span class="text-danger">*</span>
                                        </label>
                                        <input type="url" class="form-control" id="ios_driver_link"
                                               name="ios_driver_link"
                                               placeholder="@lang("$string_file.ios_driver_app_url")"
                                               value="@if(!empty($application)) {{ $application->ios_driver_link }} @endif"
                                               required>
                                        @if ($errors->has('ios_driver_link'))
                                            <label class="danger">{{ $errors->first('ios_driver_link') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.android_user_app_url")<span class="text-danger">*</span>
                                        </label>
                                        <input type="url" class="form-control"
                                               id="android_user_link"
                                               name="android_user_link"
                                               placeholder="@lang("$string_file.android_user_app_url")"
                                               value="@if(!empty($application)) {{ $application->android_user_link }} @endif"
                                               required>
                                        @if ($errors->has('android_user_link'))
                                            <label class="danger">{{ $errors->first('android_user_link') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.android_driver_app_url")<span class="text-danger">*</span>
                                        </label>
                                        <input type="url" class="form-control"
                                               id="android_driver_link"
                                               name="android_driver_link"
                                               placeholder="@lang("$string_file.android_driver_app_url")"
                                               value="@if(!empty($application)) {{ $application->android_driver_link }} @endif"
                                               required>
                                        @if ($errors->has('android_driver_link'))
                                            <label class="danger">{{ $errors->first('android_driver_link') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.ios_user_app_id")<span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control"
                                               id="ios_user_appid"
                                               name="ios_user_appid"
                                               placeholder="@lang("$string_file.ios_user_app_id")"
                                               value="@if(!empty($application)) {{ $application->ios_user_appid }} @endif">
                                        @if ($errors->has('ios_user_appid'))
                                            <label class="danger">{{ $errors->first('ios_user_appid') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.ios_driver_app_id")
                                        </label>
                                        <input type="text" class="form-control"
                                               id="ios_driver_appid"
                                               name="ios_driver_appid"
                                               placeholder="@lang("$string_file.ios_driver_app_id")"
                                               value="@if(!empty($application)) {{ $application->ios_driver_appid }} @endif">
                                        @if ($errors->has('ios_driver_appid'))
                                            <label class="danger">{{ $errors->first('ios_driver_appid') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if(Auth::user('merchant')->can('edit_applications_url'))
                                <div class="form-actions right" style="margin-bottom: 3%">
                                    @if(Auth::user('merchant')->can('edit_configuration'))
                                        @if(!$is_demo)
                                            <button type="submit" class="btn btn-primary float-right">
                                                <i class="fa fa-check-square-o"></i> Save
                                            </button>
                                        @else
                                            <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                                        @endif
                                    @endif
                                </div>

                            @endif
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection