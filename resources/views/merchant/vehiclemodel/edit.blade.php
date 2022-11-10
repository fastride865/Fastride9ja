@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
           @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('vehiclemodel.index') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                            @if(!empty($info_setting) && $info_setting->edit_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class="wb-edit" aria-hidden="true"></i>
                        @lang("$string_file.vehicle_model") (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{route('vehiclemodel.update', $vehicleModel->id)}}">
                        {{method_field('PUT')}}
                        @csrf
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.vehicle_type")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" name="vehicletype"
                                                id="vehicletype" required>
                                            @foreach($vehicles as $vehicle)
                                                <option @if($vehicle->id == $vehicleModel->vehicle_type_id) selected
                                                        @endif value="{{ $vehicle->id }}">{{ $vehicle->VehicleTypeName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('vehicletype'))
                                            <label class="danger">{{ $errors->first('vehicletype') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.vehicle_make")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" name="vehiclemake"
                                                id="vehiclemake" required>
                                            @foreach($vehiclemakes as $vehiclemake)
                                                <option @if($vehiclemake->id == $vehicleModel->vehicle_make_id) selected
                                                        @endif value="{{ $vehiclemake->id }}">{{ $vehiclemake->VehicleMakeName }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('vehiclemake'))
                                            <label class="danger">{{ $errors->first('vehiclemake') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.vehicle_model")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="vehicle_model"
                                               name="vehicle_model"
                                               value="@if($vehicleModel->LanguageVehicleModelSingle) {{ $vehicleModel->LanguageVehicleModelSingle->vehicleModelName }} @endif"
                                               placeholder=""
                                               required>
                                        @if ($errors->has('vehicle_model'))
                                            <label class="danger">{{ $errors->first('vehicle_model') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.no_of_seat")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="vehicle_seat" name="vehicle_seat"
                                               placeholder="" value="{{ $vehicleModel->vehicle_seat }}" required min="1" max="50">
                                        @if ($errors->has('vehicle_seat'))
                                            <label class="danger">{{ $errors->first('vehicle_seat') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            @lang("$string_file.description")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="description"
                                                  name="description" rows="3"
                                                  placeholder="@lang("$string_file.description")">@if($vehicleModel->LanguageVehicleModelSingle) {{ $vehicleModel->LanguageVehicleModelSingle->vehicleModelDescription }} @endif</textarea>
                                        @if ($errors->has('description'))
                                            <label class="danger">{{ $errors->first('description') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            @if(!$is_demo)
                            <button type="submit" class="btn btn-primary">
                                <i class="icon fa-check-circle"></i> @lang("$string_file.update")
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