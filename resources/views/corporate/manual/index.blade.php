@extends('corporate.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('success'))
                <div class="alert dark alert-icon alert-info " role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('success') }}
                </div>
            @endif
            @if($errors->all())
                @foreach($errors->all() as $message)
                    <div class="alert dark alert-icon alert-warning " role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                        <i class="icon fa-warning" aria-hidden="true"></i>{{ $message }}
                    </div>
                @endforeach
            @endif
            @if(session('nodriver'))
                <div class="alert dark alert-icon alert-warning " role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon fa-warning" aria-hidden="true"></i>@lang('admin.message58')
                </div>
            @endif
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title"><i class="fa fa-list-alt" aria-hidden="true"></i>
                        @lang("$string_file.manual_dispatch")</h3>
                </div>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification" enctype="multipart/form-data" action="{{ route('corporate.book.manual.dispach') }}"
                          onsubmit="return validation()">
                        @csrf
                        <h5 class="form-section col-md-12" style="color: black;"><i class="wb-user"></i> @lang("$string_file.user_details") </h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="manual_user_type">@lang("$string_file.select")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control" id="manual_user_type"
                                            name="manual_user_type">
                                        <option value="3" selected>@lang("$string_file.corporate_user")
                                        </option>
                                        <option value="1">@lang("$string_file.new_user")</option>
                                    </select>
                                </div>
                                @if ($errors->has('user_id'))
                                    <label class="text-danger">{{ $errors->first('user_id') }}</label>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="country">@lang("$string_file.country")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control" name="country"
                                            id="country">
                                        <option value="">@lang("$string_file.select_country")
                                        </option>
                                        @foreach($countries  as $country)
                                            <option data-min="{{ $country->minNumPhone }}"
                                                    data-max="{{ $country->maxNumPhone }}"
                                                    value="{{ $country->id }}" data-phone="{{$country->phonecode}}">{{ $country->LanguageCountrySingle->name }} ({{ $country->phonecode }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('country'))
                                        <label class="text-danger">{{ $errors->first('country') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="user_phone">@lang("$string_file.phone")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="user_phone" name="user_phone"
                                           placeholder="@lang("$string_file.phone")" autocomplete="off" required/>
                                    <input type="hidden" id="user_id" name="user_id">
                                </div>
                            </div>
                            <div class="col-md-4 custom-hidden" id="first_name_new_div">
                                <div class="form-group">
                                    <label class="form-control-label" for="first_name_new_div">@lang('admin.RiderName')
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="new_user_first_name" name="new_user_first_name"
                                           placeholder="@lang("$string_file.user_name")" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-4 custom-hidden" id="last_name_new_div">
                                <div class="form-group">
                                    <label class="form-control-label" for="new_user_last_name">@lang('admin.RiderLastName')
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="new_user_last_name" name="new_user_last_name"
                                           placeholder="@lang("$string_file.user_last_name")" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-4 custom-hidden" id="email_new_div" >
                                <div class="form-group">
                                    <label class="form-control-label" for="new_user_email">@lang("$string_file.email")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="new_user_email" name="new_user_email"
                                           placeholder="@lang("$string_file.email")" autocomplete="off" />
                                </div>
                            </div>
                            @if($config->gender == 1)
                                <div class="col-md-4 custom-hidden" id="gender_new_div">
                                    <div class="form-group">
                                        <label class="form-control-label" for="new_user_gender">@lang("$string_file.gender")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control form-control" name="new_user_gender"
                                                id="new_user_gender">
                                            <option value="1">@lang("$string_file.male")</option>
                                            <option value="2">@lang("$string_file.female")</option>
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row" style="margin-left: 5px">
                            <div class="form-group float-right mr-2 " id="check_corporate_user_details">
                                <button type="button" class="btn btn-primary" id="check_corporate_user_details">@lang("$string_file.search")</button>
                            </div>
                            <div class="form-group float-right mr-2 custom-hidden" id="new_user_details_div">
                                <button type="button" class="btn btn-primary" id="new_user_details">@lang('admin.Register')</button>
                            </div>
                            <div class="form-group float-right custom-hidden" id="full_details_div">
                                <div class="alert dark alert-success pt-5 pb-5" role="alert">
                                    @lang('admin.found_successfully')
                                </div>
                            </div>
                        </div>
                        <h5 class="form-section col-md-12" style="color: black;"><i
                                    class="wb-book"></i>  @lang("$string_file.ride_details")
                        </h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="manual_area">@lang("$string_file.service_area")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control" name="area"
                                            id="corporate_manual_area" onchange="changeCanter(this)">
                                        <option value="">--@lang("$string_file.area")--
                                        </option>
{{--                                        @foreach($areas as $area)--}}
{{--                                            <option id="{{ $area->CountryAreaName }}"--}}
{{--                                                    value="{{ $area->id }}">{{ $area->CountryAreaName }}</option>--}}
{{--                                        @endforeach--}}
                                    </select>
                                    @if ($errors->has('area'))
                                        <label class="text-danger">{{ $errors->first('area') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="corporate_service">@lang("$string_file.select_services")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control" name="service"
                                            id="corporate_service">
                                        <option value="">--@lang("$string_file.service_area") --
                                        </option>
                                    </select>
                                    @if ($errors->has('service'))
                                        <label class="text-danger">{{ $errors->first('service') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 custom-hidden" id="package_id">
                                <div class="form-group">
                                    <label class="form-control-label" for="package">@lang('admin.message538')
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control" name="package"
                                            id="package">
                                        <option value="">--@lang("$string_file.select_package")--</option>
                                    </select>
                                    @if ($errors->has('package'))
                                        <label class="text-danger">{{ $errors->first('package') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 custom-hidden"
                                 id="vehicle_type_id">
                                <div class="form-group">
                                    <label class="form-control-label" for="vehicle_type">@lang("$string_file.select")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control" name="vehicle_type"
                                            id="corporate_vehicle_type">
                                        <option value="">--@lang("$string_file.vehicle_type") --</option>
                                    </select>
                                    @if ($errors->has('vehicle_type'))
                                        <label class="text-danger">{{ $errors->first('vehicle_type') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
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
                            <div class="col-md-4 custom-hidden" id="start-div">
                                <div class="form-group">
                                    <label class="form-control-label" for="datepicker">@lang("$string_file.start")  @lang("$string_file.date")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon wb-calendar" aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" class="form-control customDatePicker1" data-plugin="datepicker" id="datepicker" name="date"
                                               placeholder="@lang("$string_file.start")  @lang("$string_file.date") " autocomplete="off" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 custom-hidden" id="end-div">
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
                            <div class="col-md-4 custom-hidden" id="retrun_div">
                                <div class="form-group">
                                    <label class="form-control-label" for="inputdatepicker">@lang("$string_file.return")  @lang("$string_file.date")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon wb-calendar" aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" class="form-control customDatePicker1" data-plugin="datepicker" id="datepicker" name="retrun_date"
                                               placeholder="@lang("$string_file.return")  @lang("$string_file.date") " autocomplete="off" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 custom-hidden" id="retrun_div">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="pickup_location">@lang("$string_file.pickup_location")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="pickup_location" name="pickup_location"
                                           placeholder="@lang("$string_file.pickup_location")" autocomplete="off" onkeyup="resetPromoCode()" />
                                    <input type="hidden" id="pickup_latitude" name="pickup_latitude" value="">
                                    <input type="hidden" id="pickup_longitude" name="pickup_longitude" value="">
                                    <input type="hidden" id="drop_latitude" name="drop_latitude" value="">
                                    <input type="hidden" id="drop_longitude" name="drop_longitude" value="">
                                    <input type="hidden" id="distance" name="distance" value="">
                                    <input type="hidden" id="estimate_distance" name="estimate_distance" value="">
                                    <input type="hidden" id="ride_time" name="ride_time" value="">
                                    <input type="hidden" id="estimate_time" name="estimate_time" value="">
                                    <input type="hidden" id="estimate_fare" name="estimate_fare" value="">
                                    @if ($errors->has('pickup_location'))
                                        <label class="text-danger">{{ $errors->first('pickup_location') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group after-add-more">
                                    <label class="form-control-label" for="drop_location">@lang("$string_file.drop_off_location")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="drop_location" name="drop_location"
                                           placeholder="@lang("$string_file.drop_off_location")" autocomplete="off" onkeyup="resetPromoCode()">
                                    <div class="input-group-btn mt-5">
                                        <button style="display: none" class="btn btn-success add-more" type="button" id="add_loc">
                                            <i class="wb-plus"></i>@lang("$string_file.add")</button>
                                    </div>
                                    @if ($errors->has('drop_location'))
                                        <label class="text-danger">{{ $errors->first('drop_location') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="driver_marker">@lang("$string_file.drivers_availability")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control" onchange="getDriverLocationo(this.value)"
                                            id="driver_marker" name="driver_marker">
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
                            <div id="multiple_location"></div>
                            <div>
                                <label class="text-primary"
                                       id="distance_and_time"></label><br>
                                <label class="text-primary"
                                       id="estimate_fare_ride"></label>
                            </div>
                        </div>
                        <div class="row">
                            <div id="map" style="width: 100%;height: 550px;"></div>
                        </div>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="payment_method_id">@lang("$string_file.payment_method")</label>
                                    <span class="text-danger">*</span>
                                    <select class="form-control form-control" name="payment_method_id"
                                            id="payment_method_id">
                                        <option value="">--@lang("$string_file.payment_method")--</option>
                                        <option value="1">@lang("$string_file.cash")</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="promo_code">@lang("$string_file.promo_code")</label>
                                    <span class="text-danger">*</span>
                                    <select class="form-control form-control" name="promo_code"
                                            id="corporate_promo_code">
                                        <option value="">--@lang("$string_file.promo_code")--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="placeTextarea">@lang("$string_file.service_detail")</label>
                                    <span class="text-danger">*</span>
                                    <textarea class="form-control" id="placeTextarea" rows="3" name="note" placeholder="@lang("$string_file.additional_notes")"></textarea>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="distance_unit" id="distance_unit">
                        <input type="hidden" name="multi_destination" id="multi_destination">
                        <input type="hidden" name="isocode" id="isocode">
                        <input type="hidden" name="max__multi_count" id="max__multi_count">
                        <input type="hidden" name="old_eta" id="old_eta">
                        <input type="hidden" name="promo_id" id="promo_id">
                        <br>
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
                                        <input type="radio" id="corporate_all_driver_booking" name="driver_request" value="1" />
                                        <label for="radio1"> @lang("$string_file.send_booking_to_all_drivers")</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="form-control-label" for="ride_radius_driver">@lang("$string_file.enter_radius") ( @lang("$string_file.in_km") )
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="corporate_ride_radius_driver" name="ride_radius" required="" value="0.1"
                                       placeholder="@lang("$string_file.enter_number")" min="0">
                                <label class="text-danger"><b id="number_of_drivers">0</b> @lang("$string_file.drivers") (@lang("$string_file.online_and_symbol_free") )</label>
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
                                            <input type="radio" id="corporate_manually_driver" name="driver_request" value="3" />
                                            <label for="radio1"> @lang("$string_file.select_driver_manually")</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="form-control-label" for="ride_radius_manual_driver">@lang("$string_file.enter_radius") ( @lang("$string_file.in_km") )
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="corporate_ride_radius_manual_driver" name="ride_radius_manual_driver"
                                       required value="0.1" min="0" disabled>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="driver_id">@lang("$string_file.select_driver")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control" name="driver_id"
                                            id="driver_id">
                                        <option value="">--@lang("$string_file.select_driver")--</option>
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
                                   onkeyup="get_this_element(this);resetPromoCode();" onblur="check_element_on_blur(this);"
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
    <br>
    <br>

{{--    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDkKetQwosod2SZ7ZGCpxuJdxY3kxo5Po&libraries=places"></script>--}}
    <script src="https://maps.googleapis.com/maps/api/js?key=<?=corporate_get_merchant_google_key();?>&libraries=places"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
@endsection
@section('js')
    <script>

        $('#time').on('change',function () {
            var hours = parseInt($('#time').val().split(':')[0], 10) - new Date().getHours();
            var minutes = parseInt($('#time').val().split(':')[1], 10) - new Date().getMinutes();
            var current_date_time = new Date();
            var current_date = current_date_time.getFullYear()+'-'+String(current_date_time.getMonth() + 1).padStart(2, '0')+'-'+current_date_time.getDate();
            var selected = $('#datepicker').val();
            console.log("Current Date: "+current_date);
            console.log("Selected Date: "+selected);
            if((selected <= current_date) && (hours < 0 || minutes < 0))
            {
                sweetalert("@lang("$string_file.past_time_error")");
                $("#time").val('');
                return false;
            }
            console.log($('#time').val());
            console.log(new Date().getHours());
            console.log(new Date().getMinutes());
        });

        $("input[name=date]").on('change',function ()
        {
            var selected = $('input[name=date]').val();
            console.log("Selected Date Changed: "+selected);
            var hours = parseInt($('#time').val().split(':')[0], 10) - new Date().getHours();
            var minutes = parseInt($('#time').val().split(':')[1], 10) - new Date().getMinutes();
            var current_date_time = new Date();
            var current_date = current_date_time.getFullYear()+'-'+String(current_date_time.getMonth() + 1).padStart(2, '0')+'-'+current_date_time.getDate();
            var selected = $('#datepicker').val();
            console.log("Current Date: "+current_date);
            console.log("Selected Date: "+selected);
            if((selected <= current_date) && (hours < 0 || minutes < 0))
            {
                sweetalert("@lang("$string_file.past_time_error")");
                $("#time").val('');
                return false;
            }
            console.log($('#time').val());
            console.log(new Date().getHours());
            console.log(new Date().getMinutes());
        });

        function ForManual(selected_date)
        {
            // console.log('From ForManual function ');
            var hours = parseInt($('#time').val().split(':')[0], 10) - new Date().getHours();
            var minutes = parseInt($('#time').val().split(':')[1], 10) - new Date().getMinutes();
            var current_date_time = new Date();
            var current_date = current_date_time.getFullYear()+'-'+String(current_date_time.getMonth() + 1).padStart(2, '0')+'-'+current_date_time.getDate();
            var selected = selected_date;
            if((selected == current_date) && (hours < 0 || minutes < 0))
            {
                sweetalert("@lang("$string_file.past_time_error")");
                $("#time").val('');
                return false;
            }
        }

        $('#corporate_ride_radius_driver').keypress(validateNumber);
        $('#corporate_ride_radius_manual_driver').keypress(validateNumber);

        function validateNumber(event) {
            //console.log(event.keyCode);
            //console.log(event.which);
            var key = window.event ? event.keyCode : event.which;
            if (event.keyCode === 8 /*backspace*/ || event.keyCode === 46 /*point*/) {
                return true;
            } else if (key < 48 || key > 57) {
                return false;
            } else {
                return true;
            }
        }


        $('#country').change(function () {
            var country_id = $('#country').val();
            var val = $('option:selected', this).attr('data-phone');
            var token = $('[name="_token"]').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "{{ route('corporate.getArea') }}",
                data: {phone_code: val,country_id:country_id},
                success: function (data) {
                    console.log(data);
                    $('#corporate_manual_area').html(data);
                }, error: function (err) {
                    alert(err)
                }
            });
        });

        $('#check_corporate_user_details').click(function () {
            var user_phone = $('[name="user_phone"]').val();
            var country_id = $('[name="country"]').val();
            var phone_code= $('option:selected','[name="country"]').attr('data-phone');
            if (user_phone == "") {
                alert('Enter Phone Number');
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
                    url: "Search/CorporateUser",
                    data: {user_phone: number,country_id:country_id},
                    success: function (data) {
                        console.log(data);
                        $('#user_id').val(data.id)
                        $("#full_details").attr("href", "user/show/" + data.id);
                        $("#distance_unit").val(data.distance_unit);
                        $("#multi_destination").val(data.multi_destination);
                        $("#isocode").val(data.iso);
                        $("#max__multi_count").val(data.max_multi_count);
                        $("#full_details_div").show();
                        if (data.user_gender == 1){
                            $('#driver_gender_div').hide();
                        }
                    }, error: function (err) {
                        $('#user_id').val('');
                        alert("This Phone Number Is Not Registered")
                        $("#full_details").removeAttr("href");
                        $("#full_details_div").hide();
                    }
                });
                $("#loader1").hide();
            }
        });

        $("#corporate_manual_area").on('change', function () {
            var area_id = this.value;
            if (area_id != "") {
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "getCorporateServices",
                    data: {area_id: area_id},
                    success: function (data) {
                        $("#corporate_service").html(data);
                    }
                });
            }
        });

        $('#corporate_service').on("change", function () {
            var service = this.value;
            var user_id = $('#user_id').val();
            var manual_area = $('#corporate_manual_area').val();
            if (user_id == "") {
                $("#vehicle_type_id").hide();
                alert("User information is not found");
                $('#corporate_service').prop('selectedIndex', 0);
                return false;
            } else {
                var token = $('[name="_token"]').val();
                if (service == 5) {
                    document.getElementById("radio2").disabled = true;
                    document.getElementById("radio1").checked = true;
                    document.getElementById('start-div').style.display = 'none';
                    document.getElementById('end-div').style.display = 'none';
                    document.getElementById('retrun_div').style.display = 'none';
                } else if (service == 4) {
                    document.getElementById("radio1").disabled = true;
                    document.getElementById("radio2").checked = true;
                    document.getElementById('start-div').style.display = 'block';
                    document.getElementById('end-div').style.display = 'block';
                    document.getElementById('retrun_div').style.display = 'block';
                } else {
                    document.getElementById("radio1").disabled = false;
                    document.getElementById("radio2").disabled = false;
                    document.getElementById('retrun_div').style.display = 'none';
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "getCorporateRideConfig",
                    data: {
                        user_id: user_id,
                        service: service,
                        manual_area: manual_area,
                    },
                    success: function (data) {
                        if (service == 1 || service == 5) {
                            $("#package_id").hide();
                            $("#vehicle_type_id").show();
                            $("#corporate_vehicle_type").html(data);
                            $('#drop_location_div').show();
                        } else {
                            $("#vehicle_type_id").hide();
                            $("#package_id").show();
                            $("#package").html(data);
                            if (service == 4) {
                                $.ajax({
                                    headers: {
                                        'X-CSRF-TOKEN': token
                                    },
                                    method: 'POST',
                                    url: "getCorporateVehicle",
                                    data: {
                                        area: manual_area,
                                        service: service
                                    },
                                    success: function (data) {
                                        $("#vehicle_type_id").show();
                                        $("#corporate_vehicle_type").html(data);
                                    }
                                });
                                $('#drop_location_div').show();
                            } else {
                                $('#drop_location_div').hide();
                            }
                        }
                    }
                });
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "getCorporatePromoCode",
                    data: {
                        user_id: user_id,
                        service: service,
                        manual_area: manual_area,
                    },
                    success: function (data) {
                        $('#corporate_promo_code').html(data);
                    }
                });
            }
        });

        $('#corporate_vehicle_type').on("change", function () {
            $("#loader1").show();
            var vehicle_type = this.value;
            var service = $('#corporate_service').val();
            var manual_area = $('#corporate_manual_area').val();
            var package_id = $('#package').val();
            var token = $('[name="_token"]').val();
            if (vehicle_type === "" || service === "" || manual_area === "") {
                alert("Some Data Missing");
                $('#corporate_vehicle_type').prop('selectedIndex', 0);
                return false;
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "checkCorporatePriceCard",
                data: {
                    vehicle_type: vehicle_type,
                    service: service,
                    area: manual_area,
                    package_id: package_id,
                },
                success: function (data) {
                    //alert(data);
                    if ($.trim(data)) {
                    } else {
                        alert("No Price Card Found");
                        $('#corporate_vehicle_type').prop('selectedIndex', 0);
                        return false;
                    }
                }
            });
            $("#loader1").hide();
        });

        function setFare(){
            var old_eta = $('#old_eta').val();
            var iso = document.getElementById("isocode").value;
            var estimate = "Fare Estimate: "+iso+" "+ old_eta;
            $('#estimate_fare_ride').html(estimate);
            $('#estimate_fare').val(old_eta);
        }

        $('#corporate_promo_code').on('change',function () {
            var payment_method = $('#payment_method_id').val();
            if (payment_method == ""){
                sweetalert('Please Select Any Payment Method First !!');
                $('#corporate_promo_code').prop('selectedIndex', 0);
                return false;
            }
            var code = this.value;
            var promo_id = $('#promo_id').val();
            if (code == ""){
                setFare();
                return false;
            }
            if (code != promo_id){
                setFare();
            }
            var eta = $('#estimate_fare').val();
            $('#promo_id').val(code);
            console.log(code);
            console.log(eta);
            if (eta == ""){
                sweetalert("Estimate Fare is not found");
                $('#corporate_promo_code').prop('selectedIndex', 0);
                return false;
            }else{
                var token = $('[name = "_token"]').val();
                $.ajax({
                    headers:{
                        'X-CSRF-TOKEN' : token
                    },
                    method : 'POST',
                    url : 'getCorporatePromoCodeEta',
                    data : {
                        promocode_id : code,
                        estimate_fare : eta,
                    },
                    success: function (data) {
                        console.log(data);
                        var iso = document.getElementById("isocode").value;
                        var estimate = "Fare Estimate: "+iso+" "+ data;
                        $('#estimate_fare_ride').html(estimate);
                        $('#estimate_fare').val(data);
                    }
                });
            }
        });

        $('#corporate_manually_driver').click(function () {
            $("#corporate_ride_radius_manual_driver").prop('disabled', false);
            $("#driver_id").prop('disabled', false);
            $("#corporate_ride_radius_driver").prop('disabled', true);
            $("#favourite_list").prop('disabled', true);
        });

        $('#corporate_all_driver_booking').click(function () {
            $("#corporate_ride_radius_driver").prop('disabled', false);
            $("#corporate_ride_radius_manual_driver").prop('disabled', true);
            $("#driver_id").prop('disabled', true);
            $("#favourite_list").prop('disabled', true);
        });

        $("#corporate_ride_radius_manual_driver").blur(function () {
            var radius = this.value;
            var pickup_latitude = $('[name="pickup_latitude"]').val();
            var manual_area = $('[name="area"]').val();
            var pickup_longitude = $('[name="pickup_longitude"]').val();
            var drop_latitude = $('[name="drop_latitude"]').val();
            var drop_longitude = $('[name="drop_longitude"]').val();
            var service = $('[name="service"]').val();
            var vehicle_type = $('[name="vehicle_type"]').val();
            var driver_gender = $('[name="driver_gender"]').val();
            var distance_unit = document.getElementById("distance_unit").value;
            if (pickup_latitude == "" && drop_latitude == "" && service == "" && vehicle_type == "") {
                alert("Required Field Missing");
                //$('#ride_radius_manual_driver').prop('selectedIndex', 0);
                $('#corporate_ride_radius_manual_driver').val(0);
                return false;
            } else {
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "getallDriverCorporate",
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
                    },
                    success: function (data) {
                        $('#driver_id').html(data)
                    }
                });
            }
        });



        $("#corporate_ride_radius_driver").blur(function () {
            var radius = this.value;
            var pickup_latitude = $('[name="pickup_latitude"]').val();
            var manual_area = $('[name="area"]').val();
            var pickup_longitude = $('[name="pickup_longitude"]').val();
            var drop_latitude = $('[name="drop_latitude"]').val();
            var drop_longitude = $('[name="drop_longitude"]').val();
            var service = $('[name="service"]').val();
            var vehicle_type = $('[name="vehicle_type"]').val();
            var driver_gender = $('[name="driver_gender"]').val();
            var distance_unit = document.getElementById("distance_unit").value;
            if (pickup_latitude == "" && drop_latitude == "") {
                alert("Enter Pickup And Drop Location");
                //$('#ride_radius_driver').prop('selectedIndex', 0);
                $('#corporate_ride_radius_driver').val(0);
                return false;
            } else {
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "checkCorporateDriver",
                    data: {
                        manual_area: manual_area,
                        radius: radius,
                        pickup_latitude: pickup_latitude,
                        pickup_longitude: pickup_longitude,
                        drop_latitude: drop_latitude,
                        drop_longitude: drop_longitude,
                        service: service,
                        vehicle_type: vehicle_type,
                        driver_gender: driver_gender,
                        distance_unit: distance_unit,
                    },
                    success: function (data) {
                        $('#number_of_drivers').text(data)
                    }
                });
            }
        });

        function validation() {
            var country = document.getElementById('country').value;
            var user = document.getElementById('user_id').value;
            var manual_area = document.getElementById('corporate_manual_area').value;
            var service = document.getElementById('corporate_service').value;
            var vehicle_type = document.getElementById('corporate_vehicle_type').value;
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
            if (driver_request == 1 && document.getElementById('corporate_ride_radius_driver').value == "") {
                sweetalert("@lang("$string_file.enter_request_radius")");
                return false;

            }
            if (driver_request == 3 && document.getElementById('driver_id').value == "") {
                sweetalert("@lang("$string_file.select_driver")");
                return false;
            }
            if (driver_request == 3 && document.getElementById('corporate_ride_radius_manual_driver').value == "") {
                sweetalert("@lang("$string_file.enter_request_radius")");
                return false;
            }

            return true;
        }

        function sweetalert(msg) {
            swal({
                title: "@lang("$string_file.error")",
                text: msg,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            });
        }

        function get_this_element(object_data) {
            // console.log(object_data);
            // console.log($(object_data).attr('q'));
            $("#q_value").val($(object_data).attr('q'));
        }

        function check_element_on_blur(object_data) {
            // console.log(object_data);
            // console.log($(object_data).attr('q'));
            // console.log($('#drop_loc_'+$(object_data).attr('q')).val());
            if (!$('#drop_loc_' + $(object_data).attr('q')).val()) {
                $("#drop_loc_latitude_" + $(object_data).attr('q')).val(null);
                $("#drop_loc_longitude_" + $(object_data).attr('q')).val(null);
                //document.getElementById("distance_and_time").innerText = null;
            }
        }

        function resetPromoCode(){
            var old_eta = $('#old_eta').val();
            var iso = document.getElementById("isocode").value;
            var estimate = "Fare Estimate: "+iso+" "+ old_eta;
            $('#estimate_fare_ride').html(estimate);
            $('#estimate_fare').val(old_eta);
            $('#corporate_promo_code').prop('selectedIndex', 0);
            $('#payment_method_id').prop('selectedIndex', 0);
            return false;
        }

        $(document).ready(function () {
            count = 0;
            $(".add-more").click(function () {
                var max_multi_count = document.getElementById('max__multi_count').value;
                var limit = max_multi_count;
                //return false;
                if (count == limit) {
                    return false;
                }
                count++;
                console.log(count);
                var html = $(".copy").html();
                //////////////////////////////////////////////////////////////////
                if (count > 1) {
                    $(".after-add-more").after(html);
                    var last = $('.after-add-more').next();
                } else {
                    $(".after-add-more").before(html);
                    var last = $('.after-add-more').prev();
                }
                /////////////////////////////////////////////////////////////////
                //$(".after-add-more").before(html);      //Upper One from class"after-add-more"
                //let last = $('.after-add-more').prev();  //Upper One from class"after-add-more"
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
                // if(count>1){
                //     $('.after-add-more').next().children().children().attr('disabled',true)
                // }

                //console.log($('.after-add-more').children(3).children().attr('disabled',true));
                var addmore = new google.maps.places.Autocomplete(
                    (document.getElementById('drop_loc' + '_' + count)));

                addmore.addListener('place_changed', function () {
                    var place = addmore.getPlace();
                    //console.log(this);
                    // Used to set map on that loaction START
                    //     if (place.geometry.viewport) {
                    //     map.fitBounds(place.geometry.viewport);
                    //     } else {
                    //         map.setCenter(place.geometry.location);
                    //         map.setZoom(17);  // Why 17 ? Because it looks good.
                    //     }
                    // Used to set map on that loaction END
                    $("#drop_loc_latitude_" + $("#q_value").val()).val(place.geometry.location.lat());
                    $("#drop_loc_longitude_" + $("#q_value").val()).val(place.geometry.location.lng());

                    if (($('#drop_latitude').val()) && ($('#pickup_latitude').val())) {
                        $("#loader1").show();
                        calculateAndDisplayRoute(directionsService, directionsDisplay);
                        getDistance();
                        $("#loader1").hide();
                    }

                });
            });

            $("body").on("click", ".remove", function () {
                if ($(this).parents(".input-group").children().attr('q') < count)  // IT WILL RUN IF SOME EARLIER add more ELEMENT DELETED
                {   // DELETE ALL OF THE add more ELEMENTS, AS EARLIER WAS DELETED, COUNT VALUE DISTURBED
                    console.log('Yes Small FROM .remove');
                    while (count >= 1) {
                        if ($('#drop_loc_' + count).parents().hasClass('after-add-more')) {
                            // console.log("Yes this have class (after-add-more) ");
                            let last = $('.after-add-more').next();
                            last.addClass('after-add-more');
                        }
                        $('#drop_loc_' + count).parents(".input-group").remove();
                        //$(this).parents(".input-group").remove();
                        count--;
                        console.log(count);
                    }
                    if (($('#drop_latitude').val()) && ($('#pickup_latitude').val())) {
                        $("#loader1").show();
                        calculateAndDisplayRoute(directionsService, directionsDisplay);
                        getDistance();
                        $("#loader1").hide();
                    }
                } else {
                    console.log('NO Small FROM .remove');
                    if ($(this).parents().hasClass('after-add-more')) {
                        console.log("Yes this have class (after-add-more) ");
                        if (count > 1) {
                            var last = $('.after-add-more').prev();
                        } else {
                            var last = $('.after-add-more').next();
                        }
                        //let last = $('.after-add-more').next();
                        last.addClass('after-add-more');
                        if (count > 1) {
                            last.children().children().attr('disabled', false);
                            //last.parent().parent().attr('disabled',false);
                        }
                    }
                    $(this).parents(".input-group").remove();
                    count--;
                    console.log(count);
                    if (($('#drop_latitude').val()) && ($('#pickup_latitude').val())) {
                        $("#loader1").show();
                        calculateAndDisplayRoute(directionsService, directionsDisplay);
                        getDistance();
                        $("#loader1").hide();
                    }
                }
            });


            // $("body").on("blur",".blur",function(){    // To RESET Lat Long if empty found on blur
            //     console.log("FROM BLUR: "+$(this));
            //     //console.log("FROM BLUR: "+$("#drop_loc_latitude_"+$(this).attr('q')));
            //     //console.log("FROM BLUR: "+$("#drop_loc_latitude_"+$(this).attr('q')).val());
            //     if( !$("#drop_loc_"+$(this).attr('q')).val() ) {
            //         //$("#drop_loc_latitude_"+$(this).attr('q')).val = null;
            //         //$("#drop_loc_longitude_"+$(this).attr('q')).val = null;
            //     }
            // });

        });

        $('#drop_location').blur(function () {
            if (!$('#drop_location').val()) {
                document.getElementById("drop_latitude").value = null;
                document.getElementById("drop_longitude").value = null;
                document.getElementById("distance_and_time").innerText = null;
            }
            // console.log($('#pickup_location').val());
            // console.log($('#pickup_latitude').val());
        });

        $('#pickup_location').blur(function () {
            if (!$('#pickup_location').val()) {
                document.getElementById("pickup_latitude").value = null;
                document.getElementById("pickup_longitude").value = null;
                document.getElementById("distance_and_time").innerText = null;
            }
            // console.log($('#drop_location').val());
            // console.log($('#drop_latitude').val());
        });

        $('#drop_loc_' + $("#q_value").val()).blur(function () {
            if (!$('#drop_loc' + $("#q_value").val()).val()) {
                document.getElementById("#drop_loc_latitude_" + $("#q_value").val()).value = null;
                document.getElementById("#drop_loc_longitude_" + $("#q_value").val()).value = null;
                document.getElementById("distance_and_time").innerText = null;
            }
        });

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
            var start = new google.maps.places.Autocomplete(
                (document.getElementById('pickup_location')));

            start.addListener('place_changed', function () {
                var place = start.getPlace();
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(12);  // Why 17 ? Because it looks good.
                }
                document.getElementById("pickup_latitude").value = place.geometry.location.lat();
                document.getElementById("pickup_longitude").value = place.geometry.location.lng();
                var service = $('[name="service"]').val();
                if (service == "2" || service == "3") {
                    var vehicle_type = $('[name="vehicle_type"]').val();
                    if (vehicle_type == "") {
                        document.getElementById("pickup_latitude").value = "";
                        document.getElementById("pickup_longitude").value = "";
                        document.getElementById("pickup_location").value = "";
                        return false;
                    }
                    checkestimate();
                }
                if ($('#drop_latitude').val()) {
                    $("#loader1").show();
                    calculateAndDisplayRoute(directionsService, directionsDisplay);
                    getDistance();
                    $("#loader1").hide();
                }

            });


            var end = new google.maps.places.Autocomplete(
                (document.getElementById('drop_location')));

            end.addListener('place_changed', function () {
                var getplace = end.getPlace();
                document.getElementById("drop_latitude").value = getplace.geometry.location.lat();
                document.getElementById("drop_longitude").value = getplace.geometry.location.lng();
                if ($('#pickup_latitude').val()) {
                    $("#loader1").show();
                    calculateAndDisplayRoute(directionsService, directionsDisplay);
                    getDistance();
                    $("#loader1").hide();
                }

            });

            getDriverLocationo(1);
        }

        function getDriverLocationo(type) {
            var token = $('[name="_token"]').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "{{ route('getDriverOnMap') }}",
                data: {
                    type: type,
                },
                success: function (data) {
                    driverLocations = JSON.parse(data);
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
                        content = '<table><tr><td rowspan="4"><img src="' + marker_image + '" height="60" width="60"></td></tr><tr><td>&nbsp;&nbsp;Email: </td><td><b>' + email + '</b></td></tr><tr><td>&nbsp;&nbsp;Mobile: </td><td><b>+' + marker_number + '</b></td></tr></table>';
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
                    console.log(e);
                }

            });
        }

        function calculateAndDisplayRoute(directionsService, directionsDisplay) {
            var waypts = [];
            for (var i = count; i >= 1; i--) {
                console.log("yes way point FROM calculateAndDisplayRoute(): " + $('#drop_loc_' + i).val());
                if ($('#drop_loc_' + i).val()) {
                    waypts.push({
                        location: $('#drop_loc_' + i).val(),
                        stopover: true
                    });
                }
            }
            waypts.reverse();
            // console.log(waypts);

            //console.log(waypts);
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

        function getDistance() {

            var distanceService = new google.maps.DistanceMatrixService();
            console.log('COUNT VALUE:' + count);
            if (count > 0) {
                console.log("yes count >0 FROM getDistance()");
                var waypts = [];
                for (var i = count; i >= 1; i--) {
                    console.log("yes way point FROM getDistance(): " + $('#drop_loc_' + i).val());
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
                        document.getElementById("distance_and_time").innerText = "Distnace: " + total_distance +" "+unit+ " Time: " + total_time + " Mins";

                    } else {
                        window.alert('Directions request failed due to ' + status);
                    }
                    checkestimate();
                });
            } else {
                console.log("No count !> 0 FROM getDistance()");
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
                            document.getElementById("distance_and_time").innerText = "Distnace: " + distance + " "+unit+" Time: " + time;

                            document.getElementById("ride_time").value = response.rows[0].elements[0].duration.value;
                            var vehicle_type = $('[name="vehicle_type"]').val();
                            if (vehicle_type == "") {
                                document.getElementById("drop_location").value = "";
                                document.getElementById("distance").value = "";
                                document.getElementById("estimate_distance").value = "";
                                document.getElementById("estimate_time").value = "";
                                document.getElementById("distance_and_time").innerText = "";
                                document.getElementById("ride_time").value = "";
                                return false;
                            }
                            checkestimate();
                        }
                    });
            }
        }

        function checkestimate() {
            var area = $('[name="area"]').val();
            var distance = $('[name="distance"]').val();
            var distance_unit = $('[name="distance_unit"]').val();
            var ride_time = $('[name="ride_time"]').val();
            var service = $('[name="service"]').val();
            var vehicle_type = $('[name="vehicle_type"]').val();
            var token = $('[name="_token"]').val();
            var package_id = $('[name="package"]').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "estimatePriceCorporate",
                data: {
                    distance: distance,
                    ride_time: ride_time,
                    service: service,
                    vehicle_type: vehicle_type,
                    area: area,
                    distance_unit : distance_unit,
                    package_id : package_id
                },
                success: function (data) {
                    var iso = document.getElementById("isocode").value;
                    var estimate = "Fare Estimate: "+iso+" "+ data;
                    $('#estimate_fare_ride').html(estimate);
                    $('#estimate_fare').val(data);
                }
            });
        }

        function changeCanter(s) {
            var city = s[s.selectedIndex].id;
            if (city != "") {
                var geocoder;
                geocoder = new google.maps.Geocoder();
                geocoder.geocode({'address': city}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        //alert(results[0].geometry.location);
                        map.setZoom(12);
                        map.setCenter(results[0].geometry.location)
                    }
                });
            }
        }

        google.maps.event.addDomListener(window, 'load', initialize);

        function RideType(val) {
            if (val == "1") {
                document.getElementById('start-div').style.display = 'none';
                document.getElementById('end-div').style.display = 'none';
            } else {
                document.getElementById('start-div').style.display = 'block';
                document.getElementById('end-div').style.display = 'block';
            }
        }

        $('#corporate_service').change(function () {
            var service = $('#corporate_service').val();
            var multi_destination = $('#multi_destination').val();
            if (service == 1 && multi_destination == 1) {
                $('#add_loc').css("display", "block");
            } else {
                $('#add_loc').css("display", "none");
            }
        });
    </script>
@endsection