@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            {{--            file to display error and success message --}}
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="{{ route('segment.service-time-slot') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i class="wb-reply"></i>
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
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        @lang("$string_file.service_time_slots")
                    </h3>
                </header>
                @php $id = NULL; $arr_details_data = []; @endphp
                @if($data['service_time_slot']->ServiceTimeSlotDetail->count() > 0)
                    @php
                        $arr_details_data = $data['service_time_slot']->ServiceTimeSlotDetail->toArray();
                    @endphp
                @endif
                <div class="panel-body container-fluid">
                    <section id="validation">
                        {!! Form::open(["class"=>"steps-validation wizard-notification","id"=>"time-slot-details","url"=>route("service-time-slot.detail.save")]) !!}
                        {!! Form::hidden('service_time_slot_id',$data['service_time_slot']->id) !!}
                        {!! Form::hidden('time_format',$data['time_format']) !!}
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">@lang("$string_file.service_area") : </label>
                                        {{ $data['service_time_slot']->CountryArea->CountryAreaName }}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>@lang("$string_file.segment") : </label>
                                        {{ !empty($data['service_time_slot']->Segment->Name($data['service_time_slot']->merchant_id)) ? $data['service_time_slot']->Segment->Name($data['service_time_slot']->merchant_id) : $data['service_time_slot']->Segment->slag }}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label>@lang("$string_file.day") : </label>
                                            {{ (!empty($data['arr_day'])?$data['arr_day'][$data['service_time_slot']->day]:'') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> @lang("$string_file.maximum_no_of_slots")
                                            : </label>
                                        {{ $data['service_time_slot']->max_slot }}
                                    </div>
                                </div>
                            </div>
                            @php
                                $start = strtotime($data['service_time_slot']['start_time']);
                                $start = $data['time_format'] == 2 ? date("H:i", $start) : date("h:i a", $start);
                                $end = strtotime($data['service_time_slot']['end_time']);
                                $end = $data['time_format'] == 2 ? date("H:i", $end) : date("h:i a", $end);

                            @endphp
                            <h5>@lang("$string_file.service_time_slot_details")
                                ({{$start.' '.trans("$string_file.to").' '.$end}}) </h5>
                            <hr>
                            @for($i = 0; $i< $data['service_time_slot']['max_slot']; $i++)
                                @php
                                    $id = null; $start_time = null; $end_time = null;$text = null;
                                @endphp
                                @if(isset($arr_details_data[$i]))
                                    @php
                                        $id = $arr_details_data[$i]['id'];
                                        $start_time = $arr_details_data[$i]['from_time'];
                                        $end_time = $arr_details_data[$i]['to_time'];
                                        $text = $arr_details_data[$i]['slot_time_text'];
                                    @endphp
                                @endif
                                {!! Form::hidden('slot_detail_id[]',$id) !!}
                                <div class="row">
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <br>
                                                {{$i+1}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang("$string_file.start_time") <span class="text-danger">*</span>
                                            </label>
                                            <div class="form-group">
                                                <input name="start_time[]" type="text" value="{{$start_time}}"
                                                       class="form-control timepicker" data-min-time="{{$start}}"
                                                       data-max-time="{{$end}}" data-autoclose="true"
                                                       id="start_time{{$i}}" q="{{$i}}" onfocus="" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang("$string_file.end_time")<span
                                                        class="text-danger">*</span>
                                            </label>
                                            <div class="form-group">
                                                <input type="text" name="end_time[]" value="{{$end_time}}"
                                                       class="form-control timepicker" data-autoclose="true"
                                                       data-min-time="{{$start}}" data-max-time="{{$end}}"
                                                       id="end_time{{$i}}" q="{{$i}}" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    {{--                                <div class="col-md-3">--}}
                                    {{--                                    <div class="form-group">--}}
                                    {{--                                        <label>@lang('admin.time_text') <span class="text-danger">*</span>--}}
                                    {{--                                        </label>--}}
                                    {{--                                        <div class="form-group">--}}
                                    {{--                                            <input type="text" name="slot_time_text[]" value="{{$text}}" class="form-control"   autocomplete="off">--}}
                                    {{--                                        </div>--}}
                                    {{--                                    </div>--}}
                                    {{--                                </div>--}}
                                </div>
                            @endfor
                        </fieldset>
                        <div class="form-actions float-right">
                            @if(!$is_demo)
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i>{!! $data['submit_button'] !!}
                            </button>
                            @else
                                <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                            @endif
                        </div>
                        {!! Form::close() !!}
                    </section>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'])
@endsection
@section('js')
    <script>
        $('input').blur();
        jQuery.validator.addMethod("lettersonly", function (value, element) {
            return this.optional(element) || /^[A-Za-z0-9\s\-\_]+$/i.test(value);
        }, "Only alphabetical, Number, hyphen and underscore allow");

        $("#time-slot-details").validate({
            /* @validation states + elements
            ------------------------------------------- */
            errorClass: "has-error",
            validClass: "has-success",
            errorElement: "em",
            /* @validation rules
            ------------------------------------------ */
            rules: {
                "start_time[]": {
                    required: true,
                },
                "end_time[]": {
                    required: true,
                },
                "slot_time_text[]": {
                    required: true,
                },
            },
            /* @validation highlighting + error placement
            ---------------------------------------------------- */
            highlight: function (element, errorClass, validClass) {
                $(element).parents(".form-group").addClass("has-error").removeClass("has-success");
                $(element).closest('.form-group').addClass(errorClass).removeClass(validClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents(".form-group").addClass("has-success").removeClass("has-error");
                $(element).closest('.form-group').removeClass(errorClass).addClass(validClass);
            },
            errorPlacement: function (error, element) {
                if (element.is(":radio") || element.is(":checkbox")) {
                    error.insertAfter(element.parent());
                    // element.closest('.form-group').after(error);
                } else {
                    error.insertAfter(element.parent());
                }
            },
            submitHandler: function (form) {
                form.submit();
            }
        });

        // $('.timepicker').timepicker({
        //     // showMeridian: false,
        //     timeFormat: 'H:i',
        //     // 'showDuration': true
        // });
        //$('.clockpicker').clockpicker();
        // $('.clockpicker').clockpicker({
        //     placement: 'bottom',
        //     align: 'left',
        //     autoclose: true,
        //    'default': '12:50',
        //     'twelvehour':true
        // });
        @if($data['time_format'] == 2)
        $('.timepicker').timepicker({
            timeFormat: 'H:i',
            // showMeridian: false
            // 'showDuration': true
        });
        @else
        $('.timepicker').timepicker({
            // timeFormat: 'H:i',
            // showMeridian: false
            // 'showDuration': true
        });
        @endif
    </script>
@endsection