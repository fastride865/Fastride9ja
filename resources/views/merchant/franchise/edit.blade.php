@extends('merchant.layouts.main')
@section('content')
    <div class="container-fluid">
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row">
                    <div class="col-md-6 col-12">
                        @if(session('message567'))
                            <div class=" alert alert-icon-right alert-info alert-dismissible mb-2" role="alert">
                                <span class="alert-icon"><i class="fa fa-info"></i></span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <strong>@lang('admin.message567')</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="content-body">
                    <section id="validation">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="content-header-title mb-0 d-inline-block"><i class="fa fa-edit"></i> @lang('admin.message568')</h3>
                                        <div class="btn-group float-md-right">
                                            <a href="{{ route('franchisee.index') }}">
                                                <button type="button" class="btn btn-icon btn-success mr-1"><i class="fa fa-reply"></i>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-content collapse show">
                                        <div class="card-body">
                                        <form method="POST" class="steps-validation wizard-notification"
                                              enctype="multipart/form-data"
                                              action="{{route('franchisee.update', $franchisee->id)}}">
                                            {{method_field('PUT')}}
                                            @csrf
                                            <fieldset>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="firstName3">
                                                                @lang('admin.message561') :
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control" id="name"
                                                                   name="name"
                                                                   placeholder="@lang('admin.message561')"
                                                                   value="{{ $franchisee->name }}" required>
                                                            @if ($errors->has('name'))
                                                                <label class="text-danger">{{ $errors->first('name') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="emailAddress5">
                                                                @lang('admin.message563') :
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="email" class="form-control" id="email"
                                                                   name="email"
                                                                   placeholder="@lang('admin.message563')"
                                                                   value="{{ $franchisee->email }}" required>
                                                            @if ($errors->has('email'))
                                                                <label class="text-danger">{{ $errors->first('email') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>

                                                </div>


                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="emailAddress5">
                                                                @lang('admin.message564') :
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control" id="phone"
                                                                   name="phone"
                                                                   placeholder="@lang('admin.message564')"
                                                                   value="{{$franchisee->phone}}" required>
                                                            @if ($errors->has('phone'))
                                                                <label class="text-danger">{{ $errors->first('phone') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>


                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="emailAddress5">
                                                                @lang('admin.message562') :
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control" id="contact"
                                                                   name="contact"
                                                                   placeholder="@lang('admin.message562')"
                                                                   value="{{ $franchisee->contact_person_name }}"
                                                                   required>
                                                            @if ($errors->has('contact'))
                                                                <label class="text-danger">{{ $errors->first('contact') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="emailAddress5">
                                                                @lang('admin.message565') :
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control" id="commission"
                                                                   name="commission"
                                                                   placeholder="@lang('admin.message565')" value="{{ $franchisee->commission_percentage }}" required>
                                                            @if ($errors->has('commission'))
                                                                <label class="text-danger">{{ $errors->first('commission') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
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
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <fieldset class="checkbox">
                                                            <label>
                                                                <input type="checkbox" value="1" name="edit_password"
                                                                       id="edit_password" onclick="EditPassword()">
                                                                @lang('admin.message557')
                                                            </label>
                                                        </fieldset>
                                                    </div>
                                                </div>


                                            </fieldset>
                                            <div class="form-actions right">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa fa-check-circle"></i> Save
                                                </button>
                                            </div>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
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