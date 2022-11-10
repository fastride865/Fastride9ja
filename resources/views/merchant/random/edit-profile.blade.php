@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                            <div class="btn-group float-right">
                                <a href="{{ route('merchant.dashboard') }}">
                                    <button type="button" class="btn btn-icon btn-success" style="margin:10px">
                                        <i class="wb-reply"></i>
                                    </button>
                                </a>
                            </div>
                    </div>
                    <h3 class="panel-title">
                       @lang("$string_file.edit_profile") </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="{{route('merchant.profile.update')}}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.first_name") :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="merchantFirstName"
                                           name="merchantFirstName"
                                           value="{{ Auth::user()->merchantFirstName }}"
                                           placeholder="@lang("$string_file.first_name")" required>
                                    @if ($errors->has('merchantFirstName'))
                                        <label class="danger">{{ $errors->first('merchantFirstName') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.last_name") :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="merchantLastName"
                                           name="merchantLastName"
                                           placeholder="@lang("$string_file.last_name")"
                                           value="{{ Auth::user()->merchantLastName}}" required>
                                    @if ($errors->has('merchantLastName'))
                                        <label class="danger">{{ $errors->first('merchantLastName') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.phone") :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="merchantPhone"
                                           name="merchantPhone"
                                           placeholder="@lang("$string_file.phone")"
                                           value="{{Auth::user()->merchantPhone}}" required>
                                    @if ($errors->has('merchantPhone'))
                                        <label class="danger">{{ $errors->first('merchantPhone') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="profile_image">
                                        @lang("$string_file.address") :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="merchantAddress"
                                           name="merchantAddress"
                                           placeholder="@lang("$string_file.address")"
                                           value="{{Auth::user()->merchantAddress}}">
                                    @if ($errors->has('merchantAddress'))
                                        <label class="danger">{{ $errors->first('merchantAddress') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.password") :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="password" class="form-control" id="password"
                                           name="password"
                                           placeholder="@lang("$string_file.password")" disabled>
                                    @if ($errors->has('password'))
                                        <label class="danger">{{ $errors->first('password') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.logo") :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="business_logo"
                                           name="business_logo"
                                           placeholder="@lang("$string_file.logo")">
                                    @if ($errors->has('business_logo'))
                                        <label class="danger">{{ $errors->first('business_logo') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="checkbox-custom checkbox-primary">
                                    <input type="checkbox" value="1" name="edit_password"
                                           id="edit_password" onclick="EditPassword()">
                                    <label for="inputChecked"> @lang("$string_file.edit_password") </label>
                                </div>
                            </div>
                            @if(Auth::user('merchant')->demo != 1)
                             <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.login_background_image") :
                                        <span class="danger">*</span>
                                    </label>
                                    @if(!empty(Auth::user('merchant')->ApplicationTheme->login_background_image))
                                    <a href="{{get_image(Auth::user('merchant')->ApplicationTheme->login_background_image,'login_background')}}" target="_blank">@lang("$string_file.view")</a>
                                    @endif

                                    <input type="file" class="form-control" id="business_logo"
                                           name="login_background_image"
                                           placeholder="@lang("$string_file.login_background_image")">
                                    <br>
                                    <span style="color:red;">@lang("$string_file.login_image_warning")</span>
                                    @if ($errors->has('login_background_image'))
                                        <label class="danger">{{ $errors->first('login_background_image') }}</label>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="wb-check-circle"></i>@lang("$string_file.save")
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
@endsection