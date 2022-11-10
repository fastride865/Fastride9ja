@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
          @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('merchant.serviceType.index') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px">
                                    <i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang("$string_file.service") (In @lang("$string_file.segment") : {{$segment}})</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('merchant.serviceType.update',isset($service->id) ? $service->id : NULL) }}">
                        {{method_field('PUT')}}
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.service_type")<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="service"
                                           name="service" value="@if(isset($service->service_locale_name)) {{ $service->service_locale_name }} @endif"
                                           placeholder="" required>
                                    @if ($errors->has('service'))
                                        <label class="danger">{{ $errors->first('service') }}</label>
                                    @endif
                                    {!! Form::hidden('segment_id',$segment_id) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.description")<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="description"
                                           name="description" value="@if(isset($service->service_locale_description)) {{ $service->service_locale_description }} @endif"
                                           placeholder="" required>
                                    @if ($errors->has('description'))
                                        <label class="danger">{{ $errors->first('description') }}</label>
                                    @endif
                                    {!! Form::hidden('segment_id',$segment_id) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sequence">
                                        @lang("$string_file.sequence")<span class="text-danger">*</span>
                                    </label>
                                    {!! Form::number('sequence',old('sequence',isset($service['pivot']->sequence) ? $service['pivot']->sequence : 1),['class'=>'form-control','required'=>true,'id'=>'sequence','min'=>0,'max'=>100]) !!}
                                    @if ($errors->has('sequence'))
                                        <label class="danger">{{ $errors->first('sequence') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="icon">
                                        @lang("$string_file.icon")
                                        @if(isset($service['pivot']->service_icon) && $service['pivot']->service_icon != '')
                                            <a href="{{get_image($service['pivot']->service_icon,'service')}}" target="_blank">@lang("$string_file.view")</a>
                                        @endif
                                    </label>
                                    <input type="file" class="form-control" id="icon" name="icon" placeholder="@lang("$string_file.icon")">
                                </div>
                            </div>
                        </div>
                        <div class="form-actions float-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> @lang("$string_file.update")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
