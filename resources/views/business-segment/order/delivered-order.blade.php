@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                    </div>
                    <h3 class="panel-title"><i class="fa-user" aria-hidden="true"></i>
                        @lang("$string_file.delivered_orders")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    {!! $search_view !!}
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.order_id")</th>
                            <th>@lang("$string_file.product_details")</th>
                            <th>@lang("$string_file.payment_details")</th>
                            <th>@lang("$string_file.current_status")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.order_type")</th>
                            <th>@lang("$string_file.delivered_on")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.pickup_drop")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $arr_orders->firstItem();$user_name = ''; $user_phone = ''; $user_email = '';
                        $driver_name = '';$driver_email = '';
                        @endphp
                        @foreach($arr_orders as $order)
                            @php
                                if($hide_user_info_from_store == 2)
                                   {
                                    $user_name = is_demo_data($order->User->first_name.' '.$order->User->last_name,$order->Merchant);
                                    $user_phone = is_demo_data($order->User->UserPhone,$order->Merchant);
                                    $user_email = is_demo_data($order->User->email,$order->Merchant);
                                   }
                            @endphp
                            @if(!empty($order->driver_id))
                                @php
                                   $driver_name = is_demo_data($order->Driver->first_name.' '.$order->Driver->last_name,$order->Merchant);
                                   $driver_phone = is_demo_data($order->Driver->phoneNumber,$order->Merchant);
                                   $driver_email = is_demo_data($order->Driver->email,$order->Merchant);
                                @endphp
                            @endif
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $order->merchant_order_id }}</td>
                                <td>
                                    @php $product_detail = $order->OrderDetail; $products = "";@endphp
                                    @foreach($product_detail as $product)
                                         @php $weight =  isset($product->ProductVariant->weight) ? $product->ProductVariant->weight : "";
                                             $unit = isset($product->weight_unit_id) ? $product->WeightUnit->WeightUnitName : "";
                                             $unit = !empty($weight)  ? $product->quantity.' x '.$weight.' '.$unit : $product->quantity.$unit;
                                        @endphp
                                        {{ $unit .' - '.$product->Product->Name($order->merchant_id)}} <br>
                                        {{$product->ProductVariant->Name($order->merchant_id)}}
                                        @if(!empty($product->options))
                                            <b>{{'|'}}</b>
                                            @php  $arr_cart_option = !empty($product->options) ? json_decode($product->options,true) : []; @endphp
                                            @foreach($arr_cart_option as $option)
                                                {{$option['option_name']}},
                                            @endforeach
                                            <br>
                                        @endif
                                    @endforeach
                                    <br>
                                    @if($order->Segment->slag == "PHARMACY" && !empty($order->prescription_image))
                                        @lang("$string_file.prescription") : <a href="{{get_image($order->prescription_image,'prescription_image',$order->merchant_id)}}"> @lang("$string_file.view")</a>
                                    @endif
                                </td>
                                <td>
                                    {{trans("$string_file.mode").": ". $order->PaymentMethod->payment_method}}<br>
                                    {{trans($string_file.".cart_amount").': '.$order->cart_amount}} <br>
                                    {{trans("$string_file.delivery_charge").': '. $order->delivery_amount }} <br>
                                    {{trans("$string_file.tax").': '. ($order->tax) }} <br>
                                    {{trans("$string_file.tip").': '. (!empty($order->tip_amount) ? $order->tip_amount : 0.0) }} <br>
                                    @lang("$string_file.grand_total") :  {{ $order->CountryArea->Country->isoCode.' '.$order->final_amount_paid}}
                                    <br>
                                </td>
                                <td style="text-align: center">
                                    {{ $arr_status[$order->order_status] }}
                                </td>
                                <td> {{ $order->CountryArea->CountryAreaName }}</td>
                                <td>
                                    {{$user_name}} <br>
                                    {{$user_phone}} <br>
                                    {{$user_email}} <br>
                                </td>
                                <td>
                                    @if(!empty($driver_name))
                                        {{$driver_name}} <br>
                                        {{$driver_phone}} <br>
                                        {{$driver_email}} <br>
                                    @else
                                        @lang("$string_file.not_assigned_yet")
                                    @endif
                                </td>
                                <td>{{$order->order_type == 1 ? trans("$string_file.now") : trans("$string_file.later")}}</td>
                                <td>
                                    @php $slot_time = ""; $current_time_stamp = strtotime(date('Y-m-d H:i:s')); @endphp
                                    @if(!empty($order->service_time_slot_detail_id))
                                        @php
                                            $slot_time = $order->ServiceTimeSlotDetail->to_time;
                                            $start = $order->ServiceTimeSlotDetail->from_time;
                                            $start = strtotime($start);
                                            $start = $time_format == 2  ? date("H:i",$start) : date("h:i a",$start);
                                            $end = strtotime($slot_time);
                                            $end =  $time_format == 2  ? date("H:i",$end) : date("h:i a",$end);
                                            $time = $start."-".$end;
                                            echo $time.'<br>';
                                        @endphp
                                    @endif
                                    {!! convertTimeToUSERzone($order->order_date, $order->CountryArea->timezone,null,$order->Merchant, 2) !!}
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone,null,$order->Merchant) !!}
                                </td>
                                <td>
                                    <a title="{{ $order->drop_location }}" target="_blank"
                                       href="https://www.google.com/maps/place/{{ $order->drop_location }}" class="btn btn-icon btn-danger ml-20">
                                        <i class="icon fa-tint"></i>
                                    </a>
                                </td>
                                <td>

{{--                                    <a target="_blank" title="@lang('admin.assign_order')"--}}
{{--                                       href="{{route('business-segment.order.assign',$order->id)}}" class="btn btn-sm btn-info menu-icon btn_money action_btn">--}}
{{--                                        <span class="fa fa-send" title="@lang('admin.assign_order')"></span>--}}
{{--                                    </a>--}}

                                    <a target="_blank" title="@lang("$string_file.order_details")"
                                       href="{{route('business-segment.order.detail',$order->id)}}" class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                        <span class="fa fa-info-circle" title="@lang("$string_file.order_details")"></span>
                                    </a>

                                    <a target="_blank" title="@lang('"$string_file.invoice"')"
                                       href="{{route('business-segment.order.invoice',$order->id)}}" class="btn btn-sm btn-warning menu-icon btn_eye action_btn"><span class="fa fa-print"></span>
                                    </a>
{{--                                    <a target="_blank" title="@lang("$string_file.cancel")"--}}
{{--                                       href="{{route('business-segment.order-cancel',$order->id)}}" class="btn btn-sm btn-warning menu-icon btn_eye action_btn"><span class="fa fa-eye-slash"></span>--}}
{{--                                    </a>--}}
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $arr_orders, 'data' => $arr_search])
                </div>
            </div>
        </div>
    </div>
@endsection