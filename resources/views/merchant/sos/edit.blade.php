@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('sosedit'))
                <div class="alert dark alert-icon alert-success alert-dismissible">
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message330')
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('sos.index') }}">
                                <button type="button" class="btn btn-icon btn-success mr-1" style="margin:10px"><i
                                            class="fa fa-reply"></i>
                                </button>
                            </a>
                            @if(!empty($info_setting) && $info_setting->edit_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-edit"></i> @lang("$string_file.sos")
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{route('sos.update', $sos->id)}}">
                        {{method_field('PUT')}}
                        @csrf
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.name")<span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="name"
                                               name="name"
                                               value="@if($sos->LanguageSingle){{ $sos->LanguageSingle->name }}@endif"
                                               placeholder="" required>
                                        @if ($errors->has('name'))
                                            <label class="text-danger">{{ $errors->first('name') }}</label>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            @lang("$string_file.sos_number")<span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="number"
                                               name="number"
                                               placeholder="@lang("$string_file.phone")"
                                               value="{{$sos->number}}" required>
                                        @if ($errors->has('number'))
                                            <label class="text-danger">{{ $errors->first('number') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-actions right" style="margin-bottom: 2%">
                            <button type="submit" class="btn btn-primary float-right">
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