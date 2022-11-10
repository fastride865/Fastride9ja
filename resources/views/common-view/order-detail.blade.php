<div class="panel-body container-fluid">
    <div class="row">
        <div class="col-lg-6 col-xs-12">
            <h5>@lang("$string_file.user_details") : - </h5>
            <div class="card my-2 shadow  bg-white h-240">
                <div class="row p-3 mt-30 ml-30">
                    <div class="col-md-4 col-xs-6">
                        <img height="100" width="100" class="rounded-circle"
                             src="@if($order->User->UserProfileImage) {{ get_image($order->User->UserProfileImage,'user',$order->merchant_id) }}@else{{get_image()}}@endif">
                    </div>
                    <div class="col-md-8 col-xs-6">
                        @if(!empty($calling_from_bs) && $hide_user_info_from_store == 1)
                        @else
                            <p>
                                <span class="font-size-20">{{is_demo_data($order->User->first_name.' '.$order->User->last_name,$order->Merchant)}}</span>
                                <br>
                                <span title="Phone">@lang("$string_file.phone"):</span>&nbsp;&nbsp;{{is_demo_data($order->User->UserPhone,$order->Merchant)}}
                                <span title="Email">@lang("$string_file.email"):</span>&nbsp;&nbsp;{{is_demo_data($order->User->email,$order->Merchant)}}
                                <br>
                            </p>
                        @endif
                    </div>
                </div>
                <div class="row mt-10 ml-20">
                    <div class="col-md-4 col-xs-4 pt-20 pl-30">
                        @lang("$string_file.address") :
                    </div>
                    <div class="col-md-8 col-xs-8">
                        <address>
                            @if($order->drop_location)
                                {{$order->drop_location}}
                            @else
                                {{$order->UserAddress->house_name}},
                                {{$order->UserAddress->floor}}
                                {{$order->UserAddress->building}}
                                <br>
                                {{$order->UserAddress->address}}
                            @endif
                        </address>
                        <br>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="col-lg-6 col-xs-12">
            <h5>@lang("$string_file.other_details") : - </h5>
            <div class="card my-2 shadow  bg-white h-240">
                <div class="row p-3 mt-30 ml-30">
                    <div class="col-md-4 col-xs-6 text-info">
                        <i class="icon fa-money"></i> @lang("$string_file.payment")
                    </div>
                    <div class="col-md-8 col-xs-6">
                        <p>
                            <span class="font-size-20">{{$order->PaymentMethod->payment_method}}</span>
                            <br>
                            @php $currency = $order->CountryArea->Country->isoCode; @endphp
                            <span title="">@lang("$string_file.grand_total") : </span>{{$currency.$order->final_amount_paid}}
                        </p>
                    </div>
                </div>
                <div class="row p-3 mt-30 ml-30">
                    <div class="col-md-4 col-xs-6 text-warning">
                        <i class="icon fa-comments fa-2x text-gray-300"></i> @lang("$string_file.current_status")
                    </div>
                    <div class="col-md-8 col-xs-6">
                        <p>
                            {{$arr_status[$order->order_status]}}
                            <br>
                        </p>
                    </div>
                </div>
                <div class="row p-3 ml-30">
                    <div class="col-md-4 col-xs-6 text-success">
                        <i class="icon fa-calendar fa-2x text-gray-300"></i>@lang("$string_file.created_at")
                    </div>
                    <div class="col-md-8 col-xs-6">
                        <p>
                            {!! convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone, null, $order->Merchant) !!}
{{--                            {!! date(getDateTimeFormat($order->Merchant->datetime_format),strtotime($order->created_at)) !!}--}}
                            <br>
                        </p>
                    </div>
                </div>
                 <div class="row p-3 ml-30">
                    <div class="col-md-4 col-xs-6 text-success">
                        <i class="icon fa-calendar fa-2x text-gray-300"></i>@lang("$string_file.deliver_on")
                    </div>
                    <div class="col-md-8 col-xs-6">
                        @if(!empty($order->service_time_slot_detail_id))
                            {{$order->ServiceTimeSlotDetail->slot_time_text}},
                        @endif
                        
                            {!! convertTimeToUSERzone($order->order_date, $order->CountryArea->timezone, null, $order->Merchant,2) !!}
                    </div>
                </div>
                <div class="row mt-10 ml-20">
                </div>
                <div class="clear"></div>
            </div>
        </div>
        @if(!empty($order->driver_id))
        <div class="col-lg-6 col-xs-12">
            <h5>@lang("$string_file.driver_details") : - </h5>
            <div class="card my-2 shadow  bg-white h-240">
                <div class="row p-3 mt-30 ml-30">
                    <div class="col-md-4 col-xs-6">
                        <img height="100" width="100" class="rounded-circle"
                             src="@if($order->driver_id) {{ get_image($order->Driver->profile_image,'drive',
                             $order->merchant_id) }}@else{{get_image()}}@endif">
                    </div>
                    <div class="col-md-8 col-xs-6">
                            <p>
                                <span class="font-size-20">{{is_demo_data($order->Driver->first_name.' '.$order->Driver->last_name,$order->Merchant)}}</span>
                                <br>
                                <span title="Phone">@lang("$string_file.phone"):</span>&nbsp;&nbsp;{{is_demo_data($order->Driver->phoneNumber,$order->Merchant)}}
                                <span title="Email">@lang("$string_file.email"):</span>&nbsp;&nbsp;{{is_demo_data($order->Driver->email,$order->Merchant)}}
                                <br>
                            </p>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        @if($order->reassign == 1)
            <div class="col-lg-6 col-xs-12">
                <h5>@lang("$string_file.first_driver_details") : - </h5>
                <div class="card my-2 shadow  bg-white h-240">
                    <div class="row p-3 mt-30 ml-30">
                        <div class="col-md-4 col-xs-6">
                            <img height="100" width="100" class="rounded-circle"
                                 src="@if($order->OldDriver->profile_image) {{ get_image($order->OldDriver->profile_image,'drive',$order->merchant_id) }}@else{{get_image()}}@endif">
                        </div>
                        <div class="col-md-8 col-xs-6">
                            <p>
                                <span class="font-size-20">{{is_demo_data($order->OldDriver->first_name.' '.$order->OldDriver->last_name,$order->Merchant)}}</span>
                                <br>
                                <span title="Phone">@lang("$string_file.phone"):</span>&nbsp;&nbsp;{{is_demo_data($order->OldDriver->phoneNumber,$order->Merchant)}}
                                <span title="Email">@lang("$string_file.email"):</span>&nbsp;&nbsp;{{is_demo_data($order->OldDriver->email,$order->Merchant)}}
                                <br>
                            </p>
                        </div>
                    </div>
                    <div class="row p-3 mt-30 ml-30">
                        <div class="col-md-4 col-xs-6">
                            @lang("$string_file.reassign_reason")
                        </div>
                        <div class="col-md-8 col-xs-6">
                            <p>
                                {{$order->reassign_reason}}
                            </p>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        @endif
        @endif
    </div>
    <hr>
    <h5>@lang("$string_file.product_details") : - </h5>
    <div class="page-invoice-table table-responsive">
        <table class="table table-hover text-right">
            <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">@lang("$string_file.product_name")</th>
                <th class="text-center">@lang("$string_file.product_variant")</th>
                <th class="text-center">@lang("$string_file.description")</th>
                <th class="text-right">@lang("$string_file.quantity")</th>
                <th class="text-right">@lang("$string_file.price")</th>
                @if($order->Segment->slag =="FOOD")
                    <th class="text-right">@lang("$string_file.option_amount")</th>
                @endif
                @if($order->Segment->slag =="PHARMACY")
                    <th class="text-right">@lang("$string_file.prescription")</th>
                @endif
                <th class="text-right">@lang("$string_file.discount")</th>
                <th class="text-right">@lang("$string_file.total_amount")</th>
            </tr>
            </thead>
            <tbody>
            @php $sn = 1;$col_span = 7;$arr_option_amount = [];
                                 $tax =  !empty($order->tax) ? $order->tax : 0.0;
                                 $tip =  !empty($order->tip_amount) && $order->tip_amount > 0 ? $order->tip_amount : 0.0;
                                 $time_charges =  !empty($order->time_charges) && $order->time_charges > 0 ? $order->time_charges : 0.0;
                                 $discount_amount =  !empty($order->discount_amount) && $order->discount_amount > 0 ? $order->discount_amount : 0.0;
            @endphp
            @if($order->Segment->slag =="FOOD")
                @php $col_span = 8;@endphp
            @endif
            @foreach($order->OrderDetail as $product)
                @php $lang = $product->Product->langData($order->merchant_id); $option_amount = [];
                @endphp
                <tr>
                    <td class="text-center">
                        {{$sn}}
                    </td>
                    <td class="text-center">
                        {{$lang->name}}
                        @if(!empty($product->options))
                            {{'('}}
                            @php  $arr_cart_option = !empty($product->options) ? json_decode($product->options,true) : []; @endphp
                            @foreach($arr_cart_option as $option)
                                {{--                                                @if($option['amount'])--}}
                                {{--                                                    {{$currency.$option['amount']}}--}}
                                {{--                                                @endif--}}
                                @php $arr_option_amount[] = $option['amount'];
                                                $option_amount[] = $option['amount'];
                                @endphp
                                {{$option['option_name']}},

                            @endforeach
                            {{')'}}
                        @endif
                    </td>
                    <td class="text-center">
                        {{$product->ProductVariant->Name($order->merchant_id)}}
                    </td>
                    <td class="text-center">
                        {{$lang->description}}
                    </td>
                    <td>
                        {{$product->quantity}} @if(isset($product->WeightUnit) && !empty($product->WeightUnit)) {{$product->WeightUnit->WeightUnitName}} @endif
                    </td>
                    <td>
                        {{$product->price}}
                    </td>
                    @if($order->Segment->slag =="FOOD")
                        <td>
                            {{array_sum($option_amount)}}
                        </td>
                    @endif
                    @if($order->Segment->slag =="PHARMACY")
                        <td>
                            @if(!empty($order->prescription_image))
                                <a href="{{get_image($order->prescription_image,'prescription_image',$order->merchant_id)}}"> @lang("$string_file.view")
                            @endif
                        </td>
                    @endif
                    <td>
                        {{$product->ProductVariant->discount}}
                    </td>
                    <td>
                        {{$product->total_amount}}
                    </td>
                </tr>
                @php $sn = $sn+1; @endphp
            @endforeach
            <tr>
                <td colspan="{{$col_span}}">@lang("$string_file.cart_amount")</td>
                <td>{{$currency.$order->cart_amount}}</td>
            </tr>
            <tr>
                <td colspan="{{$col_span}}">@lang("$string_file.delivery_charge")</td>
                <td>{{$currency.$order->delivery_amount}}</td>
            </tr>
            <tr>
                <td colspan="{{$col_span}}">@lang("$string_file.tax")</td>
                <td>{{$currency.$tax}}</td>
            </tr>
            <tr>
                <td colspan="{{$col_span}}">@lang("$string_file.tip")</td>
                <td>{{$currency.$tip}}</td>
            </tr>
            @if($order->Segment->slag =="DELIVERY")
                <tr>
                    <td colspan="{{$col_span}}">@lang("$string_file.time_charges")</td>
                    <td>{{$currency.$time_charges}}</td>
                </tr>
            @endif
            <tr>
                <td colspan="{{$col_span}}">@lang("$string_file.discount_amount")</td>
                <td>{{$currency.$discount_amount}}</td>
            </tr>
            <tr>
                <td colspan="{{$col_span}}">@lang("$string_file.grand_total")</td>
                <td>{{$currency.$order->final_amount_paid}}</td>
            </tr>
            </tbody>
            <tfoot>
            @if(isset($cancel_receipt) && $cancel_receipt['cancel_receipt_visibility'] == true)
                <tr>
                    <td class="text-left" colspan="{{$col_span+1}}">
                        <b>@lang("$string_file.other_action")</b></td>
                </tr>
                <tr>
                    <td class="text-left" colspan="3">{{$cancel_receipt['cancelled_tital']}}
                        <br> {{$cancel_receipt['cancelled_bottom_text']}}</td>
                    <td colspan="{{$col_span-3}}"></td>
                    <td>{{$currency.$cancel_receipt['cancelled_charges']}}</td>
                </tr>
            @endif
            </tfoot>
        </table>
    </div>
</div>
