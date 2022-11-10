@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content container-fluid">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        @if(Auth::user('merchant')->hasAnyPermission(['price_card_TAXI','price_card_DELIVERY']))
                            <a href="{{route('pricecard.add')}}">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus" title="@lang("$string_file.add_price_card")"></i>
                                </button>
                            </a>
                            <a href="{{route('excel.pricecard')}}">
                                <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                    <i class="wb-download" title="@lang("$string_file.export_excel")"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="fa-money" aria-hidden="true"></i>
                        @lang("$string_file.price_card_management") </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('merchant.pricecard.search') }}" method="GET">
                        @csrf
                        <div class="table_search row p-3">
                            <div class="col-md-2 active-margin-top">@lang("$string_file.search") :</div>
                            <div class="col-md-4 col-xs-12 form-group active-margin-top">
                                {!! Form::select('area',add_blank_option($areas,trans("$string_file.area")),old('area',$area_id),["class"=>"form-control serviceAreaList","id"=>"area","required"=>true]) !!}
                            </div>
                            <div class="col-md-1 col-xs-12 form-group active-margin-top">
                                <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search"
                                                                                                aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="col-md-1 col-xs-12 form-group active-margin-top">
                                <a href="{{ route('pricecard.index') }}">
                                    <button type="button" class="btn btn-primary"><i class="wb-reply"></i></button>
                                </a>
                            </div>
                        </div>
                    </form>
                    <table class="display nowrap table table-hover table-stripedw-full" id="customDataTable"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.services")</th>
                            <th>@lang("$string_file.vehicle_type")</th>
                            <th>@lang("$string_file.package")</th>
                            <th>@lang("$string_file.price_type")</th>
                            <th>@lang("$string_file.base_fare")</th>
                            {{--                            <th>@lang("$string_file.payment_method")</th>--}}
                            @if($config->user_wallet_status == 1)
                                <th>@lang("$string_file.min_wallet_amount")</th>
                            @endif
                            <th>@lang("$string_file.commission_from_driver")</th>
                            @if($config->company_admin == 1)
                                <th>@lang("$string_file.commission_from_taxi_company")</th>
                            @endif
                            @if($config->hotel_admin == 1)
                                <th>@lang("$string_file.commission_for_hotel")</th>
                            @endif
                            @if($config->sub_charge == 1)
                                <th>@lang("$string_file.surcharge_status")</th>
                                <th>@lang("$string_file.type_of_surcharge")</th>
                                <th>@lang("$string_file.surcharge_value")</th>
                            @endif
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $pricecards->firstItem();
                        $commission_type = get_commission_type($string_file);
                        $commission_method = get_commission_method($string_file);
                        $on_off = get_on_off($string_file);
                        $charge_type = $sub_charge_type = ["1" => trans($string_file.'.nominal'),"2"=>trans($string_file.".multiplier")];
                        @endphp
                        @foreach($pricecards as $pricecard)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    {{ $pricecard->price_card_name }}
                                </td>
                                <td>
                                    {{ $pricecard->CountryArea->CountryAreaName }} <br>
                                    <i>({{ ($pricecard->CountryArea->is_geofence == 1) ? trans("$string_file.geofence") : trans("$string_file.service") }} @lang("$string_file.area")
                                        )</i>
                                </td>

                                <td>{{ $pricecard->ServiceType->serviceName }}</td>
                                <td>
                                    {{ $pricecard->VehicleType->VehicleTypeName }}
                                </td>
                                <td>
                                    @if(empty($pricecard->service_package_id))
                                        ------
                                    @else
                                        @if($pricecard->ServiceType->additional_support == 2)
                                            {{ isset($pricecard->OutstationPackage) ? $pricecard->OutstationPackage->PackageName : "---" }}
                                        @elseif($pricecard->ServiceType->additional_support == 1)
                                            {{ isset($pricecard->ServicePackage) ? $pricecard->ServicePackage->PackageName : "---" }}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @switch($pricecard->pricing_type)
                                        @case(1)
                                        @lang("$string_file.variable")
                                        @break
                                        @case(2)
                                        @lang("$string_file.fixed_price")
                                        @break
                                        @case(3)
                                        @lang("$string_file.input_by_driver")
                                        @break
                                    @endswitch
                                </td>
                                <td>
                                    @if($pricecard->base_fare == "")
                                        ------
                                    @else
                                        {{ $pricecard->CountryArea->Country->isoCode." ".$pricecard->base_fare }}
                                    @endif
                                </td>
                                {{--                                <td>{{ implode(',',array_pluck($pricecard->paymentmethod,'payment_method')) }}</td>--}}
                                @if($config->user_wallet_status == 1)
                                    <td>{{ $pricecard->CountryArea->Country->isoCode." ".$pricecard->minimum_wallet_amount }}</td>
                                @endif
                                <td>
                                    {{--                                    @if($pricecard->PriceCardCommission->commission_type)--}}
                                    {{--                                        @lang('admin.commission_type') : {!! $commission_type[$pricecard->PriceCardCommission->commission_type] !!}--}}
                                    {{--                                    @else--}}
                                    {{--                                        ---------}}
                                    {{--                                    @endif--}}
                                    {{--                                    <br>--}}

                                    @if($pricecard->PriceCardCommission->commission_method)
                                        @lang("$string_file.commission_method")
                                        : {!! $commission_method[$pricecard->PriceCardCommission->commission_method] !!}
                                    @else
                                        -------
                                    @endif
                                    <br>

                                    @if($pricecard->PriceCardCommission->commission)
                                        @lang("$string_file.commission_value") :
                                        @if($pricecard->PriceCardCommission->commission_method == 1)
                                            {{ $pricecard->CountryArea->Country->isoCode }}
                                        @endif
                                        {!! $pricecard->PriceCardCommission->commission !!}
                                        @if($pricecard->PriceCardCommission->commission_method == 2)
                                            %
                                        @endif
                                    @else
                                        -------
                                    @endif
                                </td>
                                @if($config->company_admin == 1)
                                    <td>
                                        @if($pricecard->PriceCardCommission->taxi_commission_method)
                                            @lang("$string_file.commission_method")
                                            : {!! $commission_method[$pricecard->PriceCardCommission->taxi_commission_method] !!}
                                        @else
                                            -------
                                        @endif
                                        <br>
                                        @if($pricecard->PriceCardCommission->taxi_commission)
                                            @lang("$string_file.commission_value") :
                                            @if($pricecard->PriceCardCommission->taxi_commission_method == 1)
                                                {{ $pricecard->CountryArea->Country->isoCode }}
                                            @endif
                                            {!! $pricecard->PriceCardCommission->taxi_commission !!}
                                            @if($pricecard->PriceCardCommission->taxi_commission_method == 2)
                                                %
                                            @endif
                                        @else
                                            -------
                                        @endif
                                    </td>
                                @endif
                                @if($config->hotel_admin == 1)
                                    <td>
                                        {{--                                        @if($pricecard->PriceCardCommission->hotel_commission_method)--}}
                                        {{--                                            @lang('admin.commission_method') : {!! $commission_method[$pricecard->PriceCardCommission->hotel_commission_method] !!}--}}
                                        {{--                                        @else--}}
                                        {{--                                            ---------}}
                                        {{--                                        @endif--}}
                                        {{--                                        <br>--}}
                                        @if($pricecard->PriceCardCommission->hotel_commission)
                                            @lang("$string_file.commission_value") :
                                            @if($pricecard->PriceCardCommission->hotel_commission_method == 1)
                                                {{ $pricecard->CountryArea->Country->isoCode }}
                                            @endif
                                            {!! $pricecard->PriceCardCommission->hotel_commission !!}
                                            @if($pricecard->PriceCardCommission->hotel_commission_method == 2)
                                                %
                                            @endif
                                        @else
                                            -------
                                        @endif
                                    </td>
                                @endif
                                @if($config->sub_charge == 1)
                                    <td>
                                        {!! isset($on_off[$pricecard->sub_charge_status]) ? $on_off[$pricecard->sub_charge_status] : '------' !!}
                                    </td>
                                    <td>
                                        {!! isset($charge_type[$pricecard->sub_charge_type]) ? $charge_type[$pricecard->sub_charge_type]: '------' !!}
                                    </td>
                                    <td>
                                        {{ !empty($pricecard->sub_charge_value) ? $pricecard->CountryArea->Country->isoCode." ".$pricecard->sub_charge_value : '------' }}
                                    </td>
                                @endif
                                <td>
                                    @if($pricecard->status == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                    @if($pricecard->status == 1)
                                        <a href="{{ route('merchant.pricecard.active-deactive',['id'=>$pricecard->id,'status'=>2]) }}"
                                           data-original-title="@lang("$string_file.inactive")"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                            <i class="fa fa-eye-slash"></i> </a>
                                    @else
                                        <a href="{{ route('merchant.pricecard.active-deactive',['id'=>$pricecard->id,'status'=>1]) }}"
                                           data-original-title="@lang("$string_file.active")"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                            <i class="fa fa-eye"></i> </a>
                                    @endif
                                    @if(Auth::user('merchant')->hasAnyPermission(['price_card_TAXI','price_card_DELIVERY']))
                                        <a href="{{ route('pricecard.add',$pricecard->id) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i> </a>
                                    @endif
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $pricecards, 'data' => $data])
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection