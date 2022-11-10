@extends('merchant.layouts.main')
@section('content')
    <div class="container-fluid">
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row">
                    <div class="col-md-8">
                        @if(session('configuration'))
                            <div class="col-md-6 alert alert-icon-right alert-info alert-dismissible mb-2"
                                 role="alert">
                                <span class="alert-icon"><i class="fa fa-info"></i></span>
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <strong>@lang('admin.message110')</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="content-body">
                    <section id="validation">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="content-header-title mb-0 d-inline-block"><i class="fa fa-cogs"></i> @lang('admin.message109')</h3>

                                    </div>
                                    <div class="card-content collapse show">
                                        <div class="card-body">
                                            <form method="POST" class="steps-validation wizard-notification"
                                                  enctype="multipart/form-data"
                                                  action="{{ route('merchant.configuration.store') }}">
                                                @csrf
                                                <fieldset>
                                                    <h4 class="form-section" style="color: black"><i class="fa fa-list-ol"></i> @lang("$string_file.general_configuration")</h4>
                                                    <hr>
                                                    <div class="row">

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang("$string_file.home_screen_view")<span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control" name="home_screen"
                                                                        id="home_screen" required>
                                                                    <option value="1"
                                                                    @if(isset($configuration->home_screen)) {{$configuration->home_screen == 1 ? 'selected' : ''}} @endif>@lang("$string_file.category_view")</option>
                                                                    <option value="2"
                                                                    @if(isset($configuration->home_screen)) {{$configuration->home_screen == 2 ? 'selected' : ''}} @endif>@lang("$string_file.service_view")</option>
                                                                </select>
                                                                @if ($errors->has('home_screen'))
                                                                    <label class="danger">{{ $errors->first('home_screen') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang('admin.message111')<span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control" name="driver_wallet"
                                                                        id="driver_wallet" required>
                                                                    <option value="1" {{ $configuration->driver_wallet_status == 1 ? 'selected' : ''}}>@lang("$string_file.on")</option>
                                                                    <option value="2" {{ $configuration->driver_wallet_status == 2 ? 'selected' : ''}}>@lang("$string_file.off")</option>
                                                                </select>
                                                                @if ($errors->has('driver_wallet'))
                                                                    <label class="danger">{{ $errors->first('driver_wallet') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang('admin.message147')<span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                       id="driver_request_timeout"
                                                                       name="driver_request_timeout"
                                                                       placeholder="@lang('admin.message148')"
                                                                       value="{{ $configuration->driver_request_timeout }}"
                                                                       required>
                                                                @if ($errors->has('driver_request_timeout'))
                                                                    <label class="danger">{{ $errors->first('driver_request_timeout') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang('admin.message121')<span class="text-danger">*</span>
                                                                </label>
                                                                <input type="number" class="form-control"
                                                                       id="number_of_driver_user_map"
                                                                       name="number_of_driver_user_map"
                                                                       placeholder="@lang('admin.message149')"
                                                                       value="{{ $configuration->number_of_driver_user_map }}"
                                                                       required>
                                                                @if ($errors->has('number_of_driver_user_map'))
                                                                    <label class="danger">{{ $errors->first('number_of_driver_user_map') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang('admin.message122')<span class="text-danger">*</span>
                                                                </label>
                                                                <input type="number" class="form-control"
                                                                       id="location_update_timeband"
                                                                       name="location_update_timeband"
                                                                       placeholder="@lang('admin.message148')"
                                                                       value="{{ $configuration->location_update_timeband }}"
                                                                       required>
                                                                @if ($errors->has('location_update_timeband'))
                                                                    <label class="danger">{{ $errors->first('location_update_timeband') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang("$string_file.tracking_screen_time_band")<span class="text-danger">*</span>
                                                                </label>
                                                                <input type="number" class="form-control"
                                                                       id="tracking_screen_refresh_timeband"
                                                                       name="tracking_screen_refresh_timeband"
                                                                       placeholder="@lang('admin.message148')"
                                                                       value="{{ $configuration->tracking_screen_refresh_timeband }}"
                                                                       required>
                                                                @if ($errors->has('tracking_screen_refresh_timeband'))
                                                                    <label class="danger">{{ $errors->first('tracking_screen_refresh_timeband') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang("$string_file.report_issue_email")<span class="text-danger">*</span>
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

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                   @lang("$string_file.report_issue_phone")<span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                       id="report_issue_phone"
                                                                       name="report_issue_phone"
                                                                       placeholder="@lang('admin.message119')"
                                                                       value="{{ $configuration->report_issue_phone }}"
                                                                       required>
                                                                @if ($errors->has('report_issue_phone'))
                                                                    <label class="danger">{{ $errors->first('report_issue_phone') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang('admin.message144')
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

                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label style="visibility: hidden" for="firstName3">
                                                                    @lang('admin.message144')<span class="text-danger">*</span>
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

                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label style="visibility: hidden" for="firstName3">
                                                                    @lang('admin.message144')<span class="text-danger">*</span>
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


                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang('admin.message146')
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

                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label style="visibility: hidden" for="firstName3">
                                                                    @lang('admin.message144')<span class="text-danger">*</span>
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

                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label style="visibility: hidden" for="firstName3">
                                                                    @lang('admin.message144')<span class="text-danger">*</span>
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

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang("$string_file.google_api_key")<span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="google_key"
                                                                       name="google_key"
                                                                       placeholder="@lang('admin.message154')"
                                                                       value="{{ $configuration->google_key }}"
                                                                       required>
                                                                @if ($errors->has('google_key'))
                                                                    <label class="danger">{{ $errors->first('google_key') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang('admin.message436')<span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control"
                                                                        name="default_language"
                                                                        id="default_language" required>
                                                                    @foreach($languages as $language)
                                                                        <option value="{{ $language->locale }}" {{ $configuration->default_language == $language->locale ? 'selected' : ''}}>{{ $language->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @if ($errors->has('android_driver_mandatory_update'))
                                                                    <label class="danger">{{ $errors->first('android_driver_mandatory_update') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <br>
                                                    @if(in_array(1,$service_types))
                                                        <h4 class="form-section" style="color: black"><i
                                                                    class="fa fa-taxi"></i> @lang('admin.message536')</h4>
                                                    <hr>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message120')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="text" class="form-control"
                                                                           id="distance"
                                                                           name="distance"
                                                                           placeholder="@lang("$string_file.enter_radius") ( @lang("$string_file.in_km") )"
                                                                           value="{{ $configuration->distance }}"
                                                                           required>
                                                                    @if ($errors->has('distance'))
                                                                        <label class="danger">{{ $errors->first('distance') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>


                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message116')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="number" class="form-control"
                                                                           id="number_of_driver"
                                                                           name="number_of_driver"
                                                                           placeholder="@lang('admin.message156')"
                                                                           value="{{ $configuration->number_of_driver }}"
                                                                           required>
                                                                    @if ($errors->has('number_of_driver'))
                                                                        <label class="danger">{{ $errors->first('number_of_driver') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message124')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <select class="form-control" name="ride_later_request"
                                                                            id="ride_later_request" required>
                                                                        <option value="1"
                                                                                @if($configuration->ride_later_request == 1) selected @endif>@lang("$string_file.send_to")  @lang("$string_file.all_drivers")</option>
                                                                        <option value="2"
                                                                                @if($configuration->ride_later_request == 2) selected @endif>@lang("$string_file.cron_job")</option>
                                                                    </select>
                                                                    @if ($errors->has('ride_later_request'))
                                                                        <label class="danger">{{ $errors->first('ride_later_request') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message425')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="number" class="form-control"
                                                                           id="ride_later_hours"
                                                                           name="ride_later_hours"
                                                                           placeholder="@lang('admin.message157')"
                                                                           value="{{ $configuration->ride_later_hours }}"
                                                                           required>
                                                                    @if ($errors->has('ride_later_hours'))
                                                                        <label class="danger">{{ $errors->first('ride_later_hours') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message128')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="text" class="form-control"
                                                                           id="distance_ride_later"
                                                                           name="distance_ride_later"
                                                                           placeholder="@lang("$string_file.enter_radius") ( @lang("$string_file.in_km") )"
                                                                           value="{{ $configuration->distance_ride_later }}"
                                                                           required>
                                                                    @if ($errors->has('distance_ride_later'))
                                                                        <label class="danger">{{ $errors->first('distance_ride_later') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message127')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="number" class="form-control"
                                                                           id="ride_later_request_number_driver"
                                                                           name="ride_later_request_number_driver"
                                                                           placeholder="@lang('admin.message156')"
                                                                           value="{{ $configuration->ride_later_request_number_driver }}"
                                                                           required>
                                                                    @if ($errors->has('ride_later_request_number_driver'))
                                                                        <label class="danger">{{ $errors->first('ride_later_request_number_driver') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message543')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="number" class="form-control"
                                                                           id="ride_later_time_before"
                                                                           name="ride_later_time_before"
                                                                           placeholder="@lang('admin.message148')"
                                                                           value="{{ $configuration->ride_later_time_before }}"
                                                                           required>
                                                                    @if ($errors->has('ride_later_time_before'))
                                                                        <label class="danger">{{ $errors->first('ride_later_time_before') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <br>

                                                    @if(in_array(5,$service_types))
                                                        <h4 class="form-section" style="color: black"><i
                                                                    class="fa fa-taxi"></i> @lang('admin.message600')</h4><hr>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message601')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="text" class="form-control"
                                                                           id="pool_radius"
                                                                           name="pool_radius"
                                                                           placeholder="@lang("$string_file.enter_radius") ( @lang("$string_file.in_km") )"
                                                                           value="{{ $configuration->pool_radius }}"
                                                                           required>
                                                                    @if ($errors->has('pool_radius'))
                                                                        <label class="danger">{{ $errors->first('pool_radius') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message602')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="text" class="form-control"
                                                                           id="pool_drop_radius"
                                                                           name="pool_drop_radius"
                                                                           placeholder="@lang("$string_file.enter_radius") ( @lang("$string_file.in_km") )"
                                                                           value="{{ $configuration->pool_drop_radius }}"
                                                                           required>
                                                                    @if ($errors->has('pool_drop_radius'))
                                                                        <label class="danger">{{ $errors->first('pool_drop_radius') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message603')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="number" class="form-control"
                                                                           id="no_of_drivers"
                                                                           name="no_of_drivers"
                                                                           placeholder="@lang('admin.message156')"
                                                                           value="{{ $configuration->no_of_drivers }}"
                                                                           required>
                                                                    @if ($errors->has('no_of_drivers'))
                                                                        <label class="danger">{{ $errors->first('no_of_drivers') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message604')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="number" class="form-control"
                                                                           id="maximum_exceed"
                                                                           name="maximum_exceed"
                                                                           placeholder="@lang('admin.message159')"
                                                                           value="{{ $configuration->maximum_exceed }}"
                                                                           required>
                                                                    @if ($errors->has('maximum_exceed'))
                                                                        <label class="danger">{{ $errors->first('maximum_exceed') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                        </div>
                                                    @endif

                                                    <br>
                                                    @if(in_array(4,$service_types))
                                                        <h4 class="form-section" style="color: black"><i
                                                                    class="fa fa-taxi"></i> @lang('admin.message544')<hr>
                                                        </h4>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message545')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <select class="form-control"
                                                                            name="outstation_request_type"
                                                                            id="outstation_request_type" required>
                                                                        <option value="1"
                                                                                @if($configuration->outstation_request_type == 1) selected @endif>@lang("$string_file.send_to")  @lang("$string_file.all_drivers")</option>
                                                                        <option value="2"
                                                                                @if($configuration->outstation_request_type == 2) selected @endif>@lang("$string_file.cron_job")</option>
                                                                    </select>
                                                                    @if ($errors->has('outstation_request_type'))
                                                                        <label class="danger">{{ $errors->first('outstation_request_type') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message546')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="text" class="form-control"
                                                                           id="outstation_time"
                                                                           name="outstation_time"
                                                                           placeholder="@lang('admin.message157')"
                                                                           value="{{ $configuration->outstation_time }}"
                                                                           required>
                                                                    @if ($errors->has('outstation_time'))
                                                                        <label class="danger">{{ $errors->first('outstation_time') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message547')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="number" class="form-control"
                                                                           id="outstation_radius"
                                                                           name="outstation_radius"
                                                                           placeholder="@lang("$string_file.enter_radius") ( @lang("$string_file.in_km") )"
                                                                           value="{{ $configuration->outstation_radius }}"
                                                                           required>
                                                                    @if ($errors->has('outstation_radius'))
                                                                        <label class="danger">{{ $errors->first('outstation_radius') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message548')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="number" class="form-control"
                                                                           id="no_driver_outstation"
                                                                           name="no_driver_outstation"
                                                                           placeholder="@lang('admin.message156')"
                                                                           value="{{ $configuration->no_driver_outstation }}"
                                                                           required>
                                                                    @if ($errors->has('no_driver_outstation'))
                                                                        <label class="danger">{{ $errors->first('no_driver_outstation') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="firstName3">
                                                                        @lang('admin.message549')<span class="text-danger">*</span>
                                                                    </label>
                                                                    <input type="number" class="form-control"
                                                                           id="outstation_time_before"
                                                                           name="outstation_time_before"
                                                                           placeholder="@lang('admin.message148')"
                                                                           value="{{ $configuration->outstation_time_before }}"
                                                                           required>
                                                                    @if ($errors->has('outstation_time_before'))
                                                                        <label class="danger">{{ $errors->first('outstation_time_before') }}</label>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <br>
                                                    <h4 class="form-section" style="color: black"><i
                                                                class="fa fa-microchip"></i> @lang('admin.message129')<hr>
                                                    </h4>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang("$string_file.android_user_maintenance_mode")<span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control"
                                                                        name="android_user_maintenance_mode"
                                                                        id="android_user_maintenance_mode" required>
                                                                    <option value="1" {{ $configuration->android_user_maintenance_mode == 1 ? 'selected' : ''}}>@lang('admin.message131')</option>
                                                                    <option value="2" {{ $configuration->android_user_maintenance_mode == 2 ? 'selected' : ''}}>@lang('admin.message132')</option>
                                                                </select>
                                                                @if ($errors->has('android_user_maintenance_mode'))
                                                                    <label class="danger">{{ $errors->first('android_user_maintenance_mode') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang("$string_file.android_user_app_version")<span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                       id="android_user_version"
                                                                       name="android_user_version"
                                                                       placeholder="@lang('admin.message160')"
                                                                       value="{{ $configuration->android_user_version }}"
                                                                       required>
                                                                @if ($errors->has('android_user_version'))
                                                                    <label class="danger">{{ $errors->first('android_user_version') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang("$string_file.android_user_app_mandatory_update") <span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control"
                                                                        name="android_user_mandatory_update"
                                                                        id="android_user_mandatory_update" required>
                                                                    <option value="1" {{ $configuration->android_user_mandatory_update == 1 ? 'selected' : ''}}>@lang('admin.message131')</option>
                                                                    <option value="2" {{ $configuration->android_user_mandatory_update == 2 ? 'selected' : ''}}>@lang('admin.message132')</option>
                                                                </select>
                                                                @if ($errors->has('android_user_mandatory_update'))
                                                                    <label class="danger">{{ $errors->first('android_user_mandatory_update') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang("$string_file.ios_user_maintenance_mode")<span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control"
                                                                        name="ios_user_maintenance_mode"
                                                                        id="ios_user_maintenance_mode" required>
                                                                    <option value="1" {{ $configuration->ios_user_maintenance_mode == 1 ? 'selected' : ''}}>@lang('admin.message131')</option>
                                                                    <option value="2" {{ $configuration->ios_user_maintenance_mode == 2 ? 'selected' : ''}}>@lang('admin.message132')</option>
                                                                </select>
                                                                @if ($errors->has('ios_user_maintenance_mode'))
                                                                    <label class="danger">{{ $errors->first('ios_user_maintenance_mode') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                     @lang("$string_file.ios_user_app_version")<span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                       id="ios_user_version"
                                                                       name="ios_user_version"
                                                                       placeholder="@lang('admin.message161')"
                                                                       value="{{ $configuration->ios_user_version }}"
                                                                       required>
                                                                @if ($errors->has('ios_user_version'))
                                                                    <label class="danger">{{ $errors->first('ios_user_version') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang("$string_file.ios_user_app_mandatory_update")<span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control"
                                                                        name="ios_user_mandatory_update"
                                                                        id="ios_user_mandatory_update" required>
                                                                    <option value="1" {{ $configuration->ios_user_mandatory_update == 1 ? 'selected' : ''}}>@lang('admin.message131')</option>
                                                                    <option value="2" {{ $configuration->ios_user_mandatory_update == 2 ? 'selected' : ''}}>@lang('admin.message132')</option>
                                                                </select>
                                                                @if ($errors->has('ios_user_mandatory_update'))
                                                                    <label class="danger">{{ $errors->first('ios_user_mandatory_update') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>


                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang('admin.message138')<span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control"
                                                                        name="android_driver_maintenance_mode"
                                                                        id="android_driver_maintenance_mode" required>
                                                                    <option value="1" {{ $configuration->android_driver_maintenance_mode == 1 ? 'selected' : ''}}>@lang('admin.message131')</option>
                                                                    <option value="2" {{ $configuration->android_driver_maintenance_mode == 2 ? 'selected' : ''}}>@lang('admin.message132')</option>
                                                                </select>
                                                                @if ($errors->has('android_driver_maintenance_mode'))
                                                                    <label class="danger">{{ $errors->first('android_driver_maintenance_mode') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                   @lang("$string_file.android") @lang("String_file.driver_app_version")<span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                       id="android_driver_version"
                                                                       name="android_driver_version"
                                                                       placeholder="@lang('admin.message162')"
                                                                       value="{{ $configuration->android_driver_version }}"
                                                                       required>
                                                                @if ($errors->has('android_driver_version'))
                                                                    <label class="danger">{{ $errors->first('android_driver_version') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang("$string_file.android_driver_app_mandatory_update")<span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control"
                                                                        name="android_driver_mandatory_update"
                                                                        id="android_driver_mandatory_update" required>
                                                                    <option value="1" {{ $configuration->android_driver_mandatory_update == 1 ? 'selected' : ''}}>@lang('admin.message131')</option>
                                                                    <option value="2" {{ $configuration->android_driver_mandatory_update == 2 ? 'selected' : ''}}>@lang('admin.message132')</option>
                                                                </select>
                                                                @if ($errors->has('android_driver_mandatory_update'))
                                                                    <label class="danger">{{ $errors->first('android_driver_mandatory_update') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>


                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang("$string_file.ios_driver_maintenance_mode")<span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control"
                                                                        name="ios_driver_maintenance_mode"
                                                                        id="ios_driver_maintenance_mode" required>
                                                                    <option value="1" {{ $configuration->ios_driver_maintenance_mode == 1 ? 'selected' : ''}}>@lang('admin.message131')</option>
                                                                    <option value="2" {{ $configuration->ios_driver_maintenance_mode == 2 ? 'selected' : ''}}>@lang('admin.message132')</option>
                                                                </select>
                                                                @if ($errors->has('ios_driver_maintenance_mode'))
                                                                    <label class="danger">{{ $errors->first('ios_driver_maintenance_mode') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang("$string_file.ios_driver_app_version")<span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                       id="ios_driver_version"
                                                                       name="ios_driver_version"
                                                                       placeholder="@lang('admin.message163')"
                                                                       value="{{ $configuration->ios_driver_version }}"
                                                                       required>
                                                                @if ($errors->has('ios_driver_version'))
                                                                    <label class="danger">{{ $errors->first('ios_driver_version') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang("$string_file.android_driver_app_mandatory_update")<span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control"
                                                                        name="ios_driver_mandatory_update"
                                                                        id="ios_driver_mandatory_update" required>
                                                                    <option value="1" {{ $configuration->ios_driver_mandatory_update == 1 ? 'selected' : ''}}>@lang('admin.message131')</option>
                                                                    <option value="2" {{ $configuration->ios_driver_mandatory_update == 2 ? 'selected' : ''}}>@lang('admin.message132')</option>
                                                                </select>
                                                                @if ($errors->has('android_driver_mandatory_update'))
                                                                    <label class="danger">{{ $errors->first('android_driver_mandatory_update') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>


                                                    </div>
                                                </fieldset>
                                                <div class="form-actions right" style="margin-bottom: 3%">
                                                    @if(Auth::user('merchant')->can('edit_configuration'))
                                                        <button type="submit" class="btn btn-primary float-right">
                                                            <i class="fa fa-check-circle"></i> Save
                                                        </button>
                                                    @endif
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
    </div>
@endsection