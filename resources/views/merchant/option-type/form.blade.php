@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("business-segment.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" >
                            <a href="{{ route('merchant.option-type.index') }}">
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
                        @php $heading = trans("$string_file.add"); @endphp
                        @if(isset($data['option']['id']))
                            @php $heading = trans("$string_file.edit"); @endphp
                        @endif
                        {{$heading}} @lang("$string_file.option_type")
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>'option','id'=>'option-form','files'=>true,'url'=>$data['save_url'],'method'=>'POST'] ) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="optionname">
                                    @lang("$string_file.type")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::text('type',old('type',isset( $data['option']['id']) ? isset($data['option']->LanguageOptionTypeSingle) ? $data['option']->LanguageOptionTypeSingle->type : "" : NULL),['id'=>'','class'=>'form-control','required'=>true,'placeholder'=>""]) !!}
                                @if ($errors->has('type'))
                                    <label class="text-danger">{{ $errors->first('type') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="optiontype">
                                    @lang("$string_file.charges_type")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('charges_type',$data['charges_type'],old('charges_type',isset($data['option']['charges_type']) ? $data['option']['charges_type'] : NULL),['id'=>'','class'=>'form-control','required'=>true]) !!}
                                @if ($errors->has('charges_type'))
                                    <label class="text-danger">{{ $errors->first('charges_type') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="select_type">
                                    @lang("$string_file.type")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('select_type',$data['select_type'],old('select_type',isset($data['option']['select_type']) ? $data['option']['select_type'] : NULL),['id'=>'','class'=>'form-control','required'=>true]) !!}
                                @if ($errors->has('select_type'))
                                    <label class="text-danger">{{ $errors->first('select_type') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sequence">
                                    @lang("$string_file.maximum_options_on_app")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::number('max_option_on_app',old('max_option_on_app',isset( $data ['option']['max_option_on_app']) ? $data['option']['max_option_on_app'] : NULL),['id'=>'','min'=>'0','class'=>'form-control','required'=>true,'placeholder'=>""]) !!}
                                @if ($errors->has('max_option_on_app'))
                                    <label class="text-danger">{{ $errors->first('max_option_on_app') }}</label>
                                @endif
                            </div>
                        </div>
{{--                        <div class="col-md-4">--}}
{{--                            <div class="form-group">--}}
{{--                                <label for="sequence">--}}
{{--                                    @lang("$string_file.sequence")--}}
{{--                                    <span class="text-danger">*</span>--}}
{{--                                </label>--}}
{{--                                {!! Form::number('sequence',old('sequence',isset( $data ['option']['sequence']) ? $data['option']['sequence'] : NULL),['id'=>'','class'=>'form-control','required'=>true,'placeholder'=>""]) !!}--}}
{{--                                @if ($errors->has('sequence'))--}}
{{--                                    <label class="text-danger">{{ $errors->first('sequence') }}</label>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">
                                    @lang("$string_file.status")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('status',$data['status'],old('status',isset($data['option']['status']) ? $data['option']['status'] : NULL),['id'=>'','class'=>'form-control','required'=>true]) !!}
                                @if ($errors->has('status'))
                                    <label class="text-danger">{{ $errors->first('status') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        @if(!$is_demo)
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                        </button>
                        @else
                            <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                        @endif
                    </div>
                    {!!  Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
@endsection