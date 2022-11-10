@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content container-fluid">
            @if(session('priceadded'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon fa-info" aria-hidden="true"></i>{{ session('priceadded') }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(Auth::user('merchant')->can('create_price_card'))
                            <a href="{{route('pricecard.delivery.create')}}">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus" title="@lang('admin.message365') @lang('admin.pricecard')"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="fa-money" aria-hidden="true"></i>
                        @lang('admin.pricecard')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('pricecard.delivery.search') }}" method="GET">
                        @csrf
                        <div class="table_search row p-3">
                            <div class="col-md-2 active-margin-top">@lang('admin.message727') :</div>
                            <div class="col-md-4 col-xs-12 form-group active-margin-top">
                                <select class="form-control" name="area_id" id="area_id" required>
                                    <optgroup label="Service Area">
                                        <option value="">@lang('admin.service_area')</option>
                                        @foreach($areas as $area)
                                            <option value="{{ $area->id }}"> {{ $area->CountryAreaName }}</option>
                                        @endforeach
                                    </optgroup>
                                    @if(isset($config->geofence_module) && $config->geofence_module == 1)
                                        <optgroup label="Geofence Area">
                                            @foreach($geofenceAreas as $geofenceArea)
                                                <option value="{{ $geofenceArea->id }}"> {{ $geofenceArea->CountryAreaName }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
{{--                                    @foreach($areas as $area)--}}
{{--                                        <option value="{{ $area->id }}" @if(request()->get('area_id') == $area->id) selected @endif> {{ $area->CountryAreaName }}</option>--}}
{{--                                    @endforeach--}}
                                </select>
                            </div>
                            <div class="col-md-1 col-xs-12 form-group active-margin-top">
                                <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </form>
                    <table class="display nowrap table table-hover table-stripedw-full" id="customDataTable" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang('admin.message188')</th>
                            <th>@lang('admin.message93')</th>
                            <th>@lang('admin.delivery.type')</th>
                            <th>@lang('admin.message334')</th>
                            <th>@lang('admin.message367')</th>
                            <th>@lang('admin.price_type')</th>
                            <th>@lang('admin.message352')</th>
                            <th>@lang('admin.message532')</th>
                            <th>@lang('admin.message360')</th>
                            <th>@lang('admin.message620')</th>
                            <th>@lang('admin.message621')</th>
                            @if($config->sub_charge == 1)
                                <th>@lang('admin.SubchargeStatus')</th>
                                <th>@lang('admin.TypeOfSubcharge')</th>
                                <th>@lang('admin.SubCharge_val')</th>
                            @endif
                            <th>@lang('admin.action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $pricecards->firstItem() @endphp
                        @foreach($pricecards as $pricecard)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    {{ $pricecard->CountryArea->CountryAreaName }}
                                </td>

                                <td>{{ $pricecard->deliveryType->name }}</td>
                                <td>
                                    {{ $pricecard->VehicleType->VehicleTypeName }}
                                </td>
                                <td>
                                    @if(empty($pricecard->package_id))
                                        ------
                                    @else
                                        @if($pricecard->service_type_id == 4)
                                            {{ $pricecard->OutstationPackage->PackageName }}
                                        @else
                                            {{ $pricecard->Package->PackageName }}
                                        @endif
                                    @endif

                                </td>
                                <td>
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
                                </td>
                                <td>
                                    @if($pricecard->base_fare == "")
                                        ------
                                    @else
                                        {{ $pricecard->CountryArea->Country->isoCode." ".$pricecard->base_fare }}
                                    @endif

                                </td>
                                <?php $a = array(); ?>
                                @foreach($pricecard->paymentmethod as $payment)
                                    <?php $a[] = $payment->payment_method; ?>
                                @endforeach
                                <td>
                                    @foreach($a as $modes)
                                        {{ $modes }}<br>
                                    @endforeach
                                </td>
                                {{--                                <td>{{ implode(',',$a) }}</td>--}}
                                <td>
                                    @if($pricecard->PriceCardCommission)
                                        @if($pricecard->PriceCardCommission->commission_type == 1)
                                            @lang('admin.prepaid')
                                        @else
                                            @lang('admin.postpaid')
                                        @endif
                                    @else
                                        -------
                                    @endif

                                </td>
                                <td>
                                    @if($pricecard->PriceCardCommission)
                                        @switch($pricecard->PriceCardCommission->commission_method)
                                            @case(1)
                                            @lang('admin.flat_comission')
                                            @break
                                            @case(2)
                                            @lang('admin.percentage_bill')
                                            @break
                                        @endswitch
                                    @else
                                        -------
                                    @endif
                                </td>
                                <td>
                                    @if($pricecard->PriceCardCommission)
                                        @switch($pricecard->PriceCardCommission->commission_method)
                                            @case(1)
                                            {{ $pricecard->CountryArea->Country->isoCode." ".$pricecard->PriceCardCommission->commission }}
                                            @break
                                            @case(2)
                                            {{ $pricecard->PriceCardCommission->commission }} %
                                            @break
                                        @endswitch
                                    @else
                                        -------
                                    @endif
                                </td>
                                @if($config->sub_charge == 1)
                                    <td>
                                        @if($pricecard->sub_charge_status == 1)
                                            @lang('admin.On')
                                        @else
                                            @lang('admin.Off')
                                        @endif
                                    </td>

                                    <td>
                                        @if($pricecard->sub_charge_type == 1)
                                            @lang('admin.Nominal')
                                        @else
                                            @lang('admin.Multiplier')
                                        @endif
                                    </td>

                                    <td>
                                        {{ $pricecard->CountryArea->Country->isoCode." ".$pricecard->sub_charge_value }}
                                    </td>
                                @endif
                                <td>
                                    @if(Auth::user('merchant')->can('edit_price_card'))
                                        <a href="{{ route('pricecard.delivery.edit',$pricecard->id) }}"
                                           data-original-title="Edit" data-toggle="tooltip" data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn">
                                            <i class="fa fa-edit"></i> </a>
                                    @endif

                                    <a href="{{ route('pricecard.delivery.show',$pricecard->id) }}"
                                       class="btn btn-sm btn-primary menu-icon btn_delete action_btn">
                                        <span class="fa fa-list-alt"></span></a>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $pricecards, 'data' => []])
                </div>
            </div>
        </div>
    </div>
@endsection
