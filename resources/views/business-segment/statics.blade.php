@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                    </div>
                    <h3 class="panel-title">
                        <i class="icon wb-list" aria-hidden="true"></i>
                        @lang("$string_file.product_order_statistics")
                        </span>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <!-- First Row -->
                    <div class="row">
                        <div class="col-xl-3 col-md-6 info-panel">
                            <div class="card card-shadow">
                                <div class="card-block bg-grey-100 p-20">
                                    <button type="button" class="btn btn-floating btn-sm btn-success">
                                        <i class="icon wb-info"></i>
                                    </button>
                                    <span class="ml-15 font-weight-400">@lang("$string_file.products")</span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100">{{$business_summary['products']}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 info-panel">
                            <div class="card card-shadow">
                                <div class="card-block bg-grey-100 p-20">
                                    <button type="button" class="btn btn-floating btn-sm btn-warning">
                                        <i class="icon wb-shopping-cart"></i>
                                    </button>
                                    <span class="ml-15 font-weight-400">@lang("$string_file.orders")</span>
                                    <div class="content-text text-center mb-0">
                                        {{--                                        <i class="text-danger icon wb-triangle-up font-size-20">--}}
                                        {{--                                        </i>--}}
                                        <span class="font-size-20 font-weight-100">{{$business_summary['orders']}}</span>
                                        {{--                                        <p class="blue-grey-400 font-weight-100 m-0">+45% From previous month</p>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6 info-panel">
                            <div class="card card-shadow">
                                <div class="card-block bg-grey-100 p-20">
                                    <button type="button" class="btn btn-floating btn-sm btn-danger">
                                        <i class="icon fa-dollar"></i>
                                    </button>
                                    <span class="ml-15 font-weight-400 p-lg-60">@lang("$string_file.orders_amount") : {{isset($business_summary['income']['order_amount']) ? $currency.$business_summary['income']['order_amount'] : 0}}</span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-14 font-weight-100 green-800">
                                            {{$title.' '.trans("$string_file.earning").': '}} {{ !empty($business_summary['income']['store_earning']) ? $currency.$business_summary['income']['store_earning'] : 0 }} | </span>
                                        <span class="font-size-14 font-weight-100 blue-800">
                                            {{$merchant_name.' '.trans("$string_file.earning").': '}} {{ !empty($business_summary['income']['store_earning']) ? $currency.$business_summary['income']['merchant_earning'] : 0}}  <br></span>
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
                                <h4 class="example-title">@lang("$string_file.all_orders") </h4>
                                <div class="card-block bg-white table-responsive">
                                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>@lang("$string_file.sn")</th>
                                            <th>@lang("$string_file.order_id")</th>
                                            <th>@lang("$string_file.earning_details")</th>
                                            <th>@lang("$string_file.payment_details")</th>
                                            <th>@lang("$string_file.product_details")</th>
                                            <th>@lang("$string_file.user_details")</th>
                                            <th>@lang("$string_file.created_at")</th>
                                            <th>@lang("$string_file.deliver_on")</th>
                                            <th>@lang("$string_file.current_status")</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(!empty($arr_orders))
                                            @php $sr = $arr_orders->firstItem(); $user_name = ''; $user_phone = ''; $user_email = '';
                                                        $driver_name = '';$driver_email = '';
                                                         $tax_amount =    !empty($order->tax) ? $order->tax : 0;
                                            @endphp
                                            @foreach($arr_orders as $order)
                                                @php
                                                     $user_name = is_demo_data($order->User->UserName,$order->Merchant);
                                                     $user_phone = is_demo_data($order->User->UserPhone,$order->Merchant);
                                                     $user_email = is_demo_data($order->User->email,$order->Merchant);
                                                    $currency = $order->CountryArea->Country->isoCode;
                                                @endphp
                                                <tr>
                                                    <td>{{$sr}}</td>
                                                    <td>
                                                        <a href="{{route('business-segment.order.detail',$order->id)}}">{{ $order->merchant_order_id }}</a>
                                                    </td>
                                                    <td>
                                                        @if(!empty($order->OrderTransaction))
                                                            @php $transaction = $order->OrderTransaction;

                                                            @endphp
                                                            @lang("$string_file.grand_total") :  {{ $currency.$order->final_amount_paid}} <br>
                                                            {{$order->BusinessSegment->full_name.' '.trans("$string_file.earning").': '.$currency.$transaction->business_segment_earning }} <br>
                                                            {{trans("$string_file.merchant_earning").': '.$currency.$transaction->company_earning}} <br>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{trans("$string_file.mode").": ". $order->PaymentMethod->payment_method}}<br>
                                                        {{trans($string_file.".cart_amount").': '.$currency.$order->cart_amount}} <br>
                                                        {{trans("$string_file.delivery_charge").': '. $currency.$order->delivery_amount }} <br>
                                                        {{trans("$string_file.tax").': '.$currency.$tax_amount }} <br>
                                                        {{trans("$string_file.tip_amount").': '.$currency.$order->tip_amount }} <br>
                                                        {{trans("$string_file.discount").': '.$currency.$order->discount_amount }} <br>
                                                        @lang("$string_file.grand_total") :  {{ $currency.$order->final_amount_paid}}
                                                        <br>
                                                    </td>
                                                    <td>
                                                        @php $product_detail = $order->OrderDetail; $products = "";@endphp
                                                        @foreach($product_detail as $product)
                                                             @php $weight =  isset($product->ProductVariant->weight) ? $product->ProductVariant->weight : "";
                                             $unit = isset($product->weight_unit_id) ? $product->WeightUnit->WeightUnitName : "";
                                             $unit = !empty($weight)  ? $product->quantity.' x '.$weight.' '.$unit : $product->quantity.$unit;
                                        @endphp
                                                            {{ $unit .' - '.$product->Product->Name($order->merchant_id)}}
                                                            ({{$product->ProductVariant->Name($order->merchant_id)}}})
                                                            ,<br>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        {{$user_name}} <br>
                                                        {{$user_phone}} <br>
                                                        {{$user_email}} <br>
                                                    </td>
                                                    <td>
                                                        {!! convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone, null, $order->Merchant) !!}
                                                    </td>
                                                    <td>
                                                        {!! convertTimeToUSERzone($order->order_date, $order->CountryArea->timezone, null, $order->Merchant,2) !!}
                                                    </td>
                                                    <td style="text-align: center">
                                                        @if($order->order_status == 11)
                                                            <span class="badge badge-success font-weight-100">{{ $arr_status[$order->order_status] }}</span>
                                                        @elseif(in_array($order->order_status,[1,6,7,9,10]))
                                                            <span class="badge btn-info font-weight-100">{{ $arr_status[$order->order_status] }}</span>
                                                        @else
                                                            <span class="badge badge-danger font-weight-100">{{ $arr_status[$order->order_status] }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @php $sr++  @endphp
                                            @endforeach
                                        </tbody>
                                        @endif
                                    </table>
                                        @include('merchant.shared.table-footer', ['table_data' => $arr_orders, 'data' => []])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
