@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('vehiclemake.index') }}">
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
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang("$string_file.vehicle_make") (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{route('vehiclemake.update', $vehiclemake->id)}}">
                        {{method_field('PUT')}}
                        @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.vehicle_make")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="vehicle_make"
                                               name="vehicle_make"
                                               value="@if(!empty($vehiclemake->LanguageVehicleMakeSingle)) {{ $vehiclemake->LanguageVehicleMakeSingle->vehicleMakeName }} @endif"
                                               placeholder=""
                                               required>
                                        @if ($errors->has('vehicle_make'))
                                            <label class="text-danger">{{ $errors->first('vehicle_make') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang("$string_file.logo")
                                            <span class="text-danger">*</span>
                                        </label><span style="color: blue">(@lang("$string_file.size"))</span><i
                                                class="fa fa-info-circle fa-1"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                title=""></i>
                                        <input type="file" class="form-control" id="vehicle_make_logo" name="vehicleMakeLogo"
                                               placeholder="@lang("$string_file.logo")">
                                        @if ($errors->has('vehicleMakeLogo'))
                                            <label class="text-danger">{{ $errors->first('vehicleMakeLogo') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            @lang("$string_file.description")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="description"
                                                  name="description" rows="3"
                                                  placeholder="">@if(!empty($vehiclemake->LanguageVehicleMakeSingle)) {{ $vehiclemake->LanguageVehicleMakeSingle->vehicleMakeDescription }} @endif</textarea>
                                        @if ($errors->has('description'))
                                            <label class="text-danger">{{ $errors->first('description') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>

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
