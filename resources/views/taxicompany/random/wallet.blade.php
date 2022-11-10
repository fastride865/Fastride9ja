@extends('taxicompany.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content container-fluid">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        {{--                        <a href="{{route('excel.ratings')}}" data-toggle="tooltip">--}}
                        {{--                            <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">--}}
                        {{--                                <i class="wb-download" title="@lang("$string_file.export_excel")"></i>--}}
                        {{--                            </button>--}}
                        {{--                        </a>--}}
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-google-wallet" aria-hidden="true"></i>
                        @lang("$string_file.wallet_transaction")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.transaction_type")</th>
                            <th>@lang("$string_file.payment")</th>
                            <th>@lang("$string_file.receipt_no")</th>
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
                                        @lang('admin.wallet_money_spent_package'){{ $wallet_transaction->subscription_package_id }}
                                    @else
                                        ------------
                                    @endif
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($wallet_transaction->created_at, null,null,$wallet_transaction->merchant_id) !!}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1 float-right">{{ $wallet_transactions->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection