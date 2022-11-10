@extends('merchant.layouts.main')
@section('content')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <style>
        * {
            box-sizing: border-box
        }

        body {
            font-family: "Lato", sans-serif;
        }

        /* Style the tab */
        .tab {
            float: left;
            border: 1px solid #ccc;
            background-color: #f1f1f1;
            width: 30%;
            height: 300px;
        }

        /* Style the buttons inside the tab */
        .tab button {
            display: block;
            background-color: inherit;
            color: black;
            padding: 22px 16px;
            width: 100%;
            border: none;
            outline: none;
            text-align: left;
            cursor: pointer;
            transition: 0.3s;
            font-size: 17px;
        }

        /* Change background color of buttons on hover */
        .tab button:hover {
            background-color: #ddd;
        }

        /* Create an active/current "tab button" class */
        .tab button.active {
            background-color: #ccc;
        }

        /* Style the tab content */
        .tabcontent {
            float: left;
            padding: 0px 12px;
            border: 1px solid #ccc;
            width: 70%;
            border-left: none;
            height: 300px;
        }
    </style>
    <div class="app-content content">
        <div class="container-fluid ">
            <div class="content-wrapper">

                <div class="content-body">
                    <section id="validation">
                        <div class="row">
                            @include('merchant.shared.errors-and-messages')
                            <div class="col-12">
                                <div class="card shadow h-100">
                                    <div class="card-header py-3">
                                        <div class="content-header row">
                                            <div class="content-header-left col-md-8 col-12 mb-2 breadcrumb-new">
                                                <h3 class="content-header-title mb-0 d-inline-block">
                                                    <i class=" fa fa-user-plus" aria-hidden="true"></i>
                                                    @lang('admin.edit_cashback_for') {{$edit->CountryArea->CountryAreaName}}</h3>

                                            </div>
                                            <div class="content-header-right col-md-4 col-12">
                                                <div class="btn-group float-md-right">
                                                    <a href="{{ route('cashback.index') }}">
                                                        <button type="button" class="btn btn-icon btn-success mr-1"><i
                                                                    class="fa fa-reply"></i>
                                                        </button>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="">
                                        <a class="heading-elements-toggle"><i
                                                    class="ft-ellipsis-h font-medium-3"></i></a>
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
                                                  enctype="multipart/form-data" action="{{ route('cashback.update',$edit->id) }}">
                                                @csrf
                                                <input type="hidden" name="_method" value="put">
                                                <fieldset>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="area">
                                                                    @lang('admin.AreaName')<span
                                                                            class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="area"
                                                                       name="country_area" value="{{ $edit->CountryArea->CountryAreaName }}"
                                                                       placeholder="" required readonly>
                                                                <input type="hidden" name="area" value="{{$edit->country_area_id}}"/>
                                                                {{--<select class="form-control" name="area" id="area"
                                                                        onchange="getService(this.value)" required disabled>
                                                                    <option value="">--Select Area--</option>
                                                                        <option value="{{ $edit->country_area_id }}" selected> {{ $edit->CountryArea->CountryAreaName }}</option>
                                                                </select>--}}
                                                                @if ($errors->has('area'))
                                                                    <label class="text-danger">{{ $errors->first('area') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row" id="service_type_with_vehicles">
                                                    </div>

                                                    <div class="row">
                                                        {{--<div class="col-md-6" id="service_type_show">
                                                            <div class="form-group">
                                                                <label for="location3">@lang("$string_file.select_services") :
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <ul class="list-unstyled" id="service_type_values">
                                                                @forelse($areaServices  as $all_service)
                                                                    <div class='custom-control custom-checkbox mr-1'>
                                                                        <input type='checkbox'
                                                                               @if(isset($selected_services) && in_array($all_service->id, $selected_services))checked="checked" @endif
                                                                               data-id='{{$all_service->id}}' class='custom-control-input all_services' name='services[]' id='service-{{$all_service->id}}' value='{{$all_service->id}}'>
                                                                        <label class='custom-control-label' for='service-{{$all_service->id}}'>{{$all_service->serviceName}}</label>
                                                                    </div>
                                                                @empty
                                                                @endforelse
                                                                </ul>
                                                                @if ($errors->has('services'))
                                                                    <label class="text-danger">{{ $errors->first('services') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>--}}

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="bill_amount">
                                                                    @lang('admin.min_bill_cashback') :
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="bill_amount"
                                                                       name="bill_amount" value="{{$edit->min_bill_amount}}"
                                                                       placeholder="@lang('admin.min_bill_cashback')" required>
                                                                @if ($errors->has('bill_amount'))
                                                                    <label class="text-danger">{{ $errors->first('bill_amount') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>


                                                        @forelse($areaServices as $key => $all_service)
                                                            <div class="col-md-6">
                                                                <div class="form-group p-1">
                                                                    <label for="servive_vehicles-{{$all_service[0]['id']}}">
                                                                        {{trans('admin.select-vehicles',[$all_service[0]['serviceName']])}}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <select class="select2 form-control"
                                                                            name="{{$all_service[0]['serviceName']}}[]"
                                                                            id="servive_vehicles-{{$all_service[0]['id']}}"
                                                                            data-placeholder="@lang("$string_file.vehicle_type") "
                                                                            multiple>
                                                                            @forelse($area_vehicles as $service_match_keys => $vehicles_collect)
                                                                            {{$key}} {{$service_match_keys}}
                                                                                @if($service_match_keys == $key)
                                                                                    @forelse($vehicles_collect as $keys => $area_vehicle)
                                                                                        <option id="vehicletype-{{ $area_vehicle->id }}"
                                                                                                value="{{ $area_vehicle->id }}"
                                                                                                @if($selected_vehicles->isNotEmpty())
                                                                                                    @if(!empty($selected_vehicles->toArray()[$service_match_keys]))
                                                                                                        @if(isset($selected_vehicles) && in_array($area_vehicle->id, $selected_vehicles[$service_match_keys]->pluck('id')->toArray())) selected @endif
                                                                                                    @endif
                                                                                                @endif>
                                                                                            {{$area_vehicle->vehicleTypeName}}
                                                                                        </option>
                                                                                        @empty
                                                                                    @endforelse
                                                                                @endif
                                                                            @empty
                                                                            <p class="alert alert-warning">No Record Found.</p>
                                                                            @endforelse

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        @empty
                                                        @endforelse

                                                        {{--<div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="max_trip">
                                                                    @lang("$string_file.maximum_rides")  :
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="max_trip"
                                                                       name="max_trip" value="{{ old('max_trip') }}"
                                                                       placeholder="@lang("$string_file.maximum_rides") "
                                                                       required>
                                                                @if ($errors->has('max_trip'))
                                                                    <label class="text-danger">{{ $errors->first('max_trip') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>--}}
                                                    </div>

                                                    <div class="row" id="all_vehicle_types">
                                                        <div id="check">

                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label for="user_cashback_enable">@lang('admin.cashback_enable_users')</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <div class="input-group-text">
                                                                        <div class="custom-control custom-checkbox">
                                                                            <input type="checkbox"
                                                                                   class="custom-control-input"
                                                                                   onclick="enableuserInput(this)"
                                                                                   id="user_cashback_enable"
                                                                                   name="user_cashback_enable_checkbox"
                                                                                   value="1"
                                                                                   @if($edit->users_cashback_enable == 1) checked="checked" @endif
                                                                                   >
                                                                            <label class="custom-control-label"
                                                                                   for="user_cashback_enable"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <input type="number" class="form-control"
                                                                       id="user_cashback_from"
                                                                       {{--onkeypress="return NumberInput(event)"--}}
                                                                       name="user_cashback_from"
                                                                       value="{{$edit->users_percentage}}"
                                                                       placeholder="{{ trans('admin.cashback_from') }}"
                                                                       aria-describedby="checkbox-addon1"
                                                                       @if(($edit->users_cashback_enable != 1))
                                                                       disabled="disabled"
                                                                       @endif >
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="user_cashback_upto">
                                                                {{ trans('admin.cashback_upto') }}
                                                            </label>
                                                            <div class="form-group">
                                                                <div class="input-group-prepend">
                                                                    <input type="number" class="form-control"
                                                                           id="user_cashback_upto"
                                                                           name="user_cashback_upto"
                                                                           value="{{$edit->users_upto_amount}}"
                                                                           placeholder="{{ trans('admin.cashback_upto_placeholder') }}"
                                                                           @if(($edit->users_cashback_enable == 1) && ($edit->users_upto_amount != null))
                                                                           @else
                                                                           disabled="disabled"
                                                                            @endif >
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <h4 style="text-align: center"><b>OR</b></h4>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="user_cashback_max"> {{ trans('admin.cashback_upto_max')}}</label>
                                                                <div class="form-group">
                                                                    <div class="input-group-prepend">
                                                                        <div class="input-group-text">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                       class="custom-control-input"
                                                                                       id="user_cashback_max"
                                                                                       name="user_cashback_max"
                                                                                       value="1"
                                                                                       @if(($edit->users_cashback_enable == 1))
                                                                                       {{($edit->users_max == 1) ? "checked = checked" :'' }}
                                                                                       @else disabled="disabled"
                                                                                        @endif >
                                                                                <label class="custom-control-label"
                                                                                       for="user_cashback_max">{{ trans('admin.max')}}</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                        </div>

                                                        <div class="col-md-6" id="user_cashback_text_div" @if(($edit->users_cashback_enable != 1)) style="display: none;" @endif>
                                                            <div class="form-group">
                                                                <label for="user_cashback_text">
                                                                    @lang('admin.cashback_text_users') :
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="user_cashback_text"
                                                                       name="user_cashback_text" value="@if(!empty($edit->LangCashbackSingleUser)){{$edit->LangCashbackSingleUser->app_message}}@endif"
                                                                       placeholder="@lang('admin.cashback_text_users')"
                                                                       @if($edit->users_cashback_enable == 1) required @else disabled @endif>
                                                                @if ($errors->has('user_cashback_text'))
                                                                    <label class="text-danger">{{ $errors->first('user_cashback_text') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label for="driver_cashback_enable">@lang('admin.cashback_enable_drivers')</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <div class="input-group-text">
                                                                        <div class="custom-control custom-checkbox">
                                                                            <input type="checkbox"
                                                                                   class="custom-control-input"
                                                                                   onclick="enableDriverInput(this)"
                                                                                   id="driver_cashback_enable"
                                                                                   name="driver_cashback_enable_checkbox"
                                                                                   value="1"
                                                                                   @if($edit->drivers_cashback_enable == 1) checked="checked" @endif
                                                                            >
                                                                            <label class="custom-control-label"
                                                                                   for="driver_cashback_enable"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <input type="number" class="form-control"
                                                                       id="driver_cashback_from"
                                                                       {{--onkeypress="return NumberInput(event)"--}}
                                                                       name="driver_cashback_from"
                                                                       value="{{$edit->drivers_percentage}}"
                                                                       placeholder="{{ trans('admin.cashback_from') }}"
                                                                       aria-describedby="checkbox-addon1"
                                                                       @if(($edit->drivers_cashback_enable != 1))
                                                                       disabled="disabled"
                                                                        @endif >
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="driver_cashback_upto">
                                                                {{ trans('admin.cashback_upto') }}
                                                            </label>
                                                            <div class="form-group">
                                                                <div class="input-group-prepend">
                                                                    <input type="number" class="form-control"
                                                                           id="driver_cashback_upto"
                                                                           name="driver_cashback_upto"
                                                                           value="{{$edit->drivers_upto_amount}}"
                                                                           placeholder="{{ trans('admin.cashback_upto_placeholder') }}"
                                                                           @if(($edit->drivers_cashback_enable == 1) && ($edit->drivers_upto_amount != null))
                                                                           @else
                                                                           disabled="disabled"
                                                                            @endif>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <h4 style="text-align: center"><b>OR</b></h4>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="driver_cashback_max"> {{ trans('admin.cashback_upto_max')}}</label>
                                                            <div class="form-group">
                                                                <div class="input-group-prepend">
                                                                    <div class="input-group-text">
                                                                        <div class="custom-control custom-checkbox">
                                                                            <input type="checkbox"
                                                                                   class="custom-control-input"
                                                                                   id="driver_cashback_max"
                                                                                   name="driver_cashback_max"
                                                                                   value="1"
                                                                                   @if(($edit->drivers_cashback_enable == 1))
                                                                                   {{($edit->drivers_max == 1) ? "checked = checked" :'' }}
                                                                                   @else disabled="disabled"
                                                                                    @endif
                                                                            >
                                                                            <label class="custom-control-label"
                                                                                   for="driver_cashback_max">{{ trans('admin.max')}}</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6" id="driver_cashback_text_div" @if(($edit->drivers_cashback_enable != 1)) style="display: none;" @endif>
                                                            <div class="form-group">
                                                                <label for="driver_cashback_text">
                                                                    @lang('admin.cashback_text_drivers') :
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="driver_cashback_text"
                                                                       name="driver_cashback_text" value="@if(!empty($edit->LangCashbackSingleDriver)){{$edit->LangCashbackSingleDriver->app_message}}@endif"
                                                                       placeholder="@lang('admin.cashback_text_drivers')"
                                                                       @if($edit->drivers_cashback_enable == 1) required @else disabled @endif>
                                                                @if ($errors->has('driver_cashback_text'))
                                                                    <label class="text-danger">{{ $errors->first('driver_cashback_text') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br/>
                                                    <h6 class="mt-2">@lang('admin.message885')</h6>
                                                    <hr/>

                                                    {{--<div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="location6">@lang("$string_file.duration") :
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control" name="package_duration"
                                                                        id="location6"
                                                                        required>
                                                                    <option value="">@lang("$string_file.duration")</option>
                                                                </select>
                                                                @if ($errors->has('package_duration'))
                                                                    <label class="text-danger">{{ $errors->first('package_duration') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group p-1">
                                                                <label for="emailAddress5">
                                                                    @lang("$string_file.area")
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <select class="select2 form-control"
                                                                        name="areas[]"
                                                                        id="areas"
                                                                        data-placeholder="@lang("$string_file.area")"
                                                                        multiple>
                                                                    @foreach($areas as $all_area)
                                                                        <option id="area_{{ $all_area->id }}"
                                                                                value="{{ $all_area->id }}"> {{
                                                                    $all_area->CountryAreaName }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @if ($errors->has('areas'))
                                                                    <label class="text-danger">{{ $errors->first('areas')
                                                                }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                {!! Form::label('status', trans("$string_file.status").'<span class="text-danger">*</span> :', ['class' => 'control-label'], false) !!} &nbsp;
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        {{ Form::radio('status','1',true,['class' => 'custom-control-input','id'=>'active',])  }}
                                                                        {!! Form::label('active', trans("$string_file.active"), ['class' => 'custom-control-label'], false) !!}
                                                                    </div>
                                                                </fieldset>
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        {{ Form::radio('status','0',false,['class' => 'custom-control-input','id'=>'deactive',])  }}
                                                                        {!! Form::label('deactive', trans("$string_file.inactive"), ['class' => 'custom-control-label'], false) !!}
                                                                    </div>
                                                                </fieldset>

                                                            </div>
                                                        </div>
                                                    </div>--}}


                                                </fieldset>
                                                <div class="form-actions float-right">
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
    </div>

    <script type="text/javascript">

        function openCity(evt, cityName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += " active";
            return false;
        }
        // Get the element with id="defaultOpen" and click on it
        //document.getElementById("defaultOpen").click();

        function enableuserInput(value) {
            if($(value).prop('checked')) {

                $('#user_cashback_text_div').fadeIn();
                $('#user_cashback_text').prop("disabled", false);
                $('#user_cashback_text').prop("required", true);
                $('#user_cashback_from').prop("required", true);
                $('#user_cashback_from').prop("disabled", false);
                $('#user_cashback_upto').prop("disabled", false);
                $('#user_cashback_max').prop("disabled", false);
            }else{
                $('#user_cashback_text_div').fadeOut();
                $('#user_cashback_text').prop("disabled", true);
                $('#user_cashback_text').prop("required", false);
                $('#user_cashback_from').prop("disabled", true);
                $('#user_cashback_from').prop("required", false);
                $('#user_cashback_upto').prop("disabled", true);
                $('#user_cashback_max').prop("checked", false);
                $('#user_cashback_max').prop("disabled", true);
            }
        }

        $('#user_cashback_max').click(function ()
        {
            if ($(this).is(":checked")) {
                $('#user_cashback_upto').val(null);
                $('#user_cashback_upto').prop("disabled", true);
                $('#user_cashback_upto').prop("required", false);
            } else {
                $('#user_cashback_upto').prop("disabled", false);
                $('#user_cashback_upto').prop("required", true);
            }
        });

        function enableDriverInput(value) {
            if($(value).prop('checked')) {
                $('#driver_cashback_text_div').fadeIn();
                $('#driver_cashback_text').prop("disabled", false);
                $('#driver_cashback_text').prop("required", true);
                $('#driver_cashback_from').prop("required", true);
                $('#driver_cashback_from').prop("disabled", false);
                $('#driver_cashback_upto').prop("disabled", false);
                $('#driver_cashback_max').prop("disabled", false);
            }else{
                $('#driver_cashback_text_div').fadeOut();
                $('#driver_cashback_text').prop("disabled", true);
                $('#driver_cashback_text').prop("required", false);
                $('#driver_cashback_from').prop("disabled", true);
                $('#driver_cashback_from').prop("required", false);
                $('#driver_cashback_upto').prop("disabled", true);
                $('#driver_cashback_max').prop("checked", false);
                $('#driver_cashback_max').prop("disabled", true);
            }
        }

        $('#driver_cashback_max').click(function ()
        {
            if ($(this).is(":checked")) {
                $('#driver_cashback_upto').val(null);
                $('#driver_cashback_upto').prop("disabled", true);
                $('#driver_cashback_upto').prop("required", false);
            } else {
                $('#driver_cashback_upto').prop("disabled", false);
                $('#driver_cashback_upto').prop("required", true);
            }
        });

        function getvehicles(received) {

            var requiredCheckboxes = $('.all_services');
            $(requiredCheckboxes).on('change', function () {
                if (requiredCheckboxes.is(':checked')) {
                    requiredCheckboxes.removeAttr('required');
                } else {
                    requiredCheckboxes.attr('required', 'required');
                }

            });

            let service_id = $(received).attr('data-id');
            let area_id = $('#area').val();
            if($(received).prop('checked')) {
                console.log('checked');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': '{{csrf_token()}}'
                    },
                    method: 'POST',
                    url: '<?php echo route('merchant.area.vehicletypescashback') ?>',
                    data: {area_id: area_id,
                        service_id: service_id},
                    success: function (data) {
                        // console.log(data);
                        // var html = $(".copy").html();
                        // $("#check").after(html)
                        $('#check').before(data);
                        //initializeSelect2(data);
                        $('.select2me').select2();
                        //$('#service_type_show').css('display','inline-block');
                    }
                });
            } else {
                console.log('unchecked');
                $('#services-delete-'+service_id).remove();
            }
            console.log($(received).attr('data-id'));

        }


        $('#driverupto').click(function () {
            if ($(this).is(":checked")) {
                $('#driveruptoinput').prop("disabled", false);
            } else {
                $('#driveruptoinput').prop("disabled", true);
            }
        });
        $("#servicetype").click(function () {
            $("#servicetypebox").fadeToggle();
        });

        function getService(val) {
            if (val != "") {
                //$("#loader1").show();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': '{{csrf_token()}}'
                    },
                    method: 'POST',
                    url: '<?php echo route('merchant.area.servicescashback') ?>',
                    data: {area_id: val},
                    success: function (data) {
                        // console.log(data);
                        // $('#service_type_show').html('');
                        $('#service_type_values').html(data);
                        $('#service_type_show').css('display','inline-block');
                        $('#all_vehicle_types').empty();
                        $('#all_vehicle_types').prepend($('<div id="check">\n'+'\n'+'</div>'));
                    }
                });
                $("#loader1").hide();
            }
        }



    </script>
@endsection