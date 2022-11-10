@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('business-segment.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <button type="button" class="btn btn-icon btn-success float-right"
                                title="@lang("$string_file.add_vehicle") "
                                data-toggle="modal"
                                data-target="#cashout-request" style="margin:10px">
                            <i class="wb-plus"></i>
                        </button>
                    </div>
                    <h3 class="panel-title">
                        <i class="icon fa-taxi" aria-hidden="true"></i>
                        @lang("$string_file.cashout_request")  </h3>
                </header>
                <div class="panel-body container-fluid">
                    <h4>@lang("$string_file.wallet_money") : {{ $business_segment->Country->isoCode.' '.$business_segment->wallet_amount}}</h4>
                    {!! $search_view !!}
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.cashout_amount")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action_by")</th>
                            <th>@lang("$string_file.transaction_id")</th>
                            <th>@lang("$string_file.comment")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.updated_at")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $cashout_requests->firstItem() @endphp
                        @foreach($cashout_requests as $cashout_request)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $cashout_request->BusinessSegment->Country->isoCode.' '.$cashout_request->amount  }}</td>
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
                                    {!! convertTimeToUSERzone($cashout_request->created_at,null, null, $cashout_request->BusinessSegment->Merchant) !!}
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($cashout_request->updated_at,null, null, $cashout_request->BusinessSegment->Merchant) !!}
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('business-segment.shared.table-footer', ['table_data' => $cashout_requests, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade show" id="cashout-request" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b> @lang("$string_file.register_cashout_request") </b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{ route('business-segment.cashout.request') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang("$string_file.amount") <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="text" id="amount" name="amount" min="1" class="form-control" placeholder="" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn" data-dismiss="modal"
                               value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-outline-primary btn" value="@lang("$string_file.add")">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection