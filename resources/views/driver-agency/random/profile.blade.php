@extends('driver-agency.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ URL:: previous() }}">
                            <button type="button" class="btn btn-icon btn-success" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-user-circle"></i>
                       @lang("$string_file.edit_profile") </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="{{ route('driver-agency.profile.submit') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">
                                        @lang("$string_file.full_name") :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="name"
                                           name="name"
                                           value="{{ isset($driver_agency) ? $driver_agency->name : '' }}"
                                           placeholder="" required>
                                    @if ($errors->has('name'))
                                        <label class="danger">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="web_rest_key">
                                        @lang("$string_file.email") :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="email"
                                           name="email"
                                           placeholder=""
                                           value="{{ isset($driver_agency) ? $driver_agency->email : '' }}" required>
                                    @if ($errors->has('email'))
                                        <label class="danger">{{ $errors->first('email') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.phone") :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="phone"
                                           name="phone"
                                           placeholder=""
                                           value="{{ isset($driver_agency) ? $driver_agency->phone : '' }}" required>
                                    @if ($errors->has('phone'))
                                        <label class="danger">{{ $errors->first('phone') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.password") :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="password" class="form-control" id="password"
                                           name="password"
                                           placeholder="@lang("$string_file.password")" required disabled>
                                    @if ($errors->has('password'))
                                        <label class="danger">{{ $errors->first('password') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.address") :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="address"
                                           name="address"
                                           placeholder="@lang('admin.company_add')"
                                           value="{{ isset($driver_agency) ? $driver_agency->address : '' }}" required>
                                    @if ($errors->has('address'))
                                        <label class="danger">{{ $errors->first('address') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <fieldset class="checkbox">
                                    <label>
                                        <input type="checkbox" value="1" name="edit_password"
                                               id="edit_password" onclick="EditPassword()">
                                        @lang("$string_file.edit_password")
                                    </label>
                                </fieldset>
                            </div>
                        </div>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="wb-check-circle"></i> @lang("$string_file.save")
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