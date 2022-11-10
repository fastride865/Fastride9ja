@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
{{--            @if(session('success') || session('error'))--}}
{{--                <div class="alert dark alert-icon @if(session('success')) alert-success @else alert-danger @endif alert-dismissible"--}}
{{--                     role="alert">--}}
{{--                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--                        <span aria-hidden="true">x</span>--}}
{{--                    </button>--}}
{{--                    <i class="icon wb-info" aria-hidden="true"></i>--}}
{{--                    @if(session('success'))--}}
{{--                        {{ session('success') }}--}}
{{--                    @else--}}
{{--                        {{ session('error') }}--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--            @endif--}}
{{--            @if($errors->all())--}}
{{--                @foreach($errors->all() as $message)--}}
{{--                    <div class="alert dark alert-icon alert-warning alert-dismissible" role="alert">--}}
{{--                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--                            <span aria-hidden="true">x</span>--}}
{{--                        </button>--}}
{{--                        <i class="icon fa-warning" aria-hidden="true"></i>{{ $message }}--}}
{{--                    </div>--}}
{{--                @endforeach--}}
{{--            @endif--}}
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="fa-truck" aria-hidden="true"></i> @lang("$string_file.manual_dispatch")
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification" enctype="multipart/form-data"
                          action="{{ route('merchant.book.manual.dispach') }}" onsubmit="return validation()">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="wb-user"></i> @lang("$string_file.user_details")</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control form-control" id="manual_user_type"
                                                    name="manual_user_type" disabled="true">
                                                <option value="2" selected>@lang("$string_file.registered_user")</option>
                                                <option value="1">@lang("$string_file.new_user")</option>
                                                @if(Auth::user('merchant')->can('view_corporate') && $baseConfig->corporate_admin == 1)
                                                    <option value="3">@lang("$string_file.corporate_user")
                                                    </option>
                                                @endif
                                            </select>
                                        </div>
                                        <input type="hidden" id="user_id" name="user_id">
                                        @if ($errors->has('user_id'))
                                            <label class="text-danger">{{ $errors->first('user_id') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <select class="form-control form-control" name="country"
                                                    id="country" onchange="changeCountryCanter(this)">
                                                <option value="">@lang("$string_file.country_code")
                                                </option>
                                                @foreach($countries  as $country)
                                                    <option data-min="{{ $country->minNumPhone }}"
                                                            data-max="{{ $country->maxNumPhone }}"
                                                            data-country-code="{{ $country->country_code }}"
                                                            value="{{ $country->id }}"
                                                            data-phone="{{$country->phonecode}}">({{ $country->phonecode }}) {{ isset($country->LanguageCountrySingle->name) ? $country->LanguageCountrySingle->name : $country->LanguageCountryAny->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="user_phone" name="user_phone"
                                                   placeholder="@lang("$string_file.phone")" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group pull-right" id="check_user_details_div">
                                            <button type="button" class="btn btn-primary"
                                                    id="check_user_details">@lang("$string_file.search")</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 custom-hidden" id="first_name_new_div">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="new_user_first_name"
                                                   name="new_user_first_name"
                                                   placeholder="@lang("$string_file.first_name")" autocomplete="off"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6 custom-hidden" id="last_name_new_div">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="new_user_last_name"
                                                   name="new_user_last_name"
                                                   placeholder="@lang("$string_file.last_name")" autocomplete="off"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 custom-hidden" id="corporate_div">
                                        <div class="form-group">
                                            <select class="form-control" name="corporate_id"
                                                    id="corporate_id">
                                                <option value="">--@lang("$string_file.select_corporate")--</option>
                                                @foreach($corporates as $corporate)
                                                    <option value="{{ $corporate->id }}">{{ $corporate->corporate_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 custom-hidden" id="email_new_div">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="new_user_email"
                                                   name="new_user_email"
                                                   placeholder="@lang("$string_file.email")" autocomplete="off"/>
                                        </div>
                                    </div>
                                    @if($config->gender == 1)
                                        <div class="col-md-6 custom-hidden" id="gender_new_div">
                                            <div class="form-group">
                                                <select class="form-control form-control" name="new_user_gender"
                                                        id="new_user_gender">
                                                    <option value="1">@lang("$string_file.male")</option>
                                                    <option value="2">@lang("$string_file.female")</option>
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-3 custom-hidden" id="new_user_details_div">
                                        <div class="form-group pull-right">
                                            <button type="button" class="btn btn-primary"
                                                    id="new_user_details">@lang("$string_file.register_user")</button>
                                        </div>
                                    </div>
                                    <div class="col-md-3 custom-hidden" id="full_details_div">
                                        <div class="form-group pull-right">
                                            <a target="_blank" id="full_details" class="btn btn-primary" disabled>
                                                <i class="icon wb-user-circle" aria-hidden="true"></i>
                                                @lang("$string_file.full_details")
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <h5><i class="fa fa-dollar"></i> @lang("$string_file.price_for_ride")</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control" name="price_for_ride" id="price_for_ride">
                                                <option value="">@lang("$string_file.select")</option>
                                                <option value="1">@lang("$string_file.from_price_card")</option>
                                                <option value="2">@lang("$string_file.fix_amount")</option>
                                                <option value="3">@lang("$string_file.maximum_amount")</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 custom-hidden" id="price_ride_value_div">
                                        <div class="form-group">
                                            <input class="form-control" type="text" id="price_for_ride_value" name="price_for_ride_value" placeholder="@lang("$string_file.enter_amount")">
                                        </div>
                                    </div>
                                </div>
                                <h5><i class="wb-book"></i> @lang("$string_file.ride_details")</h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="pickup_location" name="pickup_location"
                                                   placeholder="@lang("$string_file.pickup_location")" autocomplete="off" onkeyup="resetPromoCode();getPickupLoction();" />
                                            <input type="hidden" id="pickup_latitude" name="pickup_latitude" value="">
                                            <input type="hidden" id="pickup_longitude" name="pickup_longitude" value="">
                                            <input type="hidden" id="drop_latitude" name="drop_latitude" value="">
                                            <input type="hidden" id="drop_longitude" name="drop_longitude" value="">
                                            <input type="hidden" id="distance" name="distance" value="">
                                            <input type="hidden" id="estimate_distance" name="estimate_distance" value="">
                                            <input type="hidden" id="ride_time" name="ride_time" value="">
                                            <input type="hidden" id="estimate_time" name="estimate_time" value="">
                                            <input type="hidden" id="estimate_fare" name="estimate_fare" value="">
                                            <input type="hidden" id="manual_area" name="manual_area" value="">
                                            @if ($errors->has('pickup_location'))
                                                <label class="text-danger">{{ $errors->first('pickup_location') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group form-group after-add-more" id="drop_location_div">
                                            <input type="text" class="form-control"
                                                   id="drop_location" name="drop_location"
                                                   placeholder="@lang("$string_file.drop_off_location")" autocomplete="off" onkeyup="resetPromoCode();getDropLoction();">
                                            <div class="input-group-btn">
                                                <button style="display: none" class="btn btn-success add-more" type="button" id="add_loc">
                                                    <i class="wb-plus"></i>@lang("$string_file.add")</button>
                                            </div>
                                            @if ($errors->has('drop_location'))
                                                <label class="text-danger">{{ $errors->first('drop_location') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div id="multiple_location"></div>
                                        <label class="text-primary  pull-right mr-10 text-danger"
                                               id="distance_and_time"></label><br>
                                        <label class="text-info pull-right mr-2 text-danger"
                                               id="estimate_fare_ride"></label><br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-control-label">@lang("$string_file.ride_type")</label>
                                            <span class="text-danger">*</span>
                                            <div>
                                                <div class="radio-custom radio-default radio-inline">
                                                    <input type="radio" id="radio1" name="booking_type" onclick="RideType(this.value)" value="1" />
                                                    <label for="radio1">@lang("$string_file.ride_now")</label>
                                                </div>
                                                <div class="radio-custom radio-default radio-inline">
                                                    <input type="radio" id="radio2" name="booking_type" onclick="RideType(this.value)" value="2" />
                                                    <label for="radio2">@lang("$string_file.ride")  @lang("$string_file.later") </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 custom-hidden" id="start-div">
                                        <div class="form-group">
                                            <label class="form-control-label" for="datepicker">@lang("$string_file.start")  @lang("$string_file.date")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="icon wb-calendar" aria-hidden="true"></i></span>
                                                </div>
                                                <input type="text" class="form-control customDatePicker1" id="datepicker" name="date"
                                                       placeholder="@lang("$string_file.start")  @lang("$string_file.date") " autocomplete="off" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 custom-hidden" id="end-div">
                                        <div class="form-group">
                                            <label class="form-control-label" for="inputtime">@lang("$string_file.start")  @lang("$string_file.time")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="wb-time" aria-hidden="true"></i></span>
                                                </div>
                                                <input type="text" class="timepicker form-control" data-plugin="clockpicker" data-autoclose="true"
                                                       id="time" name="time" placeholder="@lang("$string_file.start")  @lang("$string_file.time") " autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 custom-hidden" id="retrun_date_div">
                                        <div class="form-group">
                                            <label class="form-control-label" for="inputdatepicker">@lang("$string_file.return_date")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="icon wb-calendar" aria-hidden="true"></i></span>
                                                </div>
                                                <input type="text" class="form-control customDatePicker1" id="datepicker" name="retrun_date"
                                                       placeholder="" autocomplete="off" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 custom-hidden" id="retrun_time_div">
                                        <div class="form-group">
                                            <label class="form-control-label" for="time">@lang("$string_file.return_time")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                            <span class="input-group-text">
                                              <i class="wb-time" aria-hidden="true"></i>
                                            </span>
                                                </div>
                                                <input type="text" class="timepicker form-control" data-plugin="clockpicker" data-autoclose="true"
                                                       id="time" name="retrun_time" placeholder="@lang("$string_file.return_time") " autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6"
                                         id="vehicle_type_id">
                                        <div class="form-group">
                                            <select class="form-control form-control" name="vehicle_type"
                                                    id="vehicle_type">
                                                <option value="">--@lang("$string_file.vehicle_type") --</option>
                                            </select>
                                            @if ($errors->has('vehicle_type'))
                                                <label class="text-danger">{{ $errors->first('vehicle_type') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control form-control" name="service"
                                                    id="service" onchange="getDistance()">
                                                <option value="">--@lang("$string_file.service_area") --
                                                </option>
                                            </select>
                                            @if ($errors->has('service'))
                                                <label class="text-danger">{{ $errors->first('service') }}</label>
                                            @endif
                                        </div>
                                        {!! Form::hidden('additional_support',null,['class'=>'form-control','id'=>'additional_support']) !!}
                                    </div>
                                    <div class="col-md-6 custom-hidden" id="outstation_type">
                                        <div class="form-group">
                                            <select class="form-control form-control" name="outstation_type_val"
                                                    id="outstation_type_val">
                                                <option value="">@lang("$string_file.fare_type") </option>
                                                <option value="1">@lang("$string_file.round_trip_only")</option>
                                                <option value="2">@lang("$string_file.one_way_round_trip")</option>
                                            </select>
                                            @if ($errors->has('outstation_type_val'))
                                                <label class="text-danger">{{ $errors->first('outstation_type_val') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 custom-hidden" id="package_id">
                                        <div class="form-group">
                                            <select class="form-control form-control" name="package"
                                                    id="package">
                                                <option value="">--@lang("$string_file.select_package")--</option>
                                            </select>
                                            @if ($errors->has('package'))
                                                <label class="text-danger">{{ $errors->first('package') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control form-control" name="payment_method_id"
                                                    id="payment_method_id">
                                                <option value="">--@lang("$string_file.payment_method")--</option>
                                                <option value="1">@lang("$string_file.cash")</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="form-control form-control" name="promo_code"
                                                    id="promo_code">
                                                <option value="">--@lang("$string_file.promo_code")--</option>
                                            </select>
                                        </div>
                                    </div>
                                    @if($bookingConfig->additional_note == 1)
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <textarea class="form-control" id="placeTextarea" rows="3" name="note" placeholder="@lang("$string_file.additional_notes")"></textarea>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-control-label" for="driver_marker">@lang("$string_file.driver_availability"):
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control form-control-sm" name="type" id="driver_marker">
                                                <option value="1">@lang("$string_file.all")</option>
                                                <option value="2">@lang("$string_file.available")</option>
                                                <option value="3">@lang("$string_file.enroute_pickup")</option>
                                                <option value="4">@lang("$string_file.reached_pickup")</option>
                                                <option value="5">@lang("$string_file.journey_started")</option>
                                                <option value="6">@lang("$string_file.offline")</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="map" style="width: 100%;height: 550px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="distance_unit" id="distance_unit">
                        <input type="hidden" name="multi_destination" id="multi_destination">
                        <input type="hidden" name="isoCode" id="isocode">
                        <input type="hidden" name="max__multi_count" id="max__multi_count">
                        <input type="hidden" name="old_eta" id="old_eta">
                        <input type="hidden" name="promo_id" id="promo_id">
                        <input type="hidden" name="price_card_id" id="price_card_id">
                        <br>
                        <h5 class="form-section col-md-12" style="color: black;"><i
                                    class="fa fa-taxi"></i> @lang("$string_file.assign_ride")
                        </h5>
                        <hr>
                        <div class="row">
                            @if($config->gender == 1)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-control-label" for="driver_gender">@lang("$string_file.gender")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control form-control" name="driver_gender"
                                                id="driver_gender">
                                            <option value="1">@lang("$string_file.male")</option>
                                            <option value="2">@lang("$string_file.female")</option>
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="form-control-label"></label>
                                <div>
                                    <div class="radio-custom radio-default radio-inline">
                                        <input type="radio" id="all_driver_booking" name="driver_request" value="1" />
                                        <label for="all_driver_booking"> @lang("$string_file.send_booking_to_all_drivers")</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="form-control-label" for="ride_radius_driver">@lang("$string_file.enter_radius") ( @lang("$string_file.in_km") )
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="ride_radius_driver" name="ride_radius" required="" value="0.1"
                                       placeholder="@lang("$string_file.enter") @lang("$string_file.number")" min="0">
                                <label class="text-danger"><b id="number_of_drivers">0</b> @lang("$string_file.drivers") (@lang("$string_file.online") @lang("$string_file.and_symbol") @lang("$string_file.free") )</label>
                            </div>
                        </div>
                        <div class="row">
                            <h4 style="text-align:center; margin:10px auto; font-weight:bold; width:100%;">@lang("$string_file.or")</h4>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label"></label>
                                    <div>
                                        <div class="radio-custom radio-default radio-inline">
                                            <input type="radio" id="manually_driver" name="driver_request" value="3" data-plugin="icheck" />
                                            <label for="manually_driver"> @lang("$string_file.select_driver_manually")</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ride_radius_manual_driver">@lang("$string_file.enter_radius") ( @lang("$string_file.in_km") )</label>
                                    <input type="number" class="c-select form-control"
                                           id="ride_radius_manual_driver"
                                           name="ride_radius_manual_driver" value="0.1" min="0" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="driver_id">@lang("$string_file.driver")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control" name="driver_id"
                                            id="driver_id">
                                        <option value="">--@lang("$string_file.driver")--</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-actions float-right" style="margin-bottom: 1%">
                            <button type="button" id="resetForm" class="btn btn-warning mr-1">
                                <i class="fas fa-times"></i> @lang("$string_file.reset")
                            </button>
                            <button type="submit" id="manualDispatch" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> @lang("$string_file.book")
                            </button>
                        </div>
                    </form>
                    <div class="dynamic_lat_long">
                        <input type="hidden" id="q_value" value="">
                    </div>
                    <div class="copy custom-hidden">
                        <div class="input-group" style="margin-top:10px; margin-bottom:10px">
                            <input type="text" name="multiple_destination[]" class="form-control blur" id="drop_loc"
                                   onkeyup="get_this_element(this);resetPromoCode();getMultiDropLoction(this);" onblur="check_element_on_blur(this);"
                                   placeholder="@lang('admin.message577')">
                            <input type="hidden" id="drop_loc_latitude" name="multiple_destination_lat" value="">
                            <input type="hidden" id="drop_loc_longitude" name="multiple_destination_lng" value="">
                            <div class="input-group-btn">
                                <button class="btn btn-danger remove" type="button"><i
                                            class="glyphicon glyphicon-remove"></i> @lang("$string_file.remove")
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
@section('js')
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= get_merchant_google_key(NULL,'admin_backend');?>&libraries=places"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        var restric_config = "{{$config->restrict_country_wise_searching}}";
        var options = {};
        // console.log(options);
        $('#country').on('change',function () {
            options = getOption();
        });
        function getOption()
        {
            var options = {};
            var  country_val = $("#country option:selected").val();
            console.log(country_val);
            if(restric_config == 1 && country_val !="")
            {
                var  country_code = $("#country option:selected").attr("data-country-code");
                options = {
                    strictBounds: false,
                    fields: ["address_components", "geometry", "icon", "name"],
                    // types: ['(regions)'],
                    componentRestrictions: {country: country_code}
                };
            }
            return options
        }

        $('#time').on('change',function () {
            // console.log($('#time').val());
            var hours = parseInt($('#time').val().split(':')[0], 10) - new Date().getHours();
            var minutes = parseInt($('#time').val().split(':')[1], 10) - new Date().getMinutes();

            var current_date_time = new Date();
            var current_date = current_date_time.getFullYear()+'-'+String(current_date_time.getMonth() + 1).padStart(2, '0')+'-'+String(current_date_time.getDate()).padStart(2, '0');
            var selected = $('#datepicker').val();
            // console.log("Current Date: "+current_date);
            // console.log("Selected Date: "+selected);

            var total_time_diff =  minutes + (hours * 60);
            // console.log(total_time_diff);
            if((selected <= current_date) && (total_time_diff < 0)){
                sweetalert("@lang("$string_file.past_time_error")");
                $("#time").val('');
                return false;
            }
            // console.log($('#time').val());
            // console.log(new Date().getHours());
            // console.log(new Date().getMinutes());
        });

        $("input[name=date]").on('change',function ()
        {
            var selected = $('input[name=date]').val();
            // console.log("Selected Date Changed: "+selected);
            var hours = parseInt($('#time').val().split(':')[0], 10) - new Date().getHours();
            var minutes = parseInt($('#time').val().split(':')[1], 10) - new Date().getMinutes();
            var current_date_time = new Date();
            var current_date = current_date_time.getFullYear()+'-'+String(current_date_time.getMonth() + 1).padStart(2, '0')+'-'+current_date_time.getDate();
            var selected = $('#datepicker').val();
            // console.log("Current Date: "+current_date);
            // console.log("Selected Date: "+selected);
            if((selected <= current_date) && (hours < 0 || minutes < 0))
            {
                sweetalert("@lang("$string_file.past_time_error")");
                $("#time").val('');
                return false;
            }
            // console.log($('#time').val());
            // console.log(new Date().getHours());
            // console.log(new Date().getMinutes());
        });

        function sweetalert(msg) {
            swal({
                title: "@lang("$string_file.error")",
                text: msg,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            });
        }

        let drivermarker;
        let map;
        let driverMarkers = [];
        let infowindow;
        let driverLocations;

        let directionsService = new google.maps.DirectionsService;
        let directionsDisplay = new google.maps.DirectionsRenderer;

        function initialize() {
            var center = new google.maps.LatLng(20.5937, 78.9629);
            var mapOptions = {
                zoom: 2,
                center: center,
                mapTypeId: google.maps.MapTypeId.TERRAIN
            };
            map = new google.maps.Map(document.getElementById('map'), mapOptions);
            directionsDisplay.setMap(map);
        }
        google.maps.event.addDomListener(window, 'load', initialize);

        $(document).on('change','#price_for_ride',function () {
            var priceForRide = this.value;
            if(priceForRide == 1 || priceForRide == ''){
                $('#price_ride_value_div').hide();
            }else{
                $('#promo_code').hide();
                $('#price_ride_value_div').show();
            }
        });

        $(document).ready(function () {
            count = 0;
            $(".add-more").click(function () {
                console.log("Add More Location")
                var max_multi_count = document.getElementById('max__multi_count').value;
                var limit = max_multi_count;
                //return false;
                if (count == limit) {
                    return false;
                }
                count++;
                // console.log(count);
                var html = $(".copy").html();

                if (count > 1) {
                    $(".after-add-more").after(html);
                    var last = $('.after-add-more').next();
                } else {
                    $(".after-add-more").before(html);
                    var last = $('.after-add-more').prev();
                }

                $('.after-add-more').removeClass('after-add-more');
                last.addClass('after-add-more');
                $('#drop_loc').attr('q', count);
                let hold = $('#drop_loc').attr('placeholder');
                $('#drop_loc').attr('placeholder', hold + ' ' + count);
                $('#drop_loc').attr('id', 'drop_loc' + '_' + count);
                $('#drop_loc_latitude').attr('name', 'multiple_destination_lat' + '_' + count);
                $('#drop_loc_longitude').attr('name', 'multiple_destination_lng' + '_' + count);
                $('#drop_loc_latitude').attr('id', 'drop_loc_latitude' + '_' + count);
                $('#drop_loc_longitude').attr('id', 'drop_loc_longitude' + '_' + count);

                if (count > 1) {
                    $('.after-add-more').prev().children().children().attr('disabled', true)
                }
            });

            $("body").on("click", ".remove", function () {
                if ($(this).parents(".input-group").children().attr('q') < count)  // IT WILL RUN IF SOME EARLIER add more ELEMENT DELETED
                {   // DELETE ALL OF THE add more ELEMENTS, AS EARLIER WAS DELETED, COUNT VALUE DISTURBED
                    // console.log('Yes Small FROM .remove');
                    while (count >= 1) {
                        if ($('#drop_loc_' + count).parents().hasClass('after-add-more')) {
                            // console.log("Yes this have class (after-add-more) ");
                            let last = $('.after-add-more').next();
                            last.addClass('after-add-more');
                        }
                        $('#drop_loc_' + count).parents(".input-group").remove();
                        //$(this).parents(".input-group").remove();
                        count--;
                        // console.log(count);
                    }
                    if (($('#drop_latitude').val()) && ($('#pickup_latitude').val())) {
                        $("#loader1").show();
                        calculateAndDisplayRoute(directionsService, directionsDisplay);
                        getDistance();
                        $("#loader1").hide();
                    }
                } else {
                    if ($(this).parents().hasClass('after-add-more')) {
                        if (count > 1) {
                            var last = $('.after-add-more').prev();
                        } else {
                            var last = $('.after-add-more').next();
                        }
                        last.addClass('after-add-more');
                        if (count > 1) {
                            last.children().children().attr('disabled', false);
                        }
                    }
                    $(this).parents(".input-group").remove();
                    count--;
                    if (($('#drop_latitude').val()) && ($('#pickup_latitude').val())) {
                        $("#loader1").show();
                        calculateAndDisplayRoute(directionsService, directionsDisplay);
                        getDistance();
                        $("#loader1").hide();
                    }
                }
            });

        });

        $('#check_user_details').click(function () {
            var user_phone = $('[name="user_phone"]').val();
            var country_id = $('[name="country"]').val();
            var phone_code= $('option:selected','[name="country"]').attr('data-phone');
            // alert(phone_code);
            if (user_phone == "") {
                swal("Enter Phone Number !")
                return false;
            } else {
                $("#loader1").show();
                var token = $('[name="_token"]').val();
                let number = phone_code + user_phone;
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "SearchUser",
                    data: {user_phone: number,country_id:country_id},
                    success: function (data) {
                        // console.log(data);
                        if(data.id != null){
                            // Register user
                            $('#manual_user_type').val(2).trigger('change');
                            // console.log('Register');
                            $('#user_id').val(data.id);
                            $("#full_details").attr("href", "users/" + data.id);
                            $("#distance_unit").val(data.distance_unit);
                            $("#multi_destination").val(data.multi_destination);
                            $("#isocode").val(data.iso);
                            $("#max__multi_count").val(data.max_multi_count);
                            $("#full_details_div").show();
                            if (data.user_gender == 1){
                                $('#driver_gender_div').hide();
                            }
                        }else{
                            // console.log('Not Register');
                            // New user
                            $('#manual_user_type').val(1).trigger('change');
                            $('#user_id').val('');
                            swal("This Phone Number Is Not Registered !");
                            $("#full_details").removeAttr("href");
                            $("#full_details_div").hide();
                        }
                    }, error: function (err) {
                        $('#user_id').val('');
                        swal("This Phone Number Is Not Registered !");
                        $("#full_details").removeAttr("href");
                        $("#full_details_div").hide();
                        // New user
                        $('#manual_user_type').val(1).trigger('change');
                    }
                });
                $("#loader1").hide();
            }
        });

        // $(document).ready(function () {
        //     var radius = 1000;
        //     getMapDrivers(radius);
        // });

        $(document).on("change","#outstation_type_val",function(){
            checkOutstationType();
        });

        $(document).on('change',"#ride_radius_driver,#ride_radius_manual_driver,#driver_marker",function() {
            var radius = $(this).val();
            getMapDrivers(radius);
        });

        $(document).on("change","#vehicle_type",function(){
            getService();
        });

        $(document).on("change","#service",function(){
            var service = $('#service').val();
            if(service != 4){
                checkArea();
            }
            checkService();
            serviceDetails();
        });

        $(document).on("change","#package",function(){
            checkestimate();
        });

        function changeCountryCanter(s) {
            var country = s[s.selectedIndex].text;
            var index = country.indexOf(' (');
            var countryText = country.substring(0, index);
            // console.log(countryText);
            if (countryText != "") {
                var geocoder;
                geocoder = new google.maps.Geocoder();
                geocoder.geocode({'address': countryText}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        map.setZoom(6);
                        map.setCenter(results[0].geometry.location)
                    }
                });
            }
        }

        function resetPromoCode(){
            var old_eta = $('#old_eta').val();
            var iso = document.getElementById("isocode").value;
            var estimate = "Fare Estimate : "+iso+" "+ old_eta;
            if(old_eta != ''){
                $('#estimate_fare_ride').html(estimate);
            }
            $('#estimate_fare').val(old_eta);
            $('#promo_code').prop('selectedIndex', 0);
            $('#payment_method_id').prop('selectedIndex', 0);
            return false;
        }

        function getPickupLoction(){
            options = getOption();
            // console.log(options);
            // console.log($("#pickup_location").val().length);
            if($("#pickup_location").val().length > {{$bookingConfig->autocomplete_start}})
            {
                var pickup = document.getElementById('pickup_location');
                var start = new google.maps.places.Autocomplete(pickup,options);
                start.addListener('place_changed', function () {
                    var place = start.getPlace();
                    document.getElementById("pickup_latitude").value = place.geometry.location.lat();
                    document.getElementById("pickup_longitude").value = place.geometry.location.lng();
                    var pickupLat = $('#pickup_latitude').val();
                    var pickupLng = $('#pickup_longitude').val();
                    var merchant_id = {{$config->merchant_id}};
                    var token = $('[name="_token"]').val();

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        method: 'POST',
                        url: "checkArea",
                        data: {latitude: pickupLat,longitude:pickupLng,merchant_id :merchant_id},
                        success:function (data) {
                            if(data.result == 1){
                                // console.log(data);
                                $('#manual_area').val(data.area_id);
                                // $('#service').html(data.services);
                                $('#vehicle_type').html(data.vehicle_types)
                                // $('[name="vehicle_type"]').val('');
                                // $('#vehicle_type_id').hide();
                                if (place.geometry.viewport) {
                                    map.fitBounds(place.geometry.viewport);
                                } else {
                                    map.setCenter(place.geometry.location);
                                    map.setZoom(12);
                                }
                            }else{
                                alert(data.message);
                                document.getElementById("pickup_latitude").value = "";
                                document.getElementById("pickup_longitude").value = "";
                                document.getElementById("pickup_location").value = "";
                                return false;
                            }
                        }
                    });

                    if ($('#drop_latitude').val()) {
                        $("#loader1").show();
                        calculateAndDisplayRoute(directionsService, directionsDisplay);
                        if($('[name="vehicle_type"]').val() != ''){
                            getDistance();
                        }
                        $("#loader1").hide();
                    }
                });
            }
        }

        function getDropLoction(){
            // console.log('Inside Drop location');
            if($("#drop_location").val().length > {{$bookingConfig->autocomplete_start}}){
                var drop_location = document.getElementById('drop_location');
                var end = new google.maps.places.Autocomplete(drop_location,options);

                end.addListener('place_changed', function () {
                    var getplace = end.getPlace();
                    document.getElementById("drop_latitude").value = getplace.geometry.location.lat();
                    document.getElementById("drop_longitude").value = getplace.geometry.location.lng();
                    checkArea();
                    {{--var pickupLat = $('#drop_latitude').val();--}}
                    {{--var pickupLng = $('#drop_longitude').val();--}}
                    {{--var merchant_id = {{$config->merchant_id}};--}}
                    {{--var token = $('[name="_token"]').val();--}}
                    {{--var service = $('#service').val();--}}
                    {{--var manual_area = $('#manual_area').val();--}}

                    {{--$.ajax({--}}
                    {{--    headers: {--}}
                    {{--        'X-CSRF-TOKEN': token--}}
                    {{--    },--}}
                    {{--    method: 'POST',--}}
                    {{--    url: "checkArea",--}}
                    {{--    data: {latitude: pickupLat,longitude:pickupLng,merchant_id :merchant_id,service:service,manual_area:manual_area},--}}
                    {{--    success:function (data) {--}}
                    {{--        console.log(data);--}}
                    {{--        if(data.result == 1){--}}
                    {{--            if ($('#pickup_latitude').val()) {--}}
                    {{--                $("#loader1").show();--}}
                    {{--                calculateAndDisplayRoute(directionsService, directionsDisplay);--}}
                    {{--                getDistance();--}}
                    {{--                $("#loader1").hide();--}}
                    {{--            }--}}
                    {{--        }else{--}}
                    {{--            alert(data.message);--}}
                    {{--            document.getElementById("drop_latitude").value = "";--}}
                    {{--            document.getElementById("drop_longitude").value = "";--}}
                    {{--            document.getElementById("drop_location").value = "";--}}
                    {{--            return false;--}}
                    {{--        }--}}
                    {{--    }--}}
                    {{--});--}}
                });
            }
        }

        function calculateAndDisplayRoute(directionsService, directionsDisplay) {
            var service = $('#service').val();
            var waypts = [];
            if(service == 2){
                directionsService.route({
                    origin: document.getElementById('pickup_location').value,
                    destination: document.getElementById('drop_location').value,
                    waypoints: waypts,
                    travelMode: google.maps.DirectionsTravelMode.DRIVING
                }, function (response, status) {
                    if (status === 'OK') {
                        directionsDisplay.setMap(null);
                    }
                    // else {
                    //     window.alert('Directions request failed due to ' + status);
                    // }
                });
            }else{
                for (var i = count; i >= 1; i--) {
                    //console.log("yes way point FROM calculateAndDisplayRoute(): " + $('#drop_loc_' + i).val());
                    if ($('#drop_loc_' + i).val()) {
                        waypts.push({
                            location: $('#drop_loc_' + i).val(),
                            stopover: true
                        });
                    }
                }
                waypts.reverse();
                directionsService.route({
                    origin: document.getElementById('pickup_location').value,
                    destination: document.getElementById('drop_location').value,
                    waypoints: waypts,
                    travelMode: google.maps.DirectionsTravelMode.DRIVING
                }, function (response, status) {
                    if (status === 'OK') {
                        directionsDisplay.setDirections(response);
                        //console.log(response.geometry.location.lat());
                    } else {
                        window.alert('Directions request failed due to ' + status);
                    }
                });
            }
        }

        function getDistance() {
            if($('#service').val() == 2){
                // console.log('Get Distance Inside service 2');
                checkestimate();
                return false;
            }
            var distanceService = new google.maps.DistanceMatrixService();
            // console.log('COUNT VALUE:' + count);
            if (count > 0) {
                // console.log("yes count > 0 FROM getDistance()");
                var waypts = [];
                for (var i = count; i >= 1; i--) {
                    // console.log("yes way point FROM getDistance(): " + $('#drop_loc_' + i).val());
                    if ($('#drop_loc_' + i).val()) {
                        waypts.push({
                            location: $('#drop_loc_' + i).val(),
                            stopover: true
                        });
                    }
                }

                directionsService.route({
                    origin: document.getElementById('pickup_location').value,
                    destination: document.getElementById('drop_location').value,
                    waypoints: waypts,
                    provideRouteAlternatives: true,
                    travelMode: google.maps.DirectionsTravelMode.DRIVING
                }, function (response, status) {
                    if (status === 'OK') {
                        var total_distance = 0;
                        var total_time = 0;
                        for (var k = 0; k < response['routes'][0]['legs'].length; k++) {
                            total_distance += response['routes'][0]['legs'][k]['distance']['value'];
                            total_time += response['routes'][0]['legs'][k]['duration']['value'];
                        }
                        document.getElementById("distance").value = total_distance;
                        document.getElementById("ride_time").value = total_time;
                        var dis_unit = document.getElementById("distance_unit").value;
                        if(dis_unit == 1){
                            var unit = "km";
                            var Cal = 1000;
                        }else{
                            var unit = "Miles";
                            var Cal = 1609;
                        }
                        total_distance = (total_distance / Cal).toFixed(2);
                        total_time = (total_time / 60).toFixed(2);
                        document.getElementById("distance_and_time").innerText = "Distance: " + total_distance +" "+unit+ " Time: " + total_time + " Mins";
                        if($('[name="vehicle_type"]').val() != ''){
                            checkestimate();
                        }else{
                            $('#estimate_fare_ride').html('');
                        }
                    } else {
                        window.alert('Directions request failed due to ' + status);
                    }
                });
            } else {
                // console.log("No count !> 0 FROM getDistance()");
                distanceService.getDistanceMatrix({
                        origins: [$("#pickup_location").val()],
                        destinations: [$("#drop_location").val()],
                        travelMode: google.maps.TravelMode.DRIVING,
                        unitSystem: google.maps.UnitSystem.METRIC,
                        durationInTraffic: true,
                        avoidHighways: false,
                        avoidTolls: false
                    },
                    function (response, status) {
                        if (status !== google.maps.DistanceMatrixStatus.OK) {
                            console.log('Error:', status);
                        } else {
                            var distance = response.rows[0].elements[0].distance.value;
                            var time = response.rows[0].elements[0].duration.text;
                            document.getElementById("distance").value = response.rows[0].elements[0].distance.value;
                            document.getElementById("estimate_distance").value = distance;
                            document.getElementById("estimate_time").value = time;

                            var dis_unit = document.getElementById("distance_unit").value;
                            if(dis_unit == 1){
                                var unit = "km";
                                var Cal = 1000;
                            }else{
                                var unit = "Miles";
                                var Cal = 1609;
                            }
                            distance = (distance / Cal).toFixed(2);
                            document.getElementById("distance_and_time").innerText = "Distance: " + distance + " "+unit+" Time: " + time;
                            document.getElementById("ride_time").value = response.rows[0].elements[0].duration.value;
                            if($('[name="vehicle_type"]').val() != ''){
                                checkestimate();
                            }else{
                                $('#estimate_fare_ride').html('');
                            }
                        }
                    });
            }
        }

        function checkestimate() {
            // console.log('Inside estimate');
            var area = $('[name="manual_area"]').val();
            var distance = $('[name="distance"]').val();
            var distance_unit = $('[name="distance_unit"]').val();
            var ride_time = $('[name="ride_time"]').val();
            var service = $('[name="service"]').val();
            var vehicle_type = $('[name="vehicle_type"]').val();
            var token = $('[name="_token"]').val();
            var package_id = $('[name="package"]').val();
            var outstation_type_val = $('[name="outstation_type_val"]').val();
            var additional_support = $("#additional_support").val();
            $('#estimate_fare').val('');
            if(vehicle_type == ''){
                $('#estimate_fare_ride').html("Please Select Vehicle For Estimate Price");
                return false;
            }
            var additional_support = $("#service option:selected").attr('additional_support');

            if (additional_support == 1 && outstation_type_val == 2) {
                if(package_id === ""){
                    return false;
                }
            }
             // console.log(distance);
             //     console.log(ride_time);
             //     console.log(service);
             //     console.log(vehicle_type);
             //     console.log(area);
             //     console.log(distance_unit);
             //     console.log(package_id);
             //     console.log(outstation_type_val);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "estimatePrice",
                data: {
                    distance: distance,
                    ride_time: ride_time,
                    service: service,
                    vehicle_type: vehicle_type,
                    area: area,
                    distance_unit : distance_unit,
                    package_id : package_id,
                    outstation_type: outstation_type_val
                },
                success: function (data) {
                    // console.log(data);
                    if(data.result == 1){
                        var priceCardFare = parseInt(data.amount);
                        $('#price_card_id').val(data.price_card_id);
                        // console.log('priceCardFare :'+priceCardFare);
                        var outstationFareTyep = $('#outstation_type_val').val();
                        // console.log('outstationFareTyep :'+outstationFareTyep);
                        if(service == 4 && $('#outstation_type_val').val() == 1){
                            priceCardFare = priceCardFare *2;
                        }

                        var manualPrice = parseInt($('#price_for_ride_value').val());
                        var iso = document.getElementById("isocode").value;
                        var estimate = "Fare Estimate : "+iso+" "+ priceCardFare;
                        $('#estimate_fare').val(priceCardFare);
                        if($('#price_for_ride').val() == 2){
                            estimate = "Fare Estimate : "+iso+" "+ manualPrice;
                            $('#estimate_fare').val(manualPrice);
                        }else if($('#price_for_ride').val() == 3){
                            if(priceCardFare > manualPrice){
                                estimate = "Fare Estimate : "+iso+" "+ manualPrice;
                                $('#estimate_fare').val(manualPrice);
                            }else{
                                estimate = "Fare Estimate : "+iso+" "+ priceCardFare;
                                $('#estimate_fare').val(priceCardFare);
                            }
                        }
                        // console.log('fare estimate : '+estimate);
                        $('#estimate_fare_ride').html(estimate);

                        var price_card_id = $('#price_card_id').val();
                        // console.log('price_card_id : '+price_card_id);
                        // console.log('area : '+area);
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': token
                            },
                            method: 'POST',
                            url: "getPromoCode",
                            data: {
                                price_card_id: price_card_id,
                                manual_area: area,
                            },
                            success: function (data) {
                                $('#promo_code').html(data);
                            }
                        });
                    }else{
                        swal(data.message);
                    }
                }
            });
        }

        function RideType(val) {
            if (val == "1") {
                document.getElementById('start-div').style.display = 'none';
                document.getElementById('end-div').style.display = 'none';
            } else {
                document.getElementById('start-div').style.display = 'block';
                document.getElementById('end-div').style.display = 'block';
            }
        }

        function get_this_element(object_data) {
            $("#q_value").val($(object_data).attr('q'));
        }

        function check_element_on_blur(object_data) {
            if (!$('#drop_loc_' + $(object_data).attr('q')).val()) {
                $("#drop_loc_latitude_" + $(object_data).attr('q')).val(null);
                $("#drop_loc_longitude_" + $(object_data).attr('q')).val(null);
                //document.getElementById("distance_and_time").innerText = null;
            }
        }

        function getMultiDropLoction(data){
            if(data.value.length > {{$bookingConfig->autocomplete_start}}){
                var addmore = new google.maps.places.Autocomplete(
                    (document.getElementById('drop_loc' + '_' + count)));

                addmore.addListener('place_changed', function () {
                    var place = addmore.getPlace();
                    $("#drop_loc_latitude_" + $("#q_value").val()).val(place.geometry.location.lat());
                    $("#drop_loc_longitude_" + $("#q_value").val()).val(place.geometry.location.lng());

                    if (($('#drop_latitude').val()) && ($('#pickup_latitude').val())) {
                        calculateAndDisplayRoute(directionsService, directionsDisplay);
                        getDistance();
                    }
                });
            }
        }

        function validation() {
            var country = document.getElementById('country').value;
            var user = document.getElementById('user_id').value;
            var manual_area = document.getElementById('manual_area').value;
            var service = document.getElementById('service').value;
            var vehicle_type = document.getElementById('vehicle_type').value;
            var package = document.getElementById('package').value;
            var pickup_location = document.getElementById('pickup_location').value;
            var drop_location = document.getElementById('drop_location').value;
            var payment_method_id = document.getElementById('payment_method_id').value;
            var driver_request = document.getElementsByName('driver_request')[0].value;
            if (country == "") {
                sweetalert("@lang("$string_file.select_country")");
                return false;
            }
            if (user == "") {
                sweetalert("@lang("$string_file.user_not_found")");
                return false;
            }
            if (manual_area == "") {
                sweetalert("@lang("$string_file.select_service_area")");
                return false;
            }
            if (service == "") {
                sweetalert("@lang("$string_file.select_service_type")");
                return false;
            }
            if (vehicle_type == "") {
                sweetalert("@lang("$string_file.select_vehicle_type")");
                return false;
            }
            if ([2, 3].includes(service) && package == "") {
                sweetalert("@lang("$string_file.select_package")");
                return false;
            }
            if (document.getElementById("radio1").checked != true && document.getElementById('radio2').checked != true) {
                sweetalert("@lang("$string_file.select_ride_type")");
                return false;
            }
            if (pickup_location == "") {
                sweetalert("@lang("$string_file.enter_pickup_location")");
                return false;
            }
            if (payment_method_id == "") {
                sweetalert("@lang("$string_file.select_payment_method")");
                return false;
            }
            if (driver_request == 1 && document.getElementById('ride_radius_driver').value == "") {
                sweetalert("@lang("$string_file.enter_request_radius")");
                return false;

            }
            if (driver_request == 3 && document.getElementById('driver_id').value == "") {
                sweetalert("@lang("$string_file.select_driver")");
                return false;
            }
            if (driver_request == 3 && document.getElementById('ride_radius_manual_driver').value == "") {
                sweetalert("@lang("$string_file.enter_request_radius")");
                return false;
            }

            return true;
        }

        function getService() {
            $('#estimate_fare_ride').html('');
            $('#outstation_type').hide();
            $('#package_id').hide();
            $('#package').val('');
            var area_id = $('#manual_area').val();
            var vehicle_type_id = $('#vehicle_type option:selected').val();
            if (area_id != "" && vehicle_type_id != '') {
                $("#loader1").show();
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "{{route('merchant.area.services')}}",
                    data: {
                        area_id: area_id,
                        segment_id:1,
                        vehicle_type_id:vehicle_type_id,
                        segment_group:1,
                    },
                    success: function (data) {
                        $('#service').html(data);
                    }
                });
                $("#loader1").hide();
            }
            else{
                alert('{!! trans("$string_file.select_segment") !!}');
                $("#vehicle_type option:selected").prop('selected', false);
            }
        }

        function checkService() {
            // console.log('Inside Check Service');
            checkOutstationType();
            $('#estimate_fare_ride').html('');
            var val = $("#service option:selected").val();
            $("#loader1").show();
            $("#extra_charge").hide();
            $("#additional_support").val('');
            $("#outstation_type").hide();
            $("#package_id").hide();
            $("#package").prop("required",false);
            $("#package").html("<option value=''>@lang("$string_file.select_package")</option>");
            if (val !== "") {
                var token = $('[name="_token"]').val();
                var service_type_id = $("#service option:selected").val();
                var additional_support = $("#service option:selected").attr('additional_support');
                $("#additional_support").val(additional_support);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "<?php echo route('merchant.price.card.service.config') ?>",
                    data: {
                        service_type_id: service_type_id,
                        additional_support: additional_support,
                        merchant_id: "{{$bookingConfig->merchant_id}}"
                    },
                    success: function (data) {
                        // console.log(data);
                        if (additional_support == 1 || additional_support == 2) {
                            $("#package").prop("required",true);
                            $("#package").html(data); // its div of package or special city
                            if (additional_support == "1") {
                                $("#package_id").show();
                                {{--$("#newText").text("@lang('admin.package')");--}}
                            }
                            else if (additional_support == 2) {
                                <!--$("#newText").text("@lang('admin.message542')");-->
                                $("#outstation_type").show();
                            }
                        }else{
                            checkPriceCard();
                        }
                    }
                });
            }
            $("#loader1").hide();
        }

        function serviceDetails() {
            // console.log('Inside Service details.');
            var service = $('#service').val();
            var multi_destination = $('#multi_destination').val();
            // console.log(multi_destination);
            var drop_latitude = $('#drop_latitude').val();
            var drop_longitude = $('#drop_longitude').val();
            var manual_area = $('#manual_area').val();
            $('#outstation_type').hide();
            $('#package').val('');
            var max_multi_count = $('#max__multi_count').val();
            if (service == 1 && multi_destination == 1 && max_multi_count > 0) {
                $('#add_loc').css("display", "block");
            } else {
                if(service == 4){
                    $('#outstation_type').show();
                    if(drop_latitude != ''){
                        var token = $('[name="_token"]').val();
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': token
                            },
                            method: 'POST',
                            url: "checkOutstationDropArea",
                            data: {
                                area_id: manual_area,
                                service_type: service,
                                latitude: drop_latitude,
                                longitude: drop_longitude
                            },
                            success: function (data) {
                                // console.log(data);
                                if(data.result == 0){
                                    alert(data.message);
                                    $('#drop_location').val('');
                                    $("#drop_latitude").val('');
                                    $("#drop_longitude").val('');
                                }
                            }
                        });
                    }
                }
                // console.log(count);
                $('#add_loc').css("display", "none");
                for (var i = count; i >= 1; i--) {
                    // console.log("yes way point FROM calculateAndDisplayRoute(): " + $('#drop_loc_' + i).val());
                    if ($('#drop_loc_' + i).val()) {
                        $('#drop_loc_' + i).val('');
                        $("#drop_loc_latitude_" + i).val('');
                        $("#drop_loc_longitude_" + i).val('');
                        if ($('#drop_loc_' + i).parents().hasClass('after-add-more')) {
                            // console.log("Yes this have class (after-add-more) ");
                            let last = $('.after-add-more').next();
                            last.addClass('after-add-more');
                        }
                        $('#drop_loc_' + i).parents(".input-group").remove();
                        //$(this).parents(".input-group").remove();
                        count--;
                    }
                }
                if(service == 2){
                    calculateAndDisplayRoute(directionsService, directionsDisplay);
                }
                getDistance();
            }
        }

        function checkOutstationType() {
            // console.log('inside check outstation type');
            $("#package_id").hide();
            var additional_support = $("#service option:selected").attr('additional_support');
            var outstation_type_val = $("#outstation_type_val option:selected").val();
            // console.log(additional_support);
            // console.log(outstation_type_val);
            $('#package').prop('selectedIndex', 0);
            if(additional_support == 2 && outstation_type_val == 1)
            {
                checkestimate();
                $("#package").prop("required",false);
            }
            else if(additional_support == 2 && outstation_type_val == 2)
            {
                $("#package_id").show();
            }
        }

        function getMapDrivers(radius_val) {
            // if(this.id = 'driver_marker'){
            //     var pickup_latitude = '';
            //     var drop_latitude = '';
            //     var service = '';
            //     var vehicle_type = '';
            //     var radius = '';
            //     var pickup_longitude = '';
            //     var manual_area = '';
            //     var drop_longitude = '';
            //     var driver_gender = ''
            //     var distance_unit = '';
            // }else{
                var pickup_latitude = $('[name="pickup_latitude"]').val();
                var drop_latitude = $('[name="drop_latitude"]').val();
                var service = $('[name="service"]').val();
                var vehicle_type = $('[name="vehicle_type"]').val();
                var radius = radius_val;
                // console.log(radius);
                var pickup_longitude = $('[name="pickup_longitude"]').val();
                var manual_area = $('[name="manual_area"]').val();
                var drop_longitude = $('[name="drop_longitude"]').val();
                var driver_gender = $('[name="driver_gender"]').val();
                var distance_unit = document.getElementById("distance_unit").value;
            // }
            var token = $('[name="_token"]').val();
            var type = document.getElementById('driver_marker').value;

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "{{ route('getDriverOnMap') }}",
                data: {
                    radius: radius,
                    manual_area: manual_area,
                    pickup_latitude: pickup_latitude,
                    pickup_longitude: pickup_longitude,
                    drop_latitude: drop_latitude,
                    drop_longitude: drop_longitude,
                    service: service,
                    vehicle_type: vehicle_type,
                    driver_gender : driver_gender,
                    distance_unit: distance_unit,
                    type: type,
                },
                success: function (data) {
                    driverLocations = JSON.parse(data);
                    driverLocations = driverLocations['map_markers'];
                    infowindow = new google.maps.InfoWindow();
                    for (var f = 0; f < driverMarkers.length; f++) {
                        driverMarkers[f].setMap(null);
                    }
                    for (var i = 0; i < driverLocations.length; i++) {
                        newName = driverLocations[i]['marker_name'];
                        marker_number = driverLocations[i]['marker_number'];
                        icon = driverLocations[i]['marker_icon'];
                        marker_image = driverLocations[i]['marker_image'];
                        email = driverLocations[i]['marker_email'];
                        newLatitude = driverLocations[i]['marker_latitude'];
                        newLongitude = driverLocations[i]['marker_longitude'];
                        markerlatlng = new google.maps.LatLng(newLatitude, newLongitude);
                        content = '<table><tr><td rowspan="4"><img src="' + marker_image + '" height="50" width="50"></td></tr><tr><td>&nbsp;&nbsp;Email: </td><td><b>' + email + '</b></td></tr><tr><td>&nbsp;&nbsp;Mobile: </td><td><b>+' + marker_number + '</b></td></tr></table>';
                        var drivermarker = new google.maps.Marker({
                            map: map,
                            title: newName,
                            position: markerlatlng,
                            icon: icon
                        });
                        google.maps.event.addListener(drivermarker, 'click', (function (drivermarker, content, infowindow) {
                            return function () {
                                infowindow.setContent(content);
                                infowindow.open(map, drivermarker);
                                map.panTo(this.getPosition());
                                map.setZoom(15);
                            };
                        })(drivermarker, content, infowindow));
                        driverMarkers.push(drivermarker);
                    }
                }, error: function (e) {
                    // console.log(e);
                }
            });
        }

        function checkPriceCard() {
            var service = $('#service').val();
            var vehicle_type = $('#vehicle_type').val();
            var manual_area = $('#manual_area').val();
            var package_id = $('#package').val();
            var token = $('[name="_token"]').val();
            // console.log('vehicle_type :'+vehicle_type);
            // console.log('Service_Type :'+service);
            if (vehicle_type === "" || service === "" || manual_area === "") {
                alert("Some Data Missing");
                return false;
            }
            if((service == 2 || service == 4) && package_id === ""){
                return false;
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "checkPriceCard",
                data: {
                    vehicle_type: vehicle_type,
                    service: service,
                    area: manual_area,
                    package_id: package_id,
                },
                success: function (data) {
                    if (data.result == 0) {
                        alert(data.message);
                        return false;
                    }
                }
            });
            $("#loader1").hide();
        }

        function checkArea() {
            $('#estimate_fare_ride').html('');
            var pickupLat = $('#drop_latitude').val();
            var pickupLng = $('#drop_longitude').val();
            var merchant_id = {{$config->merchant_id}};
            var token = $('[name="_token"]').val();
            var service = $('#service').val();
            var manual_area = $('#manual_area').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "checkArea",
                data: {latitude: pickupLat,longitude:pickupLng,merchant_id :merchant_id,service:service,manual_area:manual_area},
                success:function (data) {
                    // console.log(data);
                    if(data.result == 1){
                        if ($('#pickup_latitude').val()) {
                            $("#loader1").show();
                            calculateAndDisplayRoute(directionsService, directionsDisplay);
                            getDistance();
                            $("#loader1").hide();
                            return true
                        }
                    }else{
                        alert(data.message);
                        document.getElementById("drop_latitude").value = "";
                        document.getElementById("drop_longitude").value = "";
                        document.getElementById("drop_location").value = "";
                        return false;
                    }
                }
            });
        }
    </script>
@endsection