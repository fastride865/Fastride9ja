@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('success'))
                <div class="alert dark alert-icon alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('success') }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('excel.payment.transactions')}}">
                            <button type="button" class="btn btn-icon btn-primary mr-1 float-right" style="margin:10px" data-original-title="@lang("$string_file.export_excel")" data-toggle="tooltip"><i
                                        class="fa fa-download"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.payment_gateway_transactions")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                            <tr>
                                <th>@lang("$string_file.sn")</th>
                                <th>@lang("$string_file.payment_gateway")</th>
                                <th>@lang("$string_file.type")</th>
                                <th>@lang("$string_file.user_details")</th>
                                <th>@lang("$string_file.driver_details")</th>
                                <th>@lang("$string_file.booking_details")</th>
                                <th>@lang("$string_file.amount")</th>
                                <th>@lang("$string_file.transaction_id")</th>
                                <th>@lang("$string_file.gateway_reference_id")</th>
                                <th>@lang("$string_file.payment_method")</th>
                                <th>@lang("$string_file.card_details")</th>
                                <th>@lang("$string_file.payment_status")</th>
                                <th>@lang("$string_file.gateway_message")</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sr = 1 @endphp
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $sr }}</td>
                                    <td>{{ $transaction->PaymentOption->name ?? '-----' }}</td>
                                    <td>
                                        @switch($transaction->status)
                                            @case(1)
                                                @lang("$string_file.user")
                                                @break
                                            @case(2)
                                                @lang("$string_file.driver")
                                                @break
                                            @case(3)
                                                @lang("$string_file.booking")
                                                @break
                                            @default
                                                -----
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if(isset($transaction->User))
                                            <a href="{{ route('users.show',$transaction->user_id) }}"
                                               class="hyperLink">
                                                <span class="long_text">
                                                    {{ $transaction->User->UserName }}
                                                    <br>
                                                    {{ $transaction->User->UserPhone }}
                                                    <br>
                                                    {{ $transaction->User->email }}
                                                </span>
                                            </a>
                                        @else
                                            -----
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($transaction->Driver))
                                            <a href="{{ route('driver.show',$transaction->driver_id) }}"
                                               class="hyperLink">
                                                <span class="long_text">
                                                    {{ $transaction->Driver->fullName }}
                                                    <br>
                                                    {{ $transaction->Driver->phoneNumber }}
                                                    <br>
                                                    {{ $transaction->Driver->email }}
                                                </span>
                                            </a>
                                        @else
                                            -----
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($transaction->Booking))
                                            <a href="{{ route('merchant.booking.details',$transaction->booking_id) }}"
                                               class="hyperLink">
                                                <span class="long_text">
                                                    #{{ $transaction->Booking->merchant_booking_id }}
                                                </span>
                                            </a>
                                        @else
                                            -----
                                        @endif
                                    </td>
                                    <td>{{ $transaction->amount ?? '-----' }}</td>
                                    <td>{{ $transaction->payment_transaction_id }}</td>
                                    <td>{{ $transaction->reference_id }}</td>
                                    <td>{{ $transaction->payment_mode ?? '-----' }}</td>
                                    <td>
                                        @if(isset($transaction->card_id))
                                            <button class="badge badge-info border-0 view_card" id="{{$transaction->id.$transaction->card_id}}"
                                                    card_id="{{$transaction->card_id}}" user="{{isset($transaction->User)}}">
                                                @lang("$string_file.view_card_details")
                                            </button>
                                            <div id="{{$transaction->id.$transaction->card_id}}"></div>
                                        @else
                                            -----
                                        @endif
                                    </td>
                                    <td>
                                        @switch($transaction->request_status)
                                            @case(1)
                                            @lang("$string_file.pending")
                                            @break
                                            @case(2)
                                            @lang("$string_file.successful")
                                            @break
                                            @case(3)
                                            @lang("$string_file.failed")
                                            @break
                                            @case(4)
                                            @lang("$string_file.unknown")
                                            @break
                                            @default
                                            -----
                                            @break
                                        @endswitch
                                    </td>
                                    <td>{{ $transaction->status_message ?? '-----' }}</td>
                                </tr>
                                @php $sr++ @endphp
                            @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $transactions, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script>
        $('.view_card').on('click', function () {
            var unique_id = $(this).attr("id");
            var card_id = $(this).attr("card_id");
            var is_user = $(this).attr("user");
            var token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                type: "POST",
                data: {
                    "card_id": card_id,
                    "is_user": is_user,
                },
                url: "{{ route('merchant.get_card_details') }}",
                success: function (data) {
                    console.log(data);
                    $("#" + unique_id).html(data);
                }, error: function (err) {
                    console.log(err);
                    $("#" + unique_id).text("No Data");
                }
            });
        });
    </script>
@endsection
