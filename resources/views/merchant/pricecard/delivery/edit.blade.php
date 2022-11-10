@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('rideradded'))
                <div class="alert dark alert-icon alert-warning alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon fa-warning" aria-hidden="true"></i>@lang('admin.message224')
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('pricecard.delivery') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                        <a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit"></i>
                        @lang('admin.message517')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{route('pricecard.delivery.update', $pricecard->id)}}">
                        {{method_field('PUT')}}
                        @csrf
                        <div class="row">
                            @if($configuration->outside_area_ratecard == 1)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang('admin.rate_card_scope')<span
                                                    class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control"
                                               name="rate_card_scope"
                                               disabled id="rate_card_scope"
                                               value="@if($pricecard->base_fare == 1) Inside Area @else OutsideArea @endif">
                                        @if ($errors->has('rate_card_scope'))
                                            <label class="danger">{{ $errors->first('rate_card_scope')
                                                            }}</label>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang('admin.price_type')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="price_type"
                                            id="price_type"
                                            onclick="PricingType(this.value)" required>
                                        @foreach($merchant->RateCard as $value)
                                            <option value="{{ $value->id }}"
                                                    @if($value->id == 1) selected @endif>{{
                                                                $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('price_type'))
                                        <label class="danger">{{ $errors->first('price_type')
                                                            }}</label>
                                    @endif
                                </div>
                            </div>
                            @if($pricecard->service_type_id == 5)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang('admin.message572')<span
                                                    class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control"
                                               id="extra_sheet_charge"
                                               name="extra_sheet_charge" value="{{ $pricecard->extra_sheet_charge }}"
                                               placeholder="@lang('admin.message572')">
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div id="dynamic_row">
                            @if($pricecard->pricing_type == 3)
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="emailAddress5">{{
                                                                $value->PricingParameter->ParameterName }}</label>
                                            <input type="checkbox" id="input_provider"
                                                   name="input_provider[]"
                                                   value="{{ $value->pricing_parameter_id }}">
                                        </div>
                                    </div>
                                </div>
                            @else
                                @if(!empty($pricecard->base_fare))
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="ProfileImage">{{ $baseFare->ParameterName
                                                            }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox"
                                                                   class="custom-control-input"
                                                                   onclick="invibleInput(this.value)"
                                                                   id="{{ $baseFare->id }}"
                                                                   name="basefareArray[{{ $baseFare->id }}]"
                                                                   value="{{ $baseFare->id }}"
                                                                   checked>
                                                            <label class="custom-control-label"
                                                                   for="{{ $baseFare->id }}"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control"
                                                       id="test{{ $baseFare->id }}"
                                                       onkeypress="return NumberInput(event)"
                                                       name="base_fare"
                                                       value="{{ $pricecard->base_fare }}"
                                                       placeholder="{{ trans('admin.message164') }}"
                                                       aria-describedby="checkbox-addon1">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="ProfileImage"> {{ trans('admin.message165')."
                                                            ".$baseFare->ParameterName }}</label>
                                            <div class="form-group">
                                                <div class="input-group-prepend">
                                                    <input type="text" class="form-control"
                                                           id="freedistance"
                                                           name="free_distance"
                                                           value="{{ $pricecard->free_distance }}"
                                                           placeholder="{{ trans('admin.message166') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="ProfileImage"> {{ trans('admin.message167')."
                                                            ".$baseFare->ParameterName }}</label>
                                            <div class="form-group">
                                                <div class="input-group-prepend">
                                                    <input type="text" class="form-control"
                                                           id="freetime" name="free_time"
                                                           value="{{ $pricecard->free_time }}"
                                                           placeholder="{{ trans('admin.message168') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @foreach ($pricecard->PriceCardValues as $value)
                                    @if($value->PricingParameter->parameterType == 9 ||
                                    $value->PricingParameter->parameterType == 6 ||
                                    $value->PricingParameter->parameterType == 13 )
                                        <div class="row" style="margin-bottom: 10px">
                                            <div class="col-md-4">
                                                <label for="ProfileImage">{{
                                                            $value->PricingParameter->ParameterName }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                       class="custom-control-input"
                                                                       onclick="invibleInput(this.value)"
                                                                       id="{{ $value->pricing_parameter_id }}"
                                                                       name="checkboxArray[{{ $value->pricing_parameter_id }}]"
                                                                       value="{{ $value->pricing_parameter_id }}"
                                                                       checked>
                                                                <label class="custom-control-label"
                                                                       for="' . $id . '"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control"
                                                           id="test{{ $value->pricing_parameter_id }}"
                                                           onkeypress="return NumberInput(event)"
                                                           name="check_box_values[{{ $value->pricing_parameter_id }}]"
                                                           placeholder="{{ trans('admin.message164') }}"
                                                           value="{{ $value->parameter_price }}"
                                                           checked
                                                           aria-describedby="checkbox-addon1">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="ProfileImage">
                                                    @if(in_array($value->PricingParameter->parameterType, [9,15])) {{trans('admin.message167')." ".$value->PricingParameter->ParameterName}}
                                                    @elseif($value->PricingParameter->parameterType == 13)
                                                        {{ $value->PricingParameter->ParameterName }}
                                                        @lang('admin.please_enter_number') @else
                                                        @lang('admin.message165') {{
                                                                            $value->PricingParameter->ParameterName }}
                                                    @endif</label>
                                                <div class="form-group">
                                                    <div class="input-group-prepend">
                                                        <input type="text"
                                                               class="form-control"
                                                               id="checkboxFreeArray"
                                                               name="checkboxFreeArray[{{ $value->pricing_parameter_id }}]"
                                                               placeholder="@if($value->PricingParameter->parameterType == 9) {{ trans('admin.message168')}} @else @lang('admin.message166') @endif"
                                                               value="{{ $value->free_value}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="row" style="margin-bottom: 10px">
                                            <div class="col-md-4">
                                                <label for="ProfileImage">{{
                                                            $value->PricingParameter->ParameterName }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                       id="{{ $value->pricing_parameter_id }}"
                                                                       class="custom-control-input"
                                                                       onclick="invibleInput(this.value)"
                                                                       name="checkboxArray[{{ $value->pricing_parameter_id }}]"
                                                                       value="{{ $value->pricing_parameter_id }}"
                                                                       checked>
                                                                <label class="custom-control-label"
                                                                       for="{{ $value->pricing_parameter_id }}"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control"
                                                           id="test{{ $value->pricing_parameter_id }}"
                                                           onkeypress="return NumberInput(event)"
                                                           value="{{ $value->parameter_price }}"
                                                           name="check_box_values[{{ $value->pricing_parameter_id }}]"
                                                           placeholder="{{ trans('admin.message164')}}"
                                                           aria-describedby="checkbox-addon1">
                                                </div>
                                            </div>
                                            @if($value->PricingParameter->parameterType == 14)
                                                <div class="col-md-4">
                                                    <label for="ProfileImage">
                                                        {{trans('admin.chargestype')}}</label>
                                                    <div class="form-group">
                                                        <div class="input-group-prepend">
                                                            <select class="form-control"
                                                                    id="checkboxFreeArray"
                                                                    name="checkboxFreeArray[' . $id . ']">
                                                                <option value="1"
                                                                        @if($value->free_value==1)
                                                                        selected @endif
                                                                >{{trans('admin.message760')}}
                                                                </option>
                                                                <option value="2"
                                                                        @if($value->free_value==2)
                                                                        selected @endif >{{ trans('admin.perkm') }}
                                                                </option>
                                                            </select>

                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        @if($config->time_charges == 1)
                            <h4 class="form-section"><i
                                        class="fa fa-moon-o"></i> @lang('admin.time_charges')
                            </h4>
                            @if(empty($pricecard->ExtraCharges->toArray()))
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
                                                           id="weekday-mon0"
                                                           class="weekday"/>
                                                    <label for="weekday-mon0">M</label>
                                                    <input type="checkbox" value="2"
                                                           name="week_days[0][]"
                                                           id="weekday-tue0"
                                                           class="weekday"/>
                                                    <label for="weekday-tue0">T</label>
                                                    <input type="checkbox" value="3"
                                                           name="week_days[0][]"
                                                           id="weekday-wed0"
                                                           class="weekday"/>
                                                    <label for="weekday-wed0">W</label>
                                                    <input type="checkbox" value="4"
                                                           name="week_days[0][]"
                                                           id="weekday-thu0"
                                                           class="weekday"/>
                                                    <label for="weekday-thu0">T</label>
                                                    <input type="checkbox" value="5"
                                                           name="week_days[0][]"
                                                           id="weekday-fri0"
                                                           class="weekday"/>
                                                    <label for="weekday-fri0">F</label>
                                                    <input type="checkbox" value="6"
                                                           name="week_days[0][]"
                                                           id="weekday-sat0"
                                                           class="weekday"/>
                                                    <label for="weekday-sat0">S</label>
                                                    <input type="checkbox" value="7"
                                                           name="week_days[0][]"
                                                           id="weekday-sun0"
                                                           class="weekday"/>
                                                    <label for="weekday-sun0">S</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group-btn">
                                                <button class="btn btn-success add-more"
                                                        type="button">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    @lang('admin.add_new_slots')
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
                                                <input type="text" class="form-control timepicker"
                                                       data-plugin="clockpicker" data-autoclose="true"
                                                       id="begintime" name="begintime[0]"
                                                       placeholder="@lang('admin.select_slot_time')" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="endtime">
                                                    @lang('admin.slot_end_time') :
                                                    <span class="danger">*</span>
                                                </label>
                                                <input type="text" class="form-control timepicker"
                                                       data-plugin="clockpicker" data-autoclose="true"
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
                                                       id="slot_charges"
                                                       name="slot_charges[0]"
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
                                    <input type="hidden" name="checkBoxCount"
                                           id="checkBoxCount"
                                           value="0">
                                </div>
                                <input type="hidden"
                                       value="{{ count($pricecard->ExtraCharges)}}"
                                       id="count_value">
                            @else
                                @php $i=0; @endphp
                                @foreach($pricecard->ExtraCharges as $value)
                                    <div id="after-add-more" class="after-add-more removeMyOldDiv{{$i}}">
                                        <div class="row" style="text-align: center;">
                                            <div class="col-md-10">
                                                <label for="select_week_days">
                                                    @lang('admin.select_week_days') :
                                                    <span class="danger">*</span>
                                                </label>
                                                <div class="form-group">
                                                    @php
                                                        $slot_week_days=explode(",",$value->slot_week_days);
                                                    @endphp
                                                    <div class="weekDays-selector">
                                                        <input type="checkbox" value="1"
                                                               name="week_days[{{$i}}][]"
                                                               @if(in_array(1, $slot_week_days))checked
                                                               @endif
                                                               id="weekday-mon{{$i}}"
                                                               class="weekday"/>
                                                        <label for="weekday-mon{{$i}}">M</label>
                                                        <input type="checkbox" value="2"
                                                               name="week_days[{{$i}}][]"
                                                               id="weekday-tue{{$i}}"
                                                               @if(in_array(2, $slot_week_days))checked
                                                               @endif
                                                               class="weekday"/>
                                                        <label for="weekday-tue{{$i}}">T</label>
                                                        <input type="checkbox" value="3"
                                                               name="week_days[{{$i}}][]"
                                                               id="weekday-wed{{$i}}"
                                                               @if(in_array(3, $slot_week_days))checked
                                                               @endif
                                                               class="weekday"/>
                                                        <label for="weekday-wed{{$i}}">W</label>
                                                        <input type="checkbox" value="4"
                                                               name="week_days[{{$i}}][]"
                                                               id="weekday-thu{{$i}}"
                                                               @if(in_array(4, $slot_week_days))checked
                                                               @endif
                                                               class="weekday"/>
                                                        <label for="weekday-thu{{$i}}">T</label>
                                                        <input type="checkbox" value="5"
                                                               name="week_days[{{$i}}][]"
                                                               id="weekday-fri{{$i}}"
                                                               @if(in_array(5, $slot_week_days))checked
                                                               @endif
                                                               class="weekday"/>
                                                        <label for="weekday-fri{{$i}}">F</label>
                                                        <input type="checkbox" value="6"
                                                               name="week_days[{{$i}}][]"
                                                               id="weekday-sat{{$i}}"
                                                               @if(in_array(6, $slot_week_days))checked
                                                               @endif
                                                               class="weekday"/>
                                                        <label for="weekday-sat{{$i}}">S</label>
                                                        <input type="checkbox" value="7"
                                                               name="week_days[{{$i}}][]"
                                                               id="weekday-sun{{$i}}"
                                                               @if(in_array(7, $slot_week_days))checked
                                                               @endif
                                                               class="weekday"/>
                                                        <label for="weekday-sun{{$i}}">S</label>
                                                    </div>
                                                </div>
                                            </div>
                                            @if($i==0)
                                                <div class="col-md-2">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-success add-more"
                                                                type="button">
                                                            <i class="glyphicon glyphicon-plus"></i>
                                                            @lang('admin.add_new_slots')
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($i > 0)
                                                <div class="col-md-2">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-danger remove_loop" onclick= "removeDiv(this.value)" value="{{$i}}"
                                                                type="button"><i
                                                                    class="glyphicon glyphicon-remove"></i>
                                                            @lang('admin.time_remove')
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
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
                                                           name="parametername[{{$i}}]"
                                                           placeholder="@lang('admin.parametername')"
                                                           value="{{$value->parameterName}}"
                                                           required
                                                           autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="slot_start_time">
                                                        @lang('admin.slot_start_time') :
                                                        <span class="danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                           class="form-control timepicker"
                                                           data-plugin="clockpicker" data-autoclose="true"
                                                           id="begintime"
                                                           name="begintime[{{$i}}]"
                                                           placeholder="@lang('admin.select_slot_time')"
                                                           required
                                                           value="{{$value->slot_start_time}}"
                                                           autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="slot_end_time">
                                                        @lang('admin.slot_end_time') :
                                                        <span class="danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                           class="form-control timepicker"
                                                           data-plugin="clockpicker" data-autoclose="true"
                                                           id="endtime"
                                                           name="endtime[{{$i}}]"
                                                           placeholder="@lang('admin.select_slot_time')"
                                                           value="{{$value->slot_end_time}}"
                                                           required
                                                           autocomplete="off">
                                                    <label class="radio-inline"
                                                           style="margin-right: 2%;margin-left: 5%;margin-top: 6%;">
                                                        <input type="radio" value="1"
                                                               @if($value->slot_end_day == 1) checked
                                                               @endif
                                                               name="optradio[{{$i}}]">@lang('admin.next_day')
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" value="2"
                                                               id="charge_type"
                                                               @if($value->slot_end_day == 2) checked
                                                               @endif
                                                               name="optradio[{{$i}}]">@lang('admin.same_day')
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
                                                           id="slot_charges"
                                                           name="slot_charges[{{$i}}]"
                                                           placeholder="@lang('admin.please_enter_slot_charge')"
                                                           value="{{$value->slot_charges}}"
                                                           required
                                                           autocomplete="off">
                                                    <label class="radio-inline"
                                                           style="margin-right: 2%;margin-left: 5%;margin-top: 3%;">
                                                        <input type="radio" value="1"
                                                               @if($value->slot_charge_type == 1) checked
                                                               @endif
                                                               name="charge_type[{{$i}}]">@lang('admin.nominal')
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" value="2"
                                                               id="charge_type"
                                                               @if($value->slot_charge_type == 2) checked
                                                               @endif
                                                               name="charge_type[{{$i}}]">@lang('admin.multiplier')
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @php $i++; @endphp
                                @endforeach
                                <input type="hidden"
                                       value="{{ count($pricecard->ExtraCharges)}}"
                                       id="count_value">
                            @endif
                        @endif
                        <br>
                        @if($merchant->cancel_charges == 1)
                            <h4 class="form-section" style="color: black"><i
                                        class="fa fa-paperclip"></i> @lang('admin.message712')
                            </h4>
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
                                            <option @if($pricecard->cancel_charges == 2) selected
                                                    @endif value="2">@lang('admin.message323')
                                            </option>
                                            <option @if($pricecard->cancel_charges == 1) selected
                                                    @endif value="1">@lang('admin.message322')
                                            </option>
                                        </select>
                                        @if ($errors->has('cancel_charges'))
                                            <label class="danger">{{ $errors->first('cancel_charges')
                                                            }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 @if($pricecard->cancel_charges != 1) custom-hidden @endif"
                                     id="cancel_first">
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
                                               value="{{ $pricecard->cancel_time }}"
                                               placeholder="@lang('admin.message714')">
                                    </div>
                                </div>
                                <div class="col-md-4  @if($pricecard->cancel_charges != 1) custom-hidden @endif"
                                     id="cancel_second">
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
                                               value="{{ $pricecard->cancel_amount  }}"
                                               placeholder="@lang('admin.message164')">
                                    </div>
                                </div>
                            </div>
                        @endif
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
                                                onclick="SubchargeMethod(this.value);">
                                            <option value="">@lang('admin.select_subcharge_status')
                                            </option>
                                            <option @if($pricecard->sub_charge_status == 1) selected
                                                    @endif value="1">@lang('admin.On')
                                            </option>
                                            <option @if($pricecard->sub_charge_status == 0) selected
                                                    @endif value="0">@lang('admin.Off')
                                            </option>
                                        </select>
                                        @if ($errors->has('sub_charge_status'))
                                            <label class="danger">{{ $errors->first('sub_charge_status')
                                                            }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($pricecard->sub_charge_status == 1)
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group" id="sub_type">
                                            <label for="emailAddress5">
                                                @lang('admin.TypeOfSubcharge') :
                                                <span class="danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="sub_charge_type"
                                                    id="sub_charge_type">
                                                <option value="">@lang('admin.calulationMethod')
                                                </option>
                                                <option @if($pricecard->sub_charge_type == 1) selected
                                                        @endif value="1">@lang('admin.Nominal')
                                                </option>
                                                <option @if($pricecard->sub_charge_type == 2) selected
                                                        @endif value="2">@lang('admin.Multiplier')
                                                </option>
                                            </select>
                                            @if ($errors->has('sub_charge_type'))
                                                <label class="danger">{{ $errors->first('sub_charge_type')
                                                            }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" id="sub_value">
                                            <label for="emailAddress5">
                                                @lang('admin.Value') :
                                                <span class="danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="sub_charge_value"
                                                   name="sub_charge_value"
                                                   placeholder="@lang('admin.message176')"
                                                   value="{{ $pricecard->sub_charge_value }}">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                        <h4 class="form-section" style="color: black"><i
                                    class="fa fa-paperclip"></i>
                            @lang('admin.commission_structure')
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
                                        @if($config->driver_wallet_status == 1)
                                            <option value="1"
                                                    @if(!empty($pricecard->PriceCardCommission) &&
                                                $pricecard->PriceCardCommission->commission_type ==
                                                1) selected @endif>@lang('admin.prepaid')
                                            </option>
                                        @endif
                                        <option value="2"
                                                @if(!empty($pricecard->PriceCardCommission) &&
                                            $pricecard->PriceCardCommission->commission_type ==
                                            2) selected @endif>@lang('admin.postpaid')
                                        </option>
                                    </select>
                                    @if ($errors->has('commission_type'))
                                        <label class="danger">{{ $errors->first('commission_type')
                                                            }}</label>
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
                                            id="commission_method" required>
                                        <option value="1"
                                                @if(!empty($pricecard->PriceCardCommission) &&
                                            $pricecard->PriceCardCommission->commission_method
                                            == 1) selected @endif>@lang('admin.flat_comission')
                                        </option>
                                        <option value="2"
                                                @if(!empty($pricecard->PriceCardCommission) &&
                                            $pricecard->PriceCardCommission->commission_method
                                            == 2) selected @endif>@lang('admin.percentage_bill')
                                        </option>
                                    </select>
                                    @if ($errors->has('commission_method'))
                                        <label class="danger">{{ $errors->first('commission_method')
                                                            }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang('admin.message518')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           step=0.01 min=0
                                           id="commission"
                                           name="commission"
                                           value="@if(!empty($pricecard->PriceCardCommission)) {{ $pricecard->PriceCardCommission->commission }} @endif"
                                           placeholder="@lang('admin.message518')">
                                </div>
                            </div>
                        </div>

                        <br>
                        <h4 class="form-section" style="color: black"><i
                                    class="far fa-money-bill-alt"></i>
                            @lang('admin.payment_methods')
                        </h4>
                        <hr>
                        <div class="row">
                            @foreach($merchant->PaymentMethod as $paymentmethod)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="payment_method[]"
                                                   value="{{ $paymentmethod->id }}"
                                                   id="{{ $paymentmethod->payment_method }}"
                                                   @if(in_array($paymentmethod->id,
                                            array_pluck($pricecard->PaymentMethod, 'id'))) checked
                                                    @endif>
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
                                               value="{{ $pricecard->driver_cash_booking_limit }}"
                                               placeholder="@lang('admin.driver_cash_booking_limit')">
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="form-actions right" style="margin-bottom: 3%">
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="fa fa-check-circle"></i> @lang('admin.update')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" value="{{ count($pricecard->ExtraCharges) }}" id="checkBoxCount">
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

        function validateForm() {
            var checkCount = document.getElementById("checkBoxCount").value;
            //alert(checkCount);
            var i;
            for (i = 0; i <= checkCount; i++) {
                var atLeastOneIsChecked = $('input[name="week_days[' + i + '][]"]:checked').length > 0;
                if (atLeastOneIsChecked == false) {
                    // alert('Please Selcet week Days for Charges Slots');
                    //return false;
                }
            }
            return true;
        }

        $(document).ready(function () {
            $('[id^=slot_charges]').keypress(validateNumber);
            var max_fields = 5;
            var count = document.getElementById("checkBoxCount").value;
            $(".add-more").click(function () {
                if (count < max_fields) {

                    var html = '<div class="dynamic-copy">' +
                        '<div class="row" style="text-align: center;">' +
                        '<div class="col-md-9">' +
                        '<label for="weekdays">' +
                        '@lang('admin.select_week_days') :' +
                        '<span class="text-danger">*</span>' +
                        '</label>' +
                        '<div class="form-group">' +
                        '<div class="weekDays-selector">' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="1" id="weekday-mon' + count + '" class="weekday mr-1 ml-1">' +
                        '<label for="weekday-mon' + count + '">M</label>' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="2" id="weekday-tue' + count + '" class="weekday mr-1 ml-1">' +
                        '<label for="weekday-tue' + count + '">T</label>' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="3" id="weekday-wed' + count + '" class="weekday mr-1 ml-1">' +
                        '<label for="weekday-wed' + count + '">W</label>' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="4" id="weekday-thu' + count + '"class="weekday mr-1 ml-1">' +
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
                        '<input type="text" class="form-control timepicker1" data-plugin="clockpicker" data-autoclose="true" id="begintime" name="begintime[' + count + ']" placeholder="@lang('admin.select_slot_time')" autocomplete="off">' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label for="emailAddress5">' +
                        '@lang('admin.slot_end_time') :' +
                        '<span class="danger">*</span>' +
                        '</label>' +
                        '<input type="text" class="form-control timepicker1" data-plugin="clockpicker" data-autoclose="true" id="endtime" name="endtime[' + count + ']" placeholder="@lang('admin.select_slot_time')" autocomplete="off">' +
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
                    $('.timepicker1').clockpicker();;
                    $('[id^=slot_charges]').keypress(validateNumber);
                    count++;
                    document.getElementById("checkBoxCount").value = count;

                }
            });
            $("body").on("click", ".remove", function () {
                count--;
                document.getElementById("checkBoxCount").value = count;
                console.log(count);
                $(this).parents(".dynamic-copy").remove();
            });

        });





        function removeDiv(that) {

            $('.removeMyOldDiv'+that).remove();
        };
        function validateNumber(event) {
            var key = window.event ? event.keyCode : event.which;
            if (event.keyCode === 8 || event.keyCode === 46) {
                return true;
            } else if (key < 48 || key > 57) {
                return false;
            } else {
                return true;
            }
        };

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

        function invibleInput(val) {
            $("#loader1").show();
            if (document.getElementById(val).checked) {
                document.getElementById('test' + val).disabled = false;
            } else {
                document.getElementById('test' + val).disabled = true;
            }
            $("#loader1").hide();
        }

    </script>
@endsection
