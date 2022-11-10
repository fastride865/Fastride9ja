@extends('corporate.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
        @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('user.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-user" aria-hidden="true"></i>
                        @lang("$string_file.add_user")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        @if(Auth::user()->demo != 1)
                            <form method="POST" class="steps-validation wizard-notification"
                                  enctype="multipart/form-data" action="{{ route('corporate.user.store') }}" autocomplete="false">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="firstName3">
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
                                            <label class="form-control-label" for="firstName3">
                                                @lang("$string_file.last_name")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="last_name"
                                                   name="last_name"
                                                   placeholder="@lang("$string_file.last_name")" value="{{ old('last_name') }}" required>
                                            @if ($errors->has('last_name'))
                                                <label class="text-danger">{{ $errors->first('last_name') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="location3">@lang("$string_file.service_area")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control" name="country" id="country"
                                                    required>
                                                <option value="">@lang("$string_file.select")</option>
                                                @foreach($countries  as $country)
                                                    <option data-min="{{ $country->minNumPhone }}"
                                                            data-max="{{ $country->maxNumPhone }}"
                                                            data-ISD="{{ $country->phonecode }}" value="{{ $country->id }}|{{ $country->phonecode }}">{{  $country->country_name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('country'))
                                                <label class="text-danger">{{ $errors->first('country') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="user_phone">@lang("$string_file.phone")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="row">
                                                <input type="text" class="form-control col-md-3 ml-15 col-sm-3 col-3" id="isd" name="isd" value="{{old('isd')}}"
                                                       placeholder="@lang("$string_file.isd_code")" readonly />
                                                <input type="number" class="form-control col-md-8 col-sm-8 col-8" id="user_phone" name="user_phone" value="{{old('user_phone')}}"
                                                       placeholder="@lang("$string_file.phone")" required />
                                            </div>
                                            @if ($errors->has('phonecode'))
                                                <label class="text-danger">{{ $errors->first('phonecode') }}</label>
                                            @endif
                                            @if ($errors->has('phone'))
                                                <label class="text-danger">{{ $errors->first('phone') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="emailAddress5">
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
                                            <label class="form-control-label" for="emailAddress5">
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
                                    @if($appConfig->gender == 1)
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label" for="location3">@lang("$string_file.gender")
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control" name="user_gender"
                                                        id="user_gender"
                                                        required>
                                                    <option value="1">@lang("$string_file.male")</option>
                                                    <option value="2">@lang("$string_file.female")</option>
                                                </select>
                                                @if ($errors->has('user_gender'))
                                                    <label class="text-danger">{{ $errors->first('user_gender') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="ProfileImage">
                                                @lang("$string_file.profile_image")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="file" class="form-control" id="profile"
                                                   name="profile"
                                                   placeholder="" required>
                                            @if ($errors->has('profile'))
                                                <label class="text-danger">{{ $errors->first('profile') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="ProfileImage">
                                                @lang("$string_file.designation")
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select name="designationId" class="form-control">
                                                <option value=""> -- @lang("$string_file.select") -- </option>
                                                @foreach($designations as $designation)
                                                    <option value="{{$designation->designation_id}}">{{$designation->designation_name}} ( @lang("$string_file.expense_limit") {{$designation->designation_expense_limit}} )</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('designationId'))
                                                <label class="text-danger">{{ $errors->first('designationId') }}</label>
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
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection