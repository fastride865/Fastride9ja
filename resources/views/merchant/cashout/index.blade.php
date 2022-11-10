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
                    </div>
                    <h3 class="panel-title">
                        <i class="icon fa-taxi" aria-hidden="true"></i>
                        @lang("$string_file.driver_cashout_management")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.cashout_amount")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action_by")</th>
                            <th>@lang("$string_file.transaction_id")</th>
                            <th>@lang("$string_file.comment")</th>
                            <th>@lang("$string_file.requested_at")</th>
                            <th>@lang("$string_file.action_date")</th>
                            @if(Auth::user('merchant')->can('edit_driver_cash_out'))
                                <th>@lang("$string_file.action")</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $driver_cashout_requests->firstItem() @endphp
                        @foreach($driver_cashout_requests as $driver_cashout_request)
                            <tr>
                                <td>{{ $sr }}</td>
                                    <td>
                                         <span class="long_text">
                                         {{ $driver_cashout_request->Driver->fullName }}
                                        <br>
                                        {{ $driver_cashout_request->Driver->phoneNumber }}
                                        <br>
                                        {{ $driver_cashout_request->Driver->email }}
                                        </span>
                                    </td>
                                <td>{{ $driver_cashout_request->Driver->CountryArea->Country->isoCode.' '.$driver_cashout_request->amount  }}</td>
                                <td>
                                    @switch($driver_cashout_request->cashout_status)
                                        @case(0)
                                        <small class="badge badge-round badge-warning float-left">@lang("$string_file.pending")</small>
                                        @break;
                                        @case(1)
                                        <small class="badge badge-round badge-info float-left">@lang("$string_file.success")</small>
                                        @break;
                                        @case(2)
                                        <small class="badge badge-round badge-danger float-left">@lang("$string_file.rejected")</small>
                                        @break;
                                        @default
                                        ----
                                    @endswitch
                                </td>
                                <td>{{ ($driver_cashout_request->action_by != '') ? $driver_cashout_request->action_by : '---' }}</td>
                                <td>{{ ($driver_cashout_request->transaction_id) ? $driver_cashout_request->transaction_id : '---' }}</td>
                                <td>{{ ($driver_cashout_request->comment != '') ? $driver_cashout_request->comment : '---' }}</td>
                                <td>
                                    {!! convertTimeToUSERzone($driver_cashout_request->created_at, $driver_cashout_request->Driver->CountryArea->timezone,null,$driver_cashout_request->Driver->Merchant) !!}
                                </td>
                                <td>
                                   @if($driver_cashout_request->cashout_status != 0)
                                    {!! convertTimeToUSERzone($driver_cashout_request->updated_at, $driver_cashout_request->Driver->CountryArea->timezone,null,$driver_cashout_request->Driver->Merchant) !!}
                                    @else
                                       ---
                                    @endif
                                </td>
                                @if(Auth::user('merchant')->can('edit_driver_cash_out'))
                                    <td>
                                        <a href="{{ route('merchant.driver.cashout_status',$driver_cashout_request->id) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                @endif
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $driver_cashout_requests, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
