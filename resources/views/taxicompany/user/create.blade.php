@extends('taxicompany.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('rideradded'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.rideradded')
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('taxicompany.users.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-user" aria-hidden="true"></i>
                        @lang("$string_file.add_user")</h3>
                </header>
                <div class="panel-body container-fluid" id="validation">
                    @if(Auth::user()->demo != 1)
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data" action="{{ route('taxicompany.users.store') }}" autocomplete="false">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                             @lang("$string_file.first_name")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="first_name"
                                               name="first_name"
                                               placeholder=" @lang("$string_file.first_name")"  value="{{ old('first_name') }}" required>
                                        @if ($errors->has('first_name'))
                                            <label class="text-danger">{{ $errors->first('first_name') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.last_name")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="last_name"
                                               name="last_name"
                                               placeholder="@lang("$string_file.last_name")" value="{{ old('last_name') }}" required>
                                        @if ($errors->has('last_name'))
                                            <label class="text-da••••••••nger">{{ $errors->first('last_name') }}</label>
                                        @endif
                                    </div>
                                </div>
                                {{--<<<<<<< HEAD--}}
                                {{--                                    <div class="col-md-4">--}}
                                {{--                                        <div class="form-group">--}}
                                {{--                                            <label for="user_phone">@lang('admin.merchantPhone')--}}
                                {{--                                                <span class="text-danger">*</span>--}}
                                {{--                                            </label>--}}
                                {{--                                            <div class="row">--}}
                                {{--                                                <input type="text" class="form-control col-md-3 ml-15 col-sm-3 col-3" id="isd" name="isd" value="{{old('isd')}}"--}}
                                {{--                                                       placeholder="@lang("$string_file.isd_code")" readonly />--}}
                                {{--                                                <input type="number" class="form-control col-md-8 col-sm-8 col-8" id="user_phone" name="phone" value="{{old('phone')}}"--}}
                                {{--                                                       placeholder="@lang("$string_file.enter_number")" required />--}}
                                {{--                                            </div>--}}
                                {{--                                            @if ($errors->has('phonecode'))--}}
                                {{--                                                <label class="text-danger">{{ $errors->first('phonecode') }}</label>--}}
                                {{--                                            @endif--}}
                                {{--                                            @if ($errors->has('phone'))--}}
                                {{--                                                <label class="text-danger">{{ $errors->first('phone') }}</label>--}}
                                {{--                                            @endif--}}
                                {{--                                        </div>--}}
                                {{--                                    </div>--}}
                                {{--=======--}}
                                {{-->>>>>>> 807e2319dcfbc5175466c7af3284576e04774ee2--}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3">@lang("common.$string_file")</label>
                                        <select class="form-control" name="country" id="country"
                                                required>
                                            <option value="">@lang("$string_file.select")</option>
                                            @foreach($countries  as $country)
                                                <option data-min="{{ $country->minNumPhone }}"
                                                        data-max="{{ $country->maxNumPhone }}"
                                                        data-ISD="{{ $country->phonecode }}" value="{{ $country->id }}|{{ $country->phonecode }}">{{  $country->CountryName }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('country'))
                                            <label class="text-danger">{{ $errors->first('country') }}</label>
                                        @endif
                                    </div>
                                </div>
                                {{--<<<<<<< HEAD--}}
                                {{--=======--}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="user_phone">@lang("$string_file.phone")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="row">
                                            <input type="text" class="form-control col-md-3 ml-15 col-sm-3 col-3" id="isd" name="isd" value="{{old('isd')}}"
                                                   placeholder="@lang("$string_file.isd_code")" readonly />
                                            <input type="number" class="form-control col-md-8 col-sm-8 col-8" id="user_phone" name="user_phone" value="{{old('user_phone')}}"
                                                   placeholder="@lang("$string_file.enter_number")" required />
                                        </div>
                                        @if ($errors->has('phonecode'))
                                            <label class="text-danger">{{ $errors->first('phonecode') }}</label>
                                        @endif
                                        @if ($errors->has('phone'))
                                            <label class="text-danger">{{ $errors->first('phone') }}</label>
                                        @endif
                                    </div>
                                </div>
                                {{-->>>>>>> 807e2319dcfbc5175466c7af3284576e04774ee2--}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang("$string_file.email")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control" id="user_email"
                                               name="user_email"
                                               placeholder="@lang("$string_file.email")" value="{{ old('user_email') }}" required>
                                        @if ($errors->has('user_email'))
                                            <label class="text-danger">{{ $errors->first('user_email') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang("$string_file.password")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" class="form-control" id="password"
                                               name="password"
                                               placeholder="@lang("$string_file.password")" required>
                                        @if ($errors->has('password'))
                                            <label class="text-danger">{{ $errors->first('password') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                {{--                                    @if($appConfig->gender == 1)--}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3">@lang("$string_file.gender")
                                            :</label>
                                        <select class="form-control" name="user_gender"
                                                id="user_gender"
                                                required>
                                            <option value="1">@lang("$string_file.male")</option>
                                            <option value="2">@lang("$string_file.female")</option>
                                        </select>
                                        @if ($errors->has('driver_gender'))
                                            <label class="text-danger">{{ $errors->first('driver_gender') }}</label>
                                        @endif
                                    </div>
                                </div>
                                {{--                                    @endif--}}
                                {{--                                    @if($appConfig->smoker == 1)--}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3"> @lang("$string_file.smoke")
                                            :</label>
                                        <br>
                                        <label class="radio-inline"
                                               style="margin-left: 5%;margin-right: 10%;margin-top: 1%;">
                                            <input type="radio" value="1"
                                                   checked id="smoker_type"
                                                   name="smoker_type"
                                                   required> @lang("$string_file.smoker")
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" value="2" id="smoker_type"
                                                   name="smoker_type"
                                                   required> @lang("$string_file.non_smoker")
                                        </label>
                                        <br>
                                        <br>
                                        <label class="checkbox-inline"
                                               style="margin-left: 5%;">
                                            <input type="checkbox" name="allow_other_smoker"
                                                   id="allow_other_smoker"
                                                   value="1"> @lang("$string_file.allow_other_to_smoke")
                                        </label>
                                    </div>
                                </div>
                                {{--                                    @endif--}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="profile_image">
                                            @lang("$string_file.profile_image")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="profile"
                                               name="profile"
                                               placeholder="@lang("$string_file.profile_image")" required>
                                        @if ($errors->has('profile'))
                                            <label class="text-danger">{{ $errors->first('profile') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions d-flex flex-row-reverse p-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="card bg-danger text-white shadow">
                            <div class="card-body">
                                Alert
                                <div class="large">@lang("$string_file.demo_user_cant_edited").</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection