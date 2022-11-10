@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <a href="{{route('excel.driver')}}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-primary float-right"
                                    style="margin:10px">
                                <i class="wb-download" title="@lang("$string_file.export_excel")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="icon wb-list" aria-hidden="true"></i>
                        @lang("$string_file.driver_earning")
                        </span>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    {!! $search_view !!}
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.id")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.other_details")</th>
                            <th>@lang("$string_file.job_details")</th>
                            <th>@lang("$string_file.earning_details")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $drivers->firstItem() @endphp
                        @foreach($drivers as $driver)
                            <tr>
                                <td>{{$sr}}</td>
                                <td><a href="{{ route('driver.show',$driver->id) }}"
                                       class="hyperLink">{{ $driver->merchant_driver_id }}</a>
                                </td>
                                <td>
                                <span class="long_text">
                                    {{ is_demo_data($driver->first_name.' '.$driver->last_name,$driver->Merchant) }}
                                </span>
                                </td>
                                <td>
                                    {{ is_demo_data($driver->phoneNumber,$driver->Merchant) }}
                                    <br>
                                    {{ is_demo_data($driver->email,$driver->Merchant) }}
                                </td>
                                <td>
                                    @if($driver->segment_group_id == 1)
                                        @php
                                            $arr_segment_sub_group_for_admin = array_pluck($driver->Segment,'sub_group_for_admin');
                                        @endphp
                                        @if(in_array(1,$arr_segment_sub_group_for_admin))
                                            @php
                                                $bookings = $driver->total_rides;
                                                $bookings_amount = !empty($driver->ride_earning) ? $driver->ride_earning : 0;
                                            @endphp
                                            <a href="{{ route('merchant.driver-taxi-services-report',['driver_id'=>$driver->id]) }}">
                                                <span class="badge badge-info font-weight-100">@lang("$string_file.rides") : {{ $bookings }}</span>
                                            </a>
                                            <br>
                                        @endif
                                        @if(in_array(2,$arr_segment_sub_group_for_admin))
                                            @php
                                                $orders = $driver->total_orders;
                                                $orders_amount = !empty($driver->order_earning) ? $driver->order_earning : 0;
                                            @endphp
                                            <a href="{{ route('merchant.driver-delivery-services-report',['driver_id'=>$driver->id]) }}">
                                                <span class="badge badge-info font-weight-100">@lang("$string_file.orders") : {{ $orders }}</span>
                                            </a>
                                        @endif
                                    @else
                                        @php
                                            $handyman_orders = isset($driver->total_bookings) ? $driver->total_bookings : 0;
                                            $handyman_orders_amount = !empty($driver->booking_earning) ? $driver->booking_earning : 0;
                                        @endphp
                                        <a href="{{ route('merchant.driver-handyman-services-report',['driver_id'=>$driver->id]) }}">
                                            <span class="badge badge-info font-weight-100">@lang("$string_file.bookings") : {{ $handyman_orders }}</span>
                                        </a>
                                    @endif
                                </td>

                                <td>
                                    @if($driver->segment_group_id == 1)
                                        @php
                                            $arr_segment_sub_group_for_admin = array_pluck($driver->Segment,'sub_group_for_admin');
                                        @endphp
                                        @if(in_array(1,$arr_segment_sub_group_for_admin))
                                            @php
                                                $bookings = $driver->total_rides;
                                                $bookings_amount = !empty($driver->ride_earning) ? $driver->ride_earning : 0;
                                            @endphp
                                            <a href="{{ route('merchant.driver.jobs',['booking',$driver->id]) }}">
                                                <span class="badge badge-success font-weight-100">@lang("$string_file.ride_amount") : {{ $bookings_amount }}</span>
                                            </a>
                                            <br>
                                        @endif
                                        @if(in_array(2,$arr_segment_sub_group_for_admin))
                                            @php
                                                $orders = $driver->total_orders;
                                                $orders_amount = !empty($driver->order_earning) ? $driver->order_earning : 0;
                                            @endphp
                                            <a href="{{ route('merchant.driver.jobs',['order',$driver->id]) }}">
                                                <span class="badge badge-success font-weight-100">@lang("$string_file.orders_amount"): {{ $orders_amount }}</span>
                                            </a>
                                        @endif
                                    @else
                                        @php
                                            $handyman_orders_amount = !empty($driver->booking_earning) ? $driver->booking_earning : 0;
                                        @endphp
                                        <a href="{{ route('merchant.driver.jobs',['handyman-order',$driver->id]) }}">
                                            <span class="badge badge-success font-weight-100">@lang("$string_file.bookings") : {{ $handyman_orders_amount }}</span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @php $sr++; @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $drivers, 'data' => $arr_search])
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
@section('js')
    <script>
        function selectSearchFields() {
            var segment_id = $('#segment_id').val();
            var area_id = $('#area_id').val();
            var by = $('#by_param').val();
            var by_text = $('#keyword').val();
            if (segment_id.length == 0 && area_id == "" && by == "" && by_text == "") {
                alert("Please select at least one search field");
                return false;
            } else if (by != "" && by_text == "") {
                alert("Please enter text according to selected parameter");
                return false;
            } else if (by_text != "" && by == "") {
                alert("Please select parameter according to entered text");
                return false;
            }
        }
    </script>
@endsection