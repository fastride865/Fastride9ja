@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                        <div class="panel-actions">
                            @if(Auth::user()->demo == 1)
                                <a href="">
                                    <button type="button" class="btn btn-icon btn-primary mr-1 float-right" style="margin:10px" data-original-title="@lang("$string_file.export_excel")" data-toggle="tooltip"><i
                                                class="fa fa-download"></i>
                                    </button>
                                </a>
                            @else
                                <a href="{{route('excel.driverwalletreport',$data)}}">
                                    <button type="button" class="btn btn-icon btn-primary mr-1 float-right" style="margin:10px" data-original-title="@lang("$string_file.export_excel")" data-toggle="tooltip"><i
                                                class="fa fa-download"></i>
                                    </button>
                                </a>
                            @endif
                        </div>
                    <h3 class="panel-title">
                        <i class=" wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.driver_wallet_report")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('report.driver.wallet.search') }}">
                        <div class="table_search">
                            <div class="row">
                                <div class="col-md-2 col-xs-4 form-group ">
                                    <div class="input-group">
                                        <select class="form-control" name="parameter"
                                                id="parameter"
                                                required>
                                            <option>@lang("$string_file.search_by")</option>
                                            <option value="1">@lang("$string_file.name")</option>
                                            <option value="2">@lang("$string_file.email")</option>
                                            <option value="3">@lang("$string_file.phone")</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 col-xs-6 form-group ">
                                    <div class="input-group">
                                        <input type="text" name="keyword"
                                               placeholder="@lang("$string_file.enter_text")"
                                               class="form-control col-md-12 col-xs-12" required>
                                    </div>
                                </div>
                                <div class="col-sm-2  col-xs-12 form-group ">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"
                                                                                     aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang("$string_file.sn")</th>
                                <th>@lang("$string_file.driver_details")</th>
                                <th>@lang("$string_file.transaction_type")</th>
                                <th>@lang("$string_file.payment")</th>
                                <th>@lang("$string_file.receipt_no")</th>
                                <th>@lang("$string_file.from")</th>
                                <th>@lang("$string_file.amount")</th>
                                <th>@lang("$string_file.narration")</th>
                                <th>@lang("$string_file.transaction_type")</th>
                                <th>@lang("$string_file.description")</th>
                                <th>@lang("$string_file.wallet_money")</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $sr = $wallet_transactions->firstItem() @endphp
                            @foreach($wallet_transactions as $wallet_transaction)
                                <tr>
                                    <td>{{ $sr  }}</td>
                                    @if(Auth::user()->demo == 1)
                                        <td>
                                            {{ "********".substr($wallet_transaction->Driver->fullName, -2) }}
                                            <br>
                                            {{"********".substr( $wallet_transaction->Driver->phoneNumber, -2) }}
                                            <br>
                                            {{ "********".substr($wallet_transaction->Driver->email, -2) }}
                                        </td>
                                    @else
                                        <td>
                                            {{ $wallet_transaction->Driver->fullName }}
                                            <br>
                                            {{ $wallet_transaction->Driver->phoneNumber }}
                                            <br>
                                            {{ $wallet_transaction->Driver->email }}
                                        </td>
                                    @endif
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
                                        @switch ($wallet_transaction->platform)
                                            @case(1)
                                            @lang("$string_file.admin")
                                            @break
                                            @case(2)
                                            @lang("$string_file.application");
                                            @break
                                            @case(3)
                                            @lang("$string_file.web")
                                            @break
                                        @endswitch
                                    </td>
                                    <td>
                                        {{ $wallet_transaction->amount }}
                                    </td>
                                    <td>
                                        @if($wallet_transaction->transaction_type == 1)
                                            @lang("$string_file.money_added")
                                        @else
                                            @lang("$string_file.money_spent"){{ $wallet_transaction->booking_id }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $wallet_transaction->created_at->toDateString() }}
                                        <br>
                                        {{ $wallet_transaction->created_at->toTimeString() }}
                                    </td>
                                    <td>
                                        {{ $wallet_transaction->description }}
                                    </td>
                                    <td>
                                        {{ $wallet_transaction->Driver->wallet_money }}
                                    </td>
                                </tr>
                                @php $sr++  @endphp
                            @endforeach
                            </tbody>
                        </table>
                    @include('merchant.shared.table-footer', ['table_data' => $wallet_transactions, 'data' => $data])
                </div>
            </div>
        </div>
    </div>
@endsection