@extends('merchant.layouts.main')
@section('content')
    @php $merchant_helper = new \App\Http\Controllers\Helper\Merchant(); @endphp
    <style>
        #ecommerceRecentOrder .table-row .card-block .table td {
            vertical-align: middle !important;
            height: 15px !important;
            font-size: 14px !important;
            padding: 8px 8px !important;
        }

        .dataTables_filter, .dataTables_info {
            display: none;
        }
    </style>
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
{{--                        <a href="{{route('merchant.delivery-services-report.export',$arr_search)}}">--}}
{{--                            <button type="button" title="@lang("$string_file.export_orders")"--}}
{{--                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i--}}
{{--                                        class="wb-download"></i>--}}
{{--                            </button>--}}
{{--                        </a>--}}
                    </div>
                    <h3 class="panel-title">
                        <i class="icon wb-list" aria-hidden="true"></i>
                        @lang("$string_file.merchant")  @lang("$string_file.net_income_statistics")
                        </span>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
{{--                    {!! $search_view !!}--}}
                    <hr>

                    <!-- Third Row -->
                    <!-- Third Left -->
                    <div class="row">
                        <div class="col-lg-12" id="ecommerceRecentOrder">
                            <div class="card card-shadow table-row">
                                {{--                                <h4 class="example-title">@lang("$string_file.all_orders") </h4>--}}
                                <div class="card-block bg-white table-responsive">
                                    <table id="customDataTable"
                                           class="display nowrap table table-hover table-stripedw-full table-bordered report_table"
                                           style="width:100%">
                                        <thead>
                                        <tr class="text-center report_table_row_heading">
                                            <th>@lang("$string_file.sn")</th>
                                            <th>@lang("$string_file.id")</th>
                                            <th>@lang("$string_file.segment")</th>
                                            <th>@lang("$string_file.paid_by_customer")</th>
                                            <th>@lang("$string_file.partner_commission")</th>
                                            <th>@lang("$string_file.commission_received")</th>
                                            <th>@lang("$string_file.other_income") <br> @lang("$string_file.if_any")</th>
                                            <th>@lang("$string_file.discount") <br> @lang("$string_file.if_any")</th>
                                            <th>@lang("$string_file.other_expenses") <br> @lang("$string_file.if_any")</th>
                                            <th>@lang("$string_file.payable_amount") <br> @lang("$string_file.if_any")</th>
                                            <th>@lang("$string_file.net_income")</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php $sn = $all_transactions->firstItem();$delivered_service = NULL;@endphp
                                        @if($all_transactions->count() > 0)
                                            @foreach($all_transactions as $transaction)
                                                @php $booking = false;$order = false;$handyman_order = false; $net_income = 0; $total_payable = 0; $other_income = 0; @endphp
                                                @if(!empty($transaction->booking_id))
                                                    @php $delivered_service = $transaction->Booking; $booking = true;
                                                    $delivered_service_id = $delivered_service->merchant_booking_id;
                                                    $net_income = $transaction->company_gross_total;
                                                    $partner_commission = $transaction->driver_total_payout_amount;
                                                    $other_income = $transaction->tax_amount;
                                                    @endphp
                                                @elseif(!empty($transaction->order_id))
                                                    @php $delivered_service = $transaction->Order; $order = true;
                                                    $delivered_service_id = $delivered_service->merchant_order_id;
                                                    $net_income = ($transaction->company_gross_total - $transaction->driver_total_payout_amount);
                                                    $total_payable = $transaction->driver_total_payout_amount;
                                                    $other_income = $transaction->Order->delivery_amount;
                                                    $partner_commission = $transaction->business_segment_total_payout_amount;
                                                @endphp
                                                @elseif(!empty($transaction->handyman_order_id))
                                                    @php $delivered_service = $transaction->HandymanOrder; $handyman_order = true;
                                                    $delivered_service_id = $delivered_service->merchant_order_id;
                                                    $net_income = $transaction->company_gross_total;
                                                    $other_income = $transaction->tax_amount;
                                                    $partner_commission = $transaction->driver_total_payout_amount;
                                                    @endphp
                                                @endif
                                                @php $segment_name = $delivered_service->Segment->Name($merchant_id); @endphp
                                             <tr>
                                             <td>{{$sn}}</td>
                                             <td>{{$delivered_service_id}}</td>
                                             <td>{{$segment_name}}</td>
                                             <td>{{$transaction->customer_paid_amount}}</td>
                                             <td>{{$partner_commission}}</td>
                                             <td>{{$transaction->company_earning}}</td>
                                             <td>{{$other_income}}</td>
                                             <td>{{$transaction->discount_amount}}</td>
                                             <td>0</td>
                                             <td>{{$total_payable}}</td>
                                             <td>{{$net_income}}</td>
                                             </tr>
                                                @php $sn = $sn+1;@endphp
                                            @endforeach
                                        </tbody>
                                        @endif
                                    </table>
                                    @include('merchant.shared.table-footer', ['table_data' => $all_transactions, 'data' =>$arr_search])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
