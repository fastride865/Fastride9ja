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
        .select2 {
            z-index: 10060 !important;/*1051;*/
        }
    </style>
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
                        <a href="{{ route('countryareas.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                        <button type="button" class="btn btn-icon btn-primary float-right add_vehicle_config" style="margin:10px" id="" vehicle-type-id="" >
                            <i class="wb-plus">&nbsp;@lang("$string_file.add_more_vehicle")</i>
                        </button>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                        {{isset($area->LanguageSingle) ? $area->LanguageSingle->AreaName : ''}}  ->  @lang("$string_file.vehicle_configuration")
                        (@lang("$string_file.you_are_adding_in")  {{ strtoupper(Config::get('app.locale')) }})
                    </h3>
                </header>
                @php $display = true; $selected_vehicle_doc = []; $selected_doc = []; $id = NULL @endphp
                @if(isset($area->id) && !empty($area->id))
                    @php $display = false;
                    $id =  $area->id;
                    @endphp
                @endif
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>'','class'=>'steps-validation wizard-notification','id'=>'country-area-step2-table']) !!}
                    {{Form::hidden('area_id',$id,['id'=>'area_id'])}}
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.vehicle_type")</th>
                            <th>@lang("$string_file.vehicle_document")</th>
                            <th>@lang("$string_file.service_type")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tfoot></tfoot>
                        <tbody>
                        @php
                            $arr_vehicle_type_id = array_keys($arr_selected_vehicle_service);
                            $sr = 1;
                        @endphp
                        @foreach($arr_vehicle_type_id as $vehicle_type_id)
                            <tr>
                            <td>{{ $sr++ }}</td>
                            <td>{{$vehicles[$vehicle_type_id]}}</td>
                            <td>

                                @foreach($documents as $document_id=>$document)
                                      @php
                                         $vehicle_doc =    isset($arr_vehicle_selected_document[$vehicle_type_id]) ? $arr_vehicle_selected_document[$vehicle_type_id] : []
                                      @endphp

                                    @if(in_array($document_id,$vehicle_doc))
                                       {{$document}},
                                    @endif
                                @endforeach
                            </td>

                            <td>
                                @php $arr_selected_segments = isset($arr_selected_vehicle_service[$vehicle_type_id]) ? $arr_selected_vehicle_service[$vehicle_type_id] : [] ;@endphp
                                @foreach($arr_selected_segments as $segment_key=>$segment)
                                    @php $arr_selected_services = !empty($arr_selected_segments)  && isset($arr_selected_segments[$segment_key]) ? $arr_selected_segments[$segment_key] : [];
                                    $arr_services = array_key_exists($segment_key, $arr_segment_services) ? $arr_segment_services[$segment_key]['arr_services'] : [];
                                    @endphp
                                    {!! array_key_exists($segment_key, $arr_segment_services) ? $arr_segment_services[$segment_key]['name'] : '' !!} =>
                                    @foreach($arr_services as $service)
                                        @if(in_array($service['id'],$arr_selected_services))
                                            {{$service['locale_service_name']}},
                                        @endif
                                    @endforeach
                                    <br>
                                @endforeach
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary menu-icon btn_edit action_btn add_vehicle_config" id="add_vehicle_config" vehicle-type-id="{{$vehicle_type_id}}">
                                    <i class="wb-edit"></i>
                                </a>
                                @csrf
                                <button type="button"
                                        data-original-title="@lang("$string_file.delete")"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        vehicle-type-id="{{ $vehicle_type_id }}"
                                        country-area-id="{{ $id }}"
                                        class="btn btn-sm btn-danger menu-icon btn_delete action_btn delete_vehicle_config">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
{{--    Add vehicle module--}}
<div id="addVehicleDiv"></div>

@endsection
@section('js')
<script>
        // add vehicle modal
        $(document).on('click','.add_vehicle_config',function()
            {
                // $("#addVehicle").modal('hide');
                var vehicle_type_id = $(this).attr('vehicle-type-id');
                // if(vehicle_type_id !='')
                // {
                    var token = $('[name="_token"]').val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        type: "POST",
                        data: {
                            area_id: $("#area_id").val(),
                            vehicle_type_id: vehicle_type_id,
                        },
                        url: "{{ route('merchant.country_area.vehicle-type') }}"
                        ,success: function(response) {
                            // console.log(response);
                            // $("#vehicle-modal-body").html('');
                            $("#addVehicleDiv").html(response);
                            $('#vehicle_doc').select2({
                                dropdownParent: $('#addVehicle')
                            });
                            $("#addVehicleDiv").show();
                            $("#addVehicle").modal('show');
                        }
                    });
                // }
                // else
                // {
                //     // alert('in');
                //     $("#vehicle-modal-body").html('');
                //     var html_code = $("#add-vehicle-config").html();
                //     $("#vehicle-modal-body").html(html_code);
                //     $("#addVehicle").modal('show');
                // }

            }
    );

        jQuery.validator.addMethod("lettersonly", function (value, element) {
            return this.optional(element) || /^[A-Za-z0-9\s\-\_]+$/i.test(value);
        }, "Only alphabetical, Number, hyphen and underscore allow");

        $("#country-area-step2").validate({
            /* @validation states + elements
            ------------------------------------------- */
            errorClass: "has-error",
            validClass: "has-success",
            errorElement: "em",
            /* @validation rules
            ------------------------------------------ */
            rules: {
                "vehicle_type[]": {
                    required: true,
                },
                "vehicle_service_type[]": {
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
    // });
    $(document).on('keypress', '#manual_toll_price', function (event) {
        if (event.keyCode == 46 || event.keyCode == 8) {
        } else {
            if (event.keyCode < 48 || event.keyCode > 57) {
                event.preventDefault();
            }
        }
    });

        $(document).on('click','.delete_vehicle_config',function(){
            var token = $('[name="_token"]').val();
            swal({
                title: "@lang("$string_file.are_you_sure")",
                text: "@lang("$string_file.delete_vehicle_from_area")",
                // icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        type: "POST",
                        data: {
                            vehicle_type_id: $(this).attr('vehicle-type-id'),
                            country_area_id: $(this).attr('country-area-id'),
                        },
                        url: "{{route('merchant.area_vehicle.destroy')}}",
                    })
                        .done(function (data) {
                            console.log(data);
                            swal({
                                title: "DELETED!",
                                text: data,
                                type: "success",
                            }).then((isConfirm) =>{
                           window.location.href = "{{route('countryareas.add.step2',$id)}}";
                            });
                        });
                }
            });
        });
</script>
@endsection