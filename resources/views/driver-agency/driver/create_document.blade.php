@extends('taxicompany.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('success'))
                <div class="alert dark alert-icon alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert dark alert-icon alert-error alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon fa-warning" aria-hidden="true"></i>{{ session('error') }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('driver.index')}}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin: 10px;">
                                <i class="wb-reply" title="@lang("$string_file.all_drivers")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-upload" aria-hidden="true"></i>
                       @lang("$string_file.upload_document")  </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('taxicompany.driver.document.store',$id) }}">
                        @csrf
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
                                                    $document_file = null;@endphp
                            @if(isset($arr_uploaded_doc[$document['id']]))
                                @php
                                    $expire_date = $arr_uploaded_doc[$document['id']]['expire_date'];
                                    $document_file = $arr_uploaded_doc[$document['id']]['document_file'];
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
                                            <a href="{{get_image($document_file,'driver_document')}}" target="_blank">@lang("$string_file.view") </a>
                                        @endif
                                        <input type="file" class="form-control" id="document"
                                               name="document[{{$document['id']}}]"
                                               placeholder=""
                                               @if($document['documentNeed'] == 1 && empty($document_file))) required @endif>
                                        @if ($errors->has('documentname'))
                                            <label class="text-danger">{{ $errors->first('documentname') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    @if($document->expire_date == 1)
                                        <div class="form-group">
                                            <label  for="datepicker">@lang("$string_file.expire_date")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="icon wb-calendar" aria-hidden="true"></i></span>
                                                </div>
                                                <input type="text"
                                                       class="form-control customDatePicker1"
                                                       id="datepicker"
                                                       name="expiredate[{{$document->id}}]"
                                                       value="{{$expire_date}}"
                                                       placeholder="@lang("$string_file.expire_date")  "
                                                       @if($document['expire_date'] == 1 && empty($expire_date)) required @endif
                                                       autocomplete="off"
                                                >
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <div class="form-actions float-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> @lang('admin.save_and_add_vehicle')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
