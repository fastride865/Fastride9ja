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
                                <i class="wb-reply" title="@lang("$string_file.all_driver")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-taxi" aria-hidden="true"></i>
                        @lang("$string_file.temporary_rejected_drivers")</h3>
                </header>
                <div class="panel-body container-fluid">
                    {!! $search_view !!}
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.id")</th>
                            <th>@lang("$string_file.service_area") </th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.vehicle_number")</th>
                            <th>@lang("$string_file.reject_reason")</th>
                            <th>@lang("$string_file.registered_date")</th>
                            <th>@lang("$string_file.updated_at")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $drivers->firstItem() @endphp
                            @foreach($drivers as $driver)
                                <tr>
                                    <td>{{$sr}}</td>
                                    <td><a href="{{ route('driver.show',$driver->id) }}"
                                           class="address_link">{{ $driver->merchant_driver_id }}</a></td>
                                    <td>{{ $driver->CountryArea->CountryAreaName }}</td>
                                    @if(Auth::user()->demo == 1)
                                        <td>
                                            {{ "********".substr($driver->last_name, -2) }}<br>
                                            {{ "********".substr($driver->phoneNumber, -2) }}
                                            <br>
                                            {{ "********".substr($driver->email, -2) }}

                                        </td>
                                    @else
                                        <td>{{ $driver->first_name." ".$driver->last_name }}<br>
                                            {{ $driver->email }}<br>
                                            {{ $driver->phoneNumber }}</td>
                                    @endif
                                    <td>
                                        @foreach($driver->DriverVehicle as $vehicle)
                                            {{$vehicle->vehicle_number}},
                                        @endforeach
                                    </td>
                                    <td>{{ $driver->temp_admin_msg }}</td>
                                    <td>
                                        {!! convertTimeToUSERzone($driver->created_at, $driver->CountryArea->timezone, null, $driver->Merchant) !!}
                                    </td>
                                    <td>
                                        {!! convertTimeToUSERzone($driver->updated_at, $driver->CountryArea->timezone, null, $driver->Merchant) !!}
                                    </td>
                                    <td>
                                        <a href="{{ route('driver.show',$driver->id) }}"
                                           class="btn btn-sm btn-info menu-icon btn_detail action_btn">
                                            <span class="fa fa-list-alt" title="View Driver Profile"></span>
                                        </a>
                                    </td>
                                </tr>
                                @php $sr++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $drivers, 'data' => $arr_search])
{{--                    <div class="pagination1 float-right">{{ $drivers->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $('.toast').toast('show');
    </script>
@endsection