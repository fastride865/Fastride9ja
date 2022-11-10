@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
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
                        <i class="icon fa-gears" aria-hidden="true"></i>
                        @lang("$string_file.driver_configuration")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              action="{{ route('merchant.driver_configuration.store') }}">
                            @csrf
                            <fieldset>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize">{{trans("$string_file.auto_verify")}}</label>
                                            <select name="auto_verify" id="auto_verify" class="form-control">
                                                <option value="1"
                                                        @if($configuration['auto_verify'] == 1) selected @endif>{{trans("$string_file.active")}}</option>
                                                <option value="0"
                                                        @if($configuration['auto_verify'] == 0) selected @endif>{{trans("$string_file.inactive")}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize">{{trans("$string_file.inactive_time")}}
                                                <span class="text-danger">*</span>(minutes)</label>
                                            {!! Form::number('inactive_time',old('inactive_time',isset($configuration['inactive_time']) ? $configuration['inactive_time'] : 15),['class'=>'form-control', 'id'=>'','placeholder'=>'Time in minutes','required'=>true]) !!}
                                        </div>
                                    </div>
                                    @if($merchant->Configuration && $merchant->Configuration->driver_suspend_penalty_enable == 1)
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="text-capitalize">@lang("$string_file.penalty_enable")</label>
                                                <select class="form-control" name="driver_penalty_enable"
                                                        onchange="handleDiv(this.value , 'driver-penalty')">
                                                    <option value="2">@lang("$string_file.disable")</option>
                                                    <option value="1" {{($configuration['driver_penalty_enable'] == 1 || old('driver_penalty_enable') == 1) ? 'selected' : ''}}>@lang("$string_file.enable")</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 driver-penalty {{($configuration['driver_penalty_enable'] != 1 && old('driver_penalty_enable') != 1) ? 'd-none' : ''}}">
                                            <div class="form-group">
                                                <label class="text-capitalize">@lang("$string_file.driver_cancel_count")</label>
                                                <input name="driver_cancel_count" class="form-control"
                                                       value="{{$configuration['driver_cancel_count']}}">
                                                @if($errors->first('driver_cancel_count'))
                                                    <span class="text-danger">{{$errors->first('driver_cancel_count')}}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4 driver-penalty {{($configuration['driver_penalty_enable'] != 1 && old('driver_penalty_enable') != 1) ? 'd-none' : ''}}">
                                            <div class="form-group">
                                                <label class="text-capitalize">@lang("$string_file.penalty_period")</label>
                                                <input name="driver_penalty_period" class="form-control"
                                                       value="{{$configuration['driver_penalty_period']}}">
                                                @if($errors->first('driver_penalty_period'))
                                                    <span class="text-danger">{{$errors->first('driver_penalty_period')}}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4 driver-penalty {{($configuration['driver_penalty_enable'] != 1 && old('driver_penalty_enable') != 1) ? 'd-none' : ''}}">
                                            <div class="form-group">
                                                <label class="text-capitalize">@lang("$string_file.penalty_period_text")</label>
                                                <input name="driver_penalty_period_next" class="form-control"
                                                       value="{{$configuration['driver_penalty_period_next']}}">
                                                @if($errors->first('driver_penalty_period_next'))
                                                    <span class="text-danger">{{$errors->first('driver_penalty_period_next')}}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                {{--                                @if(isset($merchant->Configuration->driver_cashout_module) && $merchant->Configuration->driver_cashout_module == 1)--}}
                                <br>
                                <h5 class="form-section"><i
                                            class="fa fa-taxi"></i> @lang("$string_file.cashout_configuration")
                                </h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize">
                                                @lang("$string_file.cashout_minimum_amount")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   name="driver_cashout_min_amount"
                                                   placeholder=""
                                                   value="{{$configuration['driver_cashout_min_amount']}}"
                                                   required>
                                            @if ($errors->has('driver_cashout_min_amount'))
                                                <label class="danger">{{ $errors->first('driver_cashout_min_amount') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                {{--                                @endif--}}
                            </fieldset>
                            <div class="form-actions right" style="margin-bottom: 3%">
                                @if(Auth::user('merchant')->can('edit_configuration'))
                                    @if(!$is_demo)
                                    <button type="submit" class="btn btn-primary float-right">
                                        <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                                    </button>
                                    @else
                                        <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                                    @endif
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
@section('js')
    <script>
        function handleDiv(value, cls) {
            if (parseInt(value) == 1) {
                $('.' + cls).removeClass('d-none');
                return
            }

            $('.' + cls).addClass('d-none')
        }
    </script>
@endsection