@extends('merchant.layouts.main')
@section('content')
    @php $tdt_segment_condition = is_merchant_segment_exist(['TAXI','DELIVERY','TOWING']); @endphp
    <div class="page">
        <div class="page-content">
            @if(session('applicationtheme'))
                <div class="alert dark alert-icon alert-success alert-dismissible"
                     role="alert">
                    <button type="button" class="close" data-dismiss="alert"
                            aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message8611')
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title">
                        <i class="icon fa-paint-brush" aria-hidden="true"></i>
                        @lang('admin.ApplicationTheme')
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('merchant.applicationtheme.submit') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.primary_color_user')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="color" class="form-control"
                                           id="primary_color_user" name="primary_color_user"
                                           placeholder="@lang('admin.message87')"
                                           value="@if($applicationtheme){!!$applicationtheme->primary_color_user!!}@endif"
                                           required>
                                    @if ($errors->has('primary_color_user'))
                                        <label class="danger">{{ $errors->first('primary_color_user') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.primary_color_driver')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="color" class="form-control"
                                           id="primary_color_driver"
                                           name="primary_color_driver"
                                           placeholder="@lang('admin.message88')"
                                           value="@if(!empty($applicationtheme)){{$applicationtheme->primary_color_driver}}"
                                           @endif required>
                                    @if ($errors->has('primary_color_driver'))
                                        <label class="danger">{{ $errors->first('primary_color_driver') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.chat_button_color')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="color" class="form-control"
                                           id="chat_button_color"
                                           name="chat_button_color"
                                           placeholder="@lang('admin.message89')"
                                           value="@if(!empty($applicationtheme)){{$applicationtheme->chat_button_color}}@endif"
                                           required>
                                    @if ($errors->has('chat_button_color'))
                                        <label class="danger">{{ $errors->first('chat_button_color') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.chat_button_color_driver')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="color" class="form-control"
                                           id="chat_button_color_driver"
                                           name="chat_button_color_driver"
                                           placeholder="@lang('admin.message90')"
                                           value="@if(!empty($applicationtheme)){{$applicationtheme->chat_button_color_driver}}"
                                           @endif required>
                                    @if ($errors->has('chat_button_color_driver'))
                                        <label class="danger">{{ $errors->first('chat_button_color_driver') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.share_button_color')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="color" class="form-control"
                                           id="share_button_color"
                                           name="share_button_color"
                                           placeholder="@lang('admin.message89')"
                                           value="@if(!empty($applicationtheme)){{$applicationtheme->share_button_color}}@endif"
                                           required>
                                    @if ($errors->has('share_button_color'))
                                        <label class="danger">{{ $errors->first('share_button_color') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.share_button_color_driver')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="color" class="form-control"
                                           id="share_button_color_driver"
                                           name="share_button_color_driver"
                                           placeholder="@lang('admin.message90')"
                                           value="@if(!empty($applicationtheme)){{$applicationtheme->share_button_color_driver}}@endif"
                                           required>
                                    @if ($errors->has('share_button_color_driver'))
                                        <label class="danger">{{ $errors->first('share_button_color_driver') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.cancel_button_color')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="color" class="form-control"
                                           id="cancel_button_color"
                                           name="cancel_button_color"
                                           placeholder="@lang('admin.message89')"
                                           value="@if(!empty($applicationtheme)){{$applicationtheme->cancel_button_color}}@endif"
                                           required>
                                    @if ($errors->has('cancel_button_color'))
                                        <label class="danger">{{ $errors->first('cancel_button_color') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.cancel_button_color_driver')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="color" class="form-control"
                                           id="cancel_button_color_driver"
                                           name="cancel_button_color_driver"
                                           placeholder="@lang('admin.message90')"
                                           value="@if(!empty($applicationtheme)){{$applicationtheme->cancel_button_color_driver}}@endif"
                                           required>
                                    @if ($errors->has('cancel_button_color_driver'))
                                        <label class="danger">{{ $errors->first('cancel_button_color_driver') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.call_button_color')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="color" class="form-control"
                                           id="call_button_color"
                                           name="call_button_color"
                                           placeholder="@lang('admin.message89')"
                                           value="@if(!empty($applicationtheme)){{$applicationtheme->call_button_color}}@endif"
                                           required>
                                    @if ($errors->has('call_button_color'))
                                        <label class="danger">{{ $errors->first('call_button_color') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.call_button_color_driver')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="color" class="form-control"
                                           id="call_button_color_driver"
                                           name="call_button_color_driver"
                                           placeholder="@lang('admin.message90')"
                                           value="@if(!empty($applicationtheme)){{$applicationtheme->call_button_color_driver}}@endif"
                                           required>
                                    @if ($errors->has('call_button_color_driver'))
                                        <label class="danger">{{ $errors->first('call_button_color_driver') }}</label>
                                    @endif
                                </div>
                            </div>
                            @if($tdt_segment_condition)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang('admin.message746')<span class="text-danger">*</span>
                                        </label>
                                        <input type="color" class="form-control" id="pickup_color"
                                               name="pickup_color"
                                               placeholder="@lang('admin.message746')"
                                               value="{{ $application_config->pickup_color }}"
                                               required>
                                        @if ($errors->has('pickup_color'))
                                            <label class="danger">{{ $errors->first('pickup_color') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang('admin.message747')<span class="text-danger">*</span>
                                        </label>
                                        <input type="color" class="form-control" id="dropoff_color"
                                               name="dropoff_color"
                                               placeholder="@lang('admin.message747')"
                                               value="{{ $application_config->dropoff_color }}"
                                               required>
                                        @if ($errors->has('dropoff_color'))
                                            <label class="danger">{{ $errors->first('dropoff_color') }}</label>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            @if(Auth::user('merchant')->can('edit_application_theme'))
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
