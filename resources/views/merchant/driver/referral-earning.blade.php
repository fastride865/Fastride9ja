@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('driver.index')}}">
                            <button type="button" title="@lang("$string_file.all_drivers")"
                                    class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-wallet" aria-hidden="true"></i>
                        {{ $driver->first_name." ".$driver->last_name }} @lang('admin.referral_earning')
                    </h3>
                </header>
                <div class="panel-body">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.ride_id")</th>
                            <th>@lang("$string_file.amount")</th>
                            <th>@lang("$string_file.payment_status") </th>
                            <th>@lang("$string_file.transaction_type")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($referralEarning as $referralEarn)
                            <tr>
                                <td>{{ $referralEarn->booking_id }}</td>
                                <td>{{ $referralEarn->amount }}</td>
                                <td>
                                    @if($referralEarn->payment_status == 1)
                                        @lang("$string_file.paid")
                                    @else
                                        @lang("$string_file.unpaid")
                                    @endif
                                </td>
                                <td>
                                    {{ $referralEarn->created_at }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $referralEarning, 'data' => []])
{{--                    <div class="pagination1" style="float:right;">{{$referralEarning->links()}}</div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection