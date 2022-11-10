@extends('merchant.layouts.main')
@section('content')
    <style>
        #ecommerceRecentride .table-row .card-block .table td {
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
            <div class="panel panel-brideed">
                <header class="panel-heading">
                    <div class="panel-actions">
                    </div>
                    <h3 class="panel-title">
                        <i class="icon wb-list" aria-hidden="true"></i>
                        @lang("$string_file.ride_earning_statistics_of") <span class="blue-500">{{ is_demo_data($driver->first_name.' '.$driver->last_name,$driver->Merchant) }} ({{ is_demo_data($driver->phoneNumber,$driver->Merchant) }})</span>
                        </span>
                    </h3>
                    <div class="panel-actions">
                        <a href="{{route('merchant.taxi.earning.export',$arr_search)}}">
                            <button type="button" title="@lang("$string_file.export_rides")"
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
                                    <span class="ml-15 font-weight-400">@lang("$string_file.rides")</span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100">{{$total_rides}}</span>
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
                                    <span class="ml-15 font-weight-400">@lang("$string_file.ride_amount")</span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100">{{isset($earning_summary['ride_amount']) ? $currency.$earning_summary['ride_amount'] : 0}}</span>
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
                                        <span class="font-size-20 font-weight-100">{{isset($earning_summary['merchant_earning']) ? $currency.$earning_summary['merchant_earning'] : 0}}</span>
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
                                    <span class="ml-15 font-weight-400">@lang("$string_file.driver_earning")</span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100">{{isset($earning_summary['driver_earning']) ? $currency.$earning_summary['driver_earning'] : 0}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <!-- Third Row -->
                    <!-- Third Left -->
                    <div class="row">
                        <div class="col-lg-12" id="ecommerceRecentride">
                            <div class="card card-shadow table-row">
                                <div class="card-block bg-white table-responsive">
                                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>@lang("$string_file.sn")</th>
                                            <th>@lang("$string_file.ride_id")</th>
                                            <th>@lang("$string_file.driver_earning")</th>
                                            <th>@lang("$string_file.merchant_earning")</th>
                                            <th>@lang("$string_file.ride_amount")</th>
{{--                                            <th>@lang("$string_file.tip")</th>--}}
{{--                                            <th>@lang("$string_file.other_charges")</th>--}}
                                            <th>@lang("$string_file.created_at")</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(!empty($arr_rides))
                                            @php $sr = $arr_rides->firstItem(); $user_name = ''; $user_phone = ''; $user_email = '';
                                                        $driver_name = '';$driver_email = '';
                                                         $tax_amount =    !empty($ride->tax) ? $ride->tax : 0;
                                            @endphp
                                            @foreach($arr_rides as $ride)
                                                @if(!empty($ride->BookingTransaction))
                                                    @php $transaction = $ride->BookingTransaction;
                                                     $currency = $ride->CountryArea->Country->isoCode;
                                                    @endphp
                                                @endif
                                                <tr>
                                                    <td>{{$sr}}</td>
                                                    <td>
                                                        <a href="{{route('merchant.booking.invoice',$ride->id)}}">{{ $ride->merchant_booking_id }}</a>
                                                    </td>
                                                    <td>
                                                        @if(!empty($transaction))
                                                            {{$transaction->driver_earning }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(!empty($transaction))
                                                            {{$transaction->company_earning}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $ride->final_amount_paid}}
                                                    </td>
{{--                                                    <td>--}}
{{--                                                        {{$ride->tip_amount }}--}}
{{--                                                    </td>--}}
{{--                                                    <td>--}}
{{--                                                        ----}}
{{--                                                    </td>--}}
                                                    <td>
                                                        @lang("$string_file.at") {{date('H:i',strtotime($ride->created_at))}},
                                                        {{date_format($ride->created_at,'D, M d, Y')}}
                                                    </td>
                                                </tr>
                                                @php $sr++  @endphp
                                            @endforeach
                                        </tbody>
                                        @endif
                                    </table>
                                        @include('merchant.shared.table-footer', ['table_data' => $arr_rides, 'data' => $arr_search])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
