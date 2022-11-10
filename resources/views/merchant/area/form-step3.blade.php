@extends('merchant.layouts.main')
@section('content')
    <style>
        .hidden {
            display: none;
        }

        .segment_class {
            color: #0bb2d4;
        }

        em {
            color: red;
        }
    </style>
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
                        <a href="{{ route('countryareas.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                        {{isset($area->LanguageSingle) ? $area->LanguageSingle->AreaName : ''}} ->  @lang("$string_file.handyman_services")
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})
                    </h3>
                </header>
                @php $display = true; $selected_doc = []; $id = NULL @endphp
                @if(isset($area->id) && !empty($area->id))
                    @php $display = false;
                    $id =  $area->id;
                    @endphp
                @endif
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>'','class'=>'steps-validation wizard-notification','id'=>'country-area-step3','files'=>true,'url'=>route('countryareas.save.step3',$id)]) !!}
                    {!! Form::hidden("id",$id,['class'=>'','id'=>'id']) !!}

                    <div class="row mt3">
                        <div class="col-md-12 mt-10">
                            <h5><i class="m-1 fa fa-user"></i> @lang("$string_file.handyman_configuration")
                            </h5>
                        </div>
                    </div>
                     @foreach($arr_segment_services as $key=>$segment)
                            <div class="border rounded p-4 mt-10 shadow-sm bg-light">
                                <div class="border rounded p-4 mb-2 bg-white">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <strong>{!! $segment['name'] !!}</strong>
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        @foreach($segment['arr_services'] as $key_inner=>$service)
                                                            @php $service_type_id = $service['id'];
                                                            $arr_selected_services = isset($arr_selected_segment_service[$key]) ? $arr_selected_segment_service[$key] : [];
                                                            $checked = '';
                                                            @endphp
                                                            @if(in_array($service_type_id,$arr_selected_services))
                                                            @php $checked = 'checked'; @endphp
                                                            @endif

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <input name="segment_service_type[{{$key}}][]" value="{!! $service_type_id !!}" id="{!! $service_type_id !!}" class="form-group mr-20 mt-5 ml-20 area_segment" type="checkbox" {{$checked}}>{!! $service['locale_service_name'] !!}
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="location3">{!! $segment['name'] !!}'s @lang("$string_file.document")</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                {!! Form::select('segment_document['.$key.'][]',$documents,old('segment_document',isset($arr_segment_selected_document[$key]) ? $arr_segment_selected_document[$key] : []),["class"=>"form-control select2","id"=>"segment_document".$key,"multiple"=>true]) !!}
                                                @if ($errors->has('segment_document'))
                                                    <label class="text-danger">{{ $errors->first('segment_document') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                     @endforeach
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        @if(!$is_demo)
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                        </button>
                        @else
                            <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                        @endif
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'])
@endsection
@section('js')
<script>
    jQuery(document).ready(function () {


        jQuery.validator.addMethod("lettersonly", function (value, element) {
            return this.optional(element) || /^[A-Za-z0-9\s\-\_]+$/i.test(value);
        }, "Only alphabetical, Number, hyphen and underscore allow");

        $("#country-area-step3").validate({
            /* @validation states + elements
            ------------------------------------------- */
            errorClass: "has-error",
            validClass: "has-success",
            errorElement: "em",
            /* @validation rules
            ------------------------------------------ */
            rules: {
                "segment_service_type[][]": {
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
    });
    $(document).on('keypress', '#manual_toll_price', function (event) {
        if (event.keyCode == 46 || event.keyCode == 8) {
        } else {
            if (event.keyCode < 48 || event.keyCode > 57) {
                event.preventDefault();
            }
        }
    });
</script>
@endsection