@extends('corporate.layouts.main')
@section('content')
    <div class="app-content content">
        <div class="container-fluid content-wrapper">
            <div class="content-body">
                <section id="horizontal">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header py-3">
                                    <div class="content-header row">
                                        <div class="content-header-left col-md-8 col-12 mb-2 breadcrumb-new">
                                            <h3 class="content-header-title mb-0 d-inline-block">@lang('admin.DriverRequest')</h3>
                                        </div>
                                        <div class="content-header-right col-md-4 col-12">
                                            <div class="btn-group float-md-right">
                                                <a href="{{ URL::previous() }}">
                                                    <button type="button" class="btn btn-icon btn-success mr-1"><i
                                                                class="fa fa-reply"></i>
                                                    </button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body card-dashboard">
                                        <div class="table-responsive">
                                            {{--                                            <b>OneSignal Summary =></b> Total request <b></b> Total request success<b></b> Total request failed<b></b>--}}
                                            <table id="" width="100%" cellspacing="0"
                                                   class="table display nowrap table-striped table-bordered scroll-horizontal">
                                                <thead>
                                                <tr>
                                                    <th>@lang("$string_file.sn")</th>
                                                    <th>@lang("$string_file.driver")</th>
                                                    <th>@lang('admin.distancefrompickup')</th>
                                                    <th>@lang('admin.currentstatus')</th>
                                                    <th>@lang("$string_file.created_at")</th>
                                                    <th>@lang('admin.lastUpdate')</th>
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
                                                            @switch($driver->request_status)
                                                                @case(1)
                                                                @lang("$string_file.no_action")
                                                                @break
                                                                @case(2)
                                                                @lang('admin.driverAccepted')
                                                                @break
                                                                @case(3)
                                                                @lang('admin.driverreject')
                                                                @break
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            {{ $driver->created_at->toDayDateTimeString() }}
                                                        </td>
                                                        <td>
                                                            {{ $driver->updated_at->toDayDateTimeString() }}
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
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
@endsection