@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('driver.index') }}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.all_drivers")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-car" aria-hidden="true"></i>
                        {{ $driver->first_name." ".$driver->last_name }}'s
                        @if(!empty($bookings))
                        @lang("$string_file.rides")
                        @elseif($food_grocery_orders)
                        @lang("$string_file.orders")
                        @elseif($handyman_orders)
                        @lang("$string_file.bookings")
                        @endif
                    </h3>
                </header>
                @if(!empty($bookings))
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.ride_id")</th>
                            <th>@lang("$string_file.ride_type")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.service_detail")</th>
                            <th>@lang("$string_file.payment_details")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.current_status")</th>
                            <th>@lang("$string_file.pickup_drop")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $bookings->firstItem() @endphp
                        @foreach($bookings as $booking)
                            <tr>
                                <td>{{$sr}}</td>
                                <td>{{ $booking->merchant_booking_id }}</td>
                                <td>
                                    @if($booking->booking_type == 1)
                                        @lang("$string_file.ride_now")
                                    @else
                                        @lang("$string_file.ride_later")
                                    @endif
                                </td>
                                @if(Auth::user()->demo == 1)
                                    <td>
                                        <span class="long_text">
                                            {{ "********".substr($booking->User->UserName,-2) }}
                                            <br>
                                            {{ "********".substr($booking->User->UserPhone,-2) }}
                                            <br>
                                            {{ "********".substr($booking->User->email,-2) }}
                                        </span>
                                    </td>
                                @else
                                    <td>
                                        <span class="long_text">
                                            {{ $booking->User->UserName }}
                                            <br>
                                            {{ $booking->User->UserPhone }}
                                            <br>
                                            {{ $booking->User->email }}
                                        </span>
                                    </td>
                                @endif
                                <td>
                                    @switch($booking->platform)
                                        @case(1)
                                        @lang("$string_file.application")
                                        @break
                                        @case(2)
                                        @lang("$string_file.admin")
                                        @break
                                        @case(3)
                                        @lang("$string_file.web")
                                        @break
                                    @endswitch
                                    @lang("$string_file.ride_configuration")
                                    <br>
                                 <span>@lang("$string_file.service_type")</span> : {{ isset($booking->ServiceType) ? $booking->ServiceType->serviceName : '' }} <br>
                                 <span>@lang("$string_file.vehicle_type")</span> : {{ $booking->VehicleType->VehicleTypeName }}
                                </td>
                                <td style="text-align: center">
                                    {{trans("$string_file.mode").": ". $booking->PaymentMethod->payment_method}}<br>
                                    {{ trans("$string_file.total").": ". $booking->CountryArea->Country->isoCode.' '.$booking->final_amount_paid}}
                                    <br>
                                </td>
                                <td>
                                    @lang("$string_file.at") {{date_format($booking->created_at,'H:i a')}}
                                    <br>
                                    {{date_format($booking->created_at,'D, M d, Y')}}

                                </td>
                                <td> {{ $booking->CountryArea->CountryAreaName }}</td>
                                <td style="text-align: center">
                                    @if($booking->booking_status == 1005)
                                        <span class="badge badge-success font-weight-100">{{ $booking_status[$booking->booking_status] }}</span>
                                    @elseif(in_array($booking->booking_status,[1001,1012,1002,1003,1004]))
                                        <span class="badge btn-info font-weight-100">{{ $booking_status[$booking->booking_status] }}</span>
                                    @else
                                        <span class="badge badge-danger font-weight-100">{{ $booking_status[$booking->booking_status] }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($booking->pickup_location))
                                        <a title="{{ $booking->pickup_location }}"
                                           target="_blank"
                                           href="https://www.google.com/maps/place/{{ $booking->pickup_location }}" class="btn btn-icon btn-success ml-20"><i class="icon wb-map"></i></a>
                                    @endif
                                    @if(!empty($booking->drop_location))
                                        <a title="{{ $booking->drop_location }}"
                                           target="_blank"
                                           href="https://www.google.com/maps/place/{{ $booking->drop_location }}" class="btn btn-icon btn-danger ml-20"><i class="icon fa-tint"></i></a>
                                    @endif
                                </td>
                                <td>
                                    <a target="_blank" title="@lang("$string_file.service_detail")"
                                       href="{{ route('merchant.booking.details',$booking->id) }}"
                                       class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                        <span class="fa fa-info-circle" title="@lang("$string_file.service_detail")"></span>
                                    </a>
                                </td>
                            </tr>
                            @php $sr++; @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $bookings, 'data' => []])
                </div>
                @endif

                @if(!empty($food_grocery_orders))
                    <div class="panel-body container-fluid">
                        <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang("$string_file.sn")</th>
                                <th>@lang("$string_file.order_id")</th>
                                <th>@lang("$string_file.product_details")</th>
                                <th>@lang("$string_file.payment_details")</th>
                                <th>@lang("$string_file.user_details")</th>
                                <th>@lang("$string_file.created_at")</th>
                                <th>@lang("$string_file.current_status")</th>
                                <th>@lang("$string_file.action")</th>
                            </tr>
                            </thead>
                            <tbody>

                                @php $sr = $food_grocery_orders->firstItem(); $user_name = ''; $user_phone = ''; $user_email = '';
                                                        $driver_name = '';$driver_email = '';
                                @endphp
                                @foreach($food_grocery_orders as $order)
                                    @php
                                 $user_name = is_demo_data($order->User->UserName,$order->Merchant);
                                 $user_phone = is_demo_data($order->User->UserPhone,$order->Merchant);
                                 $user_email = is_demo_data($order->User->email,$order->Merchant);
                                    @endphp
                                    <tr>
                                        <td>{{$sr}}</td>
                                        <td>{{ $order->merchant_order_id }}</td>
                                        <td>
                                            @php $product_detail = $order->OrderDetail; $products = "";@endphp
                                            @foreach($product_detail as $product)
                                                 @php $weight =  isset($product->ProductVariant->weight) ? $product->ProductVariant->weight : "";
                                             $unit = isset($product->weight_unit_id) ? $product->WeightUnit->WeightUnitName : "";
                                             $unit = !empty($weight)  ? $product->quantity.' x '.$weight.' '.$unit : $product->quantity.$unit;
                                        @endphp
                                                {{ $product->quantity.' '.$unit.' '.$product->Product->Name($order->merchant_id)}},<br>
                                            @endforeach
                                        </td>
                                        <td>
                                            {{trans("$string_file.mode").": ". $order->PaymentMethod->payment_method}}<br>
                                            {{trans($string_file.".cart_amount").': '.$order->cart_amount}} <br>
                                            {{trans("$string_file.delivery_charge").': '. ($order->final_amount_paid - $order->cart_amount) }} <br>
                                            @lang("$string_file.grand_total") :  {{ $order->CountryArea->Country->isoCode.' '.$order->final_amount_paid}}
                                            <br>
                                        </td>

                                        <td>
                                            {{$user_name}} <br>
                                            {{$user_phone}} <br>
                                            {{$user_email}} <br>
                                        </td>

                                        <td>
                                            @lang("$string_file.at") {{date_format($order->created_at,'H:i a')}}
                                            <br>
                                            {{date_format($order->created_at,'D, M d, Y')}}
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
                                        <td>
                                            <a title=""
                                               href="{{ route('driver.order.detail',$order->id) }}"
                                               class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                                <span class="fa fa-info-circle" title="@lang("$string_file.order_details")"></span>
                                            </a>
                                        </td>
                                    </tr>
                                    @php $sr++  @endphp
                                @endforeach
                            </tbody>
                        </table>
                        @include('merchant.shared.table-footer', ['table_data' => $food_grocery_orders, 'data' => []])
                        {{--                    <div class="pagination1 float-right">{{ $bookings->links() }}</div>--}}
                    </div>
                @endif
                @if(!empty($handyman_orders))
                    <div class="panel-body container-fluid">
                        <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                               style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang("$string_file.sn")</th>
                                <th>@lang("$string_file.order_id")</th>
                                <th>@lang("$string_file.user_details")</th>
                                <th>@lang("$string_file.driver_details")</th>
                                <th>@lang("$string_file.service_detail")</th>
                                <th>@lang("$string_file.service_area")</th>
                                <th>@lang("$string_file.current_status")</th>
                                <th>@lang("$string_file.created_at")</th>
                                <th>@lang("$string_file.pickup_drop")</th>
                                <th>@lang("$string_file.action")</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $sr = $handyman_orders->firstItem();
                            $user_name = ''; $user_phone = ''; $user_email = '';
                            $driver_name = '';$driver_email = '';
                            @endphp
                            @foreach($handyman_orders as $order)
                                <tr>
                                    <td>{{ $sr }}</td>
                                    <td>{{ $order->merchant_order_id }}</td>
                                    <td>
                                        {{$order->User->UserName}} <br>
                                        {{$order->User->UserPhone}} <br>
                                        {{$order->User->email}} <br>
                                    </td>
                                    <td>
                                        @if(!empty($order->Driver->id))
                                            {{$order->Driver->last_name}} <br>
                                            {{$order->Driver->UserPhone}} <br>
                                            {{$order->Driver->email}} <br>
                                        @else
                                            @lang("$string_file.not_assigned_yet")
                                        @endif
                                    </td>
                                    <td>
                                        @php $arr_services = []; $order_details = $order->HandymanOrderDetail;
                                        foreach($order_details as $details){
                                            $arr_services[] = $details->ServiceType->serviceName;
                                        }
                                        @endphp
                                        {{trans("$string_file.mode").': '.$order->PaymentMethod->payment_method}} <br>
                                        {{trans("$string_file.amount_paid").': '.$order->CountryArea->Country->isoCode.' '.$order->cart_amount}} <br>
                                        {{trans($string_file.'.booking').' '.trans("$string_file.date").': '.$order->booking_date}} <br>
                                        {{trans("$string_file.service_type").': '}}
                                        @foreach($order_details as $details)
                                            {{$details->ServiceType->serviceName}}, <br>
                                        @endforeach
                                        @lang("$string_file.segment") : <strong>{{$order->Segment->name}}</strong>
                                        <br>
                                    </td>
                                    <td> {{ $order->CountryArea->CountryAreaName }}</td>
                                    <td style="text-align: center">
                                        {{ $handyman_status[$order->order_status] }}
                                    </td>
                                    <td>
                                        {{ $order->created_at->toDateString() }}
                                        <br>
                                        {{ $order->created_at->toTimeString() }}
                                    </td>
                                    <td>
                                        <a title="{{ $order->drop_location }}" target="_blank"
                                           href="https://www.google.com/maps/place/{{ $order->drop_location }}"
                                           class="btn btn-icon btn-danger ml-20">
                                            <i class="icon fa-tint"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a target="_blank" title=""
                                           href="{{route('merchant.handyman.order.detail',$order->id)}}" class="btn btn-sm btn-info menu-icon btn_money action_btn"><span
                                                    class="fa fa-info-circle" title="@lang("$string_file.booking_details")"></span></a>
                                    </td>
                                </tr>
                                @php $sr++  @endphp
                            @endforeach
                            </tbody>
                        </table>
                        @include('merchant.shared.table-footer', ['table_data' => $handyman_orders, 'data' => []])
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection