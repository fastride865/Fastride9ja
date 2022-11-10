@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('corporate.index')}}">
                            <button type="button" title=""
                                    class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-google-wallet" aria-hidden="true"></i>
                        {{ $corporate->corporate_name }} @lang("$string_file.transactions")</h3>
                </header>
                <div class="panel-body">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
{{--                            <th>@lang("$string_file.transaction_type")</th>--}}
{{--                            <th>@lang("$string_file.payment")</th>--}}
{{--                            <th>@lang("$string_file.receipt_no")</th>--}}
{{--                            <th>@lang("$string_file.amount")</th>--}}
{{--                            <th>@lang("$string_file.narration")</th>--}}
{{--                            <th>@lang("$string_file.transaction_type")</th>--}}
                            <th>@lang("$string_file.transaction_type")</th>
                            <th>@lang("$string_file.payment")</th>
                            <th>@lang("$string_file.receipt_number")</th>
                            <th>@lang("$string_file.amount")</th>
                            <th>@lang("$string_file.narration")</th>
                            <th>@lang("$string_file.date")</th>
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
                                    @if($wallet_transaction->payment_method == 1)
                                        @lang("$string_file.cash")
                                    @else
                                        @lang("$string_file.non_cash")
                                    @endif
                                </td>
                                <td>
                                    {{$wallet_transaction->receipt_number}}
                                </td>
                                <td>
                                    {{ $wallet_transaction->amount }}
                                </td>
                                <td>
                                    @if($wallet_transaction->transaction_type == 1)
                                        @lang("$string_file.money_added")
                                    @elseif($wallet_transaction->booking_id != null)
                                        @lang("$string_file.money_spent"){{ $wallet_transaction->booking_id }}
                                    @elseif($wallet_transaction->subscription_package_id != null)
                                        @lang("$string_file.money_spent_on_package"){{ $wallet_transaction->subscription_package_id }}
                                    @else
                                        ------------
                                    @endif
                                </td>
                                <td>
                                    {{ $wallet_transaction->created_at }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $wallet_transactions, 'data' => []])
                </div>
            </div>
        </div>
    </div>
@endsection
