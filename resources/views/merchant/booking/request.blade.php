@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="{{ URL::previous() }}">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-users" aria-hidden="true"></i>
                        @lang("$string_file.requested_drivers_of_ride") #{{$booking->merchant_booking_id}}
                    </h3>

                </header>
                <div class="panel-body container-fluid">
                    @php
                        $arr_failed_player_id = [];
                        $arr_success_player_id = [];
                    @endphp
                    @if(isset($booking->OneSignalLog) && !empty($booking->OneSignalLog))
                        @php
                            $arr_failed_player_id = json_decode($booking->OneSignalLog->failed_driver_id,true);
                            $arr_success_player_id = json_decode($booking->OneSignalLog->success_driver_id,true);
                        @endphp
                        <b>@lang("$string_file.onesignal_summary") =></b> @lang("$string_file.request_sent") : <b>{!! $booking->OneSignalLog->total_request_sent !!} &nbsp; &nbsp;</b> @lang("$string_file.total_success") : &nbsp;&nbsp;<b>{!! !empty($arr_success_player_id) ? count($arr_success_player_id) : 0 !!} &nbsp;&nbsp;</b> @lang('admin.total_failed') :<b> &nbsp;&nbsp;{!! !empty($arr_failed_player_id) ? count($arr_failed_player_id) : 0 !!}</b>
                    @endif
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.pickup_distance")</th>
                            <th>@lang("$string_file.onesignal_request")</th>
                            <th>@lang("$string_file.current_status")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.updated_at")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sn =1; @endphp
                        @foreach($booking->BookingRequestDriver as $driver)
                            <tr>
                                <td>{!! $sn !!}</td>
                                <td>
                                    @if(Auth::user()->demo == 1)
                                        {{ "********".substr($driver->Driver->first_name. $driver->Driver->last_name,-2) }}
                                        <br>
                                        {{ "********".substr($driver->Driver->phoneNumber, -2) }}
                                        <br>
                                        {{ "********".substr($driver->Driver->email, -2) }}
                                    @else
                                        {{ $driver->Driver->first_name. $driver->Driver->last_name }}
                                        <br>
                                        {{ $driver->Driver->phoneNumber }}
                                        <br>
                                        {{ $driver->Driver->email }}
                                    @endif
                                </td>
                                <td>
                                    {{ round($driver->distance_from_pickup,2) }}
                                </td>
                                <td>
                                    @if(!empty($arr_success_player_id) && in_array($driver->Driver->player_id,$arr_success_player_id))
                                        @lang("$string_file.success")
                                    @elseif(!empty($arr_failed_player_id) && in_array($driver->Driver->player_id,$arr_failed_player_id))
                                        @lang("$string_file.failed")
                                    @endif
                                </td>
                                <td>
                                    @switch($driver->request_status)
                                        @case(1)
                                        @lang("$string_file.no_action")
                                        @break
                                        @case(2)
                                        @lang("$string_file.accepted")
                                        @break
                                        @case(3)
                                        @lang("$string_file.rejected")
                                        @break
                                    @endswitch
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($driver->created_at, $driver->Booking->CountryArea->timezone,null,$driver->Booking->Merchant) !!}
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($driver->updated_at, $driver->Booking->CountryArea->timezone,null,$driver->Booking->Merchant) !!}
                                </td>
                            </tr>
                            @php $sn++; @endphp
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

