@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('sosadded'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <span class="alert-icon"><i class="fa fa-info"></i></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.sms')
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('promotionsms.create')}}">
                            <button type="button" data-toggle="tooltip" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-plus" title="@lang("$string_file.send_notification")"></i>
                            </button>
                        </a>
                        <a href="{{route('excel.countriesexport')}}" >
                            <button type="button" data-toggle="tooltip" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                <i class="wb-download" title="@lang("$string_file.export_excel")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fas fa-sms" aria-hidden="true"></i>
                        @lang('admin.sms')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <th>@lang("$string_file.sn")</th>
                        <th>@lang("$string_file.service_area")</th>
                        <th>@lang("$string_file.message")</th>
                        <th>@lang("$string_file.application")</th>
                        <th>@lang('admin.receiver')</th>
                        <th>@lang("$string_file.time")</th>
                        <th>@lang("$string_file.action")</th>
                        </thead>
                        <tbody>
                        @php $sr = $promotionsms->firstItem() @endphp
                        @foreach($promotionsms as $promotion)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    @if($promotion->country_area_id)
                                        {{ $promotion->CountryArea->CountryAreaName }}
                                    @else
                                        ------
                                    @endif
                                </td>
                                <td>
                                    <span class="map_address">{{ $promotion->message }}</span>
                                </td>
                                @switch($promotion->application)
                                    @case(1)
                                    <td>@lang("$string_file.driver")</td>
                                    @break
                                    @case(2)
                                    <td>@lang("$string_file.user")</td>
                                    @break
                                @endswitch
                                @if(Auth::user()->demo == 1)
                                    <td>
                                        @switch($promotion->application)
                                            @case(1)
                                            @if($promotion->driver_id == 0)
                                                @lang("$string_file.all_drivers")
                                            @else
                                                {{ "********".substr($promotion->Driver->last_name, -2) }}
                                                <br>
                                                {{ "********".substr($promotion->Driver->phoneNumber, -2) }}
                                                <br>
                                                {{ "********".substr($promotion->Driver->email, -2) }}
                                            @endif
                                            @break
                                            @case(2)
                                            @if($promotion->user_id == 0)
                                                @lang("$string_file.all_users")
                                            @else
                                                {{  "********".substr($promotion->User->UserName, -2) }}
                                                <br>
                                                {{ "********".substr($promotion->User->UserPhone, -2) }}
                                                <br>
                                                {{  "********".substr($promotion->User->email, -2) }}
                                            @endif
                                            @break
                                        @endswitch
                                    </td>
                                @else
                                    <td>
                                        @switch($promotion->application)
                                            @case(1)
                                            @if($promotion->driver_id == 0)
                                                @lang("$string_file.all_drivers")
                                            @else
                                                {{ $promotion->Driver->first_name." ".$promotion->Driver->last_name }}
                                                <br>
                                                {{ $promotion->Driver->phoneNumber }}
                                                <br>
                                                {{ $promotion->Driver->email }}
                                            @endif
                                            @break
                                            @case(2)
                                            @if($promotion->user_id == 0)
                                                @lang("$string_file.all_users")
                                            @else
                                                {{ $promotion->User->UserName }}
                                                <br>
                                                {{ $promotion->User->UserPhone }}
                                                <br>
                                                {{ $promotion->User->email }}
                                            @endif
                                            @break
                                        @endswitch
                                    </td>
                                @endif
                                <td>{{ $promotion->created_at->toformatteddatestring() }}</td>
                                <td>
                                    @if(Auth::user('merchant')->can('delete_promotion'))
                                        <a href="{{ route('promotionsms.delete',$promotion->id) }}"
                                           data-original-title="@lang("$string_file.delete")"
                                           data-toggle="tooltip" data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                            <i class="fa fa-trash"></i> </a>
                                    @endif
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $promotionsms, 'data' => []])
{{--                    <div class="pagination1 float-right">{{ $promotionsms->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection












