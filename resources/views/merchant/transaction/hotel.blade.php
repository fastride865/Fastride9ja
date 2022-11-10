@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
           @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('hotels.index') }}">
                            <button type="button" class="btn btn-icon btn-success mr-1 float-right" style="margin:10px"><i
                                        class="fa fa-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-exchange" aria-hidden="true"></i>
                        @lang("$string_file.hotel_transaction")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="post" action="{{ route('merchant.hotel.transactions.search', [$hotel->id]) }}">
                        @csrf
                        <div class="table_search row">
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="booking_id"
                                           placeholder="@lang("$string_file.ride_id")"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="rider"
                                           placeholder="@lang("$string_file.user_details")"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="driver"
                                           placeholder="@lang("$string_file.driver_details")"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>

                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="date"
                                           placeholder="@lang("$string_file.from_date")" readonly
                                           class="form-control col-md-12 col-xs-12 datepickersearch bg-this-color"
                                           id="datepickersearch">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="date1"
                                           placeholder="@lang("$string_file.to_date")" readonly
                                           class="form-control col-md-12 col-xs-12 datepickersearch bg-this-color"
                                           id="datepickersearch">
                                </div>
                            </div>


                            <div class="col-sm-1  col-xs-12 form-group ">
                                <button class="btn btn-primary" type="submit" name="seabt12"><i
                                            class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                            <thead>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.ride_id")</th>
                            <th>@lang("$string_file.ride_type")</th>
                            <th>@lang("$string_file.area")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.payment")</th>
                            <th>@lang("$string_file.hotel_commission")</th>
                            <th>@lang("$string_file.travelled_distance")</th>
                            <th>@lang("$string_file.travelled_time")</th>
                            <th>@lang("$string_file.estimate_bill")</th>
                            <th>@lang("$string_file.date")</th>
                            </thead>
                            <tbody>
                            @php $s = 0; @endphp
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ ++$s }}</td>
                                    <td>{{ $transaction->id }}</td>
                                    <td>
                                        @if($transaction->booking_type == 1)
                                            @lang("$string_file.ride_now")
                                        @else
                                            @lang("$string_file.ride")  @lang("$string_file.later")
                                        @endif

                                    </td>
                                    <td>{{ $transaction->CountryArea->CountryAreaName }}</td>
                                    <td><span class="long_text">
                                                                {{ $transaction->User->UserName }}
                                                                <br>
                                                                {{ $transaction->User->UserPhone }}
                                                                <br>
                                                                {{ $transaction->User->email }}
                                                                </span>
                                    </td>
                                    <td><span class="long_text">
                                                                {{ $transaction->Driver->first_name." ".$transaction->Driver->last_name }}
                                                                <br>
                                                                {{ $transaction->Driver->phoneNumber }}
                                                                <br>
                                                                {{ $transaction->Driver->email }}
                                                                </span>
                                    </td>
                                    <td>{{  $transaction->PaymentMethod->payment_method }}</td>
                                    <td>{{  $transaction->CountryArea->Country->isoCode." ".$transaction->BookingTransaction->hotel_earning }}</td>
                                    <td>{{  $transaction->travel_distance }}</td>
                                    <td>{{  $transaction->travel_time }}</td>
                                    <td>{{  $transaction->CountryArea->Country->isoCode." ".$transaction->estimate_bill }}</td>
                                    <td>{{  $transaction->created_at->toDayDateTimeString() }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @include('merchant.shared.table-footer', ['table_data' => $transactions, 'data' => []])
{{--                    <div class="pagination1 float-right">{{ $transactions->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="detailBooking" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b>@lang("$string_file.transaction")</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <input type="reset" class="btn btn-outline-secondary btn" data-dismiss="modal"
                           value="@lang("$string_file.close")">
                </div>
            </div>
        </div>
    </div>
@endsection