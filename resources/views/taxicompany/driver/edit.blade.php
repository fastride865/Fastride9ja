@extends('taxicompany.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('rideradded'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message224')
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('Taxicompany.drivers.index') }}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.all_drivers")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang('admin.message682')</h3>
                </header>
                <div class="panel-body container-fluid">
                    {{-- If demo is not exist --}}
                    @if(Auth::user()->demo != 1)
                        <form method="POST" class="steps-validation wizard-notification" enctype="multipart/form-data" action="{{route('Taxicompany.drivers.update', $driver->id)}}"
                              onsubmit="return validation()">
                            {{method_field('PUT')}}
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="form-section col-md-12" style="color: #000000;"><i
                                                class="wb-user"></i> @lang("$string_file.personal_details")
                                    </h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="user_phone">@lang('admin.merchantPhone')
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">{{ $driver->CountryArea->Country->phonecode  }}</span>
                                                    </div>
                                                    <input type="number" pattern="[0-9]{*}" title="Valid Mobile Number" class="form-control" id="user_phone"
                                                           name="phone" value="{{ str_replace($driver->CountryArea->Country->phonecode,"",$driver->phoneNumber) }}"
                                                           placeholder="@lang('admin.driver_mobile_no')" required autocomplete="off" />
                                                </div>
                                            </div>
                                            @if ($errors->has('phone'))
                                                <label class="text-danger">{{ $errors->first('phone') }}</label>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="first_name"> @lang("$string_file.first_name")
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $driver->first_name }}"
                                                       placeholder=" @lang("$string_file.first_name")" required autocomplete="off" />
                                            </div>
                                            @if ($errors->has('first_name'))
                                                <label class="text-danger">{{ $errors->first('first_name') }}</label>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="last_name">@lang("$string_file.last_name")
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $driver->last_name }}"
                                                       placeholder="@lang("$string_file.last_name")" required autocomplete="off" />
                                            </div>
                                            @if ($errors->has('last_name'))
                                                <label class="text-danger">{{ $errors->first('last_name') }}</label>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="email">@lang("$string_file.email")
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                       placeholder="@lang('admin.message670')" autocomplete="off" value="{{ $driver->email }}" required/>
                                            </div>
                                            @if ($errors->has('email'))
                                                <label class="text-danger">{{ $errors->first('email') }}</label>
                                            @endif
                                        </div>
                                        @if($config->gender == 1)
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="driver_gender">@lang("$string_file.gender")
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-control form-control" name="driver_gender" id="driver_gender" required>
                                                        <option value="1" @if($driver->driver_gender == 1) selected @endif>@lang("$string_file.male")</option>
                                                        <option value="2" @if($driver->driver_gender == 2) selected @endif>@lang("$string_file.female")</option>
                                                    </select>
                                                    @if ($errors->has('driver_gender'))
                                                        <label class="text-danger">{{ $errors->first('driver_gender') }}</label>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        @if($config->smoker == 1)
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="location3"> @lang("$string_file.smoke")
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <span class="form-control">
                                                    <div class="radio-custom radio-default radio-inline">
                                                        <input type="radio" value="1" checked id="smoker_type" name="smoker_type" required
                                                            @if(!empty($driver->DriverRideConfig) && $driver->DriverRideConfig->smoker_type == 1)
                                                                    @endif/>
                                                        <label> @lang("$string_file.smoker")</label>
                                                    </div>
                                                    <div class="radio-custom radio-default radio-inline">
                                                        <input type="radio" value="2" checked id="smoker_type" name="smoker_type" required
                                                            @if(!empty($driver->DriverRideConfig) && $driver->DriverRideConfig->smoker_type == 2)
                                                                    @endif/>
                                                        <label> @lang("$string_file.non_smoker")</label>
                                                    </div>
                                                </span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-control-label"></label>
                                                <div class="checkbox-inline" style="margin-left: 5%;margin-top: 1%;">
                                                    <input type="checkbox" name="allow_other_smoker" value="1" id="allow_other_smoker"
                                                           @if(!empty($driver->DriverRideConfig) && $driver->DriverRideConfig->allow_other_smoker == 1) checked
                                                            @endif>
                                                    <label> @lang("$string_file.allow_other_to_smoke")</label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="emailAddress5">@lang("$string_file.profile_image")
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="file" class="form-control" id="image" name="image" />
                                                @if ($errors->has('image'))
                                                    <label class="text-danger">{{ $errors->first('image') }}</label>
                                                @endif
                                            </div>
                                        </div>
{{--                                        <div class="col-md-4">--}}
{{--                                            <div class="form-group">--}}
{{--                                                <label class="form-control-label" for="emailAddress5">@lang('admin.app_debug_mode')--}}
{{--                                                    <span class="text-danger">*</span>--}}
{{--                                                </label>--}}
{{--                                                {!! Form::select('app_debug_mode',\Config::get('custom.status'),old('app_debug_mode',isset($driver->app_debug_mode) ? $driver->app_debug_mode : 2),['class'=>'form-control']) !!}--}}
{{--                                                @if ($errors->has('app_debug_mode'))--}}
{{--                                                    <label class="text-danger">{{ $errors->first('app_debug_mode') }}</label>--}}
{{--                                                @endif--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input type="checkbox" value="1" name="edit_password" id="edit_password" onclick="EditPassword()">
                                                <label>@lang("$string_file.edit_password")</label>
                                                <br>
                                                <label for="password">
                                                    @lang("$string_file.password")
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="password" class="form-control"
                                                       id="password"
                                                       name="password"
                                                       placeholder="@lang("$string_file.password")"
                                                       disabled>
                                                @if ($errors->has('password'))
                                                    <label class="text-danger">{{ $errors->first('password') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($config->driver_address == 1)
                                        <br>
                                        <h5 class="form-section col-md-12" style="color: black;"><i
                                                    class="fa fa-address-card"></i> @lang("$string_file.address")
                                        </h5>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="location3"> @lang("$string_file.address_line_1")
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="address_line_1" name="address_line_1"
                                                           value="{{old('address_line_1', isset($driver_additional_data) ? $driver_additional_data->address_line_1 : '') }}"
                                                           placeholder=" @lang("$string_file.address_line_1")" autocomplete="off" />
                                                </div>
                                                @if ($errors->has('address_line_1'))
                                                    <label class="text-danger">{{ $errors->first('address_line_1')}}</label>
                                                @endif
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="location3">@lang("$string_file.address_suburb")
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="address_suburb" name="address_suburb"
                                                           value="{{old('address_suburb', isset($driver_additional_data) ? $driver_additional_data->suburb : '')}}"
                                                           placeholder="@lang("$string_file.address_suburb")" autocomplete="off" />
                                                </div>
                                                @if ($errors->has('address_suburb'))
                                                    <label class="text-danger">{{ $errors->first('address_suburb')}}</label>
                                                @endif
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="location3">@lang("$string_file.address_province")
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="address_province" name="address_province"
                                                           value="{{old('address_province', isset($driver_additional_data) ? $driver_additional_data->province : '')}}"
                                                           placeholder="@lang("$string_file.address_province")" autocomplete="off" />
                                                </div>
                                                @if ($errors->has('address_province'))
                                                    <label class="text-danger">{{ $errors->first('address_province')}}</label>
                                                @endif
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="location3">@lang("$string_file.postal_code")
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="address_postal_code" name="address_postal_code"
                                                           value="{{old('address_postal_code', isset($driver_additional_data) ? $driver_additional_data->pincode : '')}}"
                                                           placeholder="@lang("$string_file.postal_code")" autocomplete="off" />
                                                </div>
                                                @if ($errors->has('address_postal_code'))
                                                    <label class="text-danger">{{ $errors->first('address_postal_code')}}</label>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    @if($config->bank_details == 1)
                                        <br>
                                        <h5 class="form-section col-md-12" style="color: black;"><i
                                                    class="fa fa-bank"></i> @lang("$string_file.bank_details")
                                        </h5>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="lastName3">@lang("$string_file.bank_name")
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="bank_name" name="bank_name"
                                                           value="{{ $driver->bank_name }}"
                                                           placeholder="@lang("$string_file.bank_name")" autocomplete="off" />
                                                </div>
                                                @if ($errors->has('bank_name'))
                                                    <label class="text-danger">{{ $errors->first('bank_name') }}</label>
                                                @endif
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="lastName3">@lang("$string_file.account_holder_name")
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="account_holder_name" name="account_holder_name"
                                                           value="{{ $driver->account_holder_name }}"
                                                           placeholder="@lang("$string_file.account_holder_name")" autocomplete="off" />
                                                </div>
                                                @if ($errors->has('account_holder_name'))
                                                    <label class="text-danger">{{ $errors->first('account_holder_name') }}</label>
                                                @endif
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="lastName3">@lang("$string_file.account_number")
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="account_number" name="account_number"
                                                           value="{{ $driver->driver_account_number }}"
                                                           placeholder="@lang("$string_file.account_number")" autocomplete="off" />
                                                </div>
                                                @if ($errors->has('account_number'))
                                                    <label class="text-danger">{{ $errors->first('account_number') }}</label>
                                                @endif
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="location3">{{$driver->CountryArea->Country->transaction_code}}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="online_transaction" name="online_transaction"
                                                           value="{{ $driver->online_code }}"
                                                           placeholder="@lang('admin.enter')" autocomplete="off" />
                                                </div>
                                                @if ($errors->has('online_transaction'))
                                                    <label class="text-danger">{{ $errors->first('online_transaction') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-actions float-right" style="margin-bottom: 1%">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i> @lang("$string_file.update")</button>
                            </div>
                        </form>
                    @else
                        <div class="card bg-danger text-white shadow">
                            <div class="card-body">Alert
                                <div class="large">@lang('admin.demo_user_cant_edited').</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
        function EditPassword() {
            if (document.getElementById("edit_password").checked = true) {
                document.getElementById('password').disabled = false;
            }
        }
    </script>
@endsection