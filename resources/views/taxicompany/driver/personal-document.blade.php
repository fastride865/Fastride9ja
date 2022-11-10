<h5 class="form-section col-md-12" style="color: black;">
    <i class="fa fa-file"></i> @lang("$string_file.personal_document")
</h5>
<hr>
@php
    $arr_uploaded_doc = [];
@endphp
@if(isset($driver['driver_document']) && !empty($driver['driver_document']))
    @php
        $arr_uploaded_doc = array_column($driver['driver_document'],NULL,'document_id');
    @endphp
@endif
@foreach($areas->documents as $document)
@php $expire_date = null;
$document_file = null; @endphp
@if(isset($arr_uploaded_doc[$document['id']]))
    @php
        $expire_date = $arr_uploaded_doc[$document['id']]['expire_date'];
        $document_file = $arr_uploaded_doc[$document['id']]['document_file'];
        $document_number = $arr_uploaded_doc[$document['id']]['document_number'];
    @endphp
@endif
{!! Form::hidden('all_doc[]',$document['id']) !!}
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="emailAddress5">
                {{ $document->DocumentName }}:
                <span class="text-danger">*</span>
            </label>
            @if(in_array($document['pivot']['document_id'],array_keys($arr_uploaded_doc)))
                <a href="{{get_image($document_file,'driver_document',$driver['merchant_id'])}}"
                   target="_blank">@lang("$string_file.view") </a>
            @endif
            <input type="file" class="form-control" id="document"
                   name="document[{{$document['id']}}]"
                   placeholder=""
                   @if($document['documentNeed'] == 1 && empty($document_file)))
                   required @endif>
            @if ($errors->has('documentname'))
                <label class="text-danger">{{ $errors->first('documentname') }}</label>
            @endif
        </div>
    </div>
    <div class="col-md-4">
        @if($document->expire_date == 1)
            <div class="form-group">
                <label for="datepicker">@lang("$string_file.expire_date")
                    <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="icon wb-calendar"
                                                          aria-hidden="true"></i></span>
                    </div>
                    <input type="text"
                           class="form-control customDatePicker1"
                           name="expiredate[{{$document->id}}]"
                           value="{{$expire_date}}"
                           placeholder="@lang("$string_file.expire_date")  "
                           @if($document['expire_date'] == 1 && empty($expire_date)) required
                           @endif
                           autocomplete="off">
                </div>
            </div>
        @endif
    </div>
    @if($document->document_number_required == 1)
        <div class="col-md-4">
            <div class="form-group">
                <label for="emailAddress5">
                    @lang("$string_file.document_number") :
                    <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" id="document_number"
                       name="document_number[{{$document['id']}}]"
                       placeholder="@lang("$string_file.document_number")"
                       value="{{isset($document_number) ? $document_number : ''}}"
                       required>
                @if ($errors->has('document_number'))
                    <label class="text-danger">{{ $errors->first('document_number') }}</label>
                @endif
            </div>
        </div>
    @endif
</div>
@endforeach

