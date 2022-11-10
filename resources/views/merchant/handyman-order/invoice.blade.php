@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                    </div>
                    <h3 class="panel-title"><i class="fa-user" aria-hidden="true"></i>
                        @lang("$string_file.invoice")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <div class="row">
                        <div class="col-lg-3">
                            <span><img height="60" width="100"
                                       src="{{ get_image($business_segment->business_logo,'business_logo',$business_segment->merchant_id,true) }}"
                                       alt="...">
                               <br> {{$business_segment->full_name}},
                            </span>
                            <address>
                                {{$business_segment->address}}
                                <br>
                                @if(Auth::user()->demo == 1)
                                    <abbr title="Mail">@lang("$string_file.email"):</abbr>&nbsp;
                                    &nbsp;{{ "********".substr($business_segment->email,-3)}}
                                    <br>
                                    <abbr title="Phone">@lang("$string_file.phone"):</abbr>&nbsp;
                                    &nbsp;{{ "********".substr($business_segment->phone_number,-3)}}
                                    <br>
                                @else
                                    <abbr title="Mail">@lang("$string_file.email"):</abbr>&nbsp;
                                    &nbsp;{{$business_segment->email}}
                                    <br>
                                    <abbr title="Phone">@lang("$string_file.phone"):</abbr>&nbsp;
                                    &nbsp;{{$business_segment->phone_number}}
                                    <br>
                                @endif
                            </address>
                        </div>
                        <div class="col-lg-3 offset-lg-6 text-right">
                            <h4>Invoice Info</h4>
                            <p>
                                <a class="font-size-20" href="javascript:void(0)">#{{$order->merchant_order_id}}</a>
                                <br> @lang("$string_file.f_cap_to"):
                                <br>
                                <span class="font-size-20">{{$order->User->UserName}}</span>
                            </p>
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
                                <br>
                                @if(Auth::user()->demo == 1)
                                    <abbr title="Phone">@lang("$string_file.phone"):</abbr>&nbsp;
                                    &nbsp;{{ "********".substr($order->User->UserPhone,-3)}}
                                @else
                                    <abbr title="Phone">@lang("$string_file.phone"):</abbr>&nbsp;
                                    &nbsp;{{$order->User->UserPhone}}
                                @endif
                                <br>
                            </address>
                            <span>@lang("$string_file.date") : {{date('M d, Y')}}</span>
                            <br>

                        </div>
                    </div>
                    <h3>@lang("$string_file.product_details")</h3>
                    <hr>
                    <div class="page-invoice-table table-responsive">
                        <table class="table table-hover text-right">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>@lang("$string_file.product_description")</th>
                                <th class="text-right">@lang("$string_file.quantity")</th>
                                <th class="text-right">@lang("$string_file.price")</th>
                                {{--                                <th class="text-right">@lang("$string_file.discount")</th>--}}
                                <th class="text-right">@lang("$string_file.amount")</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $sn = 1 @endphp
                            @foreach($order->OrderDetail as $product)
                                <tr>
                                    <td class="text-center">
                                        {{$sn}}
                                    </td>
                                    <td class="text-center">
                                        {{$product->Product->product_name}}
                                    </td>
                                    <td>
                                        {{$product->quantity}}
                                    </td>
                                    <td>
                                        {{$product->price}}
                                    </td>
                                    {{--                                <td>--}}
                                    {{--                                    {{$product->discount}}--}}
                                    {{--                                </td>--}}
                                    <td>
                                        {{$product->total_amount}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right clearfix">
                        <div class="float-right">
                            <p>@lang("$string_file.sub_total") :
                                <span>{{$order->cart_amount}}</span>
                            </p>
                            <p>@lang("$string_file.delivery_charge") :
                                <span>{{$order->PriceCard->base_fare}}</span>
                            </p>
                            <p class="page-invoice-amount">@lang("$string_file.grand_total"):
                                <span>{{$order->final_amount_paid}}</span>
                            </p>
                        </div>
                    </div>

                    {{--                    <div class="text-right">--}}
                    {{--                        <button type="submit" class="btn btn-animate btn-animate-side btn-primary">--}}
                    {{--                <span><i class="icon wb-shopping-cart" aria-hidden="true"></i> Proceed--}}
                    {{--                  to payment</span>--}}
                    {{--                        </button>--}}
                    {{--                        <button type="button" class="btn btn-animate btn-animate-side btn-default btn-outline"--}}
                    {{--                                onclick="javascript:window.print();">--}}
                    {{--                            <span><i class="icon wb-print" aria-hidden="true"></i> Print</span>--}}
                    {{--                        </button>--}}
                    {{--                    </div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection