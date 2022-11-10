@extends('business-segment.layouts.main')
@section('content')
    <style>
        .a_text{
            color: #76838f;
        }
    </style>
    <div class="page">
        <div class="page-content">
            <div class="mr--10 ml--10">
                <div class="row" style="margin-right: 0rem;margin-left: 0rem">
                    <!-- First Row -->
                        <div class="col-12 col-md-12 col-sm-12">
                            <!-- Example Panel With Heading -->
                            <div class="panel panel-bordered">
                                <div class="panel-heading">
                                    <div class="panel-actions"></div>
                                    <h3 class="panel-title">@lang("$string_file.site_statistics")</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="{{route("business-segment.earning")}}">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-primary"
                                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="fa fa-money"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$string_file.earning")</span>
{{--                                                        <div class="content-text text-center mb-0">--}}
                                                            <span class="font-size-18 font-weight-100 pl-100">{{$earnings}}</span>
{{--                                                        </div>--}}
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="#">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-primary"
                                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="fa fa-money"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$string_file.wallet_money")</span>
{{--                                                        <div class="content-text text-center mb-0">--}}
                                                            <span class="font-size-18 font-weight-100 pl-100">{{$wallet_money}}</span>
{{--                                                        </div>--}}
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="{{route('business-segment.product.index')}}">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-warning"
                                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="fa fa-window-maximize"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$string_file.products")</span>
{{--                                                        <div class="content-text text-center mb-0">--}}
                                                            <span class="font-size-18 font-weight-100 pl-100">{{$products}}</span>
{{--                                                        </div>--}}
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Example Panel With Heading -->
                        <div class="col-12 col-md-12 col-sm-12">
                            <!-- Example Panel With Heading -->
                            <div class="panel panel-bordered">
                                <div class="panel-heading">
                                    <div class="panel-actions"></div>
                                    <h3 class="panel-title">@lang("$string_file.order_statistics") (@lang("$string_file.total") : {{$all_orders}})  </h3>
                                </div>
                                <div class="panel-body">
{{--                                    <div class="row">--}}
{{--                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">--}}
{{--                                            <a href="{{route('business-segment.order')}}">--}}
{{--                                                <div class="card card-shadow black" style="margin-bottom:0.243rem">--}}
{{--                                                    <div class="card-block bg-white p-20">--}}
{{--                                                        <button type="button" class="btn btn-floating btn-sm btn-info"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">--}}
{{--                                                            <i class="icon fa-calculator"></i>--}}
{{--                                                        </button>--}}
{{--                                                        <span class="ml-10 font-weight-400">@lang("$string_file.total")</span>--}}
{{--                                                            <span class="font-size-18 font-weight-100 pl-100">{{$all_orders}}</span>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </a>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">--}}
{{--                                            <a href="{{route('business-segment.completed-order')}}">--}}
{{--                                                <div class="card card-shadow" style="margin-bottom:0.243rem">--}}
{{--                                                    <div class="card-block bg-white p-20">--}}
{{--                                                        <button type="button" class="btn btn-floating btn-sm btn-success"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">--}}
{{--                                                            <i class="icon wb-check"></i>--}}
{{--                                                        </button>--}}
{{--                                                        <span class="ml-10 font-weight-400">@lang("$string_file.completed")</span>--}}
{{--                                                        --}}{{--                                                        <div class="content-text text-center mb-0">--}}
{{--                                                        <span class="font-size-18 font-weight-100 pl-100">{{ $completed_orders }}</span>--}}
{{--                                                        --}}{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 col-sm-12 info-panel">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-warning"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="icon fa-road"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$string_file.new")</span>
                                                        <span class="font-size-14 font-weight-100 pl-100">{{ $new_orders }}</span>
                                                        <a href="{{route('business-segment.today-order')}}"><span class="font-size-14 font-weight-100 pl-100 a_text">@lang("$string_file.today") : {{ $today_orders }}</span></a>
                                                        @if($segment_slug !="FOOD")
                                                         <a href="{{route('business-segment.upcoming-order')}}"><span class="font-size-14 font-weight-100 pl-100 a_text">@lang("$string_file.upcoming") : {{ $upcoming_orders }}</span></a>
                                                        @endif
                                                    </div>
                                                </div>
                                        </div>
                                        <div class="col-xl-12 col-md-12 col-sm-12 info-panel">
{{--                                            <a href="#" title="@lang("$string_file.select_from_left_menu")">--}}
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-success"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="icon fa-circle-o-notch"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$string_file.on_going")</span>
                                                            <span class="font-size-14 font-weight-100 pl-100">{{ $on_going_orders }}</span>
                                                        <a href="{{route('business-segment.pending-process-order')}}"><span class="font-size-14 font-weight-100 pl-100 a_text">@lang("$string_file.process") : {{ $pending_process_orders }}</span></a>
                                                        <a href="{{route('business-segment.pending-pick-order-verification')}}"><span class="font-size-14 font-weight-100 pl-100 a_text">@lang("$string_file.verification") : {{ $pending_verification }}</span></a>
                                                        <a href="{{route('business-segment.order-ontheway')}}"><span class="font-size-14 font-weight-100 pl-100 a_text">@lang("$string_file.ontheway") : {{ $ontheway }}</span></a>
                                                    </div>
                                                </div>
{{--                                            </a>--}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 col-sm-12 info-panel">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-danger"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="icon fa-times"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$string_file.expired")</span>
                                                        <span class="font-size-14 font-weight-100 pl-100">{{ $total_expired_orders }}</span>
                                                        <a href="{{route('business-segment.rejected-order')}}"><span class="font-size-14 font-weight-100 pl-100 a_text">@lang("$string_file.rejected") : {{ $rejected_orders }}</span></a>
                                                        <a href="{{route('business-segment.cancelled-order')}}"><span class="font-size-14 font-weight-100 pl-100 a_text">@lang("$string_file.cancelled") : {{ $cancelled_orders }}</span></a>
                                                        <a href="{{route('business-segment.expired-order')}}"><span class="font-size-14 font-weight-100 pl-100 a_text">@lang("$string_file.auto_expired") : {{ $auto_expired_orders }}</span></a>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 col-sm-12 info-panel">
                                            <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                <div class="card-block bg-white p-20">
                                                    <button type="button" class="btn btn-floating btn-sm btn-success"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                        <i class="icon fa-history"></i>
                                                    </button>
                                                    <span class="ml-10 font-weight-400">@lang("$string_file.history")</span>
                                                    <span class="font-size-14 font-weight-100 pl-100">{{ $history_orders }}</span>
                                                    <a href="{{route('business-segment.delivered-order')}}"><span class="font-size-14 font-weight-100 pl-100 a_text">@lang("$string_file.delivered") : {{ $delivered_orders }}</span></a>
                                                    <a href="{{route('business-segment.completed-order')}}"><span class="font-size-14 font-weight-100 pl-100 a_text">@lang("$string_file.completed") : {{ $completed_orders }}</span></a>

                                                </div>
                                            </div>
                                        </div>
                                        {{--                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">--}}
                                        {{--                                            <a href="{{route('business-segment.cancelled-order')}}">--}}
                                        {{--                                                <div class="card card-shadow" style="margin-bottom:0.243rem">--}}
                                        {{--                                                    <div class="card-block bg-white p-20">--}}
                                        {{--                                                        <button type="button" class="btn btn-floating btn-sm btn-danger"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">--}}
                                        {{--                                                            <i class="icon fa-times"></i>--}}
                                        {{--                                                        </button>--}}
                                        {{--                                                        <span class="ml-10 font-weight-400">@lang("$string_file.cancelled")</span>--}}
                                        {{--                                                        --}}{{--                                                        <div class="content-text text-center mb-0">--}}
                                        {{--                                                        <span class="font-size-18 font-weight-100 pl-100">{{ $cancelled_orders }}</span>--}}
                                        {{--                                                        --}}{{--                                                        </div>--}}
                                        {{--                                                    </div>--}}
                                        {{--                                                </div>--}}
                                        {{--                                            </a>--}}
                                        {{--                                        </div>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection
