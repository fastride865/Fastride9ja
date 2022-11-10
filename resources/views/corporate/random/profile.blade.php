@extends('corporate.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('corporateadded'))
                <div class="alert dark alert-icon alert-info alert-dismissible"
                     role="alert">
                    <button type="button" class="close" data-dismiss="alert"
                            aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.corporateadded')
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ URL:: previous() }}">
                            <button type="button" class="btn btn-icon btn-success" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit"></i>
                        @lang('admin.update_corporate')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="{{ route('corporate.update.profile') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="name">
                                        @lang('admin.corporate_name')
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="corporate_name" name="corporate_name" value="{{Auth::user()->corporate_name}}"
                                           placeholder="@lang('admin.corporate_name')" required>
                                    @if ($errors->has('corporate_name'))
                                        <label class="text-danger">{{ $errors->first('corporate_name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="web_rest_key">
                                        @lang('admin.corporateemail') :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="email" value="{{Auth::user()->email}}"
                                           name="email" placeholder="@lang("$string_file.email")" required>
                                    @if ($errors->has('email'))
                                        <label class="danger">{{ $errors->first('email') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="firstName3">
                                        @lang('admin.corporate_contactno')
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="user_phone"
                                           name="phone" value="{{substr(Auth::user()->corporate_phone, strlen(Auth::user()->Country->phonecode))}}"
                                           placeholder="@lang('admin.corporate_contactno')" required>
                                    @if ($errors->has('phone'))
                                        <label class="danger">{{ $errors->first('phone') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="web_rest_key">
                                        @lang('admin.corporate_address')
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="address"
                                           name="address" value="{{Auth::user()->corporate_address}}"
                                           placeholder="@lang('admin.corporate_address')" required>
                                    @if ($errors->has('address'))
                                        <label class="text-danger">{{ $errors->first('address') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label class="form-control-label" for="firstName3">
                                            @lang("$string_file.country")
                                            <span class="danger">*</span>
                                        </label>
                                        <select class="form-control form-control-sm" name="country" id="country"
                                                required>
                                            <option value="">@lang("$string_file.select")</option>
                                        @foreach($countries  as $country)
                                            <option data-min="{{ $country->maxNumPhone }}"
                                                    data-max="{{ $country->maxNumPhone }}"
                                                    value="{{ $country->id }}" @if(Auth::user()->country_id == $country->id) selected @endif>{{  $country->CountryName }}({{ $country->phonecode }})</option>
                                        @endforeach
                                        </select>
                                        @if ($errors->has('country'))
                                            <label class="text-danger">{{ $errors->first('country') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="firstName3">
                                        @lang('admin.corporate_logo')
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="corporate_logo"
                                           name="corporate_logo">
                                    @if ($errors->has('corporate_logo'))
                                        <label class="text-danger">{{ $errors->first('corporate_logo') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="wb-check-circle"></i> @lang("$string_file.save")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection