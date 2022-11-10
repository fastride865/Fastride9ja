@extends('taxicompany.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ URL::previous() }}">
                            <button type="button" data-toggle="tooltip" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="fa fa-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-exchange" aria-hidden="true"></i>
                        {{ $user->first_name." ".$user->last_name }}'s @lang("$string_file.wallet_transaction")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.transaction_type")</th>
                            <th>@lang("$string_file.payment_method")</th>
                            <th>@lang("$string_file.amount")</th>
                            <th>@lang("$string_file.narration")</th>
                            <th>@lang("$string_file.receipt_number")</th>
                            <th>@lang("$string_file.description")</th>
                            <th>@lang("$string_file.created_at")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $wallet_transactions->firstItem() @endphp
                        @foreach($wallet_transactions as $wallet_transaction)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    @if($wallet_transaction->type == 1)
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
                                    {{ $wallet_transaction->amount }}
                                </td>
                                <td>
                                    @if($wallet_transaction->type == 1)
                                        @if($wallet_transaction->platfrom == 1)
                                            @lang('admin.message531')
                                        @else
                                            @lang("$string_file.money_added")
                                        @endif

                                    @else
                                        @lang("$string_file.money_spent"){{ $wallet_transaction->booking_id }}
                                    @endif
                                </td>
                                <td>
                                    {{ $wallet_transaction->receipt_number }}
                                </td>
                                <td>
                                    {{ $wallet_transaction->description }}
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($wallet_transaction->created_at, null,null,$user->Merchant) !!}
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1 float-right">{{ $wallet_transactions->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection