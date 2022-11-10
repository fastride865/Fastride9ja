@extends('merchant.layouts.main')
@section('content')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #section-to-print, #section-to-print * {
                visibility: visible;
            }
        }
    </style>
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->add_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <a href="{{route('admin.send-invoice',$order->id)}}">
                            <button class="btn btn-icon btn-warning float-right" style="margin:10px;width:115px;"><i class="icon fa-send"></i>
                                @lang("$string_file.invoice")
                            </button>
                        </a>
                        <button class="btn btn-icon btn-warning float-right print_invoice" style="margin:10px;width:115px;" ><i class="icon wb-print" aria-hidden="true"></i>
                            @lang("$string_file.print")
                        </button>
                    </div>
                    <h3 class="panel-title"><i class="fa-user" aria-hidden="true"></i>
                        @lang("$string_file.booking_details") # {{$order->merchant_order_id}}
                    </h3>
                </header>
                <div id="section-to-print" class="panel">
                <div class="panel-body container-fluid printableArea">
                    <div class="row">
                        <div class="col-lg-6 col-xs-12">
                            <h5>@lang("$string_file.user_details") : - </h5>
                            <div class="card my-2 shadow  bg-white h-240">
                                <div class="row p-3 mt-30 ml-30">
                                    <div class="col-md-4 col-xs-6">
                                        <img height="100" width="100" class="rounded-circle"
                                             src="@if($order->User->UserProfileImage) {{ get_image($order->User->UserProfileImage,'user') }}@else{{get_image()}}@endif">
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            <span class="font-size-20">{{$order->User->UserName}}</span>
                                            <br>
                                            @if(Auth::user()->demo == 1)
                                                <span title="Phone">@lang("$string_file.phone"):</span>&nbsp;
                                                &nbsp;{{ "********".substr($order->User->UserPhone,-3)}}
                                            <br>
                                                <span title="Phone">@lang("$string_file.email"):</span>&nbsp;
                                                &nbsp;{{ "********".substr($order->User->email,-3)}}
                                            @else
                                                <span title="Phone">@lang("$string_file.phone"):</span>&nbsp;
                                                &nbsp;{{$order->User->UserPhone}}
                                                <br>
                                                <span title="Phone">@lang("$string_file.email"):</span>&nbsp;
                                                &nbsp;{{$order->User->email}}
                                            @endif
                                            <br>
                                        </p>
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
                        @php $currency= $order->CountryArea->Country->isoCode; @endphp
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
                                            <span title="">@lang("$string_file.grand_total"):</span>&nbsp;{{ $currency.$order->final_amount_paid}}
                                        </p>
                                    </div>
                                </div>
                                <div class="row p-3 mt-30 ml-30">
                                    <div class="col-md-4 col-xs-6 text-info">
                                        <i class="icon fa-money"></i> @lang("$string_file.price_type")
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            @php $arr_price_type = get_price_card_type("web","BOTH",$string_file); @endphp
                                            <span class="font-size-20">{{isset($arr_price_type[$order->price_type]) ? $arr_price_type[$order->price_type] : "" }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="row p-3 ml-30">
                                    <div class="col-md-4 col-xs-6 text-info">
                                        <i class="icon fa-comments"></i> @lang("$string_file.current_status")
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
                                        <i class="icon fa-calendar fa-2x text-gray-300"></i>@lang("$string_file.time_slot")
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            {{$order->ServiceTimeSlotDetail->slot_time_text}}
                                            , {!! date(getDateTimeFormat($order->Merchant->datetime_format,2),strtotime($order->booking_date)) !!}
                                            <br>
                                        </p>
                                    </div>
                                </div>
                                <div class="row p-3 ml-30">
                                    <div class="col-md-4 col-xs-6 text-success">
                                        <i class="icon fa-calendar fa-2x text-gray-300"></i> @lang("$string_file.created_at")
                                    </div>
                                    @php $created_at = $order->created_at; @endphp
                                    @if(!empty($order->CountryArea->timezone))
                                        @php $created_at = convertTimeToUSERzone($created_at, $order->CountryArea->timezone, null, $order->Merchant); @endphp
                                    @endif
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            {!! $created_at !!}
                                            <br>
                                        </p>
                                    </div>
                                </div>
                                <div class="row mt-10 ml-20">
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                        @if(in_array($order->order_status,[4,6,7]))
                        <div class="col-lg-6 col-xs-12">
                            <h5>@lang("$string_file.driver_details") : - </h5>
                            <div class="card my-2 shadow  bg-white h-240">
                                <div class="row p-3 mt-30 ml-30">
                                    <div class="col-md-4 col-xs-6">
                                        <img height="100" width="100" class="rounded-circle"
                                             src="@if($order->Driver->profile_image) {{ get_image($order->Driver->profile_image,'driver') }}@else{{get_image()}}@endif">
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            <span class="font-size-20">{{$order->Driver->first_name.' '.$order->Driver->last_name}}</span>
                                            <br>
                                            @if(Auth::user()->demo == 1)
                                                <span title="Phone">@lang("$string_file.phone"):</span>&nbsp;
                                                &nbsp;{{ "********".substr($order->Driver->phoneNumber,-3)}}
                                            <br>
                                                <span title="Phone">@lang("$string_file.email"):</span>&nbsp;
                                                &nbsp;{{ "********".substr($order->Driver->email,-3)}}
                                            @else
                                                <span title="Phone">@lang("$string_file.phone"):</span>&nbsp;
                                                &nbsp;{{$order->Driver->phoneNumber}}
                                                <br>
                                                <span title="Phone">@lang("$string_file.email"):</span>&nbsp;
                                                &nbsp;{{$order->Driver->email}}
                                            @endif
                                            <br>
                                        </p>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <hr>
                    <h5>@lang("$string_file.service_details") : - </h5>

                    <div class="page-invoice-table table-responsive">
                        <table class="table table-hover text-right">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>@lang("$string_file.service_type")</th>
                                <th class="text-right">@lang("$string_file.quantity")</th>
                                <th class="text-right">@lang("$string_file.price")</th>
                                <th class="text-right">@lang("$string_file.amount")</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $sn = 1 @endphp
                            @foreach($order->HandymanOrderDetail as $services)
                                <tr>
                                    <td class="text-center">
                                        {{$sn}}
                                    </td>
                                    <td class="text-center">
                                        {{$services->ServiceType->ServiceName($order->merchant_id)}}
                                    </td>
                                    <td>
                                        {{$services->quantity}}
                                    </td>
                                    <td>
                                        {{$services->price}}
                                    </td>
                                    <td>
                                        {{$services->total_amount}}
                                    </td>
                                </tr>
                            @endforeach
                            @php $col_span = 4; @endphp
                            @if($order->price_type == 2 && $order->order_status != 7 && $order->is_order_completed !=1)
                                <tr>
                                    <td colspan="{{$col_span}}">@lang("$string_file.cart_amount")</td>
                                    <td>{{$currency.$order->hourly_amount.' '.trans("$string_file.hourly")}}</td>
                                </tr>
                                <tr>
                                    <td colspan="{{$col_span}}"><b>@lang("$string_file.note")</b></td>
                                    <td><b>{{trans("$string_file.handyman_order_payment")}}</b></td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="{{$col_span}}">@lang("$string_file.tax")</td>
                                    <td>{{trans("$string_file.tax").' : '.$order->tax_per}} % <br>
                                        {{trans("$string_file.tax")}}
                                        : {{$currency.$order->tax}}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="{{$col_span}}">@lang("$string_file.cart_amount")</td>
                                    <td>{{$currency.$order->cart_amount}}</td>
                                </tr>
                                <tr>
                                    <td colspan="{{$col_span}}">@lang("$string_file.total_booking_amount")</td>
                                    <td>{{$currency.$order->total_booking_amount}}</td>
                                </tr>
                                <tr>
                                    <td colspan="{{$col_span}}">@lang("$string_file.minimum_booking_amount")</td>
                                    <td>{{$currency.$order->minimum_booking_amount}} (@lang("$string_file.tax_included")
                                        )
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="{{$col_span}}">@lang("$string_file.final_amount_paid") </td>
                                    <td>{{$currency.$order->final_amount_paid}}</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    @if(!empty($order->DriverGallery) && $order->DriverGallery->count() > 0)
                        @php $images = $order->DriverGallery; @endphp
                        <h5>@lang("$string_file.booking_image") : - </h5>
                        <div class="row">
                            @foreach($images as $image)
                                <div class="col-md-2">
                                    <div class="example">
                                        <img class="rounded" width="150" height="150"
                                             src="{{ get_image($image->image_title,'driver_gallery') }}" alt="...">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
@endsection
@section('js')
    <script src="{{ asset('js/jquery.PrintArea.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            $(".print_invoice").click(function(){
                var mode = 'popup'; //popup
                var close = mode == "popup";
                var options = { mode : mode, popClose : true, popHt : 900, popWd: 900, };
                $(".printableArea").printArea( options );
            });
        });
    </script>
@endsection