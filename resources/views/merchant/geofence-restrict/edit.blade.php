@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->edit_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="{{ route('geofence.restrict.index') }}">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title">
                      @lang("$string_file.restricted_area")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('geofence.restrict.save',$area->id) }}">
                        {{--                        {{method_field('PUT')}}--}}
                        @csrf
                        <div class="row">
                            <div class="col-xl-4 col-md-4">
                                <div class="example-wrap">
                                    <h4 class="example-title">@lang("$string_file.geofence_area")</h4>
                                    <p>
                                        @if(empty($area->LanguageSingle))
                                            {{ $area->LanguageAny->AreaName }}
                                        @else
                                            {{ $area->LanguageSingle->AreaName }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <div class="example-wrap">
                                    <h4 class="example-title">@lang("$string_file.service_area")</h4>
                                    <p>
                                        {{$area->Country->CountryName}}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.restrict_area_for")<span
                                                class="text-danger">*</span>
                                    </label>
                                    {{ Form::select('restrict_area',['' => 'Restrict For',1 => 'Pickup', 2 => 'Drop', 3 => 'Both'],old('restrict_area',isset($area->RestrictedArea->restrict_area) ? $area->RestrictedArea->restrict_area : ''),['class' => 'form-control', 'required']) }}
                                    @if ($errors->has('restrict_area'))
                                        <label class="text-danger">{{ $errors->first('restrict_area') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.restriction_type")<span
                                                class="text-danger">*</span>
                                    </label>
                                    {{ Form::select('restrict_type',[1 => 'Allow', 2 => 'Not Allow'],old('restrict_type',isset($area->RestrictedArea->restrict_type) ? $area->RestrictedArea->restrict_type : ''),['class' => 'form-control', 'required']) }}
                                    @if ($errors->has('restrict_type'))
                                        <label class="text-danger">{{ $errors->first('restrict_type') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.restricted_base_area")<span
                                                class="text-danger">*</span>
                                    </label>
                                    {{ Form::select('base_areas[]',$area_list, old('base_areas',isset($area->RestrictedArea->base_areas) ? explode(',',$area->RestrictedArea->base_areas) : ''),['class' => 'form-control select2', 'required', 'data-plugin' => 'select2', 'multiple']) }}
                                    @if ($errors->has('restrict_type'))
                                        <label class="text-danger">{{ $errors->first('restrict_type') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="example">
                                        <div class="float-left mr-20">
                                            <input type="checkbox" id="queue_system" name="queue_system"
                                                   data-plugin="switchery"
                                                    {{ (isset($area->RestrictedArea->queue_system) && $area->RestrictedArea->queue_system == 1) ? 'checked' : '' }} />
                                        </div>
                                        <label class="pt-3"
                                               for="queue_system">@lang("$string_file.restrict_area_queue")</label>
                                        @if ($errors->has('queue_system'))
                                            <label class="text-danger">{{ $errors->first('queue_system') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            <button type="submit" class="btn btn-primary float-right">
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