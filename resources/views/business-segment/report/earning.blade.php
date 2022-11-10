@extends('business-segment.layouts.main')
@section('content')
    <style>
        #ecommerceRecentOrder .table-row .card-block .table td {
            vertical-align: middle !important;
            height: 15px !important;
            font-size: 14px !important;
            padding: 8px 8px !important;
        }
        .dataTables_filter, .dataTables_info { display: none; }
    </style>
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                    </div>
                    <h3 class="panel-title">
                        <i class="icon wb-list" aria-hidden="true"></i>
                        @lang("$string_file.order_earning_statistics")
                        </span>
                    </h3>
                    <div class="panel-actions">
                        <a href="{{route('business-segment.earning.export')}}">
                            <button type="button" title="@lang("$string_file.export_orders")"
                                    class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-download"></i>
                            </button>
                        </a>
                    </div>
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
                                        <span class="font-size-20 font-weight-100">{{isset($business_summary['income']['store_earning']) ? $currency.$business_summary['income']['order_amount'] : 0}}</span>
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
                                        <span class="font-size-20 font-weight-100">{{isset($business_summary['income']['merchant_earning']) ? $currency.$business_summary['income']['merchant_earning'] : 0}}</span>
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
                                    <span class="ml-15 font-weight-400">@lang("$string_file.total_earning")</span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100">{{isset($business_summary['income']['store_earning']) ? $currency.$business_summary['income']['store_earning'] : 0}}</span>
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
                                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>@lang("$string_file.sn")</th>
                                            <th>@lang("$string_file.order_id")</th>
                                            <th>@lang("$string_file.store_earning")</th>
                                            <th>@lang("$string_file.merchant_earning")</th>
                                            <th>@lang("$string_file.order_amount")</th>
                                            <th>@lang("$string_file.cart_amount")</th>
                                            <th>@lang("$string_file.tax")</th>
                                            <th>@lang("$string_file.delivery_charge")</th>
                                            <th>@lang("$string_file.other_charges")</th>
                                            <th>@lang("$string_file.created_at")</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(!empty($arr_orders))
                                            @php $sr = $arr_orders->firstItem(); $user_name = ''; $user_phone = ''; $user_email = '';
                                                        $driver_name = '';$driver_email = '';
                                                         $tax_amount =    !empty($order->tax) ? $order->tax : 0;
                                            @endphp
                                            @foreach($arr_orders as $order)
                                                @if(!empty($order->OrderTransaction))
                                                    @php $transaction = $order->OrderTransaction;
                                                    @endphp
                                                @endif
                                                <tr>
                                                    <td>{{$sr}}</td>
                                                    <td>
                                                        <a href="{{route('business-segment.order.invoice',$order->id)}}">{{ $order->merchant_order_id }}</a>
                                                    </td>
                                                    <td>
                                                        @if(!empty($transaction))
                                                            {{$transaction->business_segment_earning }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(!empty($transaction))
                                                            {{$transaction->company_earning}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $order->final_amount_paid}}
                                                    </td>
                                                    <td>
                                                      {{$order->cart_amount }}
                                                    </td>
                                                    <td>
                                                        {{$order->tax }}
                                                    </td>
                                                    <td>
                                                        {{$order->delivery_amount }}
                                                    </td>
                                                    <td>
                                                        @if(!empty($order->tip_amount))
                                                         @lang("$string_file.tip") : {{ $order->tip_amount}}
                                                        @endif
                                                    </td>

                                                    <td>
                                                        {!! convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone,null,$order->Merchant) !!}
                                                    </td>
                                                </tr>
                                                @php $sr++  @endphp
                                            @endforeach
                                        </tbody>
                                        @endif
                                    </table>
                                        @include('merchant.shared.table-footer', ['table_data' => $arr_orders, 'data' => $arr_search])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
