@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('business-segment.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title">
                        <i class=" icon fa-exchange" aria-hidden="true"></i>
                        @lang("$string_file.wallet_transaction")</h3>
                </header>
                <div class="panel-body">
                    <h4>@lang("$string_file.wallet_money") : {{ $business_segment->Country->isoCode.' '.$business_segment->wallet_amount}}</h4>
                    {!! $search_view !!}
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.transaction_type")</th>
                            <th>@lang("$string_file.order_id")</th>
                            <th>@lang("$string_file.payment")</th>
                            <th>@lang("$string_file.amount")</th>
                            <th>@lang("$string_file.narration")</th>
                            <th>@lang("$string_file.created_at")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($wallet_transactions as $wallet_transaction)
                            <tr>
                                <td>
                                    @if($wallet_transaction->transaction_type == 1)
                                        @lang("$string_file.credit")
                                    @else
                                       @lang("$string_file.debit")
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($wallet_transaction->order_id))
                                        <a target="_blank" title="@lang("$string_file.order_details")"
                                           href="{{route('business-segment.order.detail',$wallet_transaction->order_id)}}">{{$wallet_transaction->order_id}}</a>
                                    @else
                                        --
                                    @endif
                                </td>
                                <td>
                                    @if($wallet_transaction->payment_method == 1)
                                        @lang("$string_file.cash")
                                    @else
                                        @lang("$string_file.non_cash")
                                    @endif
                                </td>
                                <td>
                                    {{ $wallet_transaction->BusinessSegment->Country->isoCode.' '.$wallet_transaction->amount }}
                                </td>
                                <td>
                                    {{get_narration_value('BUSINESS_SEGMENT',$wallet_transaction->narration,$wallet_transaction->merchant_id,$wallet_transaction->order_id,NULL)}}
{{--                                    @switch ($wallet_transaction->narration)--}}
{{--                                        @case(1)--}}
{{--                                            @lang('api.message44')--}}
{{--                                            @break--}}
{{--                                        @case(2)--}}
{{--                                            @lang('api.order_amount_added_by_Admin')--}}
{{--                                            @break--}}
{{--                                        @case(3)--}}
{{--                                            @lang('api.order_commission_deducted')--}}
{{--                                            @break--}}
{{--                                        @case(4)--}}
{{--                                            @lang('api.cashout_amount_deducted')--}}
{{--                                            @break--}}
{{--                                        @case(5)--}}
{{--                                            @lang('api.cashout_request_rejected_refund_amount')--}}
{{--                                            @break--}}
{{--                                        @default--}}
{{--                                            --------------}}
{{--                                    @endswitch--}}
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($wallet_transaction->created_at,null, null, $wallet_transaction->BusinessSegment->Merchant) !!}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @include('business-segment.shared.table-footer', ['table_data' => $wallet_transactions, 'data' => []])
                </div>
            </div>
        </div>
    </div>
@endsection