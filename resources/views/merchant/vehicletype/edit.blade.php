@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
           @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->edit_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <div class="btn-group float-right">
                            <a href="{{ route('vehicletype.index') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        @lang("$string_file.vehicle_type")
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{route('vehicletype.update', $vehicle->id)}}">
                        {{method_field('PUT')}}
                        @csrf
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.vehicle_type")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="vehicle_name"
                                               name="vehicle_name"
                                               value="@if($vehicle->LanguageVehicleTypeSingle) {{ $vehicle->LanguageVehicleTypeSingle->vehicleTypeName }} @endif"
                                               placeholder=""
                                               required>
                                        @if ($errors->has('vehicle_name'))
                                            <label class="text-danger">{{ $errors->first('vehicle_name')
                                                            }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                             @lang("$string_file.vehicle_rank")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="vehicle_rank"
                                               name="vehicle_rank"
                                               value="{{ $vehicle->vehicleTypeRank }}"
                                               placeholder=""
                                               min="1"
                                               required>
                                        @if ($errors->has('vehicle_rank'))
                                            <label class="text-danger">{{ $errors->first('vehicle_rank')
                                                            }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.sequence")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="sequence"
                                               name="sequence"
                                               value="{{ $vehicle->sequence }}"
                                               placeholder=""
                                               min="1"
                                               required>
                                        @if ($errors->has('sequence'))
                                            <label class="text-danger">{{ $errors->first('sequence') }}</label>
                                        @endif
                                    </div>
                                </div>
                                @if($vehicle_model_expire == 1)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <label>@lang("$string_file.model_expire_year") <span class="text-danger">*</span></label>
                                        </label>
                                        <input type="number" class="form-control" id="model_expire_year"
                                               name="model_expire_year" value="{{ $vehicle->model_expire_year }}" placeholder="" min="1" max="50" required>
                                        @if ($errors->has('model_expire_year'))
                                            <label class="text-danger">{{ $errors->first('model_expire_year') }}</label>
                                        @endif
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            @lang("$string_file.description")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="description"
                                                  name="description"
                                                  placeholder=""
                                                  rows="3">@if($vehicle->LanguageVehicleTypeSingle) {{ $vehicle->LanguageVehicleTypeSingle->vehicleTypeDescription }} @endif</textarea>
                                        @if ($errors->has('description'))
                                            <label class="text-danger">{{ $errors->first('description') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang("$string_file.image")
                                            <span class="text-danger">*</span>
                                        </label><span
                                                style="color: blue">(@lang("$string_file.size") 100*100 px)</span><i
                                                class="fa fa-info-circle fa-1"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                title=""></i>
                                        <input style="    height: 0%;" type="file" class="form-control" id="vehicle_image"
                                               name="vehicle_image"
                                               placeholder="">
                                        @if ($errors->has('vehicle_image'))
                                            <label class="text-danger">{{ $errors->first('vehicle_image')
                                                            }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <img src="{{ get_image($vehicle->vehicleTypeImage, 'vehicle')  }}" style="width:50%; height:100%; ">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang("$string_file.map_image")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <span
                                                style="color: blue">(@lang("$string_file.size") 60*60 px)
                                                        </span>
                                        <i
                                                class="fa fa-info-circle fa-1"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                title=""></i>
                                        <div class="row">
                                            @foreach(get_config_image('map_icon') as $path)
                                                <br>
                                                <div class="col-md-4 col-sm-6">
                                                    <input type="radio" name="vehicleTypeMapImage"
                                                           value="{{ $path }}"
                                                           id="male-radio-{{ $path }}" @if($vehicle['vehicleTypeMapImage'] == $path) checked @endif>                                            &nbsp;
                                                    <label for="male-radio-{{ $path }}"><img
                                                                src="{{ view_config_image($path)  }}"
                                                                style="width:10%; height:10%; margin-right:3%;">{{ explode_image_path($path) }}
                                                    </label>
                                                </div>
                                            @endforeach
                                            @if ($errors->has('vehicleTypeMapImage'))
                                                <label class="text-danger">{{ $errors->first('vehicleTypeMapImage') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="checkbox-custom checkbox-primary">
                                                <input type="checkbox" value="1" name="ride_now"
                                                       id="ride_now" @if($vehicle->ride_now == 1) checked=""  @endif>
                                                <label class="font-weight-400">@lang("$string_file.request_now")</label>
                                                <br>
                                                <input type="checkbox" value="1" name="ride_later"
                                                       id="ride_later" @if($vehicle->ride_later == 1) checked="" @endif>
                                                <label class="font-weight-400">@lang("$string_file.request_later")</label>
                                                <br>
                                                @if(in_array(5,$merchant->Service))
                                                <input type="checkbox" value="1" name="pool_enable"
                                                       id="pool_enable"
                                                       @if($vehicle->pool_enable == 1) checked=""  @endif>
                                                <label class="font-weight-400">@lang("$string_file.pool_enable")</label>
                                                    <br>
                                                    @endif
                                            </div><br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            @if(!$is_demo)
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> @lang("$string_file.update")
                            </button>
                            @else
                                <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'])
@endsection