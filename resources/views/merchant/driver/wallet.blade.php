@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('excel.driverwalletreport',['driver_id' => $driver->id])}}" >
                            <button type="button" data-toggle="tooltip" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                <i class="wb-download" title="@lang("$string_file.export_excel")"></i>
                            </button>
                        </a>
                        <a href="{{ route('driver.index') }}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.all_drivers")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-google-wallet" aria-hidden="true"></i>
                        {{ $driver->first_name." ".$driver->last_name }} @lang("$string_file.wallet_transaction")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.amount")</th>
                            <th>@lang("$string_file.transaction_type")</th>
                            <th>@lang("$string_file.payment_method")</th>
                            <th>@lang("$string_file.narration")</th>
                            <th>@lang("$string_file.receipt_number")</th>
                            <th>@lang("$string_file.date")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sn = 1; @endphp
                        @foreach($wallet_transactions as $wallet_transaction)
                            <tr>
                                <td>{{$sn}}</td>
                                <td>
                                    @if($wallet_transaction->transaction_type == 1)
                                        <span class="green-500">
                                             {{ $wallet_transaction->amount }}
                                        </span>
                                    @else
                                        <span class="red-500">
                                             {{ $wallet_transaction->amount }}
                                        </span>
                                    @endif

                                </td>
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
                                    @php $id = NULL; @endphp
                                    @if(!empty($wallet_transaction->narration))
                                        @php
                                             $booking_id = !empty($wallet_transaction->Booking) ? $wallet_transaction->Booking->merchant_booking_id : NULL;
                                             $order_id = !empty($wallet_transaction->Order) ? $wallet_transaction->Order->merchant_order_id: NULL;
                                             $handyman_order_id = !empty($wallet_transaction->HandymanOrder) ? $wallet_transaction->HandymanOrder->merchant_order_id: NULL;
                                        @endphp
                                    @if(!empty($booking_id))
                                            @php
                                                $id = $booking_id;
                                            @endphp
                                    @elseif($order_id)
                                        @php
                                            $id = $order_id;
                                        @endphp
                                    @elseif($handyman_order_id)
                                    @php
                                        $id = $handyman_order_id;
                                    @endphp
                                    @endif
                                    {{get_narration_value("DRIVER",$wallet_transaction->narration,$driver->merchant_id,$id)}}
                                    @endif
                                </td>
                                <td>
                                    {{$wallet_transaction->receipt_number}}
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($wallet_transaction->created_at, $wallet_transaction->Driver->CountryArea->timezone,null,$wallet_transaction->Driver->Merchant) !!}
                                </td>
                            </tr>
                            @php $sn++; @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $wallet_transactions, 'data' => []])
{{--                    <div class="pagination1 float-right">{{ $wallet_transactions->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection