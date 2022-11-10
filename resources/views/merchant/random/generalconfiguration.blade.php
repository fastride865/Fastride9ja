@extends('merchant.layouts.main')
@section('content')
    @php $tdt_segment_condition = is_merchant_segment_exist(['TAXI','DELIVERY','TOWING']); @endphp
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
                    <h3 class="panel-title">
                        <i class=" icon fa-gears" aria-hidden="true"></i>
                        @lang("$string_file.general_configuration")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              action="{{ route('merchant.general_configuration.store') }}">
                            @csrf
                            <fieldset>
                                <div class="row">
{{--                                    <div class="col-md-4">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label for="logo_hide">--}}
{{--                                                @lang("$string_file.logo_hide")--}}
{{--                                                <span class="text-danger">*</span>--}}
{{--                                            </label>--}}
{{--                                            <select class="form-control" name="logo_hide"--}}
{{--                                                    id="logo_hide" required>--}}
{{--                                                <option value="1" {{ $app_configuration->logo_hide == 1 ? 'selected' : ''}}>@lang("$string_file.on")</option>--}}
{{--                                                <option value="0" {{ $app_configuration->logo_hide == 0 ? 'selected' : ''}}>@lang("$string_file.off")</option>--}}
{{--                                            </select>--}}
{{--                                            @if ($errors->has('logo_hide'))--}}
{{--                                                <label class="danger">{{ $errors->first('logo_hide') }}</label>--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                    </div>--}}

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.report_issue_email")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" class="form-control"
                                                   id="report_issue_email"
                                                   name="report_issue_email"
                                                   placeholder="@lang("$string_file.report_issue_email")"
                                                   value="{{ $configuration->report_issue_email }}"
                                                   required>
                                            @if ($errors->has('report_issue_email'))
                                                <label class="danger">{{ $errors->first('report_issue_email') }}</label>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                               @lang("$string_file.report_issue_phone")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="report_issue_phone"
                                                   name="report_issue_phone"
                                                   placeholder=""
                                                   value="{{ $configuration->report_issue_phone }}"
                                                   required>
                                            @if ($errors->has('report_issue_phone'))
                                                <label class="danger">{{ $errors->first('report_issue_phone') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.android_user_maintenance_mode")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="android_user_maintenance_mode"
                                                    id="android_user_maintenance_mode" required>
                                                <option value="1"
                                                @if(isset($configuration->android_user_maintenance_mode)) {{$configuration->android_user_maintenance_mode == 1 ? 'selected' : ''}} @endif>@lang("$string_file.on")</option>
                                                <option value="2"
                                                @if(isset($configuration->android_user_maintenance_mode)) {{$configuration->android_user_maintenance_mode == 2 ? 'selected' : ''}} @endif>@lang("$string_file.off")</option>
                                            </select>
                                            @if ($errors->has('android_user_maintenance_mode'))
                                                <label class="danger">{{ $errors->first('android_user_maintenance_mode') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.android_user_app_version")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="android_user_version"
                                                   name="android_user_version"
                                                   placeholder="@lang("$string_file.android_user_app_version")"
                                                   value="{{ $configuration->android_user_version }}"
                                                   required>
                                            @if ($errors->has('android_user_version'))
                                                <label class="danger">{{ $errors->first('android_user_version') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.android_user_app_mandatory_update")
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="android_user_mandatory_update"
                                                    id="android_user_mandatory_update" required>
                                                <option value="1"
                                                @if(isset($configuration->android_user_mandatory_update)) {{$configuration->android_user_mandatory_update == 1 ? 'selected' : ''}} @endif>@lang("$string_file.on")</option>
                                                <option value="2"
                                                @if(isset($configuration->android_user_mandatory_update)) {{$configuration->android_user_mandatory_update == 2 ? 'selected' : ''}} @endif>@lang("$string_file.off")</option>
                                            </select>
                                            @if ($errors->has('android_user_mandatory_update'))
                                                <label class="danger">{{ $errors->first('android_user_mandatory_update') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.android_driver_maintenance_mode")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="android_driver_maintenance_mode"
                                                    id="android_driver_maintenance_mode" required>
                                                <option value="1"
                                                @if(isset($configuration->android_driver_maintenance_mode)) {{$configuration->android_driver_maintenance_mode == 1 ? 'selected' : ''}} @endif>@lang("$string_file.on")</option>
                                                <option value="2"
                                                @if(isset($configuration->android_driver_maintenance_mode)) {{$configuration->android_driver_maintenance_mode == 2 ? 'selected' : ''}} @endif>@lang("$string_file.off")</option>
                                            </select>
                                            @if ($errors->has('android_driver_maintenance_mode'))
                                                <label class="danger">{{ $errors->first('android_driver_maintenance_mode') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                               @lang("$string_file.android_driver_app_version")
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="android_driver_version"
                                                   name="android_driver_version"
                                                   placeholder=""
                                                   value="{{ $configuration->android_driver_version }}"
                                                   required>
                                            @if ($errors->has('android_driver_version'))
                                                <label class="danger">{{ $errors->first('android_driver_version') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.android_driver_app_mandatory_update")
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="android_driver_mandatory_update"
                                                    id="android_driver_mandatory_update" required>
                                                <option value="1"
                                                @if(isset($configuration->android_driver_mandatory_update)) {{$configuration->android_driver_mandatory_update == 1 ? 'selected' : ''}} @endif>@lang("$string_file.on")</option>
                                                <option value="2"
                                                @if(isset($configuration->android_driver_mandatory_update)) {{$configuration->android_driver_mandatory_update == 2 ? 'selected' : ''}} @endif>@lang("$string_file.off")</option>
                                            </select>
                                            @if ($errors->has('android_driver_mandatory_update'))
                                                <label class="danger">{{ $errors->first('android_driver_mandatory_update') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.ios_user_maintenance_mode")
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="ios_user_maintenance_mode"
                                                    id="ios_user_maintenance_mode" required>
                                                <option value="1"
                                                @if(isset($configuration->ios_user_maintenance_mode)) {{$configuration->ios_user_maintenance_mode == 1 ? 'selected' : ''}} @endif>@lang("$string_file.on")</option>
                                                <option value="2"
                                                @if(isset($configuration->ios_user_maintenance_mode)) {{$configuration->ios_user_maintenance_mode == 2 ? 'selected' : ''}} @endif>@lang("$string_file.off")</option>
                                            </select>
                                            @if ($errors->has('ios_user_maintenance_mode'))
                                                <label class="danger">{{ $errors->first('ios_user_maintenance_mode') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                 @lang("$string_file.ios_user_app_version")
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="ios_user_version"
                                                   name="ios_user_version"
                                                   placeholder=""
                                                   value="{{ $configuration->ios_user_version }}"
                                                   required>
                                            @if ($errors->has('ios_user_version'))
                                                <label class="danger">{{ $errors->first('ios_user_version') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.ios_user_app_mandatory_update")<span
                                                        class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="ios_user_mandatory_update"
                                                    id="ios_user_mandatory_update" required>
                                                <option value="1"
                                                @if(isset($configuration->ios_user_mandatory_update)) {{$configuration->ios_user_mandatory_update == 1 ? 'selected' : ''}} @endif>@lang("$string_file.on")</option>
                                                <option value="2"
                                                @if(isset($configuration->ios_user_mandatory_update)) {{$configuration->ios_user_mandatory_update == 2 ? 'selected' : ''}} @endif>@lang("$string_file.off")</option>
                                            </select>
                                            @if ($errors->has('ios_user_mandatory_update'))
                                                <label class="danger">{{ $errors->first('ios_user_mandatory_update') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.ios_driver_maintenance_mode")<span
                                                        class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="ios_driver_maintenance_mode"
                                                    id="ios_driver_maintenance_mode" required>
                                                <option value="1"
                                                @if(isset($configuration->ios_driver_maintenance_mode)) {{$configuration->ios_driver_maintenance_mode == 1 ? 'selected' : ''}} @endif>@lang("$string_file.on")</option>
                                                <option value="2"
                                                @if(isset($configuration->ios_driver_maintenance_mode)) {{$configuration->ios_driver_maintenance_mode == 2 ? 'selected' : ''}} @endif>@lang("$string_file.off")</option>
                                            </select>
                                            @if ($errors->has('ios_driver_maintenance_mode'))
                                                <label class="danger">{{ $errors->first('ios_driver_maintenance_mode') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.ios_driver_app_version")<span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="ios_driver_version"
                                                   name="ios_driver_version"
                                                   placeholder="@lang("$string_file.ios_driver_app_version")"
                                                   value="{{ $configuration->ios_driver_version }}"
                                                   required>
                                            @if ($errors->has('ios_driver_version'))
                                                <label class="danger">{{ $errors->first('ios_driver_version') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.android_driver_app_mandatory_update")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="ios_driver_mandatory_update"
                                                    id="ios_driver_mandatory_update" required>
                                                <option value="1"
                                                @if(isset($configuration->ios_driver_mandatory_update)) {{$configuration->ios_driver_mandatory_update == 1 ? 'selected' : ''}} @endif>@lang("$string_file.on")</option>
                                                <option value="2"
                                                @if(isset($configuration->ios_driver_mandatory_update)) {{$configuration->ios_driver_mandatory_update == 2 ? 'selected' : ''}} @endif>@lang("$string_file.off")</option>
                                            </select>
                                            @if ($errors->has('ios_driver_mandatory_update'))
                                                <label class="text-danger">{{ $errors->first('ios_driver_mandatory_update') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.admin_application_default_language")<span
                                                        class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="default_language"
                                                    id="default_language" required>
                                                @foreach($languages as $language)
                                                    <option value="{{ $language->locale }}" {{ $configuration->default_language == $language->locale ? 'selected' : ''}}>{{ $language->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('default_language'))
                                                <label class="danger">{{ $errors->first('default_language') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.user_application_default_language")<span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="user_default_language"
                                                    id="user_default_language" required>
                                                @foreach($languages as $language)
                                                    <option value="{{ $language->locale }}" {{ $app_configuration->user_default_language == $language->locale ? 'selected' : ''}}>{{ $language->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('user_default_language'))
                                                <label class="danger">{{ $errors->first('user_default_language') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                    @lang("$string_file.driver_application_default_language")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="driver_default_language"
                                                    id="driver_default_language" required>
                                                @foreach($languages as $language)
                                                    <option value="{{ $language->locale }}" {{ $app_configuration->driver_default_language == $language->locale ? 'selected' : ''}}>{{ $language->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('driver_default_language'))
                                                <label class="danger">{{ $errors->first('driver_default_language') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($configuration->user_wallet_status == 1)
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="firstName3">
                                                    @lang("$string_file.user_wallet_short_values")
                                                </label>
                                                @php $a = json_decode($configuration->user_wallet_amount,true);  @endphp
                                                <input type="text" class="form-control"
                                                       id="user_wallet_amount"
                                                       name="user_wallet_amount[]"
                                                       placeholder="@lang("$string_file.enter_amount")"
                                                       value="@if(array_key_exists(0, $a)) {{ $a[0]['amount'] }} @endif"
                                                       required>
                                                @if ($errors->has('user_wallet_amount'))
                                                    <label class="danger">{{ $errors->first('user_wallet_amount') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label style="visibility: hidden" for="firstName3">
                                                    @lang('admin.message144')<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="user_wallet_amount"
                                                       name="user_wallet_amount[]"
                                                       placeholder="@lang("$string_file.enter_value") 2"
                                                       value="@if(array_key_exists(1, $a)) {{ $a[1]['amount'] }} @endif"
                                                       required>
                                                @if ($errors->has('user_wallet_amount'))
                                                    <label class="danger">{{ $errors->first('user_wallet_amount') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label style="visibility: hidden" for="firstName3">
                                                    @lang('admin.message144')<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="user_wallet_amount"
                                                       name="user_wallet_amount[]"
                                                       placeholder="@lang("$string_file.enter_value") 3"
                                                       value="@if(array_key_exists(2, $a)) {{ $a[2]['amount'] }} @endif"
                                                       required>
                                                @if ($errors->has('user_wallet_amount'))
                                                    <label class="danger">{{ $errors->first('user_wallet_amount') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($configuration->driver_wallet_status == 1)
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="firstName3">
                                                    @lang("$string_file.driver_wallet_short_values")
                                                </label>
                                                @php $b = json_decode($configuration->driver_wallet_amount,true);  @endphp
                                                <input type="text" class="form-control"
                                                       id="driver_wallet_amount"
                                                       name="driver_wallet_amount[]"
                                                       placeholder="@lang("$string_file.enter_amount")"
                                                       value="@if(array_key_exists(0, $b)) {{ $b[0]['amount'] }} @endif"
                                                       required>
                                                @if ($errors->has('driver_wallet_amount'))
                                                    <label class="danger">{{ $errors->first('driver_wallet_amount') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label style="visibility: hidden" for="firstName3">
                                                    @lang('admin.message144')<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="driver_wallet_amount"
                                                       name="driver_wallet_amount[]"
                                                       placeholder="@lang("$string_file.enter_value") 2"
                                                       value="@if(array_key_exists(1, $b)) {{ $b[1]['amount'] }} @endif"
                                                       required>
                                                @if ($errors->has('driver_wallet_amount'))
                                                    <label class="danger">{{ $errors->first('driver_wallet_amount') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label style="visibility: hidden" for="firstName3">
                                                    @lang('admin.message144')<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="driver_wallet_amount"
                                                       name="driver_wallet_amount[]"
                                                       placeholder="@lang("$string_file.enter_value") 3"
                                                       value="@if(array_key_exists(2, $b)) {{ $b[2]['amount'] }} @endif"
                                                       required>
                                                @if ($errors->has('driver_wallet_amount'))
                                                    <label class="danger">{{ $errors->first('driver_wallet_amount') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($app_configuration->tip_status == 1)
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="firstName3">
                                                    @lang("$string_file.user_tip_short_values")
                                                </label>
                                                @php $b = !empty($app_configuration->tip_short_amount) ? json_decode($app_configuration->tip_short_amount,true) : [];  @endphp
                                                <input type="text" class="form-control"
                                                       id="tip_short_amount"
                                                       name="tip_short_amount[]"
                                                       placeholder="@lang("$string_file.enter_amount")"
                                                       value="@if(array_key_exists(0, $b)) {{ $b[0]['amount'] }} @endif"
                                                       required>
                                                @if ($errors->has('tip_short_amount'))
                                                    <label class="danger">{{ $errors->first('tip_short_amount') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label style="visibility: hidden" for="firstName3">
                                                    @lang('admin.message144')<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="tip_short_amount"
                                                       name="tip_short_amount[]"
                                                       placeholder="@lang("$string_file.enter_value") 2"
                                                       value="@if(array_key_exists(1, $b)) {{ $b[1]['amount'] }} @endif"
                                                       required>
                                                @if ($errors->has('tip_short_amount'))
                                                    <label class="danger">{{ $errors->first('tip_short_amount') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label style="visibility: hidden" for="firstName3">
                                                    @lang('admin.message144')<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="tip_short_amount"
                                                       name="tip_short_amount[]"
                                                       placeholder="@lang("$string_file.enter_value") 3"
                                                       value="@if(array_key_exists(2, $b)) {{ $b[2]['amount'] }} @endif"
                                                       required>
                                                @if ($errors->has('tip_short_amount'))
                                                    <label class="danger">{{ $errors->first('tip_short_amount') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.reminder_expire_doc")<span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="reminder_expire_doc"
                                                   name="reminder_expire_doc"
                                                   placeholder="@lang("$string_file.reminder_expire_doc")"
                                                   value="{{$configuration->reminder_doc_expire}}"
                                                   required>
                                            @if ($errors->has('reminder_expire_doc'))
                                                <label class="danger">{{ $errors->first('reminder_expire_doc') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                @if($tdt_segment_condition)

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.fare_policy_text")<span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="fare_policy_text"
                                                   name="fare_policy_text"
                                                   placeholder="@lang("$string_file.fare_policy_text")"
                                                   value="{{$configuration->fare_policy_text}}"
                                                   required>
                                            @if ($errors->has('fare_policy_text'))
                                                <label class="danger">{{ $errors->first('fare_policy_text') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.api_version")<span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="api_version"
                                                   name="api_version"
                                                   placeholder="@lang("$string_file.api_version")"
                                                   value="{{isset($version_management->api_version) ? $version_management->api_version : '0.1'}}"
                                                   required>
                                            @if ($errors->has('api_version'))
                                                <label class="danger">{{ $errors->first('api_version') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if(isset($configuration->twilio_call_masking) && $configuration->twilio_call_masking == 1)
                                    <br>
                                    <h5 class="form-section">
                                        <i class="fa fa-taxi"></i> @lang('admin.twilio_call_masking_configuration')
                                    </h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="twilio_sid">
                                                    @lang('admin.twilio_sid')<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="twilio_sid"
                                                       name="twilio_sid"
                                                       placeholder="@lang('admin.message168')"
                                                       value="{{ $configuration->twilio_sid }}"
                                                       required>
                                                @if ($errors->has('twilio_sid'))
                                                    <label class="danger">{{ $errors->first('twilio_sid') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="twilio_service_id">
                                                    @lang('admin.twilio_service_id')<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="twilio_service_id"
                                                       name="twilio_service_id"
                                                       placeholder="@lang('admin.twilio_service_id')"
                                                       value="{{ $configuration->twilio_service_id }}"
                                                       required>
                                                @if ($errors->has('twilio_service_id'))
                                                    <label class="danger">{{ $errors->first('twilio_service_id') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="twilio_token">
                                                    @lang('admin.twilio_token')<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="twilio_token"
                                                       name="twilio_token"
                                                       placeholder="@lang('admin.twilio_token')"
                                                       value="{{ $configuration->twilio_token }}"
                                                       required>
                                                @if ($errors->has('twilio_token'))
                                                    <label class="danger">{{ $errors->first('twilio_token') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(isset($configuration->face_recognition_feature) && $configuration->face_recognition_feature == 1)
                                    <br>
                                    <h5 class="form-section">
                                        <i class="fa fa-taxi"></i> @lang("$string_file.face_recognition_configuration")
                                    </h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="face_recognition_end_point">
                                                    @lang("$string_file.face_recognition_end_point")<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="face_recognition_end_point"
                                                       name="face_recognition_end_point"
                                                       placeholder="@lang("$string_file.face_recognition_end_point")"
                                                       value="{{ $configuration->face_recognition_end_point }}"
                                                       required>
                                                @if ($errors->has('face_recognition_end_point'))
                                                    <label class="danger">{{ $errors->first('face_recognition_end_point') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="face_recognition_subscription_key">
                                                    @lang("$string_file.face_recognition_subscription_key")<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="face_recognition_subscription_key"
                                                       name="face_recognition_subscription_key"
                                                       placeholder="@lang("$string_file.face_recognition_subscription_key")"
                                                       value="{{ $configuration->face_recognition_subscription_key }}"
                                                       required>
                                                @if ($errors->has('face_recognition_subscription_key'))
                                                    <label class="danger">{{ $errors->first('face_recognition_subscription_key') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="face_recognition_for_user_register">
                                                    @lang("$string_file.face_recognition_for_user_register")<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <select class="form-control"
                                                        name="face_recognition_for_user_register"
                                                        id="face_recognition_for_user_register" required>
                                                    <option value="1" {{ $configuration->face_recognition_for_user_register == 1 ? 'selected' : ''}}>@lang("$string_file.yes")</option>
                                                    <option value="2" {{ $configuration->face_recognition_for_user_register == 2 ? 'selected' : ''}}>@lang("$string_file.no")</option>
                                                </select>
                                                @if ($errors->has('face_recognition_for_user_register'))
                                                    <label class="danger">{{ $errors->first('face_recognition_for_user_register') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="face_recognition_for_driver_register">
                                                    @lang("$string_file.face_recognition_for_driver_register")<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <select class="form-control"
                                                        name="face_recognition_for_driver_register"
                                                        id="face_recognition_for_driver_register" required>
                                                    <option value="1" {{ $configuration->face_recognition_for_driver_register == 1 ? 'selected' : ''}}>@lang("$string_file.yes")</option>
                                                    <option value="2" {{ $configuration->face_recognition_for_driver_register == 2 ? 'selected' : ''}}>@lang("$string_file.no")</option>
                                                </select>
                                                @if ($errors->has('face_recognition_for_driver_register'))
                                                    <label class="danger">{{ $errors->first('face_recognition_for_driver_register') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="face_recognition_for_driver_online_offline">
                                                    @lang("$string_file.face_recognition_for_driver_online_offline")<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <select class="form-control"
                                                        name="face_recognition_for_driver_online_offline"
                                                        id="face_recognition_for_driver_online_offline" required>
                                                    <option value="1" {{ $configuration->face_recognition_for_driver_online_offline == 1 ? 'selected' : ''}}>@lang("$string_file.yes")</option>
                                                    <option value="2" {{ $configuration->face_recognition_for_driver_online_offline == 2 ? 'selected' : ''}}>@lang("$string_file.no")</option>
                                                </select>
                                                @if ($errors->has('face_recognition_for_driver_online_offline'))
                                                    <label class="danger">{{ $errors->first('face_recognition_for_driver_online_offline') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </fieldset>
                            <div class="form-actions right" style="margin-bottom: 3%">
                                @if(Auth::user('merchant')->can('edit_configuration'))
                                    @if(!$is_demo)
                                    <button type="submit" class="btn btn-primary float-right">
                                        <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                                    </button>
                                    @else
                                        <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                                    @endif
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection