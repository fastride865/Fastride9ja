@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" >
                            <a href="{{ route('subscription.index') }}">
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
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                        {{$title}}
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="{{ $submit_url }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="package_name">
                                        @lang("$string_file.package_name") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    {!! Form::text('name',old('name', isset($package_edit) && !empty($package_edit) ? $package_edit->LangSubscriptionPackageSingle['name'] : ''),['class'=>'form-control','id'=>'package_name','required'=>true,'placeholder'=>""]) !!}
                                    @if ($errors->has('name'))
                                        <label class="text-danger">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group
">
                                    <label for="emailAddress5">
                                        @lang("$string_file.area")
                                        <span class="text-danger">*</span>
                                    </label>
                                    {!! Form::select('areas[]',$arr_area,old('areas[]',isset($package_edit) && !empty($package_edit) ? $package_edit->CountryArea()->pluck('country_area_id')->all() : null),['class'=>'form-control select2','multiple data-plugin="select2"'=>true,'required'=>true]) !!}
                                    @if ($errors->has('areas'))
                                        <label class="text-danger">{{ $errors->first('areas')
                                                            }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location6">@lang("$string_file.package_type") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    {!! Form::select('package_type',$package_type,old('package_type',isset($package_edit) && !empty($package_edit) ? $package_edit->package_type : 2),['class'=>'form-control','id'=>'package_type']) !!}
                                    @if ($errors->has('package_type'))
                                        <label class="text-danger">{{ $errors->first('package_type') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_trip">
                                        @lang("$string_file.maximum_rides")  :
                                        <span class="text-danger">*</span>
                                    </label>
                                    {!! Form::number('max_trip',old('max_trip', isset($package_edit) && !empty($package_edit) ? $package_edit->max_trip : ''),['class'=>'form-control','id'=>'max_trip','required'=>true,'placeholder'=>"",'min'=>1]) !!}
                                    @if ($errors->has('max_trip'))
                                        <label class="text-danger">{{ $errors->first('max_trip') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="package_price">
                                        @lang("$string_file.price") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    @php $disable = false;@endphp
                                    @if(isset($package_edit) && !empty($package_edit->id) && $package_edit->package_type == 1)
                                        @php $disable = true;@endphp
                                    @endif
                                    {!! Form::number('price',old('price', isset($package_edit) && !empty($package_edit) ? $package_edit->price : ''),['class'=>'form-control','id'=>'package_price','required'=>true,'placeholder'=>"",'min'=>0,'disabled'=>$disable]) !!}
                                    @if ($errors->has('price'))
                                        <label class="text-danger">{{ $errors->first('price') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location6">@lang("$string_file.duration") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    {!! Form::select('package_duration',$all_durations,old('package_duration',isset($package_edit->package_duration_id) ? $package_edit->package_duration_id : null),['class'=>'form-control','id'=>'location6','required'=>true]) !!}
                                    @if ($errors->has('package_duration'))
                                        <label class="text-danger">{{ $errors->first('package_duration') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3">@lang("$string_file.select_services") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <ul class="list-unstyled" style="display:inline;">
                                        @foreach($all_services  as $all_service)
                                            <li>
                                                <div class="checkbox">
                                                    <label class="checkbox-inline">
                                                        <input name="services[]" class="category" value="{{ $all_service['id'] }}" type="checkbox"  @if(isset($selected_services) && in_array($all_service->id, $selected_services))checked="checked" @endif>
                                                        {{$all_service->serviceName}}
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if ($errors->has('services'))
                                        <label class="text-danger">{{ $errors->first('services') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="expire_date">
                                        @lang("$string_file.expire_date") :
                                    </label>
                                    {!! Form::text('expire_date',old('expire_date', isset($package_edit) && !empty($package_edit) ? $package_edit->expire_date : ''),['class'=>'form-control customDatePicker1','id'=>'expire_date','placeholder'=>"",'autocomplete'=>'off']) !!}
                                    @if ($errors->has('expire_date'))
                                        <label class="text-danger">{{ $errors->first('expire_date') }}</label>
                                    @endif
                                </div>
                            </div>
                            {{--                                                    </div>--}}
                            {{--                                                    <div class="row">--}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="image">
                                        @lang("$string_file.image") :
                                    </label>
                                    <input type="file" class="form-control" id="image"
                                           name="image"
                                           placeholder="@lang("$string_file.image")">
                                    @if ($errors->has('image'))
                                        <label class="text-danger">{{ $errors->first('image') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="message">
                                        @lang("$string_file.description") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="message"
                                              name="description"
                                              rows="4"
                                              placeholder=""
                                              required>{{ old('description',(isset($package_edit) && !empty($package_edit)) ? $package_edit->LangSubscriptionPackageSingle['description'] : '') }}</textarea>
                                    @if ($errors->has('description'))
                                        <label class="text-danger">{{ $errors->first('description') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions float-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
@endsection


