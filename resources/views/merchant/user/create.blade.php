@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            @if(session('rideradded'))
                <div class="alert dark alert-icon alert-success" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i> @lang('admin.rideradded')
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    {{session('errors')}}
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->add_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <a href="{{ route('users.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        @lang("$string_file.add_user")</h3>
                </header>
                <div class="panel-body container-fluid" id="validation">
                    @if(Auth::user()->demo != 1)
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data" action="{{ route('users.store') }}" autocomplete="false">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3">@lang("$string_file.user_type")</label>
                                        <select class="form-control" name="rider_type"
                                                id="rider_type" onclick="RideType(this.value)"
                                                required>
                                            <option value="">--Select Rider Type--</option>
                                            @if($config->corporate_admin == 1)
                                                <option value="1">Corporate</option>
                                            @endif
                                            <option value="2">Retail</option>
                                        </select>

                                        @if ($errors->has('rider_type'))
                                            <label class="text-danger">{{ $errors->first('rider_type') }}</label>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.first_name")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="first_name"
                                               name="first_name"
                                               placeholder=" @lang("$string_file.first_name")"
                                               value="{{ old('first_name') }}" required>
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
                                               placeholder="@lang("$string_file.last_name")"
                                               value="{{ old('last_name') }}" required>
                                        @if ($errors->has('last_name'))
                                            <label class="text-danger">{{ $errors->first('last_name') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row custom-hidden" id="corporate_div">
                                <div class="col-md-4 corporate_inr">
                                    <div class="form-group">
                                        <label for="location3">@lang('admin.corporate_name')
                                        </label>
                                        <select class="form-control" name="corporate_id"
                                                id="corporate_id">
                                            <option value="">--Select Corporate--</option>
                                            @foreach($corporates as $corporate)
                                                <option value="{{ $corporate->id }}">{{ $corporate->corporate_name }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('rider_type'))
                                            <label class="text-danger">{{ $errors->first('rider_type') }}</label>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4 corporate_inr">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang('admin.corporateemail')
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="corporate_email"
                                               name="corporate_email" value="{{old('corporate_email')}}"
                                               placeholder="@lang('admin.corporateemail')">
                                        @if ($errors->has('corporate_email'))
                                            <label class="text-danger">{{ $errors->first('corporate_email') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3">@lang("$string_file.country")
                                            <span class="text-danger">*</span></label>
                                        <select class="form-control" name="country" id="country"
                                                required>
                                            <option value="">@lang("$string_file.select")</option>
                                            @foreach($countries  as $country)
                                                <option data-min="{{ $country->minNumPhone }}"
                                                        data-max="{{ $country->maxNumPhone }}"
                                                        data-ISD="{{ $country->phonecode }}"
                                                        value="{{ $country->id }}|{{ $country->phonecode }}">{{  $country->CountryName }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('country'))
                                            <label class="text-danger">{{ $errors->first('country') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang("$string_file.code") :
                                        </label>
                                        <input type="text" class="form-control" id="isd"
                                               name="isd" value="{{old('isd')}}"
                                               placeholder="@lang("$string_file.isd_code")" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            @lang("$string_file.phone")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" autocomplete="false" class="form-control" id="user_phone"
                                               name="user_phone" value="{{old('user_phone')}}"
                                               placeholder="@lang("$string_file.phone")" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang("$string_file.email")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control" id="user_email"
                                               name="user_email"
                                               placeholder="@lang("$string_file.email")"
                                               value="{{ old('user_email') }}" required>
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

                                @if($appConfig->gender == 1)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="location3">@lang("$string_file.gender")
                                                :</label>
                                            <select class="form-control" name="driver_gender"
                                                    id="driver_gender"
                                                    required>
                                                <option value="1">@lang("$string_file.male")</option>
                                                <option value="2">@lang("$string_file.female")</option>
                                            </select>
                                            @if ($errors->has('driver_gender'))
                                                <label class="text-danger">{{ $errors->first('driver_gender') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                @if($appConfig->smoker == 1)
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
                                @endif
                            </div>

                            <div class="row">
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
                                    <i class="fa fa-check-circle"></i> Save
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
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
    <script>
        function RideType(val) {
            if (val == "1") {
                document.getElementById('corporate_div').style.display = 'block';
            } else {
                document.getElementById('corporate_div').style.display = 'none';
            }
        }
    </script>
@endsection
