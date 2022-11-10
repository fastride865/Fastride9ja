@extends('merchant.layouts.main')
@section('content')
    <style>
        .impo-text {
            color: red;
            font-size: 15px;
            text-wrap: normal;
            display: none;
        }
    </style>
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="{{ route('documents.index') }}">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                            @if(!empty($info_setting) && $info_setting->add_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin-left:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-add-file" aria-hidden="true"></i>
                        {!! $document['title'] !!}
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    {!! Form::open(['name'=>'','url'=>$document['submit_url'],'class'=>'steps-validation wizard-notification']) !!}
                    @php
                        $old_expire_status =  NULL;
                        $old_mandatory_status =  NULL;
                        $document_id =  NULL;
                    @endphp
                    @if(isset($document['data']->id) && !empty($document['data']->id))
                        @php
                            $document_id = $document['data']->id;
                            $old_expire_status =  $document['data']->expire_date;
                            $old_mandatory_status =  $document['data']->documentNeed;
                        @endphp
                    @endif
                    {!! Form::hidden('document_id',$document_id,['id'=>'document_id','readonly'=>true]) !!}

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="package_duation_name">
                                    @lang("$string_file.name") :
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::text('documentname',old('documentname',isset($document['data']->LanguageSingle->documentname) ? $document['data']->LanguageSingle->documentname : ''),['id'=>'documentname','class'=>'form-control','required'=>true,'placeholder'=>'']) !!}
                                @if ($errors->has('name'))
                                    <label class="text-danger">{{ $errors->first('name') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>@lang("$string_file.mandatory")? : <span
                                        class="text-danger">*</span></label>
                            <div class="form-group">
                                {!!  Form::select('documentNeed',$document['document_status'],old('documentNeed',$old_mandatory_status),['id'=>'documentNeed','class'=>'form-control','required'=>true,'old-mandatory-status'=>$old_mandatory_status]) !!}
                                @if ($errors->has('documentNeed'))
                                    <label class="text-danger">{{ $errors->first('documentNeed') }}</label>
                                @endif
                                <div class="impo-text" id="document_mandatory_text_div">
                                    @lang("$string_file.document_mandatory_text");
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>@lang("$string_file.expire_date") ?<span
                                        class="text-danger">*</span></label>
                            <div class="form-group">
                                {!!  Form::select('expire_date',$document['document_status'],old('expire_date',$old_expire_status),['id'=>'expire_date','class'=>'form-control','required'=>true,'old-expiry-status'=>$old_expire_status]) !!}
                                @if ($errors->has('expire_date'))
                                    <label class="text-danger">{{ $errors->first('expire_date') }}</label>
                                @endif
                                <div class="impo-text" id="expire_date_text_div">
                                    @lang("$string_file.expire_date_text");
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" id="expire_date_value_div" style="display: none">
                            <div class="form-group">
                                <label for="expire_date">
                                    @lang("$string_file.default_expire_date") :
                                    <span class="text-danger">*</span>
                                </label>
                                {!! Form::text('expire_date_value',old('expire_date_value'),['id'=>'expire_date_value','class'=>'form-control customDatePicker1','placeholder'=>trans('admin.expire_date'),'autocomplete'=>'off']) !!}
                                @if ($errors->has('expire_date_value'))
                                    <label class="text-danger">{{ $errors->first('expire_date_value') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>@lang("$string_file.document_number_required") ? : <span
                                        class="text-danger">*</span></label>
                            <div class="form-group">
                                @php $document_no_option = add_blank_option(get_status(true,$string_file),trans("$string_file.select")); @endphp
                                {!!  Form::select('document_number_required',$document_no_option,old('document_number_required',isset($document['data']->document_number_required) ? $document['data']->document_number_required : ''),['id'=>'document_number_required','class'=>'form-control','required'=>true]) !!}
                                @if ($errors->has('document_number_required'))
                                    <label class="text-danger">{{ $errors->first('document_number_required') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-actions float-right">
                        @if(!$is_demo)
                        {!! Form::submit($document['submit_button'],['class'=>'btn btn-primary','id'=>'']) !!}
                        @else
                            <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                        @endif
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
@endsection