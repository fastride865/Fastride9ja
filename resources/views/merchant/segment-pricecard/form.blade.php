@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->add_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="{{ route('merchant.segment.price_card') }}">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        @lang("$string_file.add_price_card")
                    </h3>
                </header>
                @php $id = NULL; @endphp
                @if(isset($data['price_card']['id']))
                    @php $id = $data['price_card']['id'];
                    @endphp
                @endif
                {!! Form::hidden('segment_price_card_id',$id,['id' =>'segment_price_card_id']) !!}
                @php $min_hour_req = false; $service_type_req = false; @endphp
                @if($id != NULL && $data['price_card']['price_type'] == 2)
                    @php $min_hour_req = true; @endphp
                @else
                    @php $service_type_req = true; @endphp
                @endif
                <div class="panel-body container-fluid">
                    <section id="validation">
                        {!! Form::open(["class"=>"steps-validation wizard-notification","files"=>true,"url"=>route("segment.price_card.save",$id)]) !!}
                        {!! Form::hidden('id',$id) !!}
                        <fieldset>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">@lang("$string_file.service_area") <span class="text-danger">*</span>
                                            </label>
                                            {!! Form::select('country_area_id',add_blank_option($data['arr_areas'],trans("$string_file.select")),old('country_area_id',isset($data['price_card']['country_area_id']) ? $data['price_card']['country_area_id'] :NULL),['class'=>'form-control','required'=>true,'id'=>'country_area_id','onChange'=>"getSegment()"]) !!}
                                            @if ($errors->has('country_area_id'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('country_area_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang("$string_file.segment") <span class="text-danger">*</span>
                                            </label>
                                            <div class="form-group">
                                                {!! Form::select('segment_id',add_blank_option($data['arr_segment'],trans("$string_file.select")),old('segment_id',isset($data['price_card']['segment_id']) ? $data['price_card']['segment_id'] :NULL),["class"=>"form-control","id"=>"area_segment","required"=>true,'onChange'=>"getService()"]) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang("$string_file.price_type") (@lang("$string_file.service_charges"))<span class="text-danger">*</span>
                                            </label>
                                            <div class="form-group">
                                                {!! Form::select('price_type',$data['arr_type'],old('price_type',isset($data['price_card']['price_type']) ? $data['price_card']['price_type'] :NULL),['class'=>'form-control','required'=>true,'id'=>'price_type']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.minimum_booking_amount")
                                                <span class="text-danger">*</span>
                                            </label>
                                            {!! Form::number('minimum_booking_amount',old('minimum_booking_amount',isset($data['price_card']['minimum_booking_amount']) ? $data['price_card']['minimum_booking_amount'] : ''),['class'=>'form-control','id'=>'minimum_booking_amount','placeholder'=>"","required"=>$min_hour_req,'min'=>0, 'step' => 'any']) !!}
                                            @if ($errors->has('minimum_booking_amount'))
                                                <label class="text-danger">{{ $errors->first('minimum_booking_amount') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang("$string_file.status") <span class="text-danger">*</span>
                                            </label>
                                            <div class="form-group">
                                                {!! Form::select('status',$data['arr_status'],old('service_type_id',isset($data['price_card']['status']) ? $data['price_card']['status'] :NULL),['class'=>'form-control','required'=>true,'id'=>'status']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="@if($id == NULL || (!empty($id) && $data['price_card']['price_type'] == 2)) custom-hidden @endif" id="service_type_div">
                                    {!! $data['arr_services'] !!}
                                </div>
                                    @php $hourly_required = false; @endphp
                                    @if(!empty($id) && $data['price_card']['price_type'] == 2)
                                    @php $hourly_required = true; @endphp
                                    @endif
                                <div class="@if($id == NULL || (!empty($id) && $data['price_card']['price_type'] == 1)) custom-hidden @endif" id="hourly_charges_div">
                                    <h5>@lang("$string_file.set_charges_as_hourly")</h5>
                                    <hr>
                                    <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.per_hour_amount")
                                                <span class="text-danger">*</span>
                                            </label>
                                            {!! Form::number('hourly_amount',old('amount',isset($data['price_card']['amount']) ? $data['price_card']['amount'] : ''),['class'=>'form-control','id'=>'hourly_amount','placeholder'=>"",'required'=>$hourly_required,'min'=>0]) !!}
                                            @if ($errors->has('hourly_amount'))
                                                <label class="text-danger">{{ $errors->first('hourly_amount') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </fieldset>
                        <div class="form-actions float-right">
                            @if(!$is_demo)
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-check-square-o"></i>{!! $data['submit_button'] !!}
                                    </button>
                            @else
                                <span style="color: red"> @lang("$string_file.demo_warning_message")</span>
                            @endif
                        </div>
                        {!! Form::close() !!}
                    </section>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
@endsection
@section('js')
    <script type="text/javascript">
        function getSegment() {
            $("#area_segment").empty();
            $("#area_segment").append('<option value="">@lang("$string_file.select")</option>');
            $("#service_type_id").empty();
            $("#service_type_id").append('<option value="">@lang("$string_file.select")</option>');
            var area_id = $("#country_area_id option:selected").val();
            if (area_id != "") {
                $("#loader1").show();
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: '<?php echo route('get.area.segment') ?>',
                    data: {area_id: area_id,segment_group_id:2},
                    success: function (data) {
                        $("#area_segment").empty();
                        $('#area_segment').html(data);
                    }
                });
                $("#loader1").hide();
            }
        }
        function getService() {
            var area_id = $("#country_area_id option:selected").val();
            var segment_id = $("#area_segment option:selected").val();
            var price_type = $("#price_type option:selected").val();
            var segment_price_card_id = $("#segment_price_card_id").val();
            // console.log(area_id);
            // setPriceTypeSetting(price_type);
            $('#service_type_div').html("");
            $("#service_type_div").hide();
            if (area_id != "" && price_type == 1) {
                // $("#loader1").show();
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: '<?php echo route('segment.price_card.services') ?>',
                    data: {area_id: area_id,segment_id:segment_id,segment_group:2,segment_price_card_id:segment_price_card_id},
                    success: function (data) {
                        $('#service_type_div').html(data);
                        $("#service_type_div").show();
                    }
                });
                // $("#loader1").hide();
            }
        }
            $(document).on("change","#price_type",function(e){
                var val = $(this).val();
                setPriceTypeSetting(val)

            });
        function setPriceTypeSetting(val)
        {
            getService();
            $("#service_type_div").hide();
            $("#hourly_charges_div").hide();
            if(val == 1)
            {
                $("#service_type_div").show();
            }
            else if(val == 2)
            {
                $("#hourly_charges_div").show();
            }
        }
    </script>
@endsection