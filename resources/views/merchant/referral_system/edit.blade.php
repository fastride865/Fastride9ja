@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content container-fluid">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->add_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <a href="{{ route('referral-system') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                                @lang("$string_file.referral_system")
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="panel-body container-fluid">
                    <form method="POST" enctype="multipart/form-data" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="{{ route('referral-system.store',["id" => $referral_system->id]) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <label>@lang("$string_file.country")
                                </label><br>
                                <b>{{$referral_system->Country->CountryName}}</b>
                            </div>
                            <div class="col-md-3">
                                <label>@lang("$string_file.area")
                                </label><br>
                                <b>{{$referral_system->CountryArea->CountryAreaName}}</b>
                            </div>
                            <div class="col-md-3">
                                <label>@lang("$string_file.referral_for")
                                </label><br>
                                <b>@if($referral_system->application == 1) @lang("$string_file.user") @else @lang("$string_file.driver") @endif</b>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            @foreach($referral_system_segments as $ref_segment)
                                <div class='col-md-2'>
                                    <div class=''>
                                        <li><label for='segment_id'>{{$ref_segment}}</label></li>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-3">
                                <label> @lang("$string_file.start_date")
                                </label><br>
                                <b>{{$referral_system->start_date}}</b>
                            </div>
                            <input type="hidden" name="start_date" value="{{date("Y-m-d")}}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="datepicker">@lang("$string_file.end_date")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon wb-calendar"
                                                                              aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" class="form-control customDatePicker1" name="end_date"
                                               id="end_date"
                                               value="{{old("end_date",isset($referral_system->end_date) ? $referral_system->end_date : "")}}"
                                               placeholder="" autocomplete="off" readonly>
                                        @if ($errors->has('end_date'))
                                            <label class="text-danger">{{ $errors->first('end_date') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label>
                                    @lang("$string_file.discount_applicable")
                                </label><br>
                                <b>
                                    @switch($referral_system->offer_applicable)
                                        @case(1) @lang("$string_file.sender")
                                            @break
                                        @case(2) @lang("$string_file.receiver")
                                            @break
                                        @case(3) @lang("$string_file.both") (@lang("$string_file.sender")/@lang("$string_file.receiver"))
                                            @break
                                    @endswitch
                                </b>
                            </div>
                            <div class="col-md-3">
                                <label>@lang("$string_file.offer_type")
                                </label><br>
                                <b>@if($referral_system->offer_type == 1) @lang("$string_file.fixed_amount") @else @lang("$string_file.discount") @endif</b>
                            </div>
                            <div class="col-md-3">
                                <label>@lang("$string_file.offer_value")
                                </label><br>
                                @if($referral_system->offer_type == 1)
                                    <b>{{$referral_system->Country->isoCode." ".$referral_system->offer_value}}</b>
                                @else
                                    <b>{{$referral_system->offer_value." %"}}</b>
                               @endif
                            </div>
                            <div class="col-md-3">
                                <label>@lang("$string_file.maximum_offer_amount")
                                </label><br>
                                <b>{{ !empty($referral_system->maximum_offer_amount) ? $referral_system->maximum_offer_amount : "--" }}</b>
                            </div>
                            <div class="col-md-3">
                                <label>@lang("$string_file.offer_condition")
                                </label><br>
                                <b>{{ getReferralSystemOfferCondition($string_file)[$referral_system->offer_condition] }}</b>
                            </div>
                        </div>
                        @php $additional_data = json_decode($referral_system->offer_condition_data,true); @endphp
                        <hr>
                        @if($referral_system->offer_condition == 1)
                            <div class="row">
                                <div class="col-md-3">
                                    <label>@lang("$string_file.no_of_uses")
                                    </label><br>
                                    <b><b>{{ $additional_data['limit_usage'] }}</b></b>
                                </div>
                                <div class="col-md-3">
                                    <label>@lang("$string_file.no_of_days")
                                    </label><br>
                                    <b><b>{{ $additional_data['day_limit'] }}</b></b>
                                </div>
                                <div class="col-md-3">
                                    <label>@lang("$string_file.days_count_start")
                                    </label><br>
                                    <b>@if($additional_data['day_count'] == 1) @lang("$string_file.after_signup") @else @lang("$string_file.after_financial_transaction") @endif</b>
                                </div>
                            </div>
                        @endif
                        @if($referral_system->offer_condition == 4)
                            <div class="row">
                                <div class="col-md-3">
                                    <label>@lang("$string_file.no_of_drivers")
                                    </label><br>
                                    <b>{{ $additional_data['conditional_no_driver'] }}</b>
                                </div>
                                <div class="col-md-3">
                                    <label>Rule @lang("$string_file.for_driver")
                                    </label><br>
                                    <b>{{ getReferralSystemDriverCondition($string_file)[$additional_data['conditional_driver_rule']] }}</b>
                                </div>
                                <div class="col-md-3">
                                    <label>@lang("$string_file.no_of_services")
                                    </label><br>
                                    <b>{{ $additional_data['conditional_no_services'] }}</b>
                                </div>
                            </div>
                        @endif
                            <div class="form-actions float-right" style="margin-bottom: 1%">
                                <button type="submit" class="btn btn-primary"><i
                                            class="fa fa-check-circle"></i> @lang("$string_file.save") </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
@endsection
@section('js')
@endsection