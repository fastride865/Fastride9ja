@extends('merchant.layouts.main')
@section('content')
    <div class="app-content content">
        <div class="container-fluid">
            <div class="content-wrapper">
                <div class="content-body">
                    <section id="horizontal">
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow">
                                    <div class="card-header py-3">
                                        <div class="content-header row">
                                            <div class="content-header-left col-md-8 col-12 mb-2 breadcrumb-new">
                                                <h3 class="content-header-title mb-0 d-inline-block">
                                                    <i class=" fa fa-exclamation-circle" aria-hidden="true"></i>
                                                    @lang('admin.message682')</h3>
                                            </div>
                                            <div class="content-header-right col-md-4 col-12">
                                                <div class="btn-group float-md-right">
                                                    <a href="{{ route('driver.index') }}">
                                                        <button type="button" class="btn btn-icon btn-success mr-1"><i
                                                                    class="fa fa-reply"></i>
                                                        </button>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <h4><i class="fa fa-user-circle"></i> @lang('admin.basic_details')</h4><hr>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="firstName3">
                                                         @lang("$string_file.first_name")<span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="first_name"
                                                           name="first_name"
                                                           placeholder=" @lang("$string_file.first_name")"
                                                           value="{{ $driver->first_name }}" required>
                                                    @if ($errors->has('first_name'))
                                                        <label class="text-danger">{{ $errors->first('first_name') }}</label>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="firstName3">
                                                        @lang("$string_file.last_name")<span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="last_name"
                                                           name="last_name"
                                                           placeholder="@lang("$string_file.last_name")"
                                                           value="{{ $driver->last_name }}" required>
                                                    @if ($errors->has('last_name'))
                                                        <label class="text-danger">{{ $errors->first('last_name') }}</label>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="lastName3">
                                                        @lang("$string_file.email")<span class="text-danger">*</span>
                                                    </label>
                                                    <input type="email" class="form-control required"
                                                           id="email"
                                                           name="email"
                                                           placeholder=""
                                                           value="{{ $driver->email }}" required>
                                                    @if ($errors->has('email'))
                                                        <label class="text-danger">{{ $errors->first('email') }}</label>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="emailAddress5">
                                                        @lang("$string_file.phone")<span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">{{ $driver->CountryArea->Country->phonecode  }}</span>
                                                        </div>
                                                        <input type="text" pattern="[0-9]{*}" id="user_phone" title="Valid Mobile Number" name="phone" value="{{ str_replace($driver->CountryArea->Country->phonecode,"",$driver->phoneNumber) }}" class="form-control" placeholder="@lang('admin.driver_mobile_no')" required>
                                                        <input type="hidden" name="phoneCode" value="{{ $driver->CountryArea->Country->phonecode  }}" />
                                                    </div>
                                                    @if ($errors->has('phone'))
                                                        <label class="text-danger">{{ $errors->first('phone') }}</label>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="emailAddress5">
                                                        @lang("$string_file.profile_image")<span class="text-danger">*</span>
                                                    </label>
                                                    <input type="file" class="form-control" id="image"
                                                           name="image">
                                                    @if ($errors->has('image'))
                                                        <label class="text-danger">{{ $errors->first('image') }}</label>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="emailAddress5">
                                                        @lang("$string_file.area")<span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-control" name="country_area_id">
                                                        <option value=""> -- Select One -- </option>
                                                        @foreach($countryAreas as $countryArea)
                                                            <option value="{{$countryArea->id}}" @if($countryArea->id == $driver->country_area_id) selected @endif>{{$countryArea->CountryAreaName}}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('country_area_id'))
                                                        <label class="text-danger">{{ $errors->first('country_area_id') }}</label>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-3"></div>
                                            <div class="col-md-3"></div>
                                        </div>
                                        @if($config->bank_details_enable == 1)
                                            <br>
                                            <h4><i class="fa fa-university"></i> @lang("$string_file.bank_details")</h4><hr>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="lastName3">
                                                            @lang("$string_file.bank_name")</label>
                                                        <input type="text" class="form-control" id="bank_name"
                                                               name="bank_name"
                                                               placeholder="@lang("$string_file.bank_name")"
                                                               value="{{ $driver->bank_name }}">
                                                        @if ($errors->has('bank_name'))
                                                            <label class="text-danger">{{ $errors->first('bank_name') }}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="lastName3">
                                                            @lang("$string_file.account_holder_name")</label>
                                                        <input type="text" class="form-control"
                                                               id="account_holder_name"
                                                               name="account_holder_name"
                                                               placeholder="@lang("$string_file.account_holder_name")"
                                                               value="{{ $driver->account_holder_name }}">
                                                        @if ($errors->has('account_holder_name'))
                                                            <label class="text-danger">{{ $errors->first('account_holder_name') }}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="lastName3">
                                                            @lang("$string_file.account_number")</label>
                                                        <input type="text" class="form-control required"
                                                               id="account_number"
                                                               name="account_number"
                                                               placeholder="@lang("$string_file.account_number")"
                                                               value="{{ $driver->account_number }}">
                                                        @if ($errors->has('account_number'))
                                                            <label class="text-danger">{{ $errors->first('account_number') }}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="location3">@lang("$string_file.account_type")</label>
                                                        <select type="text" class="form-control"
                                                                id="account_types"
                                                                name="account_types">
                                                            @foreach($account_types as $account_type)
                                                                <option value="{{$account_type->id}}" @if($account_type->id == $driver->account_type_id) selected @endif>@if($account_type->LangAccountTypeSingle){{$account_type->LangAccountTypeSingle->name}} @else {{$account_type->LangAccountTypeAny->name}} @endif</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('account_types'))
                                                            <label class="text-danger">{{ $errors->first('account_types')
                                                                }}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="location3">{{$driver->CountryArea->Country->transaction_code}}</label>
                                                        <input type="text" class="form-control"
                                                               id="online_transaction"
                                                               name="online_transaction"
                                                               value="{{$driver->online_code}}"
                                                               placeholder="@lang('admin.enter') {{$driver->CountryArea->Country->transaction_code}}">
                                                        @if ($errors->has('online_transaction'))
                                                            <label class="text-danger">{{ $errors->first('online_transaction')
                                                                    }}</label>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <br>
                                        <h4><i class="fa fa-file-alt"></i> @lang('admin.personal') @lang("$string_file.document") </h4><hr>
                                        @php
                                            $arr_uploaded_doc =[];
                                            $expire_date = null;
                                            $document_file = null;
                                        @endphp
                                        @if(isset($driver->DriverDocument) && count($driver->DriverDocument->toArray()) > 0)
                                            @php
                                                $arr_uploaded_doc =  $driver->DriverDocument->toArray();
                                                $arr_uploaded_doc = array_column($arr_uploaded_doc,NULL, 'document_id');
                                                $arr_doc_id = array_column($arr_uploaded_doc,'document_id');
                                            @endphp
                                        @endif
                                        @foreach($driver->CountryArea->documents as $docment)
                                            @php $expire_date = null;$document_file = null;@endphp
                                            @if(isset($arr_uploaded_doc[$docment['pivot']['document_id']]))
                                                @php
                                                    $expire_date = $arr_uploaded_doc[$docment['pivot']['document_id']]['expire_date'];
                                                    $document_file = $arr_uploaded_doc[$docment['pivot']['document_id']]['document_file'];
                                                @endphp
                                            @endif
                                            {!! Form::hidden('all_doc[]',$docment['id']) !!}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="emailAddress5">{{ $docment->DocumentName }}<span class="text-danger">*</span>
                                                            @if(in_array($docment['pivot']['document_id'],array_keys($arr_uploaded_doc)))
                                                                <a href="{{get_image($document_file,'driver_document')}}" target="_blank">@lang("$string_file.view") </a>
                                                            @endif
                                                        </label>
                                                        <input type="file" class="form-control" id="document"
                                                               name="document[{{$docment['id']}}]"
                                                               placeholder=""
                                                               @if($docment['documentNeed'] == 1 && empty($document_file)) required @endif>
                                                        @if ($errors->has('documentname'))
                                                            <label class="text-danger">{{ $errors->first('documentname')}}
                                                            </label>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($docment->expire_date == 1)
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="location3">@lang("$string_file.expire_date")  </label>
                                                            <input type="text"
                                                                   class="form-control docs_datepicker"
                                                                   name="expiredate[{{$docment->id}}]"
                                                                   value="{!! $expire_date !!}"
                                                                   placeholder="@lang("$string_file.expire_date")  "
                                                                   @if($docment['expire_date'] == 1 && empty($expire_date)) required @endif
                                                                   autocomplete="off">
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                        <br>
                                        <h4><i class="fa fa-car-alt"></i> @lang("$string_file.vehicle")  @lang("$string_file.details")</h4><hr>
                                        @foreach($driver->DriverVehicles as $driverVehicle)
                                            <div class="row">
                                                <div class="col-md-3"></div>
                                                <div class="col-md-3"></div>
                                                <div class="col-md-3"></div>
                                                <div class="col-md-3"></div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
