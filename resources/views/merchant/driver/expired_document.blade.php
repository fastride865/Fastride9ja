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
                    <h3 class="panel-title"><i class="wb-file" aria-hidden="true"></i>
                        @lang("$string_file.expired_document")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th> @lang("$string_file.id")</th>
                            <th>@lang("$string_file.service_area") </th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.personal_document") </th>
                            @if($merchant_type == "BOTH" || $merchant_type == "VEHICLE")
                            <th>@lang("$string_file.vehicle_document")</th>
                            @endif
                            @if($merchant_type == "BOTH" || $merchant_type == "HANDYMAN")
                            <th>@lang("$string_file.handyman_segment_documents")</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($drivers as $driver)
                                <tr>
                                    <td><a href="{{ route('driver.show',$driver->id) }}"
                                           class="hyperLink">{{ $driver->merchant_driver_id }}</a>
                                    </td>
                                    <td>{{ !empty($driver->country_area_id) ? $driver->CountryArea->CountryAreaName : "" }}</td>
                                    @if(Auth::user()->demo == 1)
                                        <td>
                                        <span class="long_text">
                                            {{ "********".substr($driver->last_name, -2) }}<br>
                                            {{ "********".substr($driver->phoneNumber, -2) }} <br>
                                            {{ "********".substr($driver->email, -2) }}
                                         </span>
                                        </td>
                                    @else
                                        <td>
                                            <span class="long_text">
                                                {{ $driver->first_name." ".$driver->last_name }}<br>
                                                {{ $driver->phoneNumber }} <br>
                                                {{ $driver->email }}
                                            </span>
                                        </td>
                                    @endif
                                    <td>
                                        @if(count($driver->DriverDocument) > 0)
                                            <a data-original-title=""
                                               data-toggle="tooltip"
                                               href="{{ route('driver.add',$driver->id) }}" target="_blank"
                                               data-placement="top"
                                               class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn p-document-upload"> <i
                                                        class="fa fa-upload"></i>
                                                @lang("$string_file.upload")
                                            </a>

                                        @else
                                            ----------
                                        @endif

                                    </td>
                                    @if($merchant_type == "BOTH" || $merchant_type == "VEHICLE")
                                    <td>
                                        @if(count($driver->DriverVehicles) > 0)
                                            @foreach($driver->DriverVehicles as $vehicle)
                                                <a data-original-title=""
                                                   data-toggle="tooltip"
                                                   href="{{ route('merchant.driver.vehicle.create',[$driver->id,$vehicle->id]) }}" target="_blank"
                                                   data-placement="top"
                                                   class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn v-document-upload v-document-upload"> <i
                                                            class="fa fa-upload"></i>
                                                    @lang("$string_file.vehicle_number"): {{$vehicle->vehicle_number}}
                                                </a>
                                                <br>
                                            @endforeach
                                        @else
                                            ----------
                                        @endif

                                    </td>
                                    @endif
                                    @if($merchant_type == "BOTH" || $merchant_type == "HANDYMAN")
                                        <td class="text-center">
                                            @if(count($driver->DriverSegmentDocument) > 0)
                                                <a data-original-title=""
                                                   data-toggle="tooltip"
                                                   href="{{ route('merchant.driver.handyman.segment',$driver->id) }}" target="_blank"
                                                   data-placement="top"
                                                   class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn s-document-upload"> <i
                                                            class="fa fa-upload"></i>
                                                    @lang("$string_file.upload")
                                                </a>
                                            @else
                                                ----------
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $drivers, 'data' => []])
{{--                    <div class="pagination1 float-right">{{ $drivers->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $('.toast').toast('show');
        $(".p-document-upload").click(function () {
            {{Session::flash('personal-document-expired-error', trans("$string_file.document_expired_error"))}}
        });
        $(".v-document-upload").click(function () {
            {{Session::flash('vehicle-document-expired-error', trans("$string_file.document_expired_error"))}}
        });
        $(".s-document-upload").click(function () {
            {{Session::flash('handyman-document-expired-error', trans("$string_file.document_expired_error"))}}
        });
    </script>
@endsection