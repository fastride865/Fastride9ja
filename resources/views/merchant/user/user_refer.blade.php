@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('users.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-file" aria-hidden="true"></i>
                        @lang('admin.referral_report') @lang('admin.of') {{$user->first_name}} {{$user->last_name}}</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.ride_id")</th>
                            <th>@lang("$string_file.receiver")</th>
                            <th>@lang("$string_file.receiver_type") </th>
                            <th>@lang("$string_file.discount_type") </th>
                            <th>@lang("$string_file.discount_value") </th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.date")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1 @endphp
                        @if(count($referral_details) > 0)
                            @foreach($referral_details as $referral_detail)
                                <tr>
                                    <td>{{ $referral_detail->receiver_details['id'] }}</td>
                                    <td>
                                        {{ $referral_detail->receiver_details['name'] }}
                                        <br>
                                        {{ $referral_detail->receiver_details['phone'] }}
                                        <br>
                                        {{ $referral_detail->receiver_details['email'] }}
                                    </td>
                                    <td>{{ $referral_detail->receiverType }}</td>
                                    <td>
                                        @if($referral_detail->offer_type == 1)
                                            @lang("$string_file.free_ride")
                                        @elseif($referral_detail->offer_type == 2)
                                            @lang("$string_file.discount")
                                        @else
                                            @lang("$string_file.fixed_amount")
                                        @endif
                                    </td>
                                    <td>
                                        {{$referral_detail->offer_value}} @if($referral_detail->offer_type == 2) % @endif
                                    </td>
                                    <td>
                                        @if($referral_detail->referral_available == 1)
                                            Available
                                        @else
                                            Offer Redeem
                                        @endif
                                    </td>
                                    <td>{{$referral_detail->created_at->toDateString()}}
                                    <br>
                                    {{ $referral_detail->created_at->toTimeString() }}</td>
                                </tr>
                                @php $sr++; @endphp
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $referral_details, 'data' => []])
{{--                    <div class="pagination1 float-right">{{ $referral_details->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection

