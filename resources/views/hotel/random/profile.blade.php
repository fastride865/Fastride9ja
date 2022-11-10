@extends('hotel.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('message181'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message181')
                </div>
            @endif
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title">
                       @lang("$string_file.edit_profile")
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('hotel.profile.submit') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">
                                        @lang('admin.message551')<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="name" name="name"
                                           placeholder="@lang('admin.message551')"
                                           value="{{ isset($hotel) ? $hotel->name : '' }}">
                                    @if ($errors->has('name'))
                                        <label class="danger">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="web_rest_key">
                                        @lang('admin.message552')<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="email"
                                           name="email"
                                           placeholder="@lang('admin.message552')"
                                           value="{{ isset($hotel) ? $hotel->email : '' }}" required>
                                    @if ($errors->has('email'))
                                        <label class="danger">{{ $errors->first('email') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.message553')<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="phone"
                                           name="phone"
                                           placeholder="@lang('admin.message553')"
                                           value="{{ isset($hotel) ? $hotel->phone : '' }}"
                                           required>
                                    @if ($errors->has('phone'))
                                        <label class="danger">{{ $errors->first('phone') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.password")<span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control"
                                           id="password" name="password"
                                           placeholder="@lang("$string_file.password")"
                                           value=""
                                           required disabled>
                                    @if ($errors->has('password'))
                                        <label class="danger">{{ $errors->first('password') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <fieldset class="checkbox">
                                    <label>
                                        <input type="checkbox" value="1" name="edit_password"
                                               id="edit_password" onclick="EditPassword()">
                                        @lang('admin.message180')
                                    </label>
                                </fieldset>
                            </div>
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="fa fa-check-circle"></i> @lang("$string_file.update")
                            </button>
                        </div>
                    </form>
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
@endsection

