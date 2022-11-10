@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
{{--                        @if(Auth::user('merchant')->can('create_taxi_company'))--}}
{{--                            <a href="{{route('taxicompany.create')}}">--}}
{{--                                <button type="button" title="@lang('admin.tax_company')"--}}
{{--                                        class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-plus"></i>--}}
{{--                                </button>--}}
{{--                            </a>--}}
{{--                        @endif--}}
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                        @lang('admin.company_referral')</h3>
                </header>
                <div class="panel-body">
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.ride_id")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang('admin.merchantPhone')</th>
                            <th>@lang("$string_file.email")</th>
                            <th>@lang("$string_file.type")</th>
                            <th>@lang("$string_file.offer_type") </th>
                            <th>@lang("$string_file.offer_value") </th>
                            <th>@lang("$string_file.date")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1 @endphp
                        @foreach($referral_details as $referral_detail)
                            <tr>
                                <td>{{ $sr }}</td>
                                @if(isset($referral_detail->receiver_details))
                                    @foreach($referral_detail->receiver_details as $receiver)
                                        @if(Auth::user()->demo == 1)
                                            <td>{{ "********".substr($receiver['name'], -2) }}</td>
                                            <td>{{ "********".substr($receiver['phone'], -2) }}</td>
                                            <td>{{ "********".substr($receiver['email'], -2) }}</td>
                                        @else
                                            <td>{{ $receiver['name'] }}</td>
                                            <td>{{ $receiver['phone'] }}</td>
                                            <td>{{ $receiver['email'] }}</td>
                                        @endif
                                    @endforeach
                                @else
                                    <td>---</td>
                                    <td>---</td>
                                    <td>---</td>
                                @endif
                                <td>
                                    @if($referral_detail->receiver_type == 1)
                                        @lang("$string_file.user")
                                    @else
                                        @lang("$string_file.driver")
                                    @endif
                                </td>
                                <td>
                                    @if($referral_detail->offer_type == 1)
                                        @lang("$string_file.free_ride")
                                    @elseif($referral_detail->offer_type == 2)
                                        @lang("$string_file.discount")
                                    @else
                                        @lang("$string_file.fixed_amount")
                                    @endif
                                </td>
                                <td>{{$referral_detail->offer_value}} @if($referral_detail->offer_type == 2) % @endif</td>
                                <td>{{$referral_detail->created_at}}</td>
                            </tr>
                            @php $sr++ @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $referral_details, 'data' => []])
{{--                    <div class="pagination1" style="float:right;">{{$referral_details->links()}}</div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection

