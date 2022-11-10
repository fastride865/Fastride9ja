@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('merchant.business-segment.cashout_request') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                            @if(!empty($info_setting) && $info_setting->edit_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <h3 class="panel-title">
                        @lang("$string_file.cashout_request_action")
                        - @if(Auth::user()->demo == 1)
                            <span class="long_text">
                                            {{ "********".substr($cashout_request->BusinessSegment->full_name,-2) }}
                                                ( {{ "********".substr($cashout_request->BusinessSegment->phone_number,-2) }} / {{ "********".substr($cashout_request->BusinessSegment->email,-2) }} ) </span>
                        @else
                            <span class="long_text">
                                            {{ $cashout_request->BusinessSegment->full_name }}
                                                ( {{ $cashout_request->BusinessSegment->phone_number }} / {{ $cashout_request->BusinessSegment->email }} )</span>
                        @endif
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{route('merchant.business-segment.cashout_status_update', $cashout_request->id)}}">
                        @csrf
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.wallet_money")
                                        </label><br>
                                        {{ $cashout_request->BusinessSegment->CountryArea->Country->isoCode.' '.$cashout_request->BusinessSegment->wallet_money }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.cashout_amount")
                                        </label><br>
                                        {{ $cashout_request->BusinessSegment->CountryArea->Country->isoCode.' '.$cashout_request->amount }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.cashout_amount")
                                        </label><br>
                                        {{ $cashout_request->BusinessSegment->CountryArea->Country->isoCode.' '.$cashout_request->amount }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.current_status")
                                        </label><br>
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
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            @lang("$string_file.status")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" id="cashout_status"
                                                name="cashout_status">
                                            <option value="0"
                                                    @if($cashout_request->cashout_status == 0) selected @endif>@lang("$string_file.pending")</option>
                                            <option value="1"
                                                    @if($cashout_request->cashout_status == 1) selected @endif>@lang("$string_file.success")</option>
                                            <option value="2"
                                                    @if($cashout_request->cashout_status == 2) selected @endif>@lang("$string_file.rejected")</option>
                                        </select>
                                        @if ($errors->has('cashout_status'))
                                            <label class="text-danger">{{ $errors->first('cashout_status') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.action_by")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="action_by"
                                               name="action_by"
                                               placeholder="@lang("$string_file.action_by")"
                                               value="{{ old('action_by',$cashout_request->action_by) }}" required>
                                        @if($errors->has('action_by'))
                                            <label class="text-danger">{{ $errors->first('action_by') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.transaction_id")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="transaction_id"
                                               name="transaction_id"
                                               placeholder="@lang("$string_file.transaction_id")"
                                               value="{{ old('transaction_id',$cashout_request->transaction_id) }}"
                                               required>
                                        @if ($errors->has('transaction_id'))
                                            <label class="text-danger">{{ $errors->first('transaction_id') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.comment")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="comment"
                                               name="comment"
                                               placeholder="@lang("$string_file.comment")"
                                               value="{{ old('comment',$cashout_request->comment) }}" required>
                                        @if ($errors->has('comment'))
                                            <label class="text-danger">{{ $errors->first('comment') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <hr>
                        <h5>@lang("$string_file.bank_details")</h5>
                        @php $arr_account_info = !empty($cashout_request->BusinessSegment['bank_details']) ? json_decode($cashout_request->BusinessSegment['bank_details'],true) : [];  @endphp
                        <div class="row">
                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label for="minimum_amount">
                                        @lang("$string_file.bank_name")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        {!! Form::text("bank_name",old("bank_name",isset($arr_account_info['account_number']) ? $arr_account_info['bank_name'] : NULL),["class"=>"form-control", "id"=>"bank_name","placeholder"=>'',"readonly"=>true]) !!}
                                    </div>
                                    @if ($errors->has('bank_name'))
                                        <label class="text-danger">{{ $errors->first('bank_name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label for="minimum_amount">
                                        @lang("$string_file.account_holder_name")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        {!! Form::text("account_holder_name",old("account_holder_name",isset($arr_account_info['account_holder_name']) ? $arr_account_info['account_holder_name'] : NULL),["class"=>"form-control", "id"=>"account_holder_name","placeholder"=>'',"readonly"=>true]) !!}
                                    </div>
                                    @if ($errors->has('account_holder_name'))
                                        <label class="text-danger">{{ $errors->first('account_holder_name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label for="minimum_amount">
                                        @lang("$string_file.account_number")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        {!! Form::text("account_number",old("account_number",isset($arr_account_info['account_number']) ? $arr_account_info['account_number'] : NULL),["class"=>"form-control", "id"=>"account_number","placeholder"=>'',"readonly"=>true]) !!}
                                    </div>
                                    @if ($errors->has('account_number'))
                                        <label class="text-danger">{{ $errors->first('account_number') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label for="bank_code">
                                        @lang("$string_file.bank_code")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        {!! Form::text("bank_code",old("bank_code",isset($arr_account_info['bank_code']) ? $arr_account_info['bank_code'] : NULL),["class"=>"form-control", "id"=>"bank_code","placeholder"=>'',"readonly"=>true]) !!}
                                    </div>
                                    @if ($errors->has('bank_code'))
                                        <label class="text-danger">{{ $errors->first('bank_code') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> @lang("$string_file.update")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'])
@endsection