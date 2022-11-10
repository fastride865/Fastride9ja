@extends('taxicompany.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('rideradded'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message224')
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
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang("$string_file.user_details")</h3>
                </header>
                <div class="panel-body container-fluid">
                    @if(Auth::user()->demo != 1)
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              action="{{route('users.update', $user->id)}}">
                            {{method_field('PUT')}}
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="firstName3">
                                             @lang("$string_file.first_name")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="first_name"
                                               name="first_name" value="{{$user->first_name}}"
                                               placeholder=" @lang("$string_file.first_name")" required>
                                        @if ($errors->has('first_name'))
                                            <label class="text-danger">{{ $errors->first('first_name')
                                                            }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.last_name")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="last_name"
                                               name="last_name" value="{{$user->last_name}}"
                                               placeholder="@lang("$string_file.last_name")" required>
                                        @if ($errors->has('last_name'))
                                            <label class="text-danger">{{ $errors->first('last_name')
                                                            }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            @lang("$string_file.phone")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="user_phone"
                                               name="user_phone"
                                               placeholder="@lang("$string_file.phone")"
                                               value="{{$user->UserPhone}}" required>
                                        @if ($errors->has('user_phone'))
                                            <label class="text-danger">{{ $errors->first('user_phone')
                                                            }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            @lang("$string_file.email")
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control" id="user_email"
                                               name="user_email"
                                               placeholder="@lang("$string_file.email")"
                                               value="{{$user->email}}" required>
                                        @if ($errors->has('user_email'))
                                            <label class="text-danger">{{ $errors->first('user_email')
                                                            }}</label>
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
                                               placeholder="@lang("$string_file.password")" disabled>
                                        @if ($errors->has('password'))
                                            <label class="text-danger">{{ $errors->first('password')
                                                            }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="1" name="edit_password"
                                                   id="edit_password" onclick="EditPassword()">
                                            @lang("$string_file.edit_password")
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @if(Auth::user('taxicompany')->Merchant->ApplicationConfiguration->gender == 1)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="location3">@lang("$string_file.gender")
                                                :</label>
                                            <select class="form-control" name="user_gender"
                                                    id="user_gender"
                                                    required>
                                                <option value="1"
                                                        @if($user->user_gender == 1) selected
                                                        @endif>@lang("$string_file.male")
                                                </option>
                                                <option value="2"
                                                        @if($user->user_gender == 2) selected
                                                        @endif>@lang("$string_file.female")
                                                </option>
                                            </select>
                                            @if ($errors->has('user_gender'))
                                                <label class="text-danger">{{ $errors->first('user_gender')
                                                            }}</label>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                    @if(Auth::user('taxicompany')->Merchant->ApplicationConfiguration->smoker == 1)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="location3"> @lang("$string_file.smoke")
                                                :</label>

                                            <label class="radio-inline"
                                                   style="margin-left: 5%;margin-right: 10%;margin-top: 1%;">
                                                <input type="radio" value="1"
                                                       checked id="smoker_type"
                                                       name="smoker_type"
                                                       @if($user->smoker_type == 1) checked
                                                       @endif
                                                       required> @lang("$string_file.smoker")
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" value="2" id="smoker_type"
                                                       name="smoker_type"
                                                       @if($user->smoker_type == 2) checked
                                                       @endif
                                                       required> @lang("$string_file.non_smoker")
                                            </label>
                                            <br>
                                            <label class="checkbox-inline" style="margin-left: 5%;margin-top: 1%;">
                                                <input type="checkbox" name="allow_other_smoker"
                                                       id="allow_other_smoker"
                                                       @if($user->allow_other_smoker == 1) checked @endif
                                                       value="1">  @lang("$string_file.allow_other_to_smoke")
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
                                               placeholder="@lang("$string_file.profile_image")"
                                               onchange="readURL(this)">
                                        @if ($errors->has('profile'))
                                            <label class="text-danger">{{ $errors->first('profile')
                                                            }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group text-center">
                                        @if(!empty($user->UserProfileImage))
                                            <img id="show_image" style="border-radius: 50%;"
                                                 src="@if($user->corporate_id){{ get_image($user->UserProfileImage,'corporate_user',$user->merchant_id)}}@else{{ get_image($user->Userprofile_image,'user')}}@endif" width="150"
                                                 height="150">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions d-flex flex-row-reverse p-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check-circle"></i> @lang("$string_file.update")
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="card bg-danger text-white shadow">
                            <div class="card-body">
                                Alert
                                <div class="large">@lang('admin.demo_user_cant_edited').</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
        function EditPassword() {
            if (document.getElementById("edit_password").checked = true) {
                document.getElementById('password').disabled = false;
            }
        }

    </script>
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#show_image')
                        .attr('src', e.target.result)
                        .width(200)
                        .height(200);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection



{{--@extends('taxicompany.layouts.main')--}}
{{--@section('content')--}}
{{--    <div class="page">--}}
{{--        <div class="page-content">--}}
{{--            @if(session('rideradded'))--}}
{{--                <div class="alert dark alert-icon alert-info" role="alert">--}}
{{--                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--                        <span aria-hidden="true">x</span>--}}
{{--                    </button>--}}
{{--                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message224')--}}
{{--                </div>--}}
{{--            @endif--}}
{{--            <div class="panel panel-bordered">--}}
{{--                <header class="panel-heading">--}}
{{--                    <div class="panel-actions">--}}
{{--                        <a href="{{ route('taxicompany.users.index') }}">--}}
{{--                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">--}}
{{--                                <i class="wb-reply"></i>--}}
{{--                            </button>--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>--}}
{{--                        @lang("$string_file.user_details")</h3>--}}
{{--                </header>--}}
{{--                <div class="panel-body container-fluid">--}}
{{--                    @if(Auth::user()->demo != 1)--}}
{{--                        <form method="POST" class="steps-validation wizard-notification"--}}
{{--                              enctype="multipart/form-data"--}}
{{--                              action="{{route('taxicompany.users.update', $user->id)}}">--}}
{{--                            {{method_field('PUT')}}--}}
{{--                            @csrf--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-md-3"></div>--}}
{{--                                <div class="col-md-4">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="firstName3">--}}
{{--                                             @lang("$string_file.first_name")--}}
{{--                                            <span class="text-danger">*</span>--}}
{{--                                        </label>--}}
{{--                                        <input type="text" class="form-control" id="first_name"--}}
{{--                                               name="first_name" value="{{$user->first_name}}"--}}
{{--                                               placeholder=" @lang("$string_file.first_name")" required>--}}
{{--                                        @if ($errors->has('first_name'))--}}
{{--                                            <label class="text-danger">{{ $errors->first('first_name')--}}
{{--                                                            }}</label>--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                    <div class="col-md-3"></div>--}}
{{--                                <div class="col-md-4">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="firstName3">--}}
{{--                                            @lang("$string_file.last_name")--}}
{{--                                            <span class="text-danger">*</span>--}}
{{--                                        </label>--}}
{{--                                        <input type="text" class="form-control" id="last_name"--}}
{{--                                               name="last_name" value="{{$user->last_name}}"--}}
{{--                                               placeholder="@lang("$string_file.last_name")" required>--}}
{{--                                        @if ($errors->has('last_name'))--}}
{{--                                            <label class="text-danger">{{ $errors->first('last_name')--}}
{{--                                                            }}</label>--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-md-4">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="lastName3">--}}
{{--                                            @lang("$string_file.phone")--}}
{{--                                            <span class="text-danger">*</span>--}}
{{--                                        </label>--}}
{{--                                        <input type="text" class="form-control" id="user_phone"--}}
{{--                                               name="user_phone"--}}
{{--                                               placeholder="@lang("$string_file.phone")"--}}
{{--                                               value="{{$user->UserPhone}}" required>--}}
{{--                                        @if ($errors->has('user_phone'))--}}
{{--                                            <label class="text-danger">{{ $errors->first('user_phone')--}}
{{--                                                            }}</label>--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-md-4">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="emailAddress5">--}}
{{--                                            @lang("$string_file.email")--}}
{{--                                            <span class="text-danger">*</span>--}}
{{--                                        </label>--}}
{{--                                        <input type="email" class="form-control" id="user_email"--}}
{{--                                               name="user_email"--}}
{{--                                               placeholder="@lang("$string_file.email")"--}}
{{--                                               value="{{$user->email}}" required>--}}
{{--                                        @if ($errors->has('user_email'))--}}
{{--                                            <label class="text-danger">{{ $errors->first('user_email')--}}
{{--                                                            }}</label>--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-md-4">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="emailAddress5">--}}
{{--                                            @lang("$string_file.password")--}}
{{--                                            <span class="text-danger">*</span>--}}
{{--                                        </label>--}}
{{--                                        <input type="password" class="form-control" id="password"--}}
{{--                                               name="password"--}}
{{--                                               placeholder="@lang("$string_file.password")" disabled>--}}
{{--                                        @if ($errors->has('password'))--}}
{{--                                            <label class="text-danger">{{ $errors->first('password')--}}
{{--                                                            }}</label>--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-md-4"></div>--}}
{{--                                <div class="col-md-4">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label class="checkbox-inline">--}}
{{--                                            <input type="checkbox" value="1" name="edit_password"--}}
{{--                                                   id="edit_password" onclick="EditPassword()">--}}
{{--                                            @lang("$string_file.edit_password")--}}
{{--                                        </label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="row">--}}
{{--                                @if(Auth::user('taxicompany')->Merchant->ApplicationConfiguration->gender == 1)--}}
{{--                                    <div class="col-md-4">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label for="location3">@lang("$string_file.gender")--}}
{{--                                                :</label>--}}
{{--                                            <select class="form-control" name="user_gender"--}}
{{--                                                    id="user_gender"--}}
{{--                                                    required>--}}
{{--                                                <option value="1"--}}
{{--                                                        @if($user->user_gender == 1) selected--}}
{{--                                                        @endif>@lang("$string_file.male")--}}
{{--                                                </option>--}}
{{--                                                <option value="2"--}}
{{--                                                        @if($user->user_gender == 2) selected--}}
{{--                                                        @endif>@lang("$string_file.female")--}}
{{--                                                </option>--}}
{{--                                            </select>--}}
{{--                                            @if ($errors->has('user_gender'))--}}
{{--                                                <label class="text-danger">{{ $errors->first('user_gender') }}--}}
{{--                                                </label>--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                @endif--}}

{{--                            </div>--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-md-4"></div>--}}
{{--                                <div class="col-md-4">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label class="checkbox-inline">--}}
{{--                                            <input type="checkbox" value="1" name="edit_password"--}}
{{--                                                   id="edit_password" onclick="EditPassword()">--}}
{{--                                            @lang("$string_file.edit_password")--}}
{{--                                        </label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="row">--}}
{{--                                @if(Auth::user('taxicompany')->Merchant->ApplicationConfiguration->smoker == 1)--}}
{{--                                    <div class="col-md-4">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label for="location3"> @lang("$string_file.smoke")--}}
{{--                                                :</label>--}}

{{--                                            <label class="radio-inline"--}}
{{--                                                   style="margin-left: 5%;margin-right: 10%;margin-top: 1%;">--}}
{{--                                                <input type="radio" value="1"--}}
{{--                                                       checked id="smoker_type"--}}
{{--                                                       name="smoker_type"--}}
{{--                                                       @if($user->smoker_type == 1) checked--}}
{{--                                                       @endif--}}
{{--                                                       required> @lang("$string_file.smoker")--}}
{{--                                            </label>--}}
{{--                                            <label class="radio-inline">--}}
{{--                                                <input type="radio" value="2" id="smoker_type"--}}
{{--                                                       name="smoker_type"--}}
{{--                                                       @if($user->smoker_type == 2) checked--}}
{{--                                                       @endif--}}
{{--                                                       required> @lang("$string_file.non_smoker")--}}
{{--                                            </label>--}}
{{--                                            <br>--}}
{{--                                            <label class="checkbox-inline" style="margin-left: 5%;margin-top: 1%;">--}}
{{--                                                <input type="checkbox" name="allow_other_smoker"--}}
{{--                                                       id="allow_other_smoker"--}}
{{--                                                       @if($user->allow_other_smoker == 1) checked @endif--}}
{{--                                                       value="1"> Allow user to smoke--}}
{{--                                            </label>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-md-4">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="profile_image">--}}
{{--                                            @lang("$string_file.profile_image")--}}
{{--                                            <span class="text-danger">*</span>--}}
{{--                                        </label>--}}
{{--                                        <input type="file" class="form-control" id="profile"--}}
{{--                                               name="profile"--}}
{{--                                               placeholder="@lang("$string_file.profile_image")"--}}
{{--                                               onchange="readURL(this)">--}}
{{--                                        @if ($errors->has('profile'))--}}
{{--                                            <label class="text-danger">{{ $errors->first('profile') }}--}}
{{--                                            </label>--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-md-4">--}}
{{--                                    <div class="form-group text-center">--}}
{{--                                        @if(!empty($user->Userprofile_image))--}}
{{--                                            <img id="show_image" style="border-radius: 50%;"--}}
{{--                                                 src="@if($user->corporate_id){{ get_image($user->Userprofile_image,'corporate_user',$user->merchant_id)}}@else{{ get_image($user->UserProfileImage,'user')}}@endif" width="150"--}}
{{--                                                 height="150">--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="form-actions d-flex flex-row-reverse p-2">--}}
{{--                                <button type="submit" class="btn btn-primary">--}}
{{--                                    <i class="fa fa-check-circle"></i> Save--}}
{{--                                </button>--}}
{{--                            </div>--}}
{{--                        </form>--}}
{{--                    @else--}}
{{--                        <div class="card bg-danger text-white shadow">--}}
{{--                            <div class="card-body">--}}
{{--                                Alert--}}
{{--                                <div class="large">It's demo user, can't be edited.</div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <script>--}}
{{--        function EditPassword() {--}}
{{--            if (document.getElementById("edit_password").checked = true) {--}}
{{--                document.getElementById('password').disabled = false;--}}
{{--            }--}}
{{--        }--}}
{{--    </script>--}}
{{--    <script>--}}
{{--        function readURL(input) {--}}
{{--            if (input.files && input.files[0]) {--}}
{{--                var reader = new FileReader();--}}

{{--                reader.onload = function (e) {--}}
{{--                    $('#show_image')--}}
{{--                        .attr('src', e.target.result)--}}
{{--                        .width(200)--}}
{{--                        .height(200);--}}
{{--                };--}}
{{--                reader.readAsDataURL(input.files[0]);--}}
{{--            }--}}
{{--        }--}}
{{--    </script>--}}
{{--@endsection--}}