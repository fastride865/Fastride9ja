@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
         @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title">
                        <i class="icon fa-gears" aria-hidden="true"></i>
                        @lang("$string_file.stripe_configuration")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              action="{{ route('merchant.stripe_connect_configuration.store') }}">
                            @csrf
                            <fieldset>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize">@lang("$string_file.personal_document")</label>
                                            {{ Form::select('personal_document',isset($docuement_list) ? $docuement_list : [],old('personal_document_id',isset($merchant_stripe_connect->personal_document_id) ? $merchant_stripe_connect->personal_document_id : 0),array('class' => 'form-control select2','required')) }}
                                            @if($errors->first('personal_document'))
                                                <span class="text-danger">{{$errors->first('personal_document')}}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize">@lang("$string_file.photo_front_document")</label>
                                            {{ Form::select('photo_front_document',isset($docuement_list) ? $docuement_list : [],old('photo_front_document_id',isset($merchant_stripe_connect->photo_front_document_id) ? $merchant_stripe_connect->photo_front_document_id : 0),array('class' => 'form-control select2','required')) }}
                                            @if($errors->first('photo_front_document'))
                                                <span class="text-danger">{{$errors->first('photo_front_document')}}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize">@lang("$string_file.photo_back_document")</label>
                                            {{ Form::select('photo_back_document',isset($docuement_list) ? $docuement_list : [],old('photo_back_document_id',isset($merchant_stripe_connect->photo_back_document_id) ? $merchant_stripe_connect->photo_back_document_id : 0),array('class' => 'form-control select2','required')) }}
                                            @if($errors->first('photo_back_document'))
                                                <span class="text-danger">{{$errors->first('photo_back_document')}}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize">@lang("$string_file.additional_document") </label>
                                            {{ Form::select('additional_document',isset($docuement_list) ? $docuement_list : [],old('additional_document_id',isset($merchant_stripe_connect->additional_document_id) ? $merchant_stripe_connect->additional_document_id : 0),array('class' => 'form-control select2','required')) }}
                                            @if($errors->first('additional_document'))
                                                <span class="text-danger">{{$errors->first('additional_document')}}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize">
                                                @lang("$string_file.stripe_connect_website") <span class="text-danger">*</span>
                                            </label>
                                            <input type="url" class="form-control"
                                                   name="business_website"
                                                   placeholder=""
                                                   value="@if(!empty($merchant_stripe_connect->business_website)) {{ $merchant_stripe_connect->business_website }} @endif"
                                                   required>
                                            @if ($errors->has('business_website'))
                                                <label class="danger">{{ $errors->first('business_website') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize">
                                                @lang("$string_file.email")<span class="text-danger">*</span>
                                            </label>
                                            <input type="email" class="form-control"
                                                   name="email"
                                                   placeholder="@lang("$string_file.email")"
                                                   value="@if(!empty($merchant_stripe_connect->email)){{$merchant_stripe_connect->email}}@else{{$merchant->email}}@endif"
                                                   required>
                                            @if ($errors->has('email'))
                                                <label class="danger">{{ $errors->first('email') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5>Note:- Please make sure the documents selected from dropdown are mandatory, have document number added in service area.</h5>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="form-actions right" style="margin-bottom: 3%">
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection