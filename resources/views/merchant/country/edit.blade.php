@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading"><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->edit_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="{{ route('country.index') }}">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang("$string_file.edit_country")
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{route('country.update', $country->id)}}">
                        {{method_field('PUT')}}
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.name")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name"
                                           name="name"
                                           placeholder=""
                                           value="@if(!empty($country->LanguageCountrySingle)) {{ $country->LanguageCountrySingle->name }} @endif"
                                           required>
                                    @if ($errors->has('name'))
                                        <label class="text-danger">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ProfileImage">
                                        @lang("$string_file.isd_code")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                                                    <span class="input-group-text"
                                                                          id="basic-addon1">+</span>
                                        </div>
                                        <input type="number" class="form-control" readonly
                                               id="phonecode"
                                               name="phonecode"
                                               value="{{  str_replace("+","",$country->phonecode) }}"
                                               placeholder="@lang("$string_file.isd_code")"
                                               required>
                                    </div>
                                    @if ($errors->has('phonecode'))
                                        <label class="text-danger">{{ $errors->first('phonecode') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.iso_code_detail")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="isocode"
                                           name="isoCode"
{{--                                           readonly--}}
                                           value="{{ $country->isoCode }}"
                                           placeholder=""
                                           required>
                                    @if ($errors->has('isoCode'))
                                        <label class="text-danger">{{ $errors->first('isoCode') }}</label>
                                    @endif
                                    <label class="text-danger">Eg:ISO code of $ is USD</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.country_code")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="country_code" readonly
                                           name="country_code"
                                           value="{{ old('country_code',$country->country_code) }}"
                                           placeholder="@lang('admin.country_code')" required>
                                    @if ($errors->has('country_code'))
                                        <label class="text-danger">{{ $errors->first('country_code') }}</label>
                                    @endif
                                    <label class="text-danger">Eg:Country code of India is IN</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3">@lang("$string_file.distance_unit")
                                    </label>
                                    <select class="c-select form-control"
                                            id="distance_unit"
                                            name="distance_unit" required>
                                        <option value="1"
                                                @if($country->distance_unit == 1) selected @endif>@lang("$string_file.km")</option>
                                        <option value="2"
                                                @if($country->distance_unit == 2) selected @endif>@lang("$string_file.miles")</option>
                                    </select>
                                    @if ($errors->has('distance_unit'))
                                        <label class="text-danger">{{ $errors->first('distance_unit') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ProfileImage">
                                        @lang("$string_file.min_digits")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="min_digits"
                                           name="minNumPhone"
                                           placeholder=""
                                           value="{{ $country->minNumPhone }}" required
                                           min="1" max="25">
                                    @if ($errors->has('minNumPhone'))
                                        <label class="text-danger">{{ $errors->first('minNumPhone') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_digits">
                                        @lang("$string_file.max_digits")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control"
                                           id="max_digits"
                                           name="maxNumPhone"
                                           placeholder=""
                                           value="{{ $country->maxNumPhone }}" required
                                           min="1" max="25">
                                    @if ($errors->has('maxNumPhone'))
                                        <label class="text-danger">{{ $errors->first('maxNumPhone') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="online_transaction">
                                        @lang("$string_file.online_transaction_code")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="online_transaction"
                                           name="online_transaction" required
                                           value="{{ $country->transaction_code }}"
                                           placeholder="">
                                    @if ($errors->has('online_transaction'))
                                        <label class="text-danger">{{ $errors->first('online_transaction') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sequance">
                                        @lang("$string_file.sequence")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="sequance"
                                           name="sequance" required value="{{ $country->sequance }}"
                                           placeholder="">
                                    @if ($errors->has('sequance'))
                                        <label class="text-danger">{{ $errors->first('sequance') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if ($applicationConfig->user_document == 1)
                            <div class="row">
                                <div class="col-md-4">
                                    <h4>@lang("$string_file.document_configuration")</h4>
                                    <hr>
                                    <div class="form-group">
                                        <label for="Documents">
                                            @lang("$string_file.document_for_user")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="select2 form-control" name="document[]"
                                                id="document"
                                                data-placeholder="@lang("$string_file.select_document")"
                                                multiple="multiple">
                                            @foreach($documents as $document)
                                                <option
                                                        @if(in_array($document->id, array_pluck($country->documents,'id'))) selected @endif
                                                value="{{ $document->id }}"
                                                >
                                                    {{ $document->DocumentName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('document'))
                                            <label class="text-danger">{{ $errors->first('document') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> @lang("$string_file.update")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'])
@endsection
@section('js')
    <script>
        $(document).ready(
            function () {
                $("input[name=additional_details]").each(function () {
                    // console.log($(this).attr('value'));
                    if($(this).is(':checked')) {
                        if($(this).attr('value') == 1)
                        {
                            $('#parameter_name').attr('required',true);
                            $('#parameter_name').attr('disabled',false);
                            $('#parameter_name').parent().parent().removeClass('hide');
                            $('#placeholder').attr('required',true);
                            $('#placeholder').attr('disabled',false);
                            $('#placeholder').parent().parent().removeClass('hide');

                        }
                        //console.log("IN IF: "+$(this).attr('id')+' '+$(this).attr('value'));
                        // $(this).removeAttr('required');
                    }
                });
            });

        function extraparameters(data)
        {
            //console.log(data);
            if(data == 1)
            {
                $('#parameter_name').attr('required',true);
                $('#parameter_name').attr('disabled',false);
                $('#parameter_name').parent().parent().removeClass('hide');
                $('#placeholder').attr('required',true);
                $('#placeholder').attr('disabled',false);
                $('#placeholder').parent().parent().removeClass('hide');

            }else{
                $('#parameter_name').attr('required',false);
                $('#parameter_name').attr('disabled',true);
                $('#parameter_name').parent().parent().addClass('hide');
                $('#placeholder').attr('required',false);
                $('#placeholder').attr('disabled',true);
                $('#placeholder').parent().parent().addClass('hide');
            }
        }
        $(document).ready(function () {
            $('form#countryForm').submit(function () {
                $(this).find(':input[type=submit]').prop('disabled', true);
            });
        });
    </script>
@endsection