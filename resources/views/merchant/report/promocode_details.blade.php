@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ URL::previous() }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-flag" aria-hidden="true"></i>
                        @lang('admin.promo_code_details')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <label>@lang("$string_file.promo_code") : {{ $promo_code->promoCode }}</label><br>
                            <label>@lang("$string_file.total_usage") : {{ $promo_code->TotalUses }}</label>
                        </div>
                    </div>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.ride_id")</th>
                            <th>@lang("$string_file.user_details")</th>
                            <th>@lang("$string_file.current_status")</th>
                            <th>@lang("$string_file.payment")</th>
                            <th>@lang("$string_file.created_at")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $bookings->firstItem() @endphp
                        @foreach($bookings as $booking)
                            <tr>
                                <td>{{ $sr  }}</td>
                                <td>{{ $booking->merchant_booking_id }}</td>
                                @if(Auth::user()->demo == 1)
                                    <td>
                                            <span class="long_text">
                                                {{ "********".substr($booking->User->UserName,-2) }}
                                                <br>
                                                {{ "********".substr($booking->User->UserPhone,-2) }}
                                                <br>
                                                {{ "********".substr($booking->User->email,-2) }}
                                            </span>
                                    </td>
                                @else
                                    <td>
                                            <span class="long_text">
                                                {{ $booking->User->UserName }}
                                                <br>
                                                {{ $booking->User->UserPhone }}
                                                <br>
                                                {{ $booking->User->email }}
                                            </span>
                                    </td>
                                @endif
                                <td style="text-align: center">
                                    @switch($booking->booking_status)
                                        @case(1001)
                                        @lang('admin.new_booking')
                                        <br>
                                        {{ $booking->updated_at->toTimeString() }}
                                        @break
                                        @case(1012)
                                        @lang('admin.message291')
                                        <br>

                                        {{ $booking->updated_at->toTimeString() }}
                                        @break
                                        @case(1002)
                                        @lang('admin.driverAccepted')

                                        <br>
                                        {{ $booking->updated_at->toTimeString() }}
                                        @break
                                        @case(1003)
                                        @lang('admin.driverArrived')
                                        <br>
                                        {{ $booking->updated_at->toTimeString() }}
                                        @break
                                        @case(1004)
                                        @lang('admin.begin')
                                        <br>
                                        {{ $booking->updated_at->toTimeString() }}
                                        @break
                                        @case(1005)
                                        @lang('admin.completedBooking')
                                        <br>
                                        {{ $booking->updated_at->toTimeString() }}
                                        @break
                                        @case(1006)
                                        @lang('admin.message48')
                                        @break
                                        @case(1007)
                                        @lang('admin.message49')
                                        @break
                                        @case(1008)
                                        @lang('admin.message50')
                                        @break
                                        @case(1016)
                                        @lang('admin.autoCancel')
                                        <br>
                                        {{ $booking->updated_at->toTimeString() }}
                                        @break
                                        @case(1018)
                                        @lang('admin.driver-no-show')
                                        @break
                                    @endswitch
                                </td>
                                <td>
                                    {{ $booking->PaymentMethod->payment_method }}
                                </td>
                                <td>
                                    {{ $booking->created_at->toDateString() }}
                                    <br>
                                    {{ $booking->created_at->toTimeString() }}
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $bookings, 'data' => []])
                    {{--                     <div class="pagination1 float-right">{{ $bookings->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection