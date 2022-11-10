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
                        @lang("$string_file.upcoming_orders")
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
                            <th>@lang("$string_file.deliver_on")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.expire_at")</th>
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
                                    $driver_phone = is_demo_data($order->Driver->UserPhone,$order->Merchant);
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
                                <td>{{ $order->order_type == 1 ? trans("$string_file.now") : trans("$string_file.later")}}</td>
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
                                    @php
                                        if(!empty($config)){
                                            $expire_minute=$config->order_expire_time;
                                        }
                                        else{
                                            $expire_minute=10; // minutes
                                        }
                                     if($order->Segment->slag == "FOOD")
                                     {
                                        $order_time_stamp = $order->order_timestamp;
                                        $expire_time = strtotime("+$expire_minute minutes", $order_time_stamp);
                                     }
                                     else
                                     {
                                         $order_time_stamp = $order->order_date;
                                         if(!empty($slot_time))
                                         {
                                         $expire_time = strtotime($order->order_date.' '.$slot_time);
                                         }
                                     }
                                     $expire_time = date("Y-m-d H:i:s",$expire_time);
                                    @endphp
                                    @isset($order->Merchant->datetime_format)
                                        @lang("$string_file.at") {{date(getDateTimeFormat($order->Merchant->datetime_format,3),$expire_time)}}
                                    @endisset
                                    <br>
                                    {{convertTimeToUSERzone($expire_time, $order->CountryArea->timezone,null,$order->Merchant)}}
                                </td>
                                <td>
                                    <a title="{{ $order->drop_location }}" target="_blank"
                                       href="https://www.google.com/maps/place/{{ $order->drop_location }}" class="btn btn-icon btn-danger ml-20">
                                        <i class="icon fa-tint"></i>
                                    </a>
                                </td>
                                <td>
                                    @php
                                        $current_time_stamp = strtotime(date('Y-m-d H:i:s'));
                                    @endphp
{{--                                    @if($diff > $booking_config->driver_request_timeout)--}}
{{--                                        <a target="_blank" title=""--}}
{{--                                           href="{{route('business-segment.order.assign',$order->id)}}" class="btn btn-sm btn-info menu-icon btn_money action_btn">--}}
{{--                                            <span class="fa fa-check" title="@lang("$string_file.manual_assign")"></span>--}}
{{--                                        </a>--}}
{{--                                    @endif--}}

{{--                                    @if(!empty($order->order_date) && $order->order_date == date('Y-m-d') && $current_time_stamp <= $order_time_stamp)--}}
{{--                                    <a target="_blank" title=""--}}
{{--                                       href="{{route('business-segment.order.assign',$order->id)}}" class="btn btn-sm btn-info menu-icon btn_money action_btn">--}}
{{--                                        <span class="fa fa-check" title="@lang("$string_file.manual_assign")"></span>--}}
{{--                                    </a>--}}
{{--                                    <a data-original-title="" data-target="#auto_assign" data-toggle="modal"--}}
{{--                                       data-id="{{ $order->id }}" data-placement="top" data-backdrop="static" data-keyboard="false"--}}
{{--                                       class="btn btn-sm btn-success menu-icon btn_money action_btn auto_assign">--}}
{{--                                        <span class="fa fa-send" title="@lang("$string_file.auto_assign")"></span>--}}
{{--                                    </a>--}}
{{--                                    @endif--}}
{{--                                    <a target="_blank" title="@lang('admin.auto_assign')"--}}
{{--                                       href="{{route('business-segment.order.assign',$order->id)}}" class="btn btn-sm btn-info menu-icon btn_money action_btn">--}}
{{--                                        <span class="fa fa-send" title="@lang('admin.auto_assign')"></span>--}}
{{--                                    </a>--}}
{{--                                    @if($order_request_receiver == 2)--}}
{{--                                        @php--}}
{{--                                            $diff = strtotime(date('Y-m-d H:i:s')) - $order->order_timestamp;--}}
{{--                                        @endphp--}}
{{--                                        @if($diff > $booking_config->driver_request_timeout)--}}
{{--                                            <a target="_blank" title=""--}}
{{--                                               href="{{route('business-segment.order.assign',$order->id)}}" class="btn btn-sm btn-info menu-icon btn_money action_btn">--}}
{{--                                                <span class="fa fa-check" title="@lang("$string_file.manual_assign")"></span>--}}
{{--                                            </a>--}}
{{--                                        @endif--}}
{{--                                    @endif--}}
                                    <a target="_blank" title="@lang("$string_file.order_details")"
                                       href="{{route('business-segment.order.detail',$order->id)}}" class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                        <span class="fa fa-info-circle" title="@lang("$string_file.order_details")"></span>
                                    </a>

                                    <a target="_blank" title="@lang('"$string_file.invoice"')"
                                       href="{{route('business-segment.order.invoice',$order->id)}}" class="btn btn-sm btn-warning menu-icon btn_eye action_btn"><span class="fa fa-print"></span>
                                    </a>
                                    <a target="_blank" title="@lang("$string_file.reject")"
                                       href="{{route('business-segment.order-reject',$order->id)}}" class="btn btn-sm btn-danger menu-icon btn-edit action_btn"><span class="fa fa-edit"></span>
                                    </a>
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
    <div class="modal fade text-left" id="auto_assign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33"><b>@lang("$string_file.assign_order_to_delivery_candidate")</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(['name'=>"","url"=>route('business-segment.order.auto-assign')]) !!}
                    <div class="modal-body">
                        <label>@lang("$string_file.are_you_sure_assign")</label>
                        <div class="form-group">
{{--                            <input type="number" name="otp" placeholder="Enter Driver App OTP"--}}
{{--                                   class="form-control" required>--}}
                            <input type="hidden" name="order_id" id="order_id">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="@lang("$string_file.no")">
                        <input type="submit" id="sub" class="btn btn-primary" value="@lang("$string_file.yes")">
                    </div>
               {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).on("click", ".auto_assign", function () {
            var order_id = $(this).data('id');
            $(".modal-body #order_id").val( order_id );
        });
    </script>
@endsection