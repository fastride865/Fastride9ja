@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('companyadd'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.companyadd')
                </div>
            @endif
            @if(session('error'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('error') }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('taxicompany.index') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px">
                                    <i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang("$string_file.edit") @lang('admin.tax_company')
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="{{ route('taxicompany.update',$company->id) }}">
                        @csrf
                        @method('PUT')
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang('admin.company_name')
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="name"
                                               name="name" value="{{$company->name}}"
                                               placeholder="@lang('admin.company_name')" required>
                                        @if ($errors->has('name'))
                                            <label class="text-danger">{{ $errors->first('name') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang('admin.company_country')
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" id="country" name="country">
                                            <option value=""> --Select One--</option>
                                            @foreach($countries as $country)
                                                <option value="{{$country->id}}" @if($company->country_id == $country->id) selected @endif>{{$country->CountryName}}</option>
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
                                            @lang('admin.company_phone')
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="phone"
                                               name="phone" value="{{$company->phone}}"
                                               placeholder="@lang('admin.company_phone')" required>
                                        @if ($errors->has('phone'))
                                            <label class="text-danger">{{ $errors->first('phone') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            @lang('admin.company_email')
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control" id="email"
                                               name="email" value="{{$company->email}}"
                                               placeholder="@lang('admin.company_email')" required>
                                        @if ($errors->has('email'))
                                            <label class="text-danger">{{ $errors->first('email') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang('admin.company_contact_pers')
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="contact_pers"
                                               name="contact_person" value="{{$company->contact_person}}"
                                               placeholder="@lang('admin.company_contact_pers')" required>
                                        @if ($errors->has('contact_person'))
                                            <label class="text-danger">{{ $errors->first('contact_person') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang('admin.company_add')
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="address"
                                               name="address" value="{{$company->address}}"
                                               placeholder="@lang('admin.company_add')" required>
                                        @if ($errors->has('address'))
                                            <label class="text-danger">{{ $errors->first('address') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4" id="areaList">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang("$string_file.password")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input class="form-control" type="password" name="password" id="password"
                                               placeholder="@lang("$string_file.password")">
                                        @if ($errors->has('password'))
                                            <label class="text-danger">{{ $errors->first('password') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 corporate_inr">
                                    <div class="form-group">
                                        <label for="location3">@lang('admin.company_logo')
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input style="height: 0%;" class="form-control" type="file" name="company_logo" id="company_logo">
                                        @if ($errors->has('company_logo'))
                                            <label class="text-danger">{{ $errors->first('company_logo') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        <h4>@lang("$string_file.bank_details")</h4>
                        <br>
                        <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            @lang("$string_file.bank_name")</label>
                                        <input type="text" class="form-control" id="bank_name"
                                               name="bank_name"
                                               placeholder="@lang("$string_file.bank_name")"
                                               value="{{$company->bank_name}}">
                                        @if ($errors->has('bank_name'))
                                            <label class="text-danger">{{ $errors->first('bank_name') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            @lang("$string_file.account_holder_name")</label>
                                        <input type="text" class="form-control"
                                               id="account_holder_name"
                                               name="account_holder_name"
                                               placeholder="@lang("$string_file.account_holder_name")"
                                               value="{{ $company->account_holder_name }}">
                                        @if ($errors->has('account_holder_name'))
                                            <label class="text-danger">{{ $errors->first('account_holder_name') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            @lang('admin.account_number')</label>
                                        <input type="text" class="form-control required"
                                               id="account_number"
                                               name="account_number"
                                               placeholder="@lang('admin.account_number')"
                                               value="{{$company->account_number}}">
                                        @if ($errors->has('account_number'))
                                            <label class="text-danger">{{ $errors->first('account_number') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3" id="transaction_label">@lang("$string_file.online_transaction_code")</label>
                                        <input type="text" class="form-control"
                                               id="online_transaction"
                                               name="online_transaction"
                                               value="{{$company->online_transaction}}"
                                               placeholder="@lang("$string_file.online_transaction_code")">
                                        @if ($errors->has('online_transaction'))
                                            <label class="text-danger">{{ $errors->first('online_transaction')
                                                                }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3">@lang("$string_file.account_type")</label>
                                        <select type="text" class="form-control"
                                                id="account_type"
                                                name="account_type">
                                            @foreach($account_types as $account_type)
                                                <option value="{{$account_type->id}}" @if($company->account_type_id == $account_type->id) selected @endif >@if($account_type->LangAccountTypeSingle){{$account_type->LangAccountTypeSingle->name}} @else {{$account_type->LangAccountTypeAny->name}} @endif</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('account_type'))
                                            <label class="text-danger">{{ $errors->first('account_type')
                                                                }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="wb-check-circle"></i> @lang("$string_file.update")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection