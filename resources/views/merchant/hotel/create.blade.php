@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
           @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('hotels.index') }}">
                            <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        @lang("$string_file.add_hotel")</h3>
                </header>
                @php $id = isset($hotel->id) ? $hotel->id : NULL; $required = !empty($id) ? "" : "required" @endphp
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="{{ route('hotels.store',$id) }}">
                        @csrf
                        {!! Form::hidden('id',$id) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.name") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name"
                                           name="name"  value="{{old('name',isset($hotel->name) ? $hotel->name : NULL)}}"
                                           placeholder="" required>
                                    @if ($errors->has('name'))
                                        <label class="text-danger">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.email") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="email"
                                           name="email" value="{{old('email',isset($hotel->email) ? $hotel->email : NULL)}}"
                                           placeholder="" required>
                                    @if ($errors->has('email'))
                                        <label class="text-danger">{{ $errors->first('email') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3">@lang("$string_file.country") :</label>
                                    <select class="form-control" name="country" id="country"
                                            required>
                                        <option value="">@lang("$string_file.select")</option>
                                        @foreach($countries  as $country)
                                            <option data-min="{{ $country->minNumPhone }}"
                                                    data-max="{{ $country->maxNumPhone }}"
                                                    data-ISD="{{ $country->phonecode }}" value="{{ $country->id }}|{{ $country->phonecode }}" @if(!empty($hotel->country_id) && $hotel->country_id == $country->id) selected @endif>{{  $country->CountryName }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('country'))
                                        <label class="text-danger">{{ $errors->first('country') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.phone") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="user_phone" min="0"
                                           name="phone" value="{{old('phone',isset($hotel->phone) ? str_replace($hotel->Country->phonecode,"",$hotel->phone) : NULL)}}"
                                           placeholder="" required>
                                    @if ($errors->has('phone'))
                                        <label class="text-danger">{{ $errors->first('phone') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.address") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="address"
                                           name="address" value="{{old('address',isset($hotel->address) ? $hotel->address : NULL)}}"
                                           placeholder="" required>
                                    @if ($errors->has('address'))
                                        <label class="text-danger">{{ $errors->first('address') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.password") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control" id="password"
                                           name="password"
                                           placeholder="" {{$required}}>
                                    @if ($errors->has('password'))
                                        <label class="text-danger">{{ $errors->first('password') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="image">
                                        @lang("$string_file.logo") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="hotel_logo"
                                           name="hotel_logo"
                                           placeholder="@lang("$string_file.profile_image")" {{$required}}>
                                    @if ($errors->has('image'))
                                        <label class="text-danger">{{ $errors->first('image') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <br>
                        <h5> @lang("$string_file.bank_details")</h5>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.bank_name")
                                        <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="bank_name"
                                           name="bank_name" value="{{old('bank_name',isset($hotel->bank_name) ? $hotel->bank_name : NULL)}}"
                                           placeholder="">
                                    @if ($errors->has('bank_name'))
                                        <label class="text-danger">{{ $errors->first('bank_name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.account_holder_name")
                                        <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control"
                                           id="account_holder_name"
                                           name="account_holder_name" value="{{old('account_holder_name',isset($hotel->account_holder_name) ? $hotel->account_holder_name : NULL)}}"
                                           placeholder="">
                                    @if ($errors->has('account_holder_name'))
                                        <label class="text-danger">{{ $errors->first('account_holder_name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.account_number")
                                        <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control required"
                                           id="account_number"
                                           name="account_number" value="{{old('account_number',isset($hotel->account_number) ? $hotel->account_number : NULL)}}"
                                           placeholder="">
                                    @if ($errors->has('account_number'))
                                        <label class="text-danger">{{ $errors->first('account_number') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3" id="transaction_label">@lang("$string_file.online_transaction_code")
                                        <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control"
                                           id="online_transaction"
                                           name="online_transaction"
                                           value="{{old('online_transaction',isset($hotel->online_transaction) ? $hotel->online_transaction : NULL)}}"
                                           placeholder="">
                                    @if ($errors->has('online_transaction'))
                                        <label class="text-danger">{{ $errors->first('online_transaction')
                                                        }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3">@lang("$string_file.account_type")
                                        <span class="text-danger">*</span></label>
                                    <select type="text" class="form-control"
                                            id="account_type"
                                            name="account_type">
                                        @foreach($account_types as $account_type)
                                            <option value="{{$account_type->id}}" @if(!empty($hotel->account_type_id) && $hotel->account_type_id == $account_type->id) selected @endif>@if($account_type->LangAccountTypeSingle){{$account_type->LangAccountTypeSingle->name}} @else {{$account_type->LangAccountTypeAny->name}} @endif</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('account_type'))
                                        <label class="text-danger">{{ $errors->first('account_type')
                                                        }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions float-right">
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