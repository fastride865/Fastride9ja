@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('message558'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message558')
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="{{ route('hotels.index') }}">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang('admin.message556')
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{route('hotels.update', $hotel->id)}}">
                        {{method_field('PUT')}}
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang('admin.message551') :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="name"
                                               name="name" value="{{$hotel->name}}"
                                               placeholder="@lang('admin.message551')" required>
                                        @if ($errors->has('name'))
                                            <label class="text-danger">{{ $errors->first('name') }}</label>
                                        @endif
                                    </div>
                                </div>

                            <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            @lang('admin.message552') :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="email"
                                               name="email"
                                               placeholder="@lang('admin.message552')"
                                               value="{{$hotel->email}}" required>
                                        @if ($errors->has('email'))
                                            <label class="text-danger">{{ $errors->first('email') }}</label>
                                        @endif
                                    </div>
                                </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang('admin.message553') :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="phone"
                                           name="phone"
                                           placeholder="@lang('admin.message553')"
                                           value="{{$hotel->phone}}" required>
                                    @if ($errors->has('phone'))
                                        <label class="text-danger">{{ $errors->first('phone') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang('admin.message554') :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="address"
                                           name="address"
                                           placeholder="@lang('admin.message554')"
                                           value="{{$hotel->address}}" required>
                                    @if ($errors->has('address'))
                                        <label class="text-danger">{{ $errors->first('address') }}</label>
                                    @endif
                                </div>
<<<<<<< HEAD
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang("$string_file.password") :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" class="form-control" id="password"
                                               name="password"
                                               placeholder="@lang("$string_file.password")" disabled>
                                        @if ($errors->has('password'))
                                            <label class="text-danger">{{ $errors->first('password') }}</label>
                                        @endif
                                    </div>
=======
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.password") :
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control" id="password"
                                           name="password"
                                           placeholder="@lang("$string_file.password")" disabled>
                                    @if ($errors->has('password'))
                                        <label class="text-danger">{{ $errors->first('password') }}</label>
                                    @endif
>>>>>>> 20d76691291f2477b897f4e7fcdbce0b89817c51
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>
                                    <input type="checkbox" value="1" name="edit_password"
                                           id="edit_password" onclick="EditPassword()">
                                    @lang('admin.message557')
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="hotel_logo">
                                        @lang("$string_file.profile_image") :
                                    </label>
                                    <input type="file" class="form-control" id="hotel_logo"
                                           name="hotel_logo"
                                           placeholder="@lang("$string_file.profile_image")">
                                    @if ($errors->has('hotel_logo'))
                                        <label class="text-danger">{{ $errors->first('hotel_logo') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                            <h5>@lang("$string_file.bank_details")</h5>
                        <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            @lang("$string_file.bank_name")</label>
                                        <input type="text" class="form-control" id="bank_name"
                                               name="bank_name"
                                               placeholder="@lang("$string_file.bank_name")"
                                               value="{{$hotel->bank_name}}">
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
                                               value="{{ $hotel->account_holder_name }}">
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
                                               value="{{$hotel->account_number}}">
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
                                               value="{{$hotel->online_transaction}}"
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
                                                <option value="{{$account_type->id}}" @if($hotel->account_type_id == $account_type->id) selected @endif >@if($account_type->LangAccountTypeSingle){{$account_type->LangAccountTypeSingle->name}} @else {{$account_type->LangAccountTypeAny->name}} @endif</option>
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
                                <i class="wb-check-circle"></i> @lang("$string_file.update")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        function EditPassword() {
            if (document.getElementById("edit_password").checked = true) {
                document.getElementById('password').disabled = false;
            }
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?=get_merchant_google_key(NULL,'admin_backend');?>&v=3.exp&libraries=places&language=en&region=ES"></script>
    <script>
        function initialize() {
            var input = document.getElementById('address');
            var autocomplete = new google.maps.places.Autocomplete(input);
        }

        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
@endsection