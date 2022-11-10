@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('driver.index') }}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.all_drivers")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-user-plus" aria-hidden="true"></i>
                        @lang("$string_file.referral_report_of") {{$driver->first_name}} {{$driver->last_name}}</h3>
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
                                        @if($referral_detail->offer_type == 3)
                                            @php $values = \GuzzleHttp\json_decode($referral_detail->offer_value,true); @endphp
                                            @foreach($values as $value)
                                                @lang("$string_file.name") :{{$value['name']}},<br>
                                                @lang("$string_file.start_range") :{{$value['start_range']}},<br>
                                                @lang("$string_file.end_range") :{{$value['end_range']}},<br>
                                                @lang("$string_file.commission") :{{$value['commission']}},
                                                <hr>
                                            @endforeach
                                        @else
                                            {{$referral_detail->offer_value}} @if($referral_detail->offer_type == 2) % @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($referral_detail->referral_available == 1)
                                            Available
                                        @else
                                            Offer Redeem
                                        @endif
                                    </td>
                                    <td>{{$referral_detail->created_at}}</td>
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