@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(!$is_config_exist)
                <div class="alert dark alert-icon alert-danger" role="alert">
                    <i class="icon wb-info" aria-hidden="true"></i> @lang('admin.driver_bonus_config_not_set')
                </div>
            @endif
            @if(session('success'))
                <div class="alert dark alert-icon alert-success" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert dark alert-icon alert-danger" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-close" aria-hidden="true"></i> {{ session('error') }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <h3 class="panel-title">
                        <i class=" wb-flag" aria-hidden="true"></i>
                        @lang('admin.driver_bonus_report') : {{ $report_month_year }}</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('merchant.driver_bonus_report') }}" method="post">
                        @csrf
                        <div class="table_search">
                            <div class="row">
                                <div class="col-md-3 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="search_val" name="search_val"
                                               placeholder="@lang("$string_file.driver_details")"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-md-2 form-group ">
                                    <div class="input-group">
                                        <input type="text"
                                               id="month_year"
                                               name="month_year"
                                               value="{{old('month_year')}}"
                                               placeholder="@lang("$string_file.ride")  @lang("$string_file.date")" readonly
                                               class="form-control col-md-12 col-xs-12 myDatePicker bg-this-color"
                                        >
                                    </div>
                                </div>
                                <div class="col-sm-2 form-group ">
                                    <button class="btn btn-primary" type="submit" name="seabt12"><i
                                                class="wb-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.driver")</th>
                            <th>@lang('admin.total_trips') & @lang("$string_file.rating_by_user") </th>
                            <th>@lang('admin.total_trip_amount')</th>
                            <th>@lang('admin.total_company_profit')</th>
                            <th>@lang('admin.total_driver_profit')</th>
                            <th>@lang('admin.eligible_for_bonus')</th>
                            <th>@lang('admin.bonus_credited')</th>
                            <th>@lang('admin.send_money')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php  $pagination  = $drivers; $sn = $pagination->firstItem(); @endphp
                        @foreach($drivers as $driver)
                            <tr>
                                <td>{{ $sn++ }}</td>
                                @if(Auth::user()->demo == 1)
                                    <td>
                                        {{ "********".substr($driver->fullName, -2) }}
                                        <br>
                                        {{ "********".substr($driver->phoneNumber, -2) }}
                                        <br>
                                        {{ "********".substr($driver->email, -2) }}
                                    </td>
                                @else
                                    <td>
                                        {{ $driver->fullName }}
                                        <br>
                                        {{ $driver->phoneNumber }}
                                        <br>
                                        {{ $driver->email }}
                                    </td>
                                @endif
                                <td>
                                    @if($driver->total_trips)
                                        <a href="{{ route('driverRides',$driver->id) }}"
                                           class="hyperLink"> {{ $driver->total_trips }}  @lang("$string_file.ride")</a>
                                    @else
                                        @lang("$string_file.no_ride")
                                    @endif
                                    <br>
                                    @if ($driver->rating == "0.0")
                                        @lang("$string_file.not_rated_yet")
                                    @else
                                        @while($driver->rating > 0)
                                            @if($driver->rating >0.5)
                                                <img src="{{ view_config_image("static-images/star.png") }}"
                                                     alt='Whole Star'>
                                            @else
                                                <img src="{{ view_config_image('static-images/halfstar.png') }}"
                                                     alt='Half Star'>
                                            @endif
                                            @php $driver->rating--; @endphp
                                        @endwhile
                                    @endif
                                </td>
                                <td>{{ $driver->Booking->sum('final_amount_paid') }}</td>
                                <td>{{ $driver->Booking->sum('company_cut') }}</td>
                                <td>{{ $driver->Booking->sum('driver_cut') }}</td>
                                <td>{{ ($driver->is_eligible == true) ? trans('admin.message322') : trans('admin.message323') }}</td>
                                <td>{{ ($driver->is_credited == true) ? trans('admin.message322') : trans('admin.message323') }}</td>
                                <td>
                                    @if(Auth::user()->demo != 1)
                                        @if($is_config_exist && $driver->is_credited == false)
                                            <a href="{{ route('merchant.driver_bonus_report.sendmoney',[$driver->id,strtotime($month_year)]) }}"
                                               class="btn btn-sm btn-success action_btn"
                                               data-original-title="@lang('admin.send_money')"
                                               data-toggle="tooltip"
                                               data-placement="top"><span
                                                        class="fas fa-money"></span></a>
                                        @else
                                            ----
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $drivers, 'data' => []])
                    {{--                    <div class="mt-10">--}}
                    {{--                        <div class="float-left">@lang('admin.showing') {{ $drivers->firstItem() }} @lang('admin.to') {{ $drivers->lastItem() }} @lang('admin.of') {{ $drivers->total() }}</div>--}}
                    {{--                        <div class="pagination1 float-right">{{$drivers->links()}}</div>--}}
                    {{--                    </div>--}}
                    {{--                    @php $sn = $sn+1; @endphp--}}
                    {{--                    <div class="pagination1 float-right">{{ $drivers->appends($data)->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            var dateToday = new Date();
            $('.myDatePicker').datepicker({
                format: "m/yyyy",
                viewMode: "months",
                minViewMode: "months",
                autoclose: true,
                orientation: "bottom auto",
                endDate: dateToday,
                onRender: function (date) {
                    return date.valueOf() < now.valueOf() ? 'disabled' : '';
                }
            });
        });
    </script>
@endsection

