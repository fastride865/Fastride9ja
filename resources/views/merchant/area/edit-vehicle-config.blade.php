<div class="modal fade" id="addVehicle" tabindex="-1" role="upload" aria-labelledby="examplePositionTops" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {!! Form::open(['name'=>'','class'=>'steps-validation wizard-notification','id'=>'country-area-step2','files'=>true,'url'=>route('countryareas.save.step2',$area->id)]) !!}
            {!! Form::hidden("id",$area->id,['class'=>'','id'=>'id']) !!}
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">@lang("$string_file.vehicle_configuration")</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="vehicle-modal-body">
                <div class="border rounded p-4 mt-10 shadow-sm bg-light" id="vehicle_count">
                    @php $vehicle_type_id = isset($vehicle_type_id) ? $vehicle_type_id : NULL;
                    @endphp
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="vehicle">@lang("$string_file.vehicle_type")<span class="text-danger">*</span> </label>
                                {{ Form::select('vehicle_type',add_blank_option($vehicles,trans("$string_file.select")), old('vehicle_type',isset($vehicle_type_id) ? $vehicle_type_id : NULL), ['class'=>'form-control segment_vehicle','id' =>'','required'=>true])  }}
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="location3">@lang("$string_file.vehicle_document")<span class="text-danger">*</span></label>
                                {!! Form::select('vehicle_document[]',$documents,old('vehicle_document',isset($arr_vehicle_selected_document[$vehicle_type_id]) ? $arr_vehicle_selected_document[$vehicle_type_id] : []),["class"=>"select2 form-control","id"=>"vehicle_doc","multiple"=>true,"required"=>true]) !!}
                                @if ($errors->has('vehicle_document'))
                                    <label class="text-danger">{{ $errors->first('vehicle_document') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="border rounded p-4 mb-2 bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                @foreach($arr_segment_services as $key =>$segment)
                                        @php
                                            $arr_selected_segments = isset($arr_selected_vehicle_service[$vehicle_type_id]) ? $arr_selected_vehicle_service[$vehicle_type_id] : [] ; @endphp
                                        @php $arr_selected_services = !empty($arr_selected_segments)  && isset($arr_selected_segments[$key]) ? $arr_selected_segments[$key] : [];@endphp
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <strong>{!! $segment['name'] !!}</strong>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="row">
                                                    @foreach($segment['arr_services'] as $key_inner=>$service)
                                                        @php $service_type_id = $service['id'];  $checked = ''; @endphp

                                                        @if(in_array($service_type_id,$arr_selected_services))
                                                            @php $checked = 'checked'; @endphp
                                                        @endif

                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <input name="vehicle_service_type[{{$key}}][]" value="{!! $service_type_id !!}" class="form-group mr-20 mt-5 ml-20" type="checkbox" id="{{$service_type_id}}" {{$checked}}>{!! $service['locale_service_name'] !!}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @if(!empty($vehicle_type_id))
                        <span class="text-danger">@lang("$string_file.note") :- @lang("$string_file.service_area_document_warning")</span>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                @if(!$is_demo)
                <button class="btn btn-secondary" type="button" data-dismiss="modal">@lang("$string_file.cancel")</button>
                <input type="submit" class="btn btn-primary btn" value="@lang("$string_file.submit")">
                @else
                    <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                @endif
            </div>
            {{Form::close()}}
        </div>
    </div>
</div>


