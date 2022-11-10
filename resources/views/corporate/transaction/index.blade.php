@extends('corporate.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('notransactionsexport'))
                <div class="alert dark alert-icon alert-info alert-dismissible"
                     role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message454')
                </div>

            @endif
            <div class="panel panel-bordered">
                <haeder class="panel-heading">
                    <div class="panel-actions">

                    </div>
                    <h3 class="panel-title">
                        <i class="fas fa-exchange-alt" aria-hidden="true"></i>
                        @lang('admin.message91')</h3>
                </haeder>
                <div class="panel-body container-fluid">
                    <form method="post" action="{{ route('corporate.transactions.search') }}">
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
                                           class="form-control col-md-12 col-xs-12 customDatePicker bg-this-color"
                                           id="datepickersearch">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="date1"
                                           placeholder="@lang("$string_file.to_date")" readonly
                                           class="form-control col-md-12 col-xs-12 customDatePicker bg-this-color"
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
                        <th>@lang("$string_file.service_area")</th>
                        <th>@lang("$string_file.user_details")</th>
                        <th>@lang("$string_file.driver_details")</th>
                        <th>@lang("$string_file.payment")</th>
                        <th>@lang("$string_file.total_amount")</th>
                        <th>@lang("$string_file.discount")</th>
                        @if($merchant->ApplicationConfiguration->sub_charge == 1)
                            <th>@lang('admin.SubCharge')</th>
                        @endif
                        @if($merchant->ApplicationConfiguration->time_charges == 1)
                            <th>@lang('admin.message763')</th>
                        @endif
                        @if($merchant->ApplicationConfiguration->tip_status == 1)
                            <th>@lang('admin.tip_charge')</th>
                        @endif
                        @if($merchant->BookingConfiguration->insurance_enable == 1)
                            <th>@lang("$string_file.insurance")</th>
                        @endif
                        @if($merchant->Configuration->toll_api == 1)
                            <th>@lang('admin.toll_charge')</th>
                        @endif
                        @if($merchant->cancel_charges == 1)
                            <th>@lang('admin.message712')</th>
                        @endif
                        <th>@lang("$string_file.travelled_distance")</th>
                        <th>@lang("$string_file.travelled_time")</th>
                        <th>@lang("$string_file.estimated_bill")</th>
                        <th>@lang("$string_file.date")</th> {{--created_at--}}
                        {{--//insurance,--}}
                        </thead>
                        <tbody>
                        {{-- TRANSACTIONS === Booking Object --}}
                        @foreach($transactions as $transaction)
                            <tr>
                                <td><a target="_blank" class="address_link"
                                       href="{{ route('corporate.booking.details',$transaction->id) }}">{{ $transaction->merchant_booking_id }}</a>
                                </td>
                                <td>{{ $transaction['id'] }}</td>
                                <td>
                                    @if($transaction->booking_type == 1)
                                        @lang("$string_file.ride_now")
                                    @else
                                        @lang("$string_file.ride")  @lang("$string_file.later")
                                    @endif

                                </td>
                                <td>{{ $transaction->CountryArea->CountryAreaName }}</td>
                                <td>
                                    @if(Auth::user()->Merchant->demo == 1)
                                        <span class="long_text">
                                                                    {{ "********".substr($transaction->User->UserName, -2) }}
                                                                    <br>
                                                                    {{ "********".substr($transaction->User->UserPhone, -2) }}
                                                                    <br>
                                                                    {{ "********".substr($transaction->User->email, -2) }}
                                                                    </span>
                                    @else
                                        <span class="long_text">
                                                                    {{ $transaction->User->UserName }}
                                                                    <br>
                                                                    {{ $transaction->User->UserPhone }}
                                                                    <br>
                                                                    {{ $transaction->User->email }}
                                                                    </span>
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user()->Merchant->demo == 1)
                                        <span class="long_text">
                                                                    {{ "********".substr($transaction->Driver['first_name']." ".$transaction->Driver['last_name'], -2) }}
                                                                    <br>
                                                                    {{ "********".substr($transaction->Driver['phoneNumber'], -2) }}
                                                                    <br>
                                                                    {{ "********".substr($transaction->Driver['email'], -2) }}
                                                                    </span>
                                    @else
                                        <span class="long_text">
                                                                    {{ $transaction->Driver['first_name']." ".$transaction->Driver['last_name'] }}
                                                                    <br>
                                                                    {{ $transaction->Driver['phoneNumber'] }}
                                                                    <br>
                                                                    {{ $transaction->Driver['email'] }}
                                                                    </span>
                                    @endif
                                </td>
                                <td>{{  $transaction->PaymentMethod->payment_method }}</td>
                                <td> @if(!empty($transaction['BookingTransaction'])) {{  $transaction->CountryArea->Country->isoCode." ".$transaction['BookingTransaction']['customer_paid_amount'] }}
                                    @else {{  $transaction->CountryArea->Country->isoCode." ".$transaction->final_amount_paid }} @endif
                                </td>
                                <td>@if(!empty($transaction['BookingTransaction'])) {{  $transaction->CountryArea->Country->isoCode." ".$transaction['BookingTransaction']['discount_amount'] }}
                                    @else {{  $transaction->CountryArea->Country->isoCode." ".$transaction['BookingDetail']['promo_discount'] }} @endif </td>

                                @if($merchant->ApplicationConfiguration->sub_charge == 1) <td> @if(!empty($transaction['BookingTransaction'])) {{ $transaction->CountryArea->Country->isoCode." ".$transaction['BookingTransaction']['surge_amount'] }} @endif </td> @endif
                                @if($merchant->ApplicationConfiguration->time_charges == 1) <td> @if(!empty($transaction['BookingTransaction'])){{  $transaction->CountryArea->Country->isoCode." ".$transaction['BookingTransaction']['extra_charges'] }} @endif </td>@endif
                                @if($merchant->ApplicationConfiguration->tip_status == 1) <td> @if(!empty($transaction['BookingTransaction'])) {{  $transaction->CountryArea->Country->isoCode." ".$transaction['BookingTransaction']['tip'] }} @endif </td> @endif
                                @if($merchant->BookingConfiguration->insurance_enable == 1) <td> @if(!empty($transaction['BookingTransaction'])) {{  $transaction->CountryArea->Country->isoCode." ".$transaction['BookingTransaction']['insurance_amount'] }} @endif </td> @endif
                                @if($merchant->Configuration->toll_api == 1) <td> @if(!empty($transaction['BookingTransaction'])) {{  $transaction->CountryArea->Country->isoCode." ".$transaction['BookingTransaction']['toll_amount'] }} @endif </td> @endif
                                @if($merchant->cancel_charges  == 1) <td> @if(!empty($transaction['BookingTransaction'])) {{  $transaction->CountryArea->Country->isoCode." ".$transaction['BookingTransaction']['cancellation_charge_applied'] }} @endif </td> @endif

                                <td>{{  $transaction->travel_distance }}</td>
                                <td>{{  $transaction->travel_time }}</td>
                                <td>{{  $transaction->CountryArea->Country->isoCode." ".$transaction->estimate_bill }}</td>
                                <td>{{  $transaction->created_at->toDateString() }}
                                <br>
                                {{ $transaction->created_at->toTimeString() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1 float-right">{{ $transactions->links() }}</div>
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
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <input type="reset" class="btn btn-outline-secondary btn" data-dismiss="modal"
                           value="@lang("$string_file.close")">
                </div>
            </div>
        </div>
    </div>
@endsection