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
                        @lang("$string_file.business_segment_cashout_request")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.business_segment")</th>
                            <th>@lang("$string_file.cashout_amount")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action_by")</th>
                            <th>@lang("$string_file.transaction_id")</th>
                            <th>@lang("$string_file.comment")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.updated_at")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $cashout_requests->firstItem() @endphp
                        @foreach($cashout_requests as $cashout_request)
                            <tr>
                                <td>{{ $sr }}</td>
                                @if(Auth::user()->demo == 1)
                                    <td>
                                         <span class="long_text">
                                         {{ "********".substr($cashout_request->BusinessSegment->full_name,-2) }}
                                        <br>
                                       {{ "********".substr($cashout_request->BusinessSegment->phone_number,-2) }}
                                        <br>
                                        {{ "********".substr($cashout_request->BusinessSegment->email,-2) }}
                                        </span>
                                    </td>
                                @else
                                    <td>
                                         <span class="long_text">
                                         {{ $cashout_request->BusinessSegment->full_name.' ('.$cashout_request->BusinessSegment->Segment->name.')' }}
                                        <br>
                                        {{ $cashout_request->BusinessSegment->phone_number }}
                                        <br>
                                        {{ $cashout_request->BusinessSegment->email }}
                                        </span>
                                    </td>
                                @endif
                                <td>{{ $cashout_request->BusinessSegment->CountryArea->Country->isoCode.' '.$cashout_request->amount  }}</td>
                                <td>
                                    @switch($cashout_request->cashout_status)
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
                                <td>{{ ($cashout_request->action_by != '') ? $cashout_request->action_by : '---' }}</td>
                                <td>{{ ($cashout_request->transaction_id) ? $cashout_request->transaction_id : '---' }}</td>
                                <td>{{ ($cashout_request->comment != '') ? $cashout_request->comment : '---' }}</td>
                                <td>
                                    {!! convertTimeToUSERzone($cashout_request->created_at, null,null,$cashout_request->Merchant) !!}
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($cashout_request->updated_at, null,null,$cashout_request->Merchant) !!}
                                </td>
                                <td>
                                    @if($cashout_request->cashout_status != 2)
                                        <a href="{{ route('merchant.business-segment.cashout_status',$cashout_request->id) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip" data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $cashout_requests, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
