@extends('taxicompany.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('message181'))
                <div class="alert dark alert-icon alert-info alert-dismissible"
                     role="alert">
                    <button type="button" class="close" data-dismiss="alert"
                            aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message181')
                </div>
            @endif
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
                          enctype="multipart/form-data" action="{{ route('taxicompany.profile.submit') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">
                                        @lang('admin.company_name') :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="name"
                                           name="name"
                                           value="{{ isset($taxicompany) ? $taxicompany->name : '' }}"
                                           placeholder="@lang('admin.company_name')" required>
                                    @if ($errors->has('name'))
                                        <label class="danger">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="web_rest_key">
                                        @lang('admin.company_email') :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="email"
                                           name="email"
                                           placeholder="@lang('admin.company_email')"
                                           value="{{ isset($taxicompany) ? $taxicompany->email : '' }}" required>
                                    @if ($errors->has('email'))
                                        <label class="danger">{{ $errors->first('email') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.company_phone') :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="phone"
                                           name="phone"
                                           placeholder="@lang('admin.company_phone')"
                                           value="{{ isset($taxicompany) ? $taxicompany->phone : '' }}" required>
                                    @if ($errors->has('phone'))
                                        <label class="danger">{{ $errors->first('phone') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.company_contact_pers') :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="contact_person"
                                           name="contact_person"
                                           placeholder="@lang('admin.company_contact_pers')"
                                           value="{{ isset($taxicompany) ? $taxicompany->contact_person : '' }}">
                                    @if ($errors->has('contact_person'))
                                        <label class="danger">{{ $errors->first('contact_person') }}</label>
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
                                        @lang('admin.company_add') :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="address"
                                           name="address"
                                           placeholder="@lang('admin.company_add')"
                                           value="{{ isset($taxicompany) ? $taxicompany->address : '' }}" required>
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