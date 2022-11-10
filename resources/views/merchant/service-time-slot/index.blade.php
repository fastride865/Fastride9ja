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
                        <a href="{{route('segment.service-time-slot.add')}}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-plus"
                                   title="@lang("$string_file.add_time_slot")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="icon fa-money" aria-hidden="true"></i>
                        @lang("$string_file.service_time_slots")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    @include('merchant.segment-pricecard.search')
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.segment")</th>
                            <th>@lang("$string_file.day")</th>
                            <th>@lang("$string_file.maximum_no_of_slots")</th>
                            <th>@lang("$string_file.start_time")</th>
                            <th>@lang("$string_file.end_time")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $arr_service_time_slot->firstItem();
                        @endphp
                        @foreach($arr_service_time_slot as $service_time_slot)
                            @php
                                $start = strtotime($service_time_slot->start_time);
                                $end = strtotime($service_time_slot->end_time);
                            @endphp
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    {{ $service_time_slot->CountryArea->CountryAreaName }}
                                </td>
                                <td>{{ !empty($service_time_slot->Segment->Name($service_time_slot->merchant_id)) ? $service_time_slot->Segment->Name($service_time_slot->merchant_id) : $service_time_slot->Segment->slag }}</td>
                                {{--                                    <td>{{ $service_time_slot->ServiceType->serviceName }}</td>--}}
                                <td>{{ $arr_day[$service_time_slot->day] }}</td>
                                <td>{{ $service_time_slot->max_slot }}</td>
                                <td>{{ $time_format == 2 ? date("H:i", $start) : date("h:i a", $start) }}</td>
                                <td>{{ $time_format == 2 ? date("H:i", $end) : date("h:i a", $end) }}</td>
                                <td>
                                    @if($service_time_slot->status == 1)

                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('segment.service-time-slot.add',$service_time_slot->id) }}"
                                       data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                class="fa fa-edit"></i> </a>

                                    <a href="{{ route('service-time-slot.detail',$service_time_slot->id) }}"
                                       data-original-title="@lang("$string_file.time_slots_configuration")"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                        <i class="fa fa-clock-o"></i>
                                    </a>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $arr_service_time_slot, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection

