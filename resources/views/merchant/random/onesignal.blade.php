@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
                @php
                    $config = get_merchant_notification_provider(null,null,null,"full");
                    $firebase_required = isset($config->fire_base) && $config->fire_base == true ? "required" : "";
                    $firebase_required_file = !empty($firebase_required) && empty($config->id) ? "required" : "";
                    $onesignal_required = empty($firebase_required) ? "required" : "";
                    $heading = empty($firebase_required) ? trans("$string_file.onesignal") : trans("$string_file.firebase");
                    $dummy_data = "******";
                @endphp
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
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                        {!! $heading !!} @lang("$string_file.configuration")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('merchant.onesignal.submit') }}">
                        @csrf

                            @if(!empty($config->push_notification_provider) && ($config->push_notification_provider == 1 || $config->push_notification_provider == 3))
                            <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="web_application_key">
                                        @lang("$string_file.web_onesignal_app_key")
                                    </label>
                                    <input type="text" class="form-control"
                                           id="web_application_key" name="web_application_key"
                                           placeholder=""
                                           value="{{ !$is_demo ? $onesignal['web_application_key'] : $dummy_data }}">
                                    @if ($errors->has('web_application_key'))
                                        <label class="danger">{{ $errors->first('web_application_key') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="web_rest_key">
                                        @lang("$string_file.web_onesignal_rest_key")
                                    </label>
                                    <input type="text" class="form-control" id="web_rest_key"
                                           name="web_rest_key"
                                           placeholder=""
                                           value="{{ !$is_demo ? $onesignal['web_rest_key'] : $dummy_data }}">
                                    @if ($errors->has('web_rest_key'))
                                        <label class="danger">{{ $errors->first('web_rest_key') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.user_application_key")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="user_application_key" name="user_application_key"
                                           placeholder=""
                                           value="{{ !$is_demo ? $onesignal->user_application_key : $dummy_data }}"
                                           {!! $onesignal_required !!}>
                                    @if ($errors->has('user_application_key'))
                                        <label class="danger">{{ $errors->first('user_application_key') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.user_rest_key")<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="user_rest_key"
                                           name="user_rest_key"
                                           placeholder=""
                                           value="{{ !$is_demo ?  $onesignal->user_rest_key : $dummy_data }}" {!! $onesignal_required !!}>
                                    @if ($errors->has('user_rest_key'))
                                        <label class="danger">{{ $errors->first('user_rest_key') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.user_channel_id")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="user_channel_id"
                                               name="user_channel_id"
                                               placeholder=""
                                               value="{{ !$is_demo ? $onesignal->user_channel_id : $dummy_data }}" {!! $onesignal_required !!}>
                                        @if ($errors->has('user_channel_id'))
                                            <label class="danger">{{ $errors->first('user_channel_id') }}</label>
                                        @endif
                                    </div>
                                </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.driver_application_key")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="driver_application_key"
                                           name="driver_application_key"
                                           placeholder=""
                                           value="{{ !$is_demo ? $onesignal->driver_application_key : $dummy_data }}"
                                           {!! $onesignal_required !!}>
                                    @if ($errors->has('driver_application_key'))
                                        <label class="danger">{{ $errors->first('driver_application_key') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.driver_rest_key")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="driver_rest_key"
                                           name="driver_rest_key"
                                           placeholder=""
                                           value="{{ !$is_demo ? $onesignal->driver_rest_key : $dummy_data }}" {!! $onesignal_required !!}>
                                    @if ($errors->has('driver_rest_key'))
                                        <label class="danger">{{ $errors->first('driver_rest_key') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.driver_channel_id")<span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="driver_channel_id"
                                               name="driver_channel_id"
                                               placeholder=""
                                               value="{{ !$is_demo ?  $onesignal->driver_channel_id : $dummy_data }}" {!! $onesignal_required !!}>
                                        @if ($errors->has('driver_channel_id'))
                                            <label class="danger">{{ $errors->first('driver_channel_id') }}</label>
                                        @endif
                                    </div>
                            </div>
{{--                                Business segment keys--}}
                                @if($food_grocery)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.business_segment_application_key")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="business_segment_application_key"
                                                   name="business_segment_application_key"
                                                   placeholder=""
                                                   value="{{ !$is_demo ? $onesignal->business_segment_application_key : $dummy_data }}"
                                                    >
                                            @if ($errors->has('business_segment_application_key'))
                                                <label class="danger">{{ $errors->first('business_segment_application_key') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.business_segment_rest_key")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="business_segment_rest_key"
                                                   name="business_segment_rest_key"
                                                   placeholder=""
                                                   value="{{ !$is_demo ? $onesignal->business_segment_rest_key : $dummy_data }}">
                                            @if ($errors->has('business_segment_rest_key'))
                                                <label class="danger">{{ $errors->first('business_segment_rest_key') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.business_segment_channel_id")<span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="driver_channel_id"
                                               name="business_segment_channel_id"
                                               placeholder=""
                                               value="{{ !$is_demo ?  $onesignal->business_segment_channel_id : $dummy_data }}">
                                        @if ($errors->has('driver_channel_id'))
                                            <label class="danger">{{ $errors->first('business_segment_channel_id') }}</label>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif
                            @if(!empty($config->push_notification_provider) && ($config->push_notification_provider == 2 || $config->push_notification_provider == 3))
                            <hr>

                                    <h3 class="panel-title">
                                        <i class=" wb-user-plus" aria-hidden="true"></i>
                                        @lang("$string_file.firebase_configuration")
                                    </h3>
{{--                            <h1>@lang("$string_file.firebase_configuration")</h1>--}}
                            <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.firebase_api_key")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="driver_rest_key"
                                           name="firebase_api_key_android"
                                           placeholder=""
                                           value="{{ !$is_demo ? $onesignal->firebase_api_key_android : $dummy_data }}" {!! $firebase_required !!}>
                                    @if ($errors->has('firebase_api_key_android'))
                                        <label class="danger">{{ $errors->first('firebase_api_key_android') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.firebase_ios_pem_user")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="document"
                                           name="firebase_ios_pem_user" placeholder="" {!! $firebase_required_file !!}>
                                    @if ($errors->has('firebase_ios_pem_user'))
                                        <label class="text-danger">{{ $errors->first('firebase_ios_pem_user') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.pem_password_user")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="pem_password_user"
                                           name="pem_password_user"
                                           placeholder=""
                                           value="{{ !$is_demo ? $onesignal->pem_password_user : $dummy_data }}" {!! $firebase_required !!}>
                                    @if ($errors->has('pem_password_user'))
                                        <label class="danger">{{ $errors->first('pem_password_user') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.firebase_ios_pem_driver")<span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="document"
                                           name="firebase_ios_pem_driver" placeholder="" {!! $firebase_required_file !!}>
                                    @if ($errors->has('firebase_ios_pem_driver'))
                                        <label class="text-danger">{{ $errors->first('firebase_ios_pem_driver') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.pem_password_driver")<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="pem_password_driver"
                                           name="pem_password_driver"
                                           placeholder=""
                                           value="{{ !$is_demo ? $onesignal->pem_password_driver : $dummy_data }}" {!! $firebase_required !!}>
                                    @if ($errors->has('pem_password_driver'))
                                        <label class="danger">{{ $errors->first('pem_password_driver') }}</label>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            @if(Auth::user('merchant')->can('edit_onesignal'))
                            @if(!$is_demo)
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                                </button>
                            @else
                                <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                            @endif
                        </div>
                            @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
