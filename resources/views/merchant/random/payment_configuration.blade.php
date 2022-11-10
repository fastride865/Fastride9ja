@extends('merchant.layouts.main')
@section('content')
  <div class="page">
    <div class="page-content">
      @if(session('configuration'))
        <div class="alert dark alert-icon alert-success alert-dismissible"
             role="alert">
          <button type="button" class="close" data-dismiss="alert"
                  aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
          <i class="icon wb-info" aria-hidden="true"></i>{{ session('configuration') }}
        </div>
      @endif
      <div class="panel panel-bordered">
        <div class="panel-heading">
          <div class="panel-actions"></div>
          <h3 class="panel-title">
            <i class=" icon fa-gears" aria-hidden="true"></i>
            @lang('admin.payment.configuration')
          </h3>
        </div>
        <div class="panel-body container-fluid">
          <form method="POST" class="steps-validation wizard-notification"
                enctype="multipart/form-data"
                action="{{ route('merchant.payment-configuration.store') }}">
            @csrf
            <div class="row">
{{--              <div class="col-md-4">--}}
{{--                <div class="form-group">--}}
{{--                  <label class="text-capitalize">--}}
{{--                    @lang('admin.outstanding.payment.to')<span class="text-danger">*</span>--}}
{{--                  </label>--}}
{{--                  <select class="form-control" name="outstanding_payment_to"--}}
{{--                          id="sweet_alert_admin">--}}
{{--                    <option value="1" {{ $payment_configuration->outstanding_payment_to == 1 ? 'selected' : ''}}>@lang('admin.company')</option>--}}
{{--                    <option value="2" {{ $payment_configuration->outstanding_payment_to == 2 ? 'selected' : ''}}>@lang("$string_file.driver")</option>--}}
{{--                  </select>--}}
{{--                  @if ($errors->has('outstanding_payment_to'))--}}
{{--                    <label class="danger">{{ $errors->first('outstanding_payment_to') }}</label>--}}
{{--                  @endif--}}
{{--                </div>--}}
{{--              </div>--}}
              @if($configuration->fare_table_based_referral_enable == 1)
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="text-capitalize">
                      @lang('admin.fare_table_based_refer')
                    </label>
                    <select class="form-control" name="fare_table_based_refer"
                            id="fare_table_based_refer"
                            onchange="switchDisabled(this.value , 'fare-switch')"
                    >
                      <option value="2"> @lang("$string_file.disable")</option>
                      <option value="1" {{ $payment_configuration->fare_table_based_refer == 1 ? 'selected' : ''}}>@lang("$string_file.enable")</option>
                    </select>
                    @if ($errors->has('fare_table_based_refer'))
                      <label class="danger">{{ $errors->first('fare_table_based_refer') }}</label>
                    @endif
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="text-capitalize">
                      @lang('admin.fare_table_refer_type')
                    </label>
                    <select class="fare-switch form-control" name="fare_table_refer_type"
                            id="fare_table_refer_type"
                            {{($payment_configuration->fare_table_based_refer == 1) ? '' : 'disabled'}}
                    >
                      <option value="1"> @lang('admin.number_of_trips')</option>
                      <option value="2" {{ $payment_configuration->fare_table_refer_type == 2 ? 'selected' : ''}}>@lang('admin.minimum_cf_per_week')</option>
                    </select>
                    @if ($errors->has('fare_table_refer_type'))
                      <label class="danger">{{ $errors->first('fare_table_refer_type') }}</label>
                    @endif
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="text-capitalize">
                      @lang('admin.fare_table_refer_pass_value')
                    </label>
                    <input type="number" name="fare_table_refer_pass_value" value="{{$payment_configuration->fare_table_refer_pass_value}}"
                           class="fare-switch form-control"
                            {{($payment_configuration->fare_table_based_refer == 1) ? '' : 'disabled'}}
                    />
                    @if ($errors->has('fare_table_refer_pass_value'))
                      <label class="danger">{{ $errors->first('fare_table_refer_pass_value') }}</label>
                    @endif
                  </div>
                </div>
              @endif
              @if($configuration->driver_wallet_withdraw_enable == 1)
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="text-capitalize">
                      @lang('admin.driver_wallet_withdrawal_min_amount')
                    </label>
                    <input type="number" name="wallet_withdrawal_min_amount" value="{{$payment_configuration->wallet_withdrawal_min_amount}}" class="withdrawal-switch form-control"
                            {{($payment_configuration->wallet_withdrawal_enable == 1) ? '' : 'disabled'}}
                    />
                    @if ($errors->has('wallet_withdrawal_min_amount'))
                      <label class="danger">{{ $errors->first('wallet_withdrawal_min_amount') }}</label>
                    @endif
                  </div>
                </div>
              @endif
              @if($configuration->cancel_rate_table_based_cancel_charges_enable == 1)
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="text-capitalize">
                      @lang('admin.cancel_rate_table_enable')
                    </label>
                    <select class="form-control" name="cancel_rate_table_enable"
                            id="cancel_rate_table_enable">
                      <option value="2">@lang("$string_file.disable")</option>
                      <option value="1" {{($payment_configuration->cancel_rate_table_enable == 1) ?'selected' : ''}}> @lang("$string_file.enable")</option>
                    </select>
                  </div>
                </div>
              @endif
            </div>
            <div class="form-actions right" style="margin-bottom: 3%">
              @if(Auth::user('merchant')->can('edit_configuration'))
                <button type="submit" class="btn btn-primary float-right">
                  <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                </button>
              @endif
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('js')
  <script>
    function switchDisabled (value , target) {
      if (value == 1) {
        $('.'+target).prop('disabled' , false)
        return
      }
      $('.'+target).prop('disabled' , true)
    }
  </script>
@endsection


