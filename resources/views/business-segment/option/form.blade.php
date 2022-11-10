@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("business-segment.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" >
                            <a href="{{ route('business-segment.option.index') }}">
                                <button type="button" class="btn btn-icon btn-success"style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        @php $heading = trans("$string_file.add"); @endphp
                        @if(isset($data['option']['id']))
                            @php $heading = trans("$string_file.edit"); @endphp
                        @endif
                       {{$heading}} @lang("$string_file.option")
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>'option','id'=>'option-form','files'=>true,'url'=>$data['save_url'],'method'=>'POST'] ) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="optionname">
                                    @lang("$string_file.name")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::text('name',old('name',isset( $data ['option']['id']) ? $data['option']->Name($data['option']['business_segment_id']) : NULL),['id'=>'','class'=>'form-control','required'=>true,'placeholder'=>""]) !!}
                                @if ($errors->has('name'))
                                    <label class="text-danger">{{ $errors->first('name') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="optiontype">
                                    @lang("$string_file.type")
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::select('option_type_id',$data['arr_option_type'],old('option_type_id',isset( $data ['option']['option_type_id']) ? $data['option']['option_type_id'] : NULL),['id'=>'','class'=>'form-control','required'=>true]) !!}
                                @if ($errors->has('option_type_id'))
                                    <label class="text-danger">{{ $errors->first('option_type_id') }}</label>
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
@endsection