@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" >
                            <a href="{{ route('duration.index') }}">
                                <button type="button" class="btn btn-icon btn-success"style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                            @if(!empty($info_setting) && $info_setting->add_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class="wb-user" aria-hidden="true"></i>
                        {!! $duration['title'] !!} (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</h3>
                </header>
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>'','url'=>$duration['submit_url'],'file'=>true,'class'=>'steps-validation wizard-notification']) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="package_duation_name">
                                    @lang("$string_file.period") (@lang("$string_file.in_days")) :
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::number('sequence',old('sequence',isset($duration['data']['sequence']) ? $duration['data']['sequence'] : NULL),['id'=>'duration_period','class'=>'form-control','required'=>true,'min'=>1,'placeholder'=>""]) !!}
                                {{--                                                                {!! Form::select('sequence',$duration['duration_period'],old('sequence',isset($duration['data']['sequence']) ? $duration['data']['sequence'] : NULL),['id'=>'duration_period','class'=>'form-control','required'=>true]) !!}--}}
                                @if ($errors->has('duration_period'))
                                    <label class="text-danger">{{ $errors->first('duration_period') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="package_duation_name">
                                    @lang("$string_file.name") :
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::text('name',old('name',isset($duration['data']['LangPackageDurationAccMerchantSingle']['name']) ? $duration['data']['LangPackageDurationAccMerchantSingle']['name'] : ''),['id'=>'package_duation_name','class'=>'form-control','required'=>true,'placeholder'=>""]) !!}
                                @if ($errors->has('name'))
                                    <label class="text-danger">{{ $errors->first('name') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-actions float-right">
                        {!! Form::submit($duration['submit_button'],['class'=>'btn btn-primary','id'=>'']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
@endsection

