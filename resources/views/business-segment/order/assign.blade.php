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
                        @if($driver_not)
                            @lang("$string_file.change_delivery_person")
                        @else
                            @lang("$string_file.assign_order_to_delivery_candidate")
                        @endif
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>'','id'=>'','url'=>route('business-segment.order-assign-to-driver')]) !!}
                    {!! Form::hidden('order_id',$order->id) !!}
                    {!! Form::hidden('order_status',$order->order_status) !!}

                    <h5>@lang("$string_file.order_details") : - </h5>

                    <div class="row p-4 mb-2 bg-blue-grey-100 ml-15 mr-15">
                    <div class="col-md-3">
                        <strong>  @lang("$string_file.order_details") </strong> : <br>
                       @lang("$string_file.order_id") : #{{ $order->merchant_order_id }} <br>
                       @lang("$string_file.product") :
                        @php $product_detail = $order->OrderDetail; $products = "";@endphp
                        @foreach($product_detail as $product)
                             @php $weight =  isset($product->ProductVariant->weight) ? $product->ProductVariant->weight : "";
                                             $unit = isset($product->weight_unit_id) ? $product->WeightUnit->WeightUnitName : "";
                                             $unit = !empty($weight)  ? $product->quantity.' x '.$weight.' '.$unit : $product->quantity.$unit;
                                        @endphp
                            {{ $product->quantity.' '.$unit.' '.$product->Product->Name($order->merchant_id)}},<br>
                        @endforeach
                    </div>
                    <div class="col-md-3">
                        <strong>@lang("$string_file.payment_details")</strong> : <br>
                        {{trans("$string_file.mode").": ". $order->PaymentMethod->payment_method}}<br>
                        {{trans($string_file.".cart_amount").': '.$order->cart_amount}} <br>
                        {{trans("$string_file.delivery_charge").': '. $order->delivery_amount }} <br>
                        {{trans("$string_file.tax").': '. ($order->tax) }} <br>
                        @lang("$string_file.grand_total") :  {{ $order->CountryArea->Country->isoCode.' '.$order->final_amount_paid}}
                    </div>
                    <div class="col-md-5">
                       <strong> @lang("$string_file.user_details") </strong> : {!! is_demo_data($order->User->first_name,$order->Merchant).' '.is_demo_data($order->User->last_name,$order->Merchant).',<br>'. $order->drop_location !!}
                    </div>
                    </div>
                    <h5>@lang("$string_file.delivery_drivers") : - </h5>
                    <table id="" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.assign")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.estimate_distance")</th>
                            <th>@lang("$string_file.rating")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1; $drivers = $arr_driver->count(); @endphp
                        @if($drivers > 0)
                         @foreach($arr_driver as $driver)
                            <tr>
                                <td>
                                    <input type="checkbox" name="driver_id[]" value="{{$driver->id}}" class="assign-driver" driver-id="{{$driver->id}}" order-id="{{$order->id}}">
                                    {!! Form::hidden('distance['.$driver->id.']',$driver->distance) !!}
                                </td>
                                <td>
                                    {{ is_demo_data($driver->first_name,$order->Merchant).' '.is_demo_data($driver->last_name,$order->Merchant) }}<br>
                                    {{ is_demo_data($driver->email,$order->Merchant)}}<br>
                                    {{ is_demo_data($driver->phoneNumber,$order->Merchant)}}
                                </td>
                                <td>
                                    @if(!empty($driver->distance)){{ number_format($driver->distance,2)}} @else 0 @endif @lang("$string_file.km")
                                </td>
                                <td>
{{--                                    @if ($driver->rating == "0.0")--}}
{{--                                        @lang("$string_file.not_rated_yet")--}}
{{--                                    @else--}}
{{--                                        @while($driver->rating >0)--}}
{{--                                            @if($driver->rating >0.5)--}}
{{--                                                <img src="{{ view_config_image("static-images/star.png") }}"--}}
{{--                                                     alt='Whole Star'>--}}
{{--                                            @else--}}
{{--                                                <img src="{{ view_config_image('static-images/halfstar.png') }}"--}}
{{--                                                     alt='Half Star'>--}}
{{--                                            @endif--}}
{{--                                            @php $driver->rating--; @endphp--}}
{{--                                        @endwhile--}}
{{--                                    @endif--}}
                                    @if($driver->rating) {{ $driver->rating}} @else @lang("$string_file.not_rated_yet") @endif
                                </td>
{{--                                <td>--}}
{{--                                    @if($driver->is_favourite) {{$driver->is_favourite}} @else @lang("$string_file.no") @endif--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                     @php $order_detail = $driver->Order; $products = "";@endphp--}}
{{--                                     @foreach($order_detail as $order)--}}
{{--                                     <a href="{{route('business-segment.order.detail',$order->id)}}" target="_blank">@lang("$string_file.order_no") : #{{$order->id}}</a> ,--}}
{{--                                     @endforeach--}}
{{--                                </td>--}}
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center"> @lang("$string_file.no_driver_available")</td>
                            </tr>

                        @endif
                        </tbody>
                    </table>
                    @if( $drivers > 0)
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i>@lang("$string_file.send")
                        </button>
                    </div>
                    @endif
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
</script>
@endsection
