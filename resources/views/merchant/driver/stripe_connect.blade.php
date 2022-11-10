@extends('merchant.layouts.main')
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
                <div class="alert dark alert-icon alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon fa-warning" aria-hidden="true"></i>{{ session('error') }}
                </div>
            @endif
            @if($errors->all())
                @foreach($errors->all() as $message)
                    <div class="alert dark alert-icon alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                        <i class="icon fa-warning" aria-hidden="true"></i>{{ $message }}
                    </div>
                @endforeach
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('driver.index') }}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.all_drivers")"></i>
                            </button>
                        </a>
                        <a href="{{ route('merchant.driver.stripe_connect.sync',$id) }}">
                            <button type="button" class="btn btn-icon btn-dark float-right" style="margin:10px" data-toggle="tooltip" title="Sync">
                                <i class="icon wb-refresh" title="Sync"></i>
                            </button>
                        </a>
                        <a href="{{ route('merchant.driver.stripe_connect.delete',$id) }}">
                            <button type="button" class="btn btn-icon btn-danger float-right" style="margin:10px" data-toggle="tooltip" title="Delete">
                                <i class="icon wb-trash" title="Sync"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        @lang('admin.stripe_connect') : {{ $driver->fullName }} : ( {{ ($driver->sc_account_id != NULL) ? $driver->sc_account_id : '---' }} )</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('merchant.driver.stripe_connect.store',$id) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <h5>@lang('admin.stripe_connect_status')
                                    : {{ $driver->sc_account_status == 'active' ? 'Active' : ($driver->sc_account_status == null ? 'Not Found or Pending' : 'Reject') }}</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="first_name">
                                         @lang("$string_file.first_name") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="first_name"
                                           name="first_name"
                                           placeholder=" @lang("$string_file.first_name")"
                                           value="{{$driver->first_name}}"
                                           readonly>
                                    @if ($errors->has('first_name'))
                                        <label class="text-danger">{{ $errors->first('first_name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="last_name">
                                        @lang("$string_file.last_name") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="last_name"
                                           name="last_name"
                                           placeholder="@lang("$string_file.last_name")"
                                           value="{{$driver->last_name}}"
                                           readonly>
                                    @if ($errors->has('last_name'))
                                        <label class="text-danger">{{ $errors->first('last_name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dob">
                                         @lang("$string_file.dob") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="dob"
                                           name="dob"
                                           placeholder=" @lang("$string_file.dob")"
                                           value="{{$driver->dob}}"
                                           readonly>
                                    @if ($errors->has('dob'))
                                        <label class="text-danger">{{ $errors->first('dob') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email">
                                        @lang("$string_file.email") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="email"
                                           name="email"
                                           placeholder="@lang("$string_file.email")"
                                           value="{{$driver->email}}"
                                           readonly>
                                    @if ($errors->has('email'))
                                        <label class="text-danger">{{ $errors->first('email') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone">
                                        @lang("$string_file.phone") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="phone"
                                           name="phone"
                                           placeholder="@lang("$string_file.phone")"
                                           value="{{$driver->phoneNumber}}"
                                           readonly>
                                    @if ($errors->has('phone'))
                                        <label class="text-danger">{{ $errors->first('phone') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <br>
                        <h5 class="form-section col-md-12" style="color: black;"><i
                                    class="fa fa-address-card"></i> @lang("$string_file.address")
                        </h5>
                        <hr>
                        @php
                            $driver_additional_data = (object)json_decode($driver->driver_additional_data, true);
                        @endphp
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="location3"> @lang("$string_file.address_line_1")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="address_line_1"
                                           name="address_line_1"
                                           value="{{old('address_line_1', isset($driver_additional_data->address_line_1) ? $driver_additional_data->address_line_1 : '') }}"
                                           placeholder=" @lang("$string_file.address_line_1")"
                                           autocomplete="off" readonly/>
                                </div>
                                @if ($errors->has('address_line_1'))
                                    <label class="text-danger">{{ $errors->first('address_line_1')}}</label>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">@lang("$string_file.address_line_2")
                                    </label>
                                    <input type="text" class="form-control" id="address_line_2"
                                           name="address_line_2"
                                           value="{{old('address_line_2', isset($driver_additional_data->address_line_2) ? $driver_additional_data->address_line_2 : '') }}"
                                           placeholder="@lang("$string_file.address_line_2")"
                                           autocomplete="off" readonly/>
                                </div>
                                @if ($errors->has('address_line_2'))
                                    <label class="text-danger">{{ $errors->first('address_line_2')}}</label>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">@lang("$string_file.city_name")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="city_name"
                                           name="city_name"
                                           value="{{old('city_name', isset($driver_additional_data->city_name) ? $driver_additional_data->city_name : '') }}"
                                           placeholder="@lang("$string_file.city_name")"
                                           autocomplete="off" readonly/>
                                </div>
                                @if ($errors->has('city_name'))
                                    <label class="text-danger">{{ $errors->first('city_name')}}</label>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="location3">@lang("$string_file.address_province")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="address_province"
                                           name="address_province"
                                           value="{{old('address_province', isset($driver_additional_data->province) ? $driver_additional_data->province : '')}}"
                                           readonly
                                    />
                                </div>
                                @if ($errors->has('address_province'))
                                    <label class="text-danger">{{ $errors->first('address_province')}}</label>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="location3">@lang("$string_file.postal_code")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="address_postal_code"
                                           name="address_postal_code"
                                           value="{{old('address_postal_code', isset($driver_additional_data->pincode) ? $driver_additional_data->pincode : '')}}"
                                           placeholder="@lang("$string_file.postal_code")"
                                           autocomplete="off" readonly/>
                                </div>
                                @if ($errors->has('address_postal_code'))
                                    <label class="text-danger">{{ $errors->first('address_postal_code')}}</label>
                                @endif
                            </div>
                        </div>
                        <br>
                        <h5 class="form-section col-md-12" style="color: black;"><i
                                    class="fa fa-bank"></i> @lang("$string_file.bank_details")
                        </h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="lastName3">@lang("$string_file.account_holder_name")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="account_holder_name"
                                           name="account_holder_name"
                                           value="{{ $driver->account_holder_name }}"
                                           placeholder="@lang("$string_file.account_holder_name")" readonly
                                           autocomplete="off"/>
                                </div>
                                @if ($errors->has('account_holder_name'))
                                    <label class="text-danger">{{ $errors->first('account_holder_name') }}</label>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="lastName3">@lang("$string_file.account_number")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="account_number"
                                           name="account_number"
                                           value="{{ $driver->account_number }}" readonly
                                           placeholder="@lang("$string_file.account_number")"
                                           autocomplete="off"/>
                                </div>
                                @if ($errors->has('account_number'))
                                    <label class="text-danger">{{ $errors->first('account_number') }}</label>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="lastName3">@lang("$string_file.bsb_routing_number")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="routing_number"
                                           name="routing_number"
                                           value="@if($driver->bsb_number){{$driver->bsb_number}}@else{{ $driver->routing_number }}@endif"
                                           placeholder="@lang('admin.routing_number')" readonly
                                           autocomplete="off"/>
                                </div>
                                @if ($errors->has('routing_number'))
                                    <label class="text-danger">{{ $errors->first('routing_number') }}</label>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="lastName3">@lang("$string_file.abn_number")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="abn_number" name="abn_number"
                                           value="{{ old('abn_number',isset($driver->abn_number) ? $driver->abn_number : '') }}"
                                           placeholder="@lang("$string_file.abn_number")" autocomplete="off" readonly/>
                                </div>
                                @if ($errors->has('abn_number'))
                                    <label class="text-danger">{{ $errors->first('abn_number') }}</label>
                                @endif
                            </div>
                        </div>
                        <br>
                        <h5 class="form-section col-md-12" style="color: black;"><i
                                    class="fa fa-address-card"></i> @lang('admin.driver_docs')
                        </h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-3 mb-10">
                                <div class="card-title">@lang('admin.personal_document_number')</div>
                                @if($stripe_docs_list['personal_document']['doc_number'] != '')
                                    <h4>{{ $stripe_docs_list['personal_document']['doc_number'] }}</h4>
                                @else
                                    <span class="text-danger">@lang('admin.document_number_not_found')</span>
                                @endif
                            </div>
                            <div class="col-md-3 mb-10">
                                <div class="card-title">@lang('admin.photo_front_document')</div>
                                @if($stripe_docs_list['photo_front_document']['image'] != '')
                                    <img class="rounded img-bordered img-bordered-primary" width="150" height="150"
                                         src="{{$stripe_docs_list['photo_front_document']['image']}}" alt="...">
                                @else
                                    <span class="text-danger">@lang('admin.document_not_found')</span>
                                @endif
                            </div>
                            <div class="col-md-3 mb-10">
                                <div class="card-title">@lang('admin.photo_back_document')</div>
                                @if($stripe_docs_list['photo_back_document']['image'] != '')
                                    <img class="rounded img-bordered img-bordered-primary" width="150" height="150"
                                         src="{{$stripe_docs_list['photo_back_document']['image']}}" alt="...">
                                @else
                                    <span class="text-danger">@lang('admin.document_not_found')</span>
                                @endif
                            </div>
                            <div class="col-md-3 mb-10">
                                <div class="card-title">@lang("$string_file.additional_document") </div>
                                @if($stripe_docs_list['additional_document']['image'] != '')
                                    <img class="rounded img-bordered img-bordered-primary" width="150" height="150"
                                         src="{{$stripe_docs_list['additional_document']['image']}}" alt="...">
                                @else
                                    <span class="text-danger">@lang('admin.document_not_found')</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-actions float-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i>
                                @lang("$string_file.save")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection