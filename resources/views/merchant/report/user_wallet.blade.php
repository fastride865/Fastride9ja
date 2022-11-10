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
                                <a href="{{route('excel.userwalletreport',$data)}}">
                                    <button type="button" class="btn btn-icon btn-primary mr-1 float-right" style="margin:10px" data-original-title="@lang("$string_file.export_excel")" data-toggle="tooltip"><i
                                                class="fa fa-download"></i>
                                    </button>
                                </a>
                            @endif
                        </div>
                    <h3 class="panel-title">
                        <i class=" wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.users_wallet_report")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('report.user.wallet.search') }}">
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
                                <th>@lang("$string_file.user_details")</th>
                                <th>@lang("$string_file.ride_id")</th>
                                <th>@lang("$string_file.amount")</th>
                                <th>@lang("$string_file.user_type")</th>
                                <th>@lang("$string_file.date")</th>
                                <th>@lang("$string_file.from")</th>
                                <th>@lang("$string_file.narration")</th>
                                <th>@lang("$string_file.reference_no")</th>
                                <th>@lang("$string_file.description")</th>
                                <th>@lang("$string_file.wallet_money")</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $sr = $wallet_transactions->firstItem() @endphp
                            @foreach($wallet_transactions as $transaction)
                                <tr>
                                    <td>{{ $sr  }}</td>
                                    @if(Auth::user()->demo == 1)
                                        <td>
                                            {{ "********".substr($transaction->User->UserName, -2) }}
                                            <br>
                                            {{"********".substr( $transaction->User->UserPhone, -2) }}
                                            <br>
                                            {{ "********".substr($transaction->User->email, -2) }}
                                        </td>
                                    @else
                                        <td>
                                            {{ $transaction->User->UserName }}
                                            <br>
                                            {{ $transaction->User->UserPhone }}
                                            <br>
                                            {{ $transaction->User->email }}
                                        </td>
                                    @endif
                                    <td>
                                        @if($transaction->booking_id)
                                            {{ $transaction->booking_id }}
                                        @else
                                            ------------
                                        @endif
                                    </td>
                                    <td>{{ $transaction->amount  }}</td>
                                    <td>
                                        @if($transaction->type == 1)
                                            @lang("$string_file.credit")
                                        @else
                                            @lang("$string_file.debit")
                                        @endif
                                    </td>
                                    <td>{{ $transaction->created_at->toDateString() }}
                                    <br>
                                    {{ $transaction->created_at->toTimestring() }}</td>
                                    <td>
                                        @if($transaction->platfrom == 1)
                                            @lang("$string_file.admin")
                                        @else
                                            @lang("$string_file.application")
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->type == 1)
                                            @lang("$string_file.money_added")
                                        @else
                                            @lang("$string_file.money_spent") {{ $transaction->booking_id }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $transaction->receipt_number }}
                                    </td>
                                    <td>
                                        {{ $transaction->description }}
                                    </td>
                                    <td>
                                        {{ $transaction->User->wallet_balance }}
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
    <div class="modal fade text-left" id="sendNotificationModelUser" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33">@lang("$string_file.send_notification") </label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('merchant.sendsingle-user') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-body">
                        <label>@lang("$string_file.title"): </label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="title"
                                   name="title"
                                   placeholder="@lang("$string_file.title")" required>
                        </div>

                        <label>@lang("$string_file.message"): </label>
                        <div class="form-group">
                           <textarea class="form-control" id="message" name="message"
                                     rows="3"
                                     placeholder="@lang("$string_file.message")"></textarea>
                        </div>

                        <label>@lang("$string_file.image"): </label>
                        <div class="form-group">
                            <input type="file" class="form-control" id="image"
                                   name="image"
                                   placeholder="@lang("$string_file.image")" required>
                            <input type="hidden" name="persion_id" id="persion_id">
                        </div>

                        <label>@lang("$string_file.url"): </label>
                        <div class="form-group">
                            <input type="url" class="form-control" id="url"
                                   name="url"
                                   placeholder="@lang("$string_file.url")" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-outline-primary btn-lg" value="@lang("$string_file.send")">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="addMoneyModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33">@lang("$string_file.add_money_in_driver_wallet")</label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('merchant.user.add.wallet') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <label>@lang("$string_file.payment_method"): </label>
                        <div class="form-group">
                            <select class="form-control" name="payment_method" id="payment_method" required>
                                <option value="1">@lang("$string_file.cash")</option>
                                <option value="2">@lang("$string_file.non_cash")</option>
                            </select>
                        </div>

                        <label>@lang("$string_file.amount"): </label>
                        <div class="form-group">
                            <input type="text" name="amount" placeholder="@lang("$string_file.amount")"
                                   class="form-control" required>
                            <input type="hidden" name="add_money_user_id" id="add_money_driver_id">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-outline-primary btn-lg" value="@lang("$string_file.add")">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection