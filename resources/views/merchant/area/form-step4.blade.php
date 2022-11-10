@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
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
                        {{isset($area->LanguageSingle) ? $area->LanguageSingle->AreaName : ''}} ->  @lang("$string_file.vehicle_type_categorization")
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
                    {!! Form::open(['name'=>'','class'=>'steps-validation wizard-notification','id'=>'country-area-step3','files'=>true,'url'=>route('country-area.category.vehicle.type.save',$id)]) !!}
                    {!! Form::hidden("country_area_id",$id,['class'=>'','id'=>'country_area_id']) !!}
                    {!! Form::hidden("segment_id",$segment_id,['class'=>'','id'=>'segment_id']) !!}
                    <div class="row mt3">
                        <div class="col-md-12 mt-10">
                            <h5><i class="m-1 fa fa-taxi"></i> {{$segment['name']}}'s @lang("$string_file.service_type")
                            </h5>
                        </div>
                    </div>
                    @php $key = NULL @endphp
                    <div class="border rounded p-4 mt-10 shadow-sm bg-light">
                        <div class="border rounded p-4 mb-2 bg-white">
                            <div class="row">
                                <div class="col-md-3"><h5>@lang("$string_file.service_type")</h5></div>
                                <div class="col-md-9 "><h5>@lang("$string_file.category_vehicle_type")</h5></div>
                            </div>
                            <hr>
                    @foreach($segment['arr_services'] as $key=>$service)
                        @php $service_type_id = $service['id'];
                            $arr_selected_services = isset($arr_selected_segment_service[$key]) ? $arr_selected_segment_service[$key] : [];
                            $checked = '';
                        @endphp
                        @if(in_array($service_type_id,$arr_selected_services))
                            @php $checked = 'checked'; @endphp
                        @endif
                                       <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {!! $service['locale_service_name']  !!}
                                                        {!! Form::hidden('service_type_id[]',$service_type_id,["class"=>"form-control","id"=>"service_type_id"]) !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        @foreach($arr_category as $key_inner=>$category)
                                                            @php $category_id = $category->id;
                                                            $checked = '';
                                                            $rand_num = $key_inner+1;
                                                            $selected_vehicle =  isset($arr_selected_vehicle[$service_type_id][$category_id]) ? $arr_selected_vehicle[$service_type_id][$category_id] : [];
                                                            @endphp
                                                            <div class="col-md-4">
                                                                <div class="form-group">

{{--                                                                    <input name="service_category[{{$service_type_id}}][]" value="{!! $category_id !!}" id="{!! $category_id !!}" class="form-group mr-20 mt-5 ml-20" type="checkbox" {{$checked}} required>--}}
                                                                    <input type="hidden" name="service_category[{{$service_type_id}}][]" value="{!! $category_id !!}" id="{!! $category_id !!}" class="">
                                                                    {!! $category->Name($category->merchant_id) !!}
{{--                                                                    <span class="red-900">*</span>--}}
                                                                </div>
                                                                {!! Form::select('category_vehicle['.$service_type_id.']['.$category_id.'][]',isset($arr_vehicle[$service_type_id]) ? $arr_vehicle[$service_type_id] : [],old('category_vehicle',$selected_vehicle),["class"=>"form-control select2","id"=>"category_vehicle".$key.$rand_num,"multiple"=>true]) !!}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                 <br>
                                @endforeach
                                </div>
                            </div>
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        @if(!$is_demo)
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i>@lang("$string_file.save")
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
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
@section('js')
<script>
    // jQuery(document).ready(function () {
    //     jQuery.validator.addMethod("lettersonly", function (value, element) {
    //         return this.optional(element) || /^[A-Za-z0-9\s\-\_]+$/i.test(value);
    //     }, "Only alphabetical, Number, hyphen and underscore allow");
    //
    //     $("#country-area-step3").validate({
    //         /* @validation states + elements
    //         ------------------------------------------- */
    //         errorClass: "has-error",
    //         validClass: "has-success",
    //         errorElement: "em",
    //         /* @validation rules
    //         ------------------------------------------ */
    //         rules: {
    //             "segment_service_type[][]": {
    //                 required: true,
    //             },
    //         },
    //         /* @validation highlighting + error placement
    //         ---------------------------------------------------- */
    //         highlight: function (element, errorClass, validClass) {
    //             $(element).parents(".form-group").addClass("has-error").removeClass("has-success");
    //             $(element).closest('.form-group').addClass(errorClass).removeClass(validClass);
    //         },
    //         unhighlight: function (element, errorClass, validClass) {
    //             $(element).parents(".form-group").addClass("has-success").removeClass("has-error");
    //             $(element).closest('.form-group').removeClass(errorClass).addClass(validClass);
    //         },
    //         errorPlacement: function (error, element) {
    //             if (element.is(":radio") || element.is(":checkbox")) {
    //                 error.insertAfter(element.parent());
    //                 // element.closest('.form-group').after(error);
    //             } else {
    //                 error.insertAfter(element.parent());
    //             }
    //         },
    //         submitHandler: function (form) {
    //             form.submit();
    //         }
    //     });
    // });
    // $(document).on('keypress', '#manual_toll_price', function (event) {
    //     if (event.keyCode == 46 || event.keyCode == 8) {
    //     } else {
    //         if (event.keyCode < 48 || event.keyCode > 57) {
    //             event.preventDefault();
    //         }
    //     }
    // });
</script>
@endsection