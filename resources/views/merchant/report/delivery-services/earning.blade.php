@extends('merchant.layouts.main')
@section('content')
    @php $merchant_helper = new \App\Http\Controllers\Helper\Merchant(); @endphp
    <style>
        #ecommerceRecentOrder .table-row .card-block .table td {
            vertical-align: middle !important;
            height: 15px !important;
            font-size: 14px !important;
            padding: 8px 8px !important;
        }

        .dataTables_filter, .dataTables_info {
            display: none;
        }
    </style>
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <a href="{{route('merchant.delivery-services-report.export',$arr_search)}}">
                            <button type="button" title="@lang("$string_file.export_orders")"
                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-download"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="icon wb-list" aria-hidden="true"></i>
                        @lang("$string_file.order_earning_statistics")
                        </span>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    {!! $search_view !!}
                    <hr>
                    <!-- First Row -->
                    <div class="row">
                        <div class="col-xl-3 col-md-6 info-panel">
                            <div class="card card-shadow">
                                <div class="card-block bg-grey-100 p-20">
                                    <button type="button" class="btn btn-floating btn-sm btn-warning">
                                        <i class="icon wb-shopping-cart"></i>
                                    </button>
                                    <span class="ml-15 font-weight-400">@lang("$string_file.orders")</span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100">{{$business_summary['orders']}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-3 info-panel">
                            <div class="card card-shadow">
                                <div class="card-block bg-grey-100 p-20">
                                    <button type="button" class="btn btn-floating btn-sm btn-danger">
                                        <i class="icon fa-dollar"></i>
                                    </button>
                                    <span class="ml-15 font-weight-400">@lang("$string_file.orders_amount")</span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100">{{isset($business_summary['income']['store_earning']) ? $currency.$merchant_helper->TripCalculation($business_summary['income']['order_amount'],$merchant_id) : 0}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-3 info-panel">
                            <div class="card card-shadow">
                                <div class="card-block bg-grey-100 p-20">
                                    <button type="button" class="btn btn-floating btn-sm btn-danger">
                                        <i class="icon fa-percent"></i>
                                    </button>
                                    <span class="ml-15 font-weight-400">@lang("$string_file.merchant_earning")</span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100">{{isset($business_summary['income']['merchant_earning']) ? $currency.$merchant_helper->TripCalculation($business_summary['income']['merchant_earning'], $merchant_id) : 0}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-3 info-panel">
                            <div class="card card-shadow">
                                <div class="card-block bg-grey-100 p-20">
                                    <button type="button" class="btn btn-floating btn-sm btn-danger">
                                        <i class="icon fa-dollar"></i>
                                    </button>
                                    <span class="ml-15 font-weight-400">@lang("$string_file.store_earning")</span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100">{{isset($business_summary['income']['store_earning']) ? $currency.$merchant_helper->TripCalculation($business_summary['income']['store_earning'], $merchant_id) : 0}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <!-- Third Row -->
                    <!-- Third Left -->
                    <div class="row">
                        <div class="col-lg-12" id="ecommerceRecentOrder">
                            <div class="card card-shadow table-row">
                                {{--                                <h4 class="example-title">@lang("$string_file.all_orders") </h4>--}}
                                <div class="card-block bg-white table-responsive">
                                    <table id="customDataTable"
                                           class="display nowrap table table-hover table-stripedw-full table-bordered report_table"
                                           style="width:100%">
                                        <thead>
                                        <tr class="text-center report_table_row_heading">
                                            <th rowspan="2">@lang("$string_file.sn")</th>
                                            <th rowspan="2">@lang("$string_file.order_id")</th>
                                            <th rowspan="2">@lang("$string_file.payment_method")</th>
                                            <th rowspan="2">@lang("$string_file.user_details")</th>
                                            <th rowspan="2">@lang("$string_file.driver_details")</th>
                                            <th colspan="6">@lang("$string_file.order_amount")</th>
                                            <th colspan="4">@lang("$string_file.merchant_earning")</th>
                                            <th colspan="3">@lang("$string_file.store_earning")</th>
                                            <th colspan="3">@lang("$string_file.driver_earning")</th>
{{--                                            <th rowspan="2">@lang("$string_file.payment_status")</th>--}}
                                            {{--                                            <th>@lang("$string_file.cart_amount")</th>--}}
                                            {{--                                            <th>@lang("$string_file.tax")</th>--}}
                                            {{--                                            <th>@lang("$string_file.delivery_charge")</th>--}}
                                            {{--                                            <th>@lang("$string_file.other_charges")</th>--}}
                                            <th rowspan="2">@lang("$string_file.created_at")</th>
                                        </tr>
                                        <tr class="report_table_row_heading">
                                            <th>@lang("$string_file.cart_amount")</th>
                                            <th>@lang("$string_file.tax")</th>
                                            <th>@lang("$string_file.delivery_charge")</th>
                                            <th>@lang("$string_file.tip")</th>
                                            <th>@lang("$string_file.discount")</th>
                                            <th>@lang("$string_file.total")</th>


                                            <th>@lang("$string_file.earning")</th>
                                            <th>@lang("$string_file.delivery_charge")</th>
                                            <th>@lang("$string_file.discount")</th>
                                            <th>@lang("$string_file.total")</th>

                                            <th>@lang("$string_file.earning")</th>
                                            <th>@lang("$string_file.tax")</th>
                                            <th>@lang("$string_file.total")</th>

                                            <th>@lang("$string_file.earning")</th>
                                            <th>@lang("$string_file.tip")</th>
                                            <th>@lang("$string_file.total")</th>
                                        </tr>
                                        {{--                                        <tr>--}}
                                        {{--                                            <th>@lang("$string_file.sn")</th>--}}
                                        {{--                                            <th>@lang("$string_file.order_id")</th>--}}
                                        {{--                                            <th>@lang("$string_file.store_earning")</th>--}}
                                        {{--                                            <th>@lang("$string_file.merchant_earning")</th>--}}
                                        {{--                                            <th>@lang("$string_file.order_amount")</th>--}}
                                        {{--                                            <th>@lang("$string_file.cart_amount")</th>--}}
                                        {{--                                            <th>@lang("$string_file.tax")</th>--}}
                                        {{--                                            <th>@lang("$string_file.delivery_charge")</th>--}}
                                        {{--                                            <th>@lang("$string_file.other_charges")</th>--}}
                                        {{--                                            <th>@lang("$string_file.created_at")</th>--}}
                                        {{--                                        </tr>--}}
                                        </thead>
                                        <tbody>
                                        @if(!empty($arr_orders))
                                            @php $sr = $arr_orders->firstItem(); $user_name = ''; $user_phone = ''; $user_email = '';
                                                        $driver_name = '';$driver_email = '';
                                            @endphp
                                            @foreach($arr_orders as $order)
                                                @php
                                                    $tax_amount = !empty($order->tax) ? $order->tax : 0;
                                                    $currency = !empty($order->CountryArea->Country->isoCode) ? $order->CountryArea->Country->isoCode : 0;
                                                @endphp
                                                @if(!empty($order->OrderTransaction))
                                                    @php $transaction = $order->OrderTransaction;
                                                    $tax_amount = !empty($order->tax) ? $order->tax : 0;
                                                    @endphp
                                                @endif
                                                <tr>
                                                    <td>{{$sr}}</td>
                                                    <td>
                                                        <a href="{{route('merchant.business-segment.order.detail',$order->id)}}">{{ $order->merchant_order_id }}</a>
                                                    </td>

                                                    <td>{{!empty($order->PaymentMethod->MethodName($order->merchant_id)) ? $order->PaymentMethod->MethodName($order->merchant_id) : $order->PaymentMethod->payment_method}}</td>
                                                    <td>{{is_demo_data($order->User->first_name.' '.$order->User->last_name,$order->Merchant)}}</td>
                                                    <td>{{is_demo_data($order->Driver->first_name.' '.$order->Driver->last_name,$order->Merchant)}}</td>

                                                    <td>
                                                        {{$order->cart_amount }}
                                                    </td>
                                                    <td>
                                                        {{$transaction->tax_amount }}
                                                    </td>
                                                    <td>
                                                        {{$order->delivery_amount }}
                                                    </td>
                                                    <td>
                                                        @if(!empty($transaction->tip))
                                                            {{ $transaction->tip}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{$order->discount_amount }}
                                                    </td>
                                                    <td>
                                                        <b>{{ $currency.' '.$order->final_amount_paid}}</b>
                                                    </td>
                                                    {{--                                                    merchant earning--}}

                                                    <td>
                                                        @if(!empty($transaction))
                                                            {{($transaction->company_earning)}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{$order->delivery_amount }}
                                                    </td>
                                                    <td>
                                                        {{$order->discount_amount }}
                                                    </td>
                                                    <td>
                                                        <b>{{$currency.' '.($transaction->company_gross_total)}}</b>
                                                    </td>

                                                    <td>
                                                        {{($transaction->business_segment_earning) }}
                                                    </td>
                                                    <td>
                                                        {{$transaction->tax_amount }}
                                                    </td>
                                                    <td>
                                                        <b>{{$currency.' '.($transaction->business_segment_total_payout_amount)}}</b>
                                                    </td>
                                                    <td>
                                                        @if(!empty($transaction))
                                                            {{$transaction->driver_earning}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{$transaction->tip}}
                                                    </td>
                                                    <td>
                                                        <b>{{$currency.' '.($transaction->driver_total_payout_amount)}}</b>
                                                    </td>
{{--                                                    <td>--}}
{{--                                                        {{$transaction->payment_status}}--}}
{{--                                                    </td>--}}
                                                    <td>
                                                        {!! convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone, null, $order->Merchant) !!}
                                                    </td>
                                                </tr>
                                                @php $sr++  @endphp
                                            @endforeach
                                        </tbody>
                                        @endif
                                    </table>
                                    @include('merchant.shared.table-footer', ['table_data' => $arr_orders, 'data' =>$arr_search])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
