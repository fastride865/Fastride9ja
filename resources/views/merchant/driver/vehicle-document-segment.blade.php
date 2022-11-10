<h5 class="form-section"><i class="fa fa-paperclip"></i>@lang("$string_file.upload_document")
</h5>
@php
    $arr_uploaded_doc =[];
    $expire_date = null;
    $document_file = null;
@endphp
@if(isset($vehicle_details->DriverVehicleDocument) && count($vehicle_details->DriverVehicleDocument->toArray()) > 0)
    @php
        $arr_uploaded_doc =  $vehicle_details->DriverVehicleDocument->toArray();
        $arr_uploaded_doc = array_column($arr_uploaded_doc,NULL, 'document_id');
        $arr_doc_id = array_column($arr_uploaded_doc,'document_id');
    @endphp
@endif
@foreach($docs->VehicleDocuments as $doc)
    @php $expire_date = null; $document_file = null;$document_number = NULL @endphp
    @if(isset($arr_uploaded_doc[$doc['pivot']['document_id']]))
        @php
            $expire_date = $arr_uploaded_doc[$doc['pivot']['document_id']]['expire_date'];
            $document_file = $arr_uploaded_doc[$doc['pivot']['document_id']]['document'];
            $document_number = $arr_uploaded_doc[$doc['pivot']['document_id']]['document_number'];
        @endphp
    @endif
    {!! Form::hidden('all_doc[]',$doc['id']) !!}
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="location3">
                    @if(empty($doc->LanguageSingle))
                        {{ $doc->LanguageAny->documentname }}
                    @else
                        {{ $doc->LanguageSingle->documentname }}
                    @endif
                        <span class="text-danger">*</span>:</label>
                @if(in_array($doc['pivot']['document_id'],array_keys($arr_uploaded_doc)))
                    <a href="{{get_image($document_file,'vehicle_document')}}"
                       target="_blank">@lang("$string_file.view") </a>
                @endif
                <input type="file" class="form-control"
                       name="document[{{$doc->id}}]"
                       placeholder=""
                       @if($doc->documentNeed == 1 && empty($document_file))) required @endif>
            </div>
        </div>
        @if($doc->expire_date == 1)
            <div class="col-md-4">
                <div class="form-group">
                    <label for="location3">
                        @lang("$string_file.expire_date")  <span class="text-danger">*</span>
                        :</label>
                    <input type="text"
                           class="form-control customDatePicker1"
                           name="expiredate[{{$doc->id}}]" value="{{$expire_date}}"
                           placeholder=""
                           @if($doc['expire_date'] == 1 && empty($expire_date)) required @endif
                           autocomplete="off">
                </div>
            </div>
        @endif
        @if($doc->document_number_required == 1)
            <div class="col-md-4">
                <div class="form-group">
                    <label for="emailAddress5">
                        @lang("$string_file.document_number") :
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="document_number"
                           name="document_number[{{$doc['id']}}]"
                           placeholder="@lang("$string_file.document_number")"
                           value="{{$document_number}}"
                           required>
                    @if ($errors->has('document_number'))
                        <label class="text-danger">{{ $errors->first('document_number') }}</label>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endforeach
<hr>
<h5 class="form-section"><i class="fa fa-paperclip"></i> @lang("$string_file.segment") & @lang("$string_file.services_configuration")
</h5>
{{--segment and services of selected vehicle type --}}
@foreach($arr_segment_services as $key=>$segment)
    @php $segment_id = $key; @endphp
    @if(count($segment['arr_services']) > 0)
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
                                    @php $service_type_id = $service['id']; $checked  = "";@endphp
                                    @if(in_array($service_type_id,$selected_services))
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
        </div>
    </div>
    @endif
@endforeach
