@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="{{ route('pricingparameter.index') }}">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                            @if(!empty($info_setting) && $info_setting->add_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin-left:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        @lang("$string_file.pricing_parameter")
                    </h3>
                </header>
                @php $id = NULL; $on = "add"; $disable = false;@endphp
                @if(isset($data['pricing_parameter']['id']))
                    @php $id = $data['pricing_parameter']['id'];
                    $on = "edit"; $disable = true;
                     @endphp
                @endif
                <div class="panel-body container-fluid">
                    <section id="validation">
                        {!! Form::open(["class"=>"steps-validation wizard-notification","files"=>true,"url"=>route("priceparameter.save",$id)]) !!}
                        {!! Form::hidden('id',$id) !!}
                        {!! $data['segment_html'] !!}
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.name"):
                                            <span class="text-danger">*</span>
                                        </label>
                                        {!! Form::text('parametername',old('parametername',isset($data['pricing_parameter']->LanguageSingle->parameterName) ? $data['pricing_parameter']->LanguageSingle->parameterName : ''),['class'=>'form-control','placeholder'=>'','required'=>true]) !!}
                                        @if ($errors->has('parametername'))
                                            <label class="text-danger">{{ $errors->first('parametername') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.parameter_display_name"):
                                            <span class="text-danger">*</span>
                                        </label>
                                        {!! Form::text('parameter_display_name',old('parameter_display_name',isset($data['pricing_parameter']->LanguageSingle->parameterNameApplication) ? $data['pricing_parameter']->LanguageSingle->parameterNameApplication : ''),['class'=>'form-control','id'=>'parameter_display_name','placeholder'=>'','required'=>true]) !!}
                                        @if ($errors->has('parameter_display_name'))
                                            <label class="text-danger">{{ $errors->first('parameter_display_name') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.sequence") :
                                            <span class="text-danger">*</span>
                                        </label>
                                        {!! Form::number('sequence_number',old('sequence_number',isset($data['pricing_parameter']['sequence_number']) ? $data['pricing_parameter']['sequence_number'] : ''),['class'=>'form-control','id'=>'sequence_number','placeholder'=>'','required'=>true,'min'=>1]) !!}
                                        @if ($errors->has('sequence_number'))
                                            <label class="text-danger">{{ $errors->first('sequence_number') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">@lang("$string_file.type")<span
                                                    class="text-danger">*</span>
                                        </label>
                                        {!! Form::select('parameterType',get_price_parameter($string_file,$on),old('parameterType',isset($data['pricing_parameter']['parameterType']) ? $data['pricing_parameter']['parameterType'] :NULL),['class'=>'form-control','required'=>true,'id'=>'parameterType','disabled'=>$disable]) !!}
                                        @if ($errors->has('parametertype'))
                                            <span class="help-block">
                                                    <strong>{{ $errors->first('parametertype') }}</strong>
                                                </span>
                                        @endif
                                    </div>
                                </div>
                                @if(empty($id))
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>
                                                @lang("$string_file.applicable_for")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="form-group">
                                                {!! Form::select('price_type[]',merchant_price_type($merchant->RateCard),old('parameterType',isset($data['priceparameter']['parameterType']) ? $data['priceparameter']['parameterType'] :NULL),['class'=>'form-control select2','required'=>true,'id'=>'parameterType','multiple'=>true]) !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </fieldset>
                        <div class="form-actions float-right">
                            @if(!$is_demo)
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i>{!! $data['submit_button'] !!}
                            </button>
                            @else
                                <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                            @endif
                        </div>
                        {!! Form::close() !!}
                    </section>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
@endsection