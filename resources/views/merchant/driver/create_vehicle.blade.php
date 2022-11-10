@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            @if(Session::has('vehicle-document-expire-warning'))
                <p class="alert alert-info">{{ Session::get('vehicle-document-expire-warning') }}</p>
            @endif
            @if(Session::has('vehicle-document-expired-error'))
                <div class="alert dark alert-icon alert-danger" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon fa-warning" aria-hidden="true"></i> {{ Session::get('vehicle-document-expired-error') }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->add_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <a href="{{ route('driver.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        @lang("$string_file.add_vehicle")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          onSubmit="return validateForm();"
                          action="{{ route('merchant.driver.vehicle.store',$driver->id) }}">
                        @csrf
                        {!! Form::hidden('vehicle_model_expire',$vehicle_model_expire,['class'=>'']) !!}
                        @php
                            $vehicle_image = NULL;
                            $plate_image = NULL;
                            $vehicle_model_id = NULL;
                            $vehicle_id = NULL;
                            $registration_date = NULL;
                            $expire_date = NULL;
                            $yes_no = ["0"=>trans("$string_file.no"),"1"=>trans("$string_file.yes")];
                        @endphp
                        @if(!empty($vehicle_details))
                            @php
                                $vehicle_id = $vehicle_details['id'];
                                $vehicle_image = $vehicle_details['vehicle_image'];
                                $registration_date = $vehicle_details['vehicle_register_date'];
                                $expire_date = $vehicle_details['vehicle_expire_date'];
                                $plate_image = $vehicle_details['vehicle_number_plate_image'];
                            @endphp
                        @endif
                        {!! Form::hidden('vehicle_id',$vehicle_id,['id'=>"vehicle_id"]) !!}
                        {!! Form::hidden('request_from',$request_from,['id'=>"request_from"]) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3">@lang("$string_file.vehicle_type") <span class="text-danger">*</span>
                                        :</label>
                                    @if(empty($vehicle_id))
                                    <select class="form-control required"
                                            name="vehicle_type_id"
                                            id="vehicle_type_id"
                                            required>
                                        <option value="">@lang("$string_file.vehicle_type")</option>
                                        @foreach($vehicletypes as $vehicle)
                                            <option value="{{ $vehicle->id }}" {!! isset($vehicle_details['vehicle_type_id']) && $vehicle_details['vehicle_type_id'] == $vehicle->id ? "selected" : NULL !!}>{{ $vehicle->VehicleTypeName }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('vehicle_type_id'))
                                        <label class="text-danger">{{ $errors->first('vehicle_type_id') }}</label>
                                    @endif
                                    @else
                                        {!! Form::text('vehicle_type',$vehicle_details->VehicleType->vehicleTypeName,['class'=>'form-control','disabled'=>true]) !!}
                                    @endif
                                </div>
                            </div>
                            @if($appConfig->vehicle_make_text == 1 && empty($vehicle_id))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3">@lang("$string_file.vehicle_make") <span class="text-danger">*</span>
                                            :</label>
                                        <input type="text" class="form-control" name="vehicle_make_id"
                                               id="vehicle_make_id" required>
                                        @if ($errors->has('vehicle_make_id'))
                                            <label class="text-danger">{{ $errors->first('vehicle_make_id') }}</label>
                                        @endif
                                    </div>
                                </div>

                            @else
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3">@lang("$string_file.vehicle_make") <span class="text-danger">*</span>
                                            :</label>
                                        @if(empty($vehicle_id))
                                        <select class="form-control required"
                                                name="vehicle_make_id"
                                                onchange="return vehicleModel(this.value)"
                                                id="vehicle_make_id"
                                                required>
                                            <option value="">--@lang("$string_file.select")--</option>
                                            @foreach($vehiclemakes as $vehiclemake)
                                                <option value="{{ $vehiclemake->id }}" {!! isset($vehicle_details['vehicle_make_id']) && $vehicle_details['vehicle_make_id'] == $vehiclemake->id ? "selected" : NULL !!}>{{ $vehiclemake->VehicleMakeName }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('vehicle_make_id'))
                                            <label class="text-danger">{{ $errors->first('vehicle_make_id') }}</label>
                                        @endif
                                        @else
                                            {!! Form::text('vehicle_make',$vehicle_details->VehicleMake->vehicleMakeName,['class'=>'form-control','disabled'=>true]) !!}
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if($appConfig->vehicle_model_text == 1 && empty($vehicle_id))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3">@lang("$string_file.vehicle_model") <span class="text-danger">*</span>
                                            :</label>
                                        <input class="form-control" type="text" name="vehicle_model_id"
                                               id="vehicle_model_id" required>
                                        @if ($errors->has('vehicle_make_id'))
                                            <label class="text-danger">{{ $errors->first('vehicle_make_id') }}</label>
                                        @endif
                                    </div>
                                </div>
                                {{--<div class="col-md-4">--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label for="location3">@lang("$string_file.vehicle_seat") <span class="text-danger">*</span>--}}
                                            {{--:</label>--}}
                                        {{--<input class="form-control" type="text" name="vehicle_seat" id="vehicle_seat"--}}
                                               {{--required>--}}
                                        {{--@if ($errors->has('vehicle_seat'))--}}
                                            {{--<label class="text-danger">{{ $errors->first('vehicle_seat') }}</label>--}}
                                        {{--@endif--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            @else
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3">@lang("$string_file.vehicle_model") <span class="text-danger">*</span>
                                            :</label>
                                        @if(empty($vehicle_id))
                                        <select class="form-control required"
                                                name="vehicle_model_id"
                                                id="vehicle_model_id"
                                                required>
                                            <option value="">--@lang("$string_file.select")--</option>
                                        </select>
                                        @if ($errors->has('vehicle_make_id'))
                                            <label class="text-danger">{{ $errors->first('vehicle_make_id') }}</label>
                                        @endif
                                        @else
                                            {!! Form::text('vehicle_model',$vehicle_details->VehicleModel->vehicleModelName,['class'=>'form-control','disabled'=>true]) !!}
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3">@lang("$string_file.vehicle_number") <span class="text-danger">*</span>
                                        :</label>
                                    <input type="text" class="form-control"
                                           id="vehicle_number"
                                           name="vehicle_number"
                                           placeholder="@lang("$string_file.vehicle_number") "
                                           value="{!! isset($vehicle_details['vehicle_number']) ? $vehicle_details['vehicle_number'] : NULL !!}" pattern="^[a-zA-Z0-9]+$" title="@lang("$string_file.value_should_contain_alpha_numeric")"
                                           required>
                                    @if ($errors->has('vehicle_number'))
                                        <label class="text-danger">{{ $errors->first('vehicle_number') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3">@lang("$string_file.vehicle_image") <span class="text-danger">*</span>
                                        :</label>
                                    @if(!empty($vehicle_image))
                                        <a href="{{get_image($vehicle_image,'vehicle_document')}}"
                                           target="_blank">@lang("$string_file.view") </a>
                                    @endif
                                    <input type="file" class="form-control" id="car_image"
                                           name="car_image" {!! empty($vehicle_image) ? "required" : '' !!}>
                                    @if ($errors->has('car_image'))
                                        <label class="text-danger">{{ $errors->first('car_image') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3">@lang("$string_file.number_plate")
                                        :</label>
                                    @if(!empty($plate_image))
                                        <a href="{{get_image($plate_image,'vehicle_document')}}"
                                           target="_blank">@lang("$string_file.view") </a>
                                    @endif
                                    <input type="file" class="form-control"
                                           id="car_number_plate_image"
                                           name="car_number_plate_image">
                                    @if ($errors->has('car_number_plate_image'))
                                        <label class="text-danger">{{ $errors->first('car_number_plate_image') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3">@lang("$string_file.vehicle_color")  <span
                                                class="text-danger">*</span> :</label>
                                    <input type="text" class="form-control"
                                           id="vehicle_color"
                                           name="vehicle_color"
                                           value="{!! isset($vehicle_details['vehicle_color']) ? $vehicle_details['vehicle_color'] : NULL !!}"
                                           placeholder="@lang("$string_file.vehicle")  @lang("$string_file.color") "
                                           required>
                                    @if ($errors->has('vehicle_color'))
                                        <label class="text-danger">{{ $errors->first('vehicle_color') }}</label>
                                    @endif
                                </div>
                            </div>
                            @if($vehicle_model_expire == 1)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3">@lang("$string_file.registered_date")  <span class="text-danger">*</span>
                                        :</label>
                                    <input type="text"
                                           class="form-control customDatePicker2"
                                           name="vehicle_register_date"
                                           value="{{old('vehicle_register_date',$registration_date)}}"
                                           placeholder="@lang("$string_file.vehicle_registered_date")  "
                                          required autocomplete="off">
                                    @if ($errors->has('vehicle_register_date'))
                                        <label class="text-danger">{{ $errors->first('vehicle_register_date') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3">@lang("$string_file.expire_date")  <span class="text-danger">*</span>
                                        :</label>
                                    <input type="text"
                                           class="form-control customDatePicker1"
                                           name="vehicle_expire_date"
                                           value="{{old('vehicle_expire_date',$expire_date)}}"
                                           placeholder=""
                                           required
                                           autocomplete="off">
                                    @if ($errors->has('vehicle_expire_date'))
                                        <label class="text-danger">{{ $errors->first('vehicle_expire_date') }}</label>
                                    @endif
                                </div>
                            </div>
                            @endif
                            @if(!empty($baby_seat_enable))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="baby_seat">@lang("$string_file.baby_seat_enable") :</label>
                                        {!! Form::select('baby_seat',$yes_no,old('baby_seat',isset($vehicle_details['baby_seat']) ? $vehicle_details['baby_seat'] : 0),['class'=>'form-control','id'=>'baby_seat']) !!}
                                        @if ($errors->has('baby_seat'))
                                            <label class="text-danger">{{ $errors->first('baby_seat') }}</label>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if(!empty($wheel_chair_enable))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="wheel_chair">@lang("$string_file.wheel_chair_enable") :</label>
                                        {!! Form::select('wheel_chair',$yes_no,old('wheel_chair',isset($vehicle_details['wheel_chair']) ? $vehicle_details['wheel_chair'] : 0),['class'=>'form-control','id'=>'wheel_chair']) !!}
                                        @if ($errors->has('wheel_chair'))
                                            <label class="text-danger">{{ $errors->first('wheel_chair') }}</label>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if(!empty($vehicle_ac_enable))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ac_nonac">@lang("$string_file.ac_enable") :</label>
                                        {!! Form::select('ac_nonac',$yes_no,old('ac_nonac',isset($vehicle_details['ac_nonac']) ? $vehicle_details['ac_nonac'] : 0),['class'=>'form-control','id'=>'ac_nonac']) !!}
                                        @if ($errors->has('ac_nonac'))
                                            <label class="text-danger">{{ $errors->first('ac_nonac') }}</label>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div id="vehicle-doc-segment">
                            {!! $vehicle_doc_segment !!}
                        </div>

                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit"
                                    class="btn btn-primary">
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
@section('js')
    <script>
        $(document).on('change','#vehicle_type_id',function(e){
            var vehicle_type  = $("#vehicle_type_id option:selected").val();
            var driver = {!! $driver->id !!};
            if (driver != "") {
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "{{ route('ajax.services') }}",
                    data: {driver_id: driver, vehicle: vehicle_type},
                    success: function (data) {
                        $('#vehicle-doc-segment').html(data);
                        var dateToday = new Date();
                        $('.customDatePicker1').datepicker({
                            format: 'yyyy-mm-dd',
                            startDate: dateToday,
                            onRender: function (date) {
                                return date.valueOf() < now.valueOf() ? 'disabled' : '';
                            }
                        });
                    }
                });
            }
        });

        function vehicleModel() {
            var vehicle_type_id = document.getElementById('vehicle_type_id').value;
            var vehicle_make_id = document.getElementById('vehicle_make_id').value;
            if (vehicle_type_id == "") {
                alert("Select Vehicle Type");
                var vehicle_make_index = document.getElementById('vehicle_make_id');
                vehicle_make_index.selectedIndex = 0;
                return false;
            } else {
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "{{ route('ajax.vehiclemodel') }}",
                    data: {vehicle_type_id: vehicle_type_id, vehicle_make_id: vehicle_make_id},
                    success: function (data) {
                        $('#vehicle_model_id').html(data);
                    }
                });
            }
        }
    </script>
@endsection