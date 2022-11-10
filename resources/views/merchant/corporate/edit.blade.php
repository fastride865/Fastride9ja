@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('corporateadded'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.corporateadded')
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="{{ route('corporate.index') }}">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang('admin.edit_corporate')
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="{{ route('corporate.update',$corporate->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.corporate_name')<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="corporate_name"
                                           name="corporate_name" value="{{$corporate->corporate_name}}"
                                           placeholder="@lang('admin.corporate_name')" required>
                                    @if ($errors->has('corporate_name'))
                                        <label class="text-danger">{{ $errors->first('corporate_name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3">@lang("$string_file.service_area")</label>
                                    <select class="form-control" name="country" id="country"
                                            required>
                                        <option value="">@lang("$string_file.select")</option>
                                        @foreach($countries  as $country)
                                            <option data-min="{{ $country->maxNumPhone }}"
                                                    data-max="{{ $country->maxNumPhone }}"
                                                    value="{{ $country->id }}" @if($corporate->country_id == $country->id) selected @endif>{{  $country->CountryName }}({{ $country->phonecode }})</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('country'))
                                        <label class="text-danger">{{ $errors->first('country') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.corporate_contactno')<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="user_phone"
                                           name="phone" value="{{$corporate->corporate_phone}}"
                                           placeholder="@lang('admin.corporate_contactno')" required>
                                    @if ($errors->has('phone'))
                                        <label class="text-danger">{{ $errors->first('phone') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.corporateemail')<span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="email" value="{{$corporate->email}}"
                                           name="email" placeholder="@lang("$string_file.email")"
                                           required>
                                    @if ($errors->has('email'))
                                        <label class="text-danger">{{ $errors->first('email') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.corporate_address')<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="address"
                                           name="address" value="{{$corporate->corporate_address}}"
                                           placeholder="@lang('admin.corporate_address')"
                                           required>
                                    @if ($errors->has('address'))
                                        <label class="text-danger">{{ $errors->first('address') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.corporate_logo')<span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="corporate_logo"
                                           name="corporate_logo">
                                    @if ($errors->has('corporate_logo'))
                                        <label class="text-danger">{{ $errors->first('corporate_logo') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="wb-check-circle"></i> @lang("$string_file.update")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection