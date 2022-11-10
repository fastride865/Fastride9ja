@extends('merchant.layouts.main')
@section('content')
    @php $id = NULL; @endphp
    @if(isset($data['price_card']['id']))
        @php $id = $data['price_card']['id']; @endphp
    @endif
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
                            <a href="{{ route('food-grocery.price_card',[$price_card_for]) }}">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        @lang("$string_file.add_price_card")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        {!! Form::open(["class"=>"steps-validation wizard-notification","files"=>true,"url"=>route("food-grocery.price_card.save",$id)]) !!}
                        {!! Form::hidden('id',$id) !!}
                        {!! Form::hidden('price_card_for',$price_card_for) !!}
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">@lang("$string_file.service_area")<span
                                                    class="text-danger">*</span>
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
                                        <label>@lang("$string_file.service_type") <span
                                                    class="text-danger">*</span>
                                        </label>
                                        <div class="form-group">
                                            {!! Form::select('service_type_id',add_blank_option($data['arr_services'],trans("$string_file.select")),old('service_type_id',isset($data['price_card']['service_type_id']) ? $data['price_card']['service_type_id'] :NULL),['class'=>'form-control','required'=>true,'id'=>'service_type_id']) !!}
                                        </div>
                                    </div>
                                </div>
                                @if($price_card_for == 2)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.tax") (%)
                                                <!--<span class="text-danger">*</span>-->
                                            </label>
                                            {!! Form::number('tax',old('tax',isset($data['price_card']['tax']) ?
                                            $data['price_card']['tax'] : ''),['class'=>'form-control','id'=>'tax',
                                            'placeholder'=>'','min'=>$data['step_value'],'step'=>$data['step_value']]) !!}
                                            @if ($errors->has('tax'))
                                                <label class="text-danger">{{ $errors->first('tax') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                @if($price_card_for == 1)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.pickup_amount")
                                                <span class="text-danger">*</span>
                                            </label>
                                            {!! Form::number('pick_up_fee',old('pick_up_fee',isset($data['price_card']['pick_up_fee']) ? $data['price_card']['pick_up_fee'] : ''),['class'=>'form-control','id'=>'pick_up_fee','placeholder'=>'','required'=>true,'min'=>0,'step'=>$data['step_value']]) !!}
                                            @if ($errors->has('pick_up_fee'))
                                                <label class="text-danger">{{ $errors->first('pick_up_fee') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                @lang("$string_file.drop_off_amount")
                                                <span class="text-danger">*</span>
                                            </label>
                                            {!! Form::number('drop_off_fee',old('drop_off_fee',isset
                                            ($data['price_card']['drop_off_fee']) ? $data['price_card']['drop_off_fee'] : ''),['class'=>'form-control','id'=>'drop_off_fee','placeholder'=>'','required'=>true,'min'=>0,'step'=>$data['step_value']]) !!}
                                            @if ($errors->has('drop_off_fee'))
                                                <label class="text-danger">{{ $errors->first('drop_off_fee') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang("$string_file.status") <span class="text-danger">*</span>
                                        </label>
                                        <div class="form-group">
                                            {!! Form::select('status',$data['arr_status'],old('status',isset($data['price_card']['status']) ? $data['price_card']['status'] :NULL),['class'=>'form-control','required'=>true,'id'=>'status']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <h5 class=""><i class="icon fa-clock-o" aria-hidden="true"></i>
                            @lang("$string_file.cancel_order")
                        </h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">@lang("$string_file.cancel_order")
                                        <span
                                                class="text-danger">*</span>
                                    </label>
                                    {!! Form::select('cancel_charges',array(1 => trans("$string_file.enable"), 2 => trans("$string_file.disable")),old('cancel_charges',isset($data['price_card']['cancel_charges']) ? $data['price_card']['cancel_charges'] :2),['class'=>'form-control','required'=>true,'id'=>'cancel_charges','onChange'=>"changeCancellation()"]) !!}
                                    @if ($errors->has('cancel_charges'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cancel_charges') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang("$string_file.cancel_charges")
                                    </label>
                                    {!! Form::number('cancel_amount',old('cancel_amount',isset
                                    ($data['price_card']['cancel_amount']) ? $data['price_card']['cancel_amount'] :NULL),["class"=>"form-control","id"=>"cancel_amount",'min'=>$data['step_value'],'step'=>$data['step_value']]) !!}
                                    @if ($errors->has('cancel_amount'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cancel_amount') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang("$string_file.free_minutes")
                                    </label>
                                    {!! Form::number('cancel_time',old('cancel_time',isset($data['price_card']['cancel_time']) ? $data['price_card']['cancel_time'] :NULL),["class"=>"form-control","id"=>"cancel_time"]) !!}
                                    @if($errors->has('cancel_amount'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('cancel_amount') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <h5 class=""><i class="icon fa-map-marker" aria-hidden="true"></i>
                            @lang("$string_file.distance_slab_details")
                            (<span class="text-danger">@lang("$string_file.admin_slab_fill")</span>)

                            <button class="btn btn-info btn-sm float-right" type="button" id="add_more_slot">
                                <i class="fa fa-plus">@lang("$string_file.add_more")</i>
                            </button>
                        </h5>

                        @php  $arr_detail = !empty($data['price_card']->PriceCardDetail) ? $data['price_card']->PriceCardDetail->toArray() : [];
                        @endphp
                        <div id="time_slot_original">
                        @for($i = 0; $i<$slab_count; $i++)
                            @php
                                $required = $i == 0 ? true:false ;
                                $mandatory_astrick = $i == 0 ? "*":"";
                                $detail = isset($arr_detail[$i]) ? $arr_detail[$i] : NULL;
                            @endphp
                            {!! Form::hidden('price_card_detail_id[]',isset($detail['id']) ? $detail['id'] : NULL) !!}
                            <div class="row" id="row_id_{{$i}}">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">@lang("$string_file.distance")  @lang("$string_file.from")
                                            <span class="text-danger">{{$mandatory_astrick}}</span>
                                        </label>
                                        {!! Form::number('distance_from[]',old('distance_from[]',isset($detail['distance_from']) ? $detail['distance_from'] : ''),['class'=>'form-control','id'=>'distance_from','placeholder'=>'','required'=>$required,'min'=>0]) !!}
                                        @if ($errors->has('distance_from'))
                                            <span class="help-block"><strong>{{ $errors->first('distance_from') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">@lang("$string_file.distance")  @lang("$string_file.to") <span
                                                    class="text-danger">{{$mandatory_astrick}}</span>
                                        </label>
                                        {!! Form::number('distance_to[]',old('distance_to[]',isset($detail['distance_to']) ? $detail['distance_to'] : ''),['class'=>'form-control','id'=>'distance_from','placeholder'=>'','required'=>$required,'min'=>0]) !!}
                                        @if ($errors->has('distance_to'))
                                            <span class="help-block"><strong>{{ $errors->first('distance_from') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                @if($price_card_for == 2)
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="name">@lang("$string_file.cart_amount")
                                                <span class="text-danger">{{$mandatory_astrick}}</span>
                                            </label>
                                            {!! Form::number('cart_amount[]',old('cart_amount[]',isset
                                            ($detail['cart_amount']) ? $detail['cart_amount'] :NULL),
                                            ['class'=>'form-control','id'=>'cart_amount','placeholder'=>'',
                                            'required'=>$required,'min'=>$data['step_value'],'step'=>$data['step_value']]) !!}
                                            @if ($errors->has('cart_amount'))
                                                <span class="help-block"><strong>{{ $errors->first('cart_amount') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="name">@lang("$string_file.condition") <span
                                                        class="text-danger">{{$mandatory_astrick}}</span>
                                            </label>
                                            {!! Form::select('condition[]',$data['condition'],old('condition[]',isset($detail['condition']) ? $detail['condition'] :NULL),['class'=>'form-control','required'=>$required,'id'=>'condition']) !!}
                                            @if ($errors->has('condition'))
                                                <span class="help-block"><strong>{{ $errors->first('distance_from') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">@lang("$string_file.slab_amount") <span
                                                    class="text-danger">{{$mandatory_astrick}}</span>
                                        </label>
                                        {!! Form::number('slab_amount[]',old('slab_amount[]',isset($detail['slab_amount']) ? $detail['slab_amount'] : ''),['class'=>'form-control','id'=>'slab_amount','required'=>$required,'min'=>$data['step_value'],'step'=>$data['step_value']]) !!}
                                        @if ($errors->has('slab_amount'))
                                            <span class="help-block"><strong>{{ $errors->first('slab_amount') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="name">@lang("$string_file.status")
                                            <span class="text-danger">{{$mandatory_astrick}}</span>
                                        </label>
                                        {!! Form::select('detail_status[]',['1' =>trans("$string_file.active"),'2' =>trans("$string_file.inactive")],old('detail_status[]',isset($detail['status']) ? $detail['status'] :1),['class'=>'form-control','required'=>$required,'id'=>'detail_status']) !!}
                                        @if ($errors->has('detail_status'))
                                            <span class="help-block"><strong>{{ $errors->first('detail_status') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endfor
                        </div>
                        <input type="hidden" id="total_slab" value="{{$slab_count}}">
                        @if(isset($configuration->user_time_charges) && $configuration->user_time_charges == 1 && $price_card_for == 2)
                            @php $time_charges = isset($data['price_card']['time_charges_details']) ? json_decode($data['price_card']['time_charges_details']) : [] @endphp
                            {{-- except grocery--}}
                            <div id="time_charges_div"
                                 @if($id == NULL || (!empty($data['price_card']['segment_id'] && $data['price_card']['segment_id'] !=4))) class="custom-hidden" @endif>
                                <h5 class=""><i class="icon fa-clock-o" aria-hidden="true"></i>
                                    @lang("$string_file.time_charges")
                                </h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">@lang("$string_file.time_from")<span
                                                        class="text-danger">*</span>
                                            </label>
                                            {!! Form::text('time_from',old('time_from',isset($time_charges->time_from) ? $time_charges->time_from : NULL),['class'=>'form-control timepicker', "data-plugin"=>"clockpicker", "data-autoclose"=>"true",'id'=>'time_from']) !!}
                                            @if ($errors->has('time_from'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('time_from') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">@lang("$string_file.time_to")<span
                                                        class="text-danger">*</span>
                                            </label>
                                            {!! Form::text('time_to',old('time_to',isset($time_charges->time_to) ? $time_charges->time_to : NULL),['class'=>'form-control timepicker', "data-plugin"=>"clockpicker", "data-autoclose"=>"true", 'id'=>'time_to']) !!}
                                            @if ($errors->has('time_to'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('time_to') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">@lang("$string_file.charges_type")
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            {!! Form::select('charges_type',array('' => 'select','1' => trans("$string_file.flat"),'2' => trans("$string_file.percentage")),old('charges_type',isset($time_charges->charges_type) ? $time_charges->charges_type : NULL),['class'=>'form-control','id'=>'charges_type']) !!}
                                            @if ($errors->has('charges_type'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('charges_type') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">@lang("$string_file.charges")<span
                                                        class="text-danger">*</span>
                                            </label>
                                            {!! Form::number('charges',old('charges',isset($time_charges->charges) ? $time_charges->charges : NULL),['class'=>'form-control','id'=>'charges','min'=>$data['step_value'],'step'=>$data['step_value']]) !!}
                                            @if ($errors->has('charges'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('charges') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">@lang("$string_file.charges_parameter")
                                                <span class="text-danger">*</span>
                                            </label>
                                            {!! Form::text('charge_parameter',old('charge_parameter',isset($time_charges->charge_parameter) ? $time_charges->charge_parameter : NULL),['class'=>'form-control','id'=>'charge_parameter']) !!}
                                            @if ($errors->has('charge_parameter'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('charge_parameter') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endif
                        <div class="form-actions float-right">
                            @if(!$is_demo)
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check-square-o"></i>{!! $data['submit_button'] !!}
                                </button>
                            @else
                                <span style="color: red"
                                      class="float-right">@lang("$string_file.demo_warning_message")</span>
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
                    data: {area_id: area_id, segment_group_id: 1, sub_group_for_admin: 2},
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
            if (area_id != "") {
                $("#loader1").show();
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: '<?php echo route('merchant.area.services') ?>',
                    data: {area_id: area_id, segment_id: segment_id, segment_group: 2},
                    success: function (data) {
                        $('#service_type_id').html(data);
                    }
                });
                $("#loader1").hide();
            }
        }

        $(document).ready(function (e) {
            $(document).on("change", "#area_segment", function (e) {
                var segment_id = $("#area_segment option:selected").val();
                $("#time_charges_div").hide();
                if (segment_id == 4) {
                    $("#time_charges_div").show();
                }
            });
        });

        // add more slots
        $(document).ready(function (e) {
            $(document).on("click", "#add_more_slot", function (e) {
                var total_slab = $("#total_slab").val();
                var row_id = total_slab;
                var next_row = parseInt(row_id) + 1;
                $("#total_slab").val(next_row);
                var new_row = '<div class="row" id="row_id_' + row_id + '"><div class="col-md-2"><div class="form-group">' +
                    '<label for="name">@lang("$string_file.distance")  @lang("$string_file.from")' +
                    '<span class="text-danger">{{$mandatory_astrick}}</span>' +
                    '</label>' +
                    '{!! Form::number('distance_from[]',old('distance_from[]',''),['class'=>'form-control','id'=>'distance_from','placeholder'=>'','required'=>$required,'min'=>0]) !!}' +
                    '@if ($errors->has('distance_from'))' +
                    '<span class="help-block"><strong>{{ $errors->first('distance_from') }}</strong></span>' +
                    '@endif' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-2">' +
                    '<div class="form-group">' +
                    '<label for="name">@lang("$string_file.distance")@lang("$string_file.to")' +
                    '<span class="text-danger">{{$mandatory_astrick}}</span>' +
                    '</label>' +
                    '{!! Form::number('distance_to[]',old('distance_to[]',''),['class'=>'form-control','id'=>'distance_from','placeholder'=>'','required'=>$required,'min'=>0]) !!}' +
                    '@if ($errors->has('distance_to'))' +
                    '<span class="help-block"><strong>{{ $errors->first('distance_from') }}</strong></span>' +
                    '@endif' +
                    '</div>' +
                    '</div>' +
                    '@if($price_card_for == 2)' +
                    '<div class="col-md-2">' +
                    '<div class="form-group">' +
                    '<label for="name">@lang("$string_file.cart_amount")' +
                    '<span class="text-danger">{{$mandatory_astrick}}</span>' +
                    '</label>' +
                    '{!! Form::number('cart_amount[]',old('cart_amount[]',''),['class'=>'form-control','id'=>'cart_amount','placeholder'=>'','required'=>$required,'step'=>$data['step_value'],'min'=>$data['step_value']]) !!}' +
                    '@if ($errors->has('slab_amount'))' +
                    '<span class="help-block"><strong>{{ $errors->first('slab_amount') }}</strong></span>' +
                    '@endif' +
                    '</div></div>' +

                    '<div class="col-md-2">' +
                    '<div class="form-group">' +
                    '<label for="name">@lang("$string_file.condition")' +
                    '<span class="text-danger">{{$mandatory_astrick}}</span>' +
                    '</label>' +
                    '{!! Form::select('condition[]',$data['condition'],old('condition[]',NULL),['class'=>'form-control','required'=>$required,'id'=>'condition'])
                    !!}' +
                    '@if ($errors->has('condition'))' +
                    '<span class="help-block"><strong>{{ $errors->first('distance_from') }}</strong></span>' +
                    '@endif' +
                    '</div>' +
                    '</div>' +
                    '@endif' +
                    '<div class="col-md-2">' +
                    '<div class="form-group">' +
                    '<label for="name">@lang("$string_file.slab_amount")' +
                    '<span class="text-danger">{{$mandatory_astrick}}</span>' +
                    '</label>' +
                    '{!! Form::number('slab_amount[]',old('slab_amount[]',''),['class'=>'form-control','id'=>'slab_amount','required'=>$required,'step'=>$data['step_value'],'min'=>$data['step_value']]) !!}' +
                    '@if($errors->has('slab_amount'))' +
                    '<span class="help-block"><strong>{{ $errors->first('slab_amount') }}</strong></span>' +
                    '@endif' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-2">' +
                    '<div class="form-group">' +
                    '<label for="name"></label>' +
                    '<button type="button" class="btn btn-danger btn-sm mt-35 remove_button" id="' + row_id + '">' +
                    '@lang("$string_file.delete")' +
                    '</button>'+
                '</div>' +
                '</div>' +
                '</div>';

                $("#time_slot_original").append(new_row);

            });
        });

        $(document).ready(function (e)
        {
            $(document).on("click", ".remove_button", function (e)
            {
              var row_id = $(this).attr('id');
              $("#row_id_"+row_id).remove();
            });
        });

    </script>
@endsection