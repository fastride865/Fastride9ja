@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('priceadded'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('priceadded') }}
                </div>
            @endif
            @if($errors->all())
                @foreach($errors->all() as $message)
                    <div class="alert dark alert-icon alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                        <i class="icon fa-warning" aria-hidden="true"></i>{{ $message }}
                    </div>
                @endforeach
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(Auth::user('merchant')->can('create_price_card'))
                            <div class="btn-group float-md-right">
                                <a href="{{ route('pricecard.delivery') }}">
                                    <button type="button" class="btn btn-icon btn-success mr-1" style="margin:10px  "><i
                                                class="wb-reply"></i>
                                    </button>
                                </a>
                            </div>
                        @endif
                    </div>
                    <h3 class="panel-title">
                        <i class="icon wb-plus" aria-hidden="true"></i>
                        @lang('admin.message365') @lang('admin.pricecard')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="{{ route('pricecard.delivery.store') }}">
                        @csrf
                        <div class="row">
                            @if($configuration->outside_area_ratecard == 1)
                                <div class="col-md-4">
                                    @else
                                        <div class="col-md-4">
                                            @endif
                                            <div class="form-group">
                                                <label for="firstName3">
                                                    @lang('admin.area_name')<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <select class="form-control" name="area" id="area"
                                                        onchange="getDeliveryType(this.value)" required>
                                                    <optgroup label="Service Area">
                                                        <option value="">@lang('admin.service_area')</option>
                                                        @foreach($areas as $area)
                                                            <option value="{{ $area->id }}"> {{ $area->CountryAreaName }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                    @if(isset($configuration->geofence_module) && $configuration->geofence_module == 1)
                                                        <optgroup label="Geofence Area">
                                                            @foreach($geofenceAreas as $geofenceArea)
                                                                <option value="{{ $geofenceArea->id }}"> {{ $geofenceArea->CountryAreaName }}</option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endif
{{--                                                    <option value="">--@lang('admin.message427')--</option>--}}
{{--                                                    @foreach($areas as $area)--}}
{{--                                                        <option {{(old('area') == $area->id) ? 'selected' : '' }} value="{{ $area->id }}"> {{ $area->CountryAreaName }}</option>--}}
{{--                                                    @endforeach--}}
                                                </select>
                                                @if ($errors->has('area'))
                                                    <label class="danger">{{ $errors->first('area') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        @if($configuration->outside_area_ratecard == 1)
                                            <div class="col-md-4">
                                                @else
                                                    <div class="col-md-4">
                                                        @endif
                                                        <div class="form-group">
                                                            <label for="lastName3">
                                                                @lang('admin.delivery_type')<span class="text-danger">*</span>
                                                            </label>
                                                            <select class="form-control" name="delivery_type"
                                                                    id="delivery_type"
                                                                    required>
                                                                <option value="">--@lang('admin.message573')--</option>
                                                            </select>
                                                            @if ($errors->has('service'))
                                                                <label class="danger">{{ $errors->first('service') }}</label>
                                                            @endif
                                                            @if ($errors->has('delivery_type'))
                                                                <label class="danger">{{ $errors->first('service_type') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if($configuration->outside_area_ratecard == 1)
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="lastName3">
                                                                    @lang('admin.rate_card_scope')<span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control" name="rate_card_scope"
                                                                        id="rate_card_scope"
                                                                        required>
                                                                    <option value="1">-- @lang('admin.with_in_area') --</option>
                                                                    <option value="2">-- @lang('admin.outside_area') --</option>
                                                                </select>
                                                                @if ($errors->has('rate_card_scope'))
                                                                    <label class="danger">{{ $errors->first('rate_card_scope') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @else
                                                        <input type="hidden" value="0" name="rate_card_scope">
                                                    @endif
                                            </div>
                                            <div class="row custom-hidden" id="outstation_div">
                                                <div class="col-md-4 corporate_inr">
                                                    <div class="form-group">
                                                        <label for="emailAddress5">
                                                            @lang('admin.Fare_Type')<span
                                                                    class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-control" name="outstation_type"
                                                                id="outstation_type"
                                                                onchange="outstation(this.value)">
                                                            <option value="">@lang('admin.Select_Fare_Type')</option>
                                                            <option value="1">@lang('admin.Round_Trip')</option>
                                                            <option value="2">@lang('admin.One_Way_And_Round_Trip')</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="row custom-hidden" id="fixed_div">
                                                <div class="col-md-4 corporate_inr">
                                                    <div class="form-group">
                                                        <label for="emailAddress5" id="newTaxt">
                                                            @lang('admin.package')<span class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-control" name="package_id"
                                                                id="package_id"
                                                                onclick="GetVehcile(this.value)">
                                                            <option value="">@lang('admin.message215')</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="emailAddress5">
                                                            @lang('admin.message216')<span
                                                                    class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-control" name="vehicle_type"
                                                                id="vehicle_type" required>
                                                            <option value="">--@lang('admin.message216')--</option>
                                                        </select>
                                                        @if($errors->has('vehicle_type'))
                                                            <label class="danger">{{ $errors->first('vehicle_type') }}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <h4 style="text-align: center"><b>@lang('admin.or')</b></h4>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="emailAddress5">
                                                            @lang('admin.message174')<span
                                                                    class="text-danger">*</span>
                                                            <input type="checkbox" class="mycheckbox"
                                                                   name="all_vehile_type" id="all_vehile_type"
                                                                   value="1"
                                                                   onclick="disbleField()">
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="emailAddress5">
                                                            @lang('admin.price_type')<span
                                                                    class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-control" name="price_type"
                                                                id="price_type"
                                                                onclick="PricingType(this.value)" required>
                                                            <option value="">@lang('admin.select_pricing_type')</option>
                                                            @foreach($merchant->RateCard as $value)
                                                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('price_type'))
                                                            <label class="danger">{{ $errors->first('price_type') }}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group custom-hidden" id="extra_charge">
                                                        <label for="emailAddress5">
                                                            @lang('admin.message572')<span
                                                                    class="text-danger">*</span>
                                                        </label>
                                                        <input type="number" class="form-control" min="0"
                                                               id="extra_sheet_charge" step="0.01"
                                                               name="extra_sheet_charge"
                                                               placeholder="@lang('admin.message572')">
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="dynamic_row">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group custom-hidden" id="end-div">
                                                        <label for="emailAddress5">
                                                            @lang('admin.maximum_bill_amount')<span
                                                                    class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control"
                                                               id="maximum_bill_amount"
                                                               name="maximum_bill_amount"
                                                               placeholder="@lang('admin.maximum_bill_amount')">
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            @if($config->time_charges == 1)
                                                <h4 class="form-section" style="color: black"><i
                                                            class="fa fa-moon-o"></i> @lang('admin.time_charges')
                                                </h4>
                                                <hr>
                                                <div id="after-add-more">
                                                    <div class="row" style="text-align: center;">
                                                        <div class="col-md-10">
                                                            <label for="weekdays">
                                                                @lang('admin.select_week_days') :
                                                                <span class="danger">*</span>
                                                            </label>
                                                            <div class="form-group">
                                                                <div class="weekDays-selector">
                                                                    <input type="checkbox" value="1"
                                                                           name="week_days[0][]"
                                                                           id="weekday-mon"
                                                                           class="weekday"/>
                                                                    <label for="weekday-mon">M</label>
                                                                    <input type="checkbox" value="2"
                                                                           name="week_days[0][]"
                                                                           id="weekday-tue"
                                                                           class="weekday"/>
                                                                    <label for="weekday-tue">T</label>
                                                                    <input type="checkbox" value="3"
                                                                           name="week_days[0][]"
                                                                           id="weekday-wed"
                                                                           class="weekday"/>
                                                                    <label for="weekday-wed">W</label>
                                                                    <input type="checkbox" value="4"
                                                                           name="week_days[0][]"
                                                                           id="weekday-thu"
                                                                           class="weekday"/>
                                                                    <label for="weekday-thu">T</label>
                                                                    <input type="checkbox" value="5"
                                                                           name="week_days[0][]"
                                                                           id="weekday-fri"
                                                                           class="weekday"/>
                                                                    <label for="weekday-fri">F</label>
                                                                    <input type="checkbox" value="6"
                                                                           name="week_days[0][]"
                                                                           id="weekday-sat"
                                                                           class="weekday"/>
                                                                    <label for="weekday-sat">S</label>
                                                                    <input type="checkbox" value="7"
                                                                           name="week_days[0][]"
                                                                           id="weekday-sun"
                                                                           class="weekday"/>
                                                                    <label for="weekday-sun">S</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="input-group-btn">
                                                                <button class="btn btn-success add-more"
                                                                        type="button">
                                                                    <i class="glyphicon glyphicon-plus"></i> @lang('admin.add_new_slots')
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="parametername">
                                                                    @lang('admin.parametername') :
                                                                    <span class="danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                       id="parametername"
                                                                       name="parametername[0]"
                                                                       placeholder="@lang('admin.parametername')"
                                                                       autocomplete="off">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="begintime">
                                                                    @lang('admin.slot_start_time') :
                                                                    <span class="danger">*</span>
                                                                </label>
                                                                <input type="text"
                                                                       class="form-control timepicker" data-plugin="clockpicker" data-autoclose="true"
                                                                       id="begintime" name="begintime[0]"
                                                                       placeholder="@lang('admin.select_slot_time')"
                                                                       autocomplete="off">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="endtime">
                                                                    @lang('admin.slot_end_time') :
                                                                    <span class="danger">*</span>
                                                                </label>
                                                                <input type="text"
                                                                       class="form-control timepicker" data-plugin="clockpicker" data-autoclose="true"
                                                                       id="endtime" name="endtime[0]"
                                                                       placeholder="@lang('admin.select_slot_time')"
                                                                       autocomplete="off">
                                                                <label class="radio-inline"
                                                                       style="margin-right: 2%;margin-left: 5%;margin-top: 6%;">
                                                                    <input type="radio" value="1"
                                                                           checked
                                                                           name="optradio[0]">@lang('admin.next_day')
                                                                </label>
                                                                <label class="radio-inline">
                                                                    <input type="radio" value="2"
                                                                           id="charge_type"
                                                                           name="optradio[0]">@lang('admin.same_day')
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="slot_charges">
                                                                    @lang('admin.slot_charges') :
                                                                    <span class="danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                       id="slot_charges" name="slot_charges[0]"
                                                                       placeholder="@lang('admin.please_enter_slot_charge')"
                                                                       autocomplete="off">
                                                                <label class="radio-inline"
                                                                       style="margin-right: 2%;margin-left: 5%;margin-top: 3%;">
                                                                    <input type="radio" value="1"
                                                                           checked
                                                                           name="charge_type[0]">@lang('admin.nominal')
                                                                </label>
                                                                <label class="radio-inline">
                                                                    <input type="radio" value="2"
                                                                           id="charge_type"
                                                                           name="charge_type[0]">@lang('admin.multiplier')
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="checkBoxCount" id="checkBoxCount"
                                                           value="0">
                                                </div>
                                            @endif
                                            @if($merchant->cancel_charges == 1)
                                                <h5 class="form-section" style="color: black"><i
                                                            class="fa fa-paperclip"></i> @lang('admin.message712')
                                                </h5>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="emailAddress5">
                                                                @lang('admin.message716')<span
                                                                        class="text-danger">*</span>
                                                            </label>
                                                            <select class="form-control" name="cancel_charges"
                                                                    id="cancel_charges"
                                                                    required>
                                                                <option value="2">@lang('admin.message323')</option>
                                                                <option value="1">@lang('admin.message322')</option>
                                                            </select>
                                                            @if ($errors->has('cancel_charges'))
                                                                <label class="danger">{{ $errors->first('cancel_charges') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 custom-hidden" id="cancel_first">
                                                        <div class="form-group">
                                                            <label for="emailAddress5">
                                                                @lang('admin.message713')<span
                                                                        class="text-danger">*</span>
                                                            </label>
                                                            <input type="text"
                                                                   onkeypress="return NumberInput(event)"
                                                                   class="form-control"
                                                                   id="cancel_time"
                                                                   name="cancel_time"
                                                                   placeholder="@lang('admin.message714')">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 custom-hidden" id="cancel_second">
                                                        <div class="form-group">
                                                            <label for="emailAddress5">
                                                                @lang('admin.message715')<span
                                                                        class="text-danger">*</span>
                                                            </label>
                                                            <input type="text"
                                                                   onkeypress="return NumberInput(event)"
                                                                   class="form-control"
                                                                   id="cancel_amount"
                                                                   name="cancel_amount"
                                                                   placeholder="@lang('admin.message164')">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <br>
                                            <h4 class="form-section" style="color: black"><i
                                                        class="fa fa-paperclip"></i> @lang('admin.commission_structure')
                                            </h4>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="emailAddress5">
                                                            @lang('admin.commission_payout_method')<span
                                                                    class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-control" name="commission_type"
                                                                id="commission_type"
                                                                required>
                                                            <option value="">@lang('admin.select_comission_payout')</option>
                                                            @if($config->driver_wallet_status == 1)
                                                                <option value="1">@lang('admin.prepaid')</option>
                                                            @endif
                                                            <option value="2">@lang('admin.postpaid')</option>
                                                        </select>
                                                        @if ($errors->has('commission_type'))
                                                            <label class="danger">{{ $errors->first('commission_type') }}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="emailAddress5">
                                                            @lang('admin.calculation_method')<span
                                                                    class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-control" name="commission_method"
                                                                id="commission_method"
                                                                onclick="Commsionmethod(this.value)" required>
                                                            <option value="">@lang('admin.calulationMethod')</option>
                                                            <option value="1">@lang('admin.flat_comission')</option>
                                                            <option value="2">@lang('admin.percentage_bill')</option>
                                                        </select>
                                                        @if ($errors->has('commission_method'))
                                                            <label class="danger">{{ $errors->first('commission_method') }}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group custom-hidden" id="flat-div">
                                                        <label for="emailAddress5">
                                                            @lang('admin.message175')<span
                                                                    class="text-danger">*</span>
                                                        </label>
                                                        <input type="number" class="form-control"
                                                               step=0.01 min=0
                                                               id="flat_commission"
                                                               name="flat_commission"
                                                               placeholder="@lang('admin.message176')">
                                                    </div>
                                                    <div class="form-group custom-hidden" id="percantage-div">
                                                        <label for="emailAddress5">
                                                            @lang('admin.message177')<span
                                                                    class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control"
                                                               id="percentage_commission"
                                                               name="percentage_commission"
                                                               placeholder="@lang('admin.message176') (@lang('admin.message674'))">
                                                    </div>
                                                </div>
                                            </div>

                                            @if($config->sub_charge == 1)
                                                <h4 class="form-section" style="color: black"><i
                                                            class="fa fa-money"></i> @lang('admin.SubCharge')
                                                </h4>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="emailAddress5">
                                                                @lang('admin.SubchargeStatus') :
                                                                <span class="danger">*</span>
                                                            </label>
                                                            <select class="form-control"
                                                                    name="sub_charge_status"
                                                                    id="sub_charge_status"
                                                                    onclick="SubchargeMethod(this.value);"
                                                                    required>
                                                                <option value="">@lang('admin.select_subcharge_status')</option>
                                                                <option value="1">@lang('admin.On')</option>
                                                                <option value="0">@lang('admin.Off')</option>
                                                            </select>
                                                            @if ($errors->has('sub_charge_status'))
                                                                <label class="danger">{{ $errors->first('sub_charge_status') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group custom-hidden" id="sub_type">
                                                            <label for="emailAddress5">
                                                                @lang('admin.TypeOfSubcharge') :
                                                                <span class="danger">*</span>
                                                            </label>
                                                            <select class="form-control" name="sub_charge_type"
                                                                    id="sub_charge_type">
                                                                <option value="">@lang('admin.calulationMethod')
                                                                </option>
                                                                <option value="1">@lang('admin.Nominal')</option>
                                                                <option value="2">@lang('admin.Multiplier')</option>
                                                            </select>
                                                            @if ($errors->has('sub_charge_type'))
                                                                <label class="danger">{{ $errors->first('sub_charge_type') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-group custom-hidden" id="sub_value">
                                                            <label for="emailAddress5">
                                                                @lang('admin.Value') :
                                                                <span class="danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control"
                                                                   id="sub_charge_value"
                                                                   name="sub_charge_value"
                                                                   placeholder="@lang('admin.message176')">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <h4 class="form-section" style="color: black"><i
                                                        class="far fa-money-bill-alt"></i> @lang('admin.payment_methods')
                                            </h4>
                                            <hr>
                                            <div class="row">
                                                @foreach($merchant->PaymentMethod as $paymentmethod)
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>
                                                                <input type="checkbox" name="payment_method[]"
                                                                       value="{{ $paymentmethod->id }}"
                                                                       id="{{ $paymentmethod->payment_method }}">
                                                                {{ $paymentmethod->payment_method }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if($configuration->driver_cash_limit == 1)
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="flat-div">
                                                            <label for="emailAddress5">
                                                                @lang('admin.driver_cash_booking_limit')<span
                                                                        class="text-danger">*</span>
                                                            </label>
                                                            <input type="number" class="form-control"
                                                                   id="driver_cash_booking_limit"
                                                                   name="driver_cash_booking_limit"
                                                                   placeholder="@lang('admin.driver_cash_booking_limit')">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="form-actions right" style="margin-bottom: 3%">
                                                <button type="submit" class="btn btn-primary float-right">
                                                    <i class="fa fa-check-circle"></i> @lang('admin.save')
                                                </button>
                                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>

        function SubchargeMethod(val) {
            //alert(val);
            $("#loader1").show();
            if (val == 1) {
                document.getElementById('sub_type').style.display = 'block';
                document.getElementById('sub_value').style.display = 'block';
            } else if (val == 0) {
                document.getElementById('sub_type').style.display = 'none';
                document.getElementById('sub_value').style.display = 'none';
            }
            $("#loader1").hide();
        }

        function validateNumber(event) {
            var key = window.event ? event.keyCode : event.which;
            if (event.keyCode === 8 || event.keyCode === 46) {
                return true;
            } else if (key < 48 || key > 57) {
                return false;
            } else {
                return true;
            }
        }

        function validateForm() {
            var checkCount = document.getElementById("checkBoxCount").value;
            var i;
            for (i = 0; i <= checkCount; i++) {
                var atLeastOneIsChecked = $('input[name="week_days[' + i + '][]"]:checked').length > 0;
                if (atLeastOneIsChecked == false) {
                    alert('Please Selcet week Days for Charges Slots');
                    return false;
                }
            }
            return true;
        }

        $(document).ready(function () {
            $('[id^=slot_charges]').keypress(validateNumber);
            var max_fields = 5;
            var count = 0;
            $(".add-more").click(function () {
                if (count < max_fields) {
                    count++;
                    document.getElementById("checkBoxCount").value = count;
                    var html = '<div class="dynamic-copy">' +
                        '<div class="row" style="text-align: center;">' +
                        '<div class="col-md-10">' +
                        '<label for="emailAddress5">' +
                        '@lang('admin.select_week_days') :' +
                        '<span class="danger">*</span>' +
                        '</label>' +
                        '<div class="form-group">' +
                        '<div class="weekDays-selector">' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="1" id="weekday-mon' + count + '" class="weekday mr-1 ml-1">' +
                        '<label for="weekday-mon' + count + '">M</label>' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="2" id="weekday-tue' + count + '" class="weekday mr-1 ml-1">' +
                        '<label for="weekday-tue' + count + '">T</label>' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="3" id="weekday-wed' + count + '" class="weekday mr-1 ml-1">' +
                        '<label for="weekday-wed' + count + '">W</label>' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="4" id="weekday-thu' + count + '"class="weekdaymr-1 ml-1">' +
                        '<label for="weekday-thu' + count + '">T</label>' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="5" id="weekday-fri' + count + '"class="weekday mr-1 ml-1">' +
                        '<label for="weekday-fri' + count + '">F</label>' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="6" id="weekday-sat' + count + '" class="weekday mr-1 ml-1">' +
                        '<label for="weekday-sat' + count + '">S</label>' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="7" id="weekday-sun' + count + '" class="weekday mr-1 ml-1">' +
                        '<label for="weekday-sun' + count + '">S</label>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-2">' +
                        '<div class="input-group-btn">' +
                        '<button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i>' +
                        '@lang('admin.time_remove')' +
                        '</button>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="row">' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label for="emailAddress5"> @lang('admin.parametername') : ' +
                        '<span class="danger">*</span>' +
                        '</label>' +
                        '<input type = "text" class = "form-control" id = "parametername" name = "parametername[' + count + ']" placeholder = "@lang('admin.parametername')" autocomplete = "off" >' +
                        '</div>' +
                        '</div>' +

                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label for="emailAddress5">' +
                        '@lang('admin.slot_start_time') :' +
                        '<span class="danger">*</span>' +
                        '</label>' +
                        '<input type="text" class="form-control timepicker1" data-plugin="clockpicker" data-autoclose="true" id="begintime" name="begintime[' + count + ']" placeholder="@lang('admin.select_slot_time')" autocomplete="off" >' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label for="emailAddress5">' +
                        '@lang('admin.slot_end_time') :' +
                        '<span class="danger">*</span>' +
                        '</label>' +
                        '<input type="text" class="form-control timepicker1" data-plugin="clockpicker" data-autoclose="true" id="endtime" name="endtime[' + count + ']" placeholder="@lang('admin.select_slot_time')" autocomplete="off">' +
                        {{--'<input type="text" class="form-control mytimepicker" id="endtime" name="endtime[' + count + ']" placeholder="@lang('admin.select_slot_time')" autocomplete="off">' +--}}
                        '<label class="radio-inline" style="margin-right: 2%;margin-left: 5%;margin-top: 6%;">' +
                        '<input type="radio" value="1"checked name="optradio[' + count + ']">@lang('admin.next_day')' +
                        '</label>' +
                        '<label class="radio-inline"><input type="radio" value="2" id="charge_type" name="optradio[' + count + ']">@lang('admin.same_day')' +
                        '</label>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label for="emailAddress5">' +
                        '@lang('admin.slot_charges') :' +
                        '<span class="danger">*</span>' +
                        '</label>' +
                        '<input type="text" class="form-control" id="slot_charges" name="slot_charges[' + count + ']" placeholder="@lang('admin.please_enter_slot_charge')" autocomplete="off">' +
                        '<label class="radio-inline" style="margin-right: 2%;margin-left: 5%;margin-top: 3%;">' +
                        '<input type="radio" value="1" checked name="charge_type[' + count + ']">@lang('admin.nominal')' +
                        '</label>' +
                        '<label class="radio-inline">' +
                        '<input type="radio" value="2" id="charge_type" name="charge_type[' + count + ']">@lang('admin.multiplier') </label>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3" style="margin-top: 34px;">' +
                        '</div>' +
                        '</div>' +
                        '<hr>' +
                        '</div>';
                    $("#after-add-more").append(html);
                    $('.timepicker1').clockpicker();
                    $('[id^=slot_charges]').keypress(validateNumber);
                }
            });
            $("body").on("click", ".remove", function () {
                count--;
                document.getElementById("checkBoxCount").value = count;
                $(this).parents(".dynamic-copy").remove();
            });
        });

        getDeliveryType("{{old('area')}}");

        function getDeliveryType(val) {
            if (val != "") {
                $("#loader1").show();
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: '<?php echo route('merchant.get-delivery-types') ?>',
                    data: {area_id: val , delivery_type : "{{old('delivery_type')}}"},
                    success: function (data) {
                        $('#delivery_type').html(data);
                    }
                });
                $("#loader1").hide();
            }
        }

        function GetVehcile(ID) {
            if (ID !== "") {
                $("#loader1").show();
                var area = $('[name="area"]').val();
                var token = $('[name="_token"]').val();
                var service = $('[name="service"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "<?php echo route('package.vehicles');?>",
                    data: {
                        area: area,
                        service: service
                    },
                    success: function (data) {
                        $("#vehicle_type").html(data);
                    }
                });
                $("#loader1").hide();
            }
        }

        $(document).on('change','#delivery_type',function(){
            var delivery_type = $('[name="delivery_type"]').val();
            $("#vehicle_type").html('<option value="">--Select Vehile Type--</option>');
            if (delivery_type !== "") {
                $('#myLoader').removeClass('d-none');
                $('#myLoader').addClass('d-flex');
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: '<?php echo route('merchant.get-vehicle-types') ?>',
                    data: {
                        delivery_type: delivery_type,
                        vehicle_type : "{{old('vehicle_type')}}"
                    },
                    success: function (data) {
                        $("#vehicle_type").html(data);
                        $('#myLoader').addClass('d-none');
                        $('#myLoader').removeClass('d-flex');
                    }
                });
            }
        });


        function outstation(val) {
            var token = $('[name="_token"]').val();
            var area = $('[name="area"]').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: 'POST',
                url: "<?php echo route('package.vehicles');?>",
                data: {
                    area: area,
                    service: 4
                },
                success: function (data) {
                    $("#vehicle_type").html(data);
                }
            });
            if (val == 2) {
                $("#fixed_div").show();
                $("#newTaxt").text("{{ trans('admin.message542') }}");
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: '<?php echo route('merchant.getRideConfig') ?>',
                    data: {
                        service: 4,
                        manual_area: area,
                    },
                    success: function (data) {
                        $("#package_id").html(data);
                        $("#extra_charge").hide();
                    }
                });
            } else {
                $("#fixed_div").hide();
            }

        }

        function Commsionmethod(val) {
            $("#loader1").show();
            if (val == 1) {
                document.getElementById('flat-div').style.display = 'block';
                document.getElementById('percantage-div').style.display = 'none';
            } else if (val == 2) {
                document.getElementById('flat-div').style.display = 'none';
                document.getElementById('percantage-div').style.display = 'block';
            } else {
                document.getElementById('flat-div').style.display = 'none';
                document.getElementById('percantage-div').style.display = 'none';
            }
            $("#loader1").hide();
        }

        function disbleField() {
            $("#loader1").show();
            if (document.getElementById('all_vehile_type').checked) {
                document.getElementById('vehicle_type').disabled = true;
            } else {
                document.getElementById('vehicle_type').disabled = false;
            }
            $("#loader1").hide();
        }

        function PricingType(val) {
            if (val != "") {
                $("#loader1").show();
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: '<?php echo route('admin.getinputfield') ?>',
                    data: {type: val},
                    success: function (data) {
                        $('#dynamic_row').html(data);
                        $("#loader1").hide();
                    }
                });
                if (val == 3) {
                    document.getElementById('end-div').style.display = 'block';
                } else if (val == 1) {
                    //document.getElementById('varible_div').style.display = 'block';
                    document.getElementById('end-div').style.display = 'none';
                } else {
                    // document.getElementById('varible_div').style.display = 'none';
                    document.getElementById('end-div').style.display = 'none';
                }
            }
        }

        function NumberInput(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31
                && (charCode < 48 || charCode > 57))
                return false;

            return true;
        }

        function invibleInput(val) {
            $("#loader1").show();
            if (document.getElementById(val).checked) {
                document.getElementById('test' + val).disabled = false;
            } else {
                document.getElementById('test' + val).disabled = true;
            }
            $("#loader1").hide();
        }

        function invibleRadioButton(val) {
            $("#loader1").show();
            if (document.getElementById(val).checked) {
                document.getElementById('driver' + val).disabled = false;
                document.getElementById('admin' + val).disabled = false;
                document.getElementById('admin' + val).checked = true;
                document.getElementById('check_box_values' + val).disabled = false;
            } else {
                document.getElementById('driver' + val).checked = false;
                document.getElementById('admin' + val).checked = false;
                document.getElementById('driver' + val).disabled = true;
                document.getElementById('admin' + val).disabled = true;
                document.getElementById('check_box_values' + val).disabled = true;
            }
            $("#loader1").hide();
        }
    </script>
@endsection




