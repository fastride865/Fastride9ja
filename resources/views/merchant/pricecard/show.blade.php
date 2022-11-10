@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ URL::previous() }}">
                                <button type="button" class="btn btn-icon btn-success mr-1 float-right" style="margin:10px"><i class="fa fa-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class="icon fa-info-circle" aria-hidden="true"></i>
                        @lang('admin.message368')
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <div class="row">
                        <div class="col-sm-4">
                            <h5 class="user-name">@lang('admin.message717')</h5>
                            <p class="user-info">{{ $pricecard->CountryArea->CountryAreaName }}
                                ,{{ $pricecard->ServiceType->serviceName }}
                                ,{{ $pricecard->VehicleType->VehicleTypeName }}
                                @if(!empty($pricecard->Package))
                                    ,{{ $pricecard->Package->PackageName }}
                                @endif
                            </p>
                        </div>
                        <div class="col-sm-4">
                            <h5 class="user-name">@lang('admin.price_type')</h5>
                            <p class="user-info">
                                @switch($pricecard->pricing_type)
                                    @case(1)
                                    @lang('admin.Variable')
                                    @break
                                    @case(2)
                                    @lang('admin.fixed_price')
                                    @break
                                    @case(3)
                                    @lang('admin.inputDriver')
                                    @break
                                @endswitch
                            </p>
                        </div>
                        @if($pricecard->outside_area_ratecard == 1)
                            <div class="col-sm-4">
                                <h5 class="user-name">@lang('admin.rate_card_scope')</h5>
                                <p class="user-info"><?php $a = array(); ?>
                                    Inside Area
                                </p>
                            </div>
                        @endif
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-4">
                            <h5 class="user-name">@lang("$string_file.payment_method")</h5>
                            <p class="user-info"><?php $a = array(); ?>
                                @foreach($pricecard->paymentmethod as $payment)
                                    <?php $a[] = $payment->payment_method; ?>
                                @endforeach
                                {{ implode(',',$a) }}
                            </p>
                        </div>
                        <div class="col-sm-4">
                            <h5 class="user-name">@lang('admin.message352')</h5>
                            <p class="user-info">{{ $pricecard->CountryArea->Country->isoCode." ".$pricecard->base_fare }} @lang('admin.message165') {{ $pricecard->free_distance }} And Time {{  $pricecard->free_time }}</p>
                        </div>
                        @if($pricecard->outstation_max_distance)
                            <div class="col-sm-4">
                                <h5 class="user-name">@lang('admin.message537') @lang('admin.maxdistance')</h5>
                                <p class="user-info">
                                    {{$pricecard->outstation_max_distance}} Km
                                </p>
                            </div>
                        @endif
                    </div>
                    <br>
                    @if($pricecard->cancel_charges == 1)
                        <div class="row">
                            <div class="col-sm-4">
                                <h5 class="user-name">@lang('admin.message715')</h5>
                                <p class="user-info">{{ $pricecard->CountryArea->Country->isoCode." ".$pricecard->cancel_amount }} @lang('admin.message718') {{ $pricecard->cancel_time }} @lang("$string_file.min")</p>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-12 mb-30">
                        <div class="table-responsive">
                            <table class="table table-hover" id="dataTable">
                                <thead>
                                <tr>
                                    <th>@lang('admin.parameter')</th>
                                    <th>@lang("$string_file.type")</th>
                                    <th>@lang('admin.message719')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($pricecard->PriceCardValues as $value)
                                    <tr>
                                        <td>{{ $value->PricingParameter->ParameterName }}</td>
                                        <td>
                                            @switch($value->PricingParameter->parameterType)
                                                @case(1)
                                                @lang('admin.permilekm')
                                                @break
                                                @case(2)
                                                @lang('admin.perhour')
                                                @break
                                                @case(3)
                                                @lang('admin.standard')
                                                @break
                                                @case(4)
                                                @lang("$string_file.discount")
                                                @break
                                                @case(5)
                                                @lang('admin.time_interval_type')
                                                @break
                                                @case(6)
                                                @lang("$string_file.tax")
                                                @break
                                                @case(7)
                                                @lang('admin.toll')
                                                @break
                                                @case(8)
                                                @lang('admin.message4')
                                                @break
                                                @case(9)
                                                @lang('admin.message219')
                                                @break
                                                @case(10)
                                                @lang('admin.message220')
                                                @break
                                                @case(12)
                                                @lang("$string_file.promo_discount")
                                                @break
                                                @case(16)
                                                @lang('admin.minimum_fare_type')
                                                @break
                                                @case(14)
                                                @lang('admin.ac_charges')
                                                @break
                                                @case(17)
                                                @lang('admin.booking_fee_type')
                                                @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($value->PricingParameter->parameterType)
                                                @case(1)
                                                @case(2)
                                                @case(3)
                                                @case(4)
                                                @case(14)
                                                @case(16)
                                                @case(17)
                                                {{ $pricecard->CountryArea->Country->isoCode." ".$value->parameter_price }}
                                                @break
                                                @case(5)
                                                ----
                                                @break
                                                @case(6)
                                                {{ $pricecard->CountryArea->Country->isoCode." ".$value->parameter_price }} @lang('admin.message721') {{ $value->free_value }}
                                                @break
                                                @case(8)
                                                {{ $pricecard->CountryArea->Country->isoCode." ".$value->parameter_price }}
                                                @break
                                                @case(9)
                                                {{ $pricecard->CountryArea->Country->isoCode." ".$value->parameter_price }} @lang('admin.message718') {{ $value->free_value }} @lang("$string_file.min")
                                                @break
                                                @case(11)
                                                {{ $pricecard->CountryArea->Country->isoCode." ".$value->parameter_price }}
                                                @break
                                                @case(12)
                                                -----
                                                @break
                                                @case(13)
                                                {{ $value->parameter_price."%" }}
                                                @break
                                            @endswitch
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                    @if($config->time_charges == 1)
                        @if(!empty($pricecard->ExtraCharges->toArray()))
                            <div class="col-md-12 col-xs-12" style="float:left;">
                                <h5 class="form-section"><i class="fa fa-moon-o"></i> @lang('admin.time_charges')</h5>
                                @php $i=0; @endphp
                                @foreach($pricecard->ExtraCharges as $value)
                                    <div id="after-add-more">
                                        <div class="row" style="text-align: center;">
                                            <div class="col-md-10">
                                                <label for="week_days">@lang('admin.select_week_days') :
                                                    <span class="danger">*</span>
                                                </label>
                                                <div class="form-group">
                                                    @php $slot_week_days=explode(",",$value->slot_week_days); @endphp
                                                    <div class="weekDays-selector">
                                                        <input type="checkbox" value="1"
                                                               name="week_days[{{$i}}][]"
                                                               @if(in_array(1, $slot_week_days))checked @endif
                                                               id="weekday-mon"
                                                               class="weekday"/>
                                                        <label for="weekday-mon">M</label>
                                                        <input type="checkbox" value="2"
                                                               name="week_days[{{$i}}][]"
                                                               id="weekday-tue"
                                                               @if(in_array(2, $slot_week_days))checked @endif
                                                               class="weekday"/>
                                                        <label for="weekday-tue">T</label>
                                                        <input type="checkbox" value="3"
                                                               name="week_days[{{$i}}][]"
                                                               id="weekday-wed"
                                                               @if(in_array(3, $slot_week_days))checked @endif
                                                               class="weekday"/>
                                                        <label for="weekday-wed">W</label>
                                                        <input type="checkbox" value="4"
                                                               name="week_days[{{$i}}][]"
                                                               id="weekday-thu"
                                                               @if(in_array(4, $slot_week_days))checked @endif
                                                               class="weekday"/>
                                                        <label for="weekday-thu">T</label>
                                                        <input type="checkbox" value="5"
                                                               name="week_days[{{$i}}][]"
                                                               id="weekday-fri"
                                                               @if(in_array(5, $slot_week_days))checked
                                                               @endif
                                                               class="weekday"/>
                                                        <label for="weekday-fri">F</label>
                                                        <input type="checkbox" value="6"
                                                               name="week_days[{{$i}}][]"
                                                               id="weekday-sat"
                                                               @if(in_array(6, $slot_week_days))checked @endif
                                                               class="weekday"/>
                                                        <label for="weekday-sat">S</label>
                                                        <input type="checkbox" value="7"
                                                               name="week_days[{{$i}}][]"
                                                               id="weekday-sun"
                                                               @if(in_array(7, $slot_week_days))checked @endif
                                                               class="weekday"/>
                                                        <label for="weekday-sun">S</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="parameterName">@lang('admin.parametername') :
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
                                                    <label for="begintime">@lang('admin.slot_start_time') :
                                                        <span class="danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                           class="form-control mytimepicker"
                                                           id="begintime" name="begintime[{{$i}}]"
                                                           placeholder="@lang('admin.select_slot_time')"
                                                           required
                                                           value="{{$value->slot_start_time}}"
                                                           autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="endtime">@lang('admin.slot_end_time') :
                                                        <span class="danger">*</span>
                                                    </label>
                                                    <input type="text"
                                                           class="form-control mytimepicker"
                                                           id="endtime" name="endtime[{{$i}}]"
                                                           placeholder="@lang('admin.select_slot_time')"
                                                           value="{{$value->slot_end_time}}"
                                                           required
                                                           autocomplete="off">
                                                    <label class="radio-inline"
                                                           style="margin-right: 2%;margin-left: 5%;margin-top: 3%;">
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
                                                           value="{{$value->slot_charges}}" required
                                                           autocomplete="off">
                                                    <label class="radio-inline"
                                                           style="margin-right: 2%;margin-left: 5%;margin-top: 3%;">
                                                        <input type="radio" value="1"
                                                               @if($value->slot_charge_type == 1) checked @endif
                                                               name="charge_type[{{$i}}]">@lang('admin.nominal')
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" value="2"
                                                               id="charge_type"
                                                               @if($value->slot_charge_type == 2) checked @endif
                                                               name="charge_type[{{$i}}]">@lang('admin.multiplier')
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <hr></hr>
                                    </div>
                                    @php $i++; @endphp
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
