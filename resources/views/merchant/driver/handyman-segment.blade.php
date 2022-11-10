@extends('merchant.layouts.main')
@section('content')
    <style>
        em {
            color: red;
        }
    </style>
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            @if(Session::has('handyman-document-expire-warning'))
                <p class="alert alert-info">{{ Session::get('handyman-document-expire-warning') }}</p>
            @endif
            @if(Session::has('handyman-document-expired-error'))
                <div class="alert dark alert-icon alert-danger" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon fa-warning" aria-hidden="true"></i> {{ Session::get('handyman-document-expired-error') }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('driver.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                         @lang("$string_file.driver_name") : {{$driver->first_name .' '.$driver->last_name}} ->  @lang("$string_file.handyman_services_configuration")
                    </h3>
                </header>
                @php $display = true; $selected_doc = []; $id = NULL @endphp
                @if(isset($driver->id) && !empty($driver->id))
                    @php $display = false;
                    $id =  $driver->id;
                    @endphp
                @endif
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>'','class'=>'steps-validation wizard-notification','id'=>'driver-handyman-segment','files'=>true,'url'=>route('merchant.driver.handyman.segment.save',$id)]) !!}
                    {!! Form::hidden("id",$id,['class'=>'','id'=>'id']) !!}
{{--                    <div class="row mt3">--}}
{{--                        <div class="col-md-12 mt-10">--}}
{{--                            <h5><i class="m-1 fa fa-user"></i> @lang('admin.handyman_config')--}}
{{--                            </h5>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                     @foreach($arr_segment_services as $key=>$segment)
                         @php $segment_id = $key; @endphp
                            <div class="border rounded p-4 mt-10 shadow-sm bg-light">
                                <div class="border rounded p-4 mb-2 bg-white">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <strong>{!! $segment['name'] !!}'s <br></strong>@lang("$string_file.services")
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

                                                            <div class="col-md-6">
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
                                                <label for="location3">@lang("$string_file.document")</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                        @php $arr_uploaded_doc = []; $document_file = ""; $expire_date = NULL; $document_number = "";@endphp
                                        @foreach($documents as $document)
                                         @if($segment_id == $document['pivot']->segment_id)
                                             @php $uploaded_document = isset($arr_segment_selected_document[$segment_id][$document->id]) ? $arr_segment_selected_document[$segment_id][$document->id] : NULL; @endphp
                                             @if($uploaded_document)
                                                 @php
                                                 $document_file = $uploaded_document->document_file;
                                                 $expire_date = $uploaded_document->expire_date;
                                                 $document_number = $uploaded_document->document_number;
                                                 @endphp
                                             @endif
                                            <div class="row">
                                            {!! Form::hidden('segment_document_id['.$segment_id.'][]',$document->id) !!}
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="emailAddress5">
                                                        {{ $document->DocumentName }}:
                                                        <span class="text-danger">*</span>
                                                        @if(!empty($uploaded_document)))
                                                            <a href="{{get_image($document_file,'segment_document')}}" target="_blank">@lang("$string_file.view") </a>
                                                        @endif
                                                    </label>
                                                    <input type="file" class="form-control" id="document"
                                                           name="segment_document[{{$segment_id}}][{{$document['id']}}]"
                                                           placeholder=""
                                                           @if($document['documentNeed'] == 1 && empty($document_file)) {{--required--}} @endif>
                                                    @if ($errors->has('documentname'))
                                                        <label class="text-danger">{{ $errors->first('documentname')}}
                                                        </label>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($document->expire_date == 1)
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="datepicker">@lang("$string_file.expire_date")
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="icon wb-calendar" aria-hidden="true"></i></span>
                                                            </div>
                                                            <input type="text" class="form-control customDatePicker1" name="expire_date[{{$segment_id}}][{{$document->id}}]"
                                                                   placeholder="@lang("$string_file.expire_date")  " value="{{isset($expire_date) ? $expire_date : ''}}"
                                                                   @if($document['expire_date'] == 1 && empty($expire_date)) {{--required--}} @endif autocomplete="off" >
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($document->document_number_required == 1)
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="emailAddress5">
                                                        @lang("$string_file.document_number") :
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="document_number"
                                                           name="document_number[{{$segment_id}}][{{$document['id']}}]"
                                                           placeholder="@lang("$string_file.document_number")"
                                                           value="{{$document_number}}">
{{--                                                    required--}}
                                                    @if ($errors->has('document_number'))
                                                        <label class="text-danger">{{ $errors->first('document_number') }}</label>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                         </div>
                                         @endif
                                        @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                     @endforeach
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i>@lang("$string_file.save")
                        </button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
        @endsection
@section('js')
<script>
</script>
@endsection