@extends('merchant.layouts.main')
@section('content')
    <div class="container-fluid">
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row">
                    <div class="col-md-6 col-12">
                        @if(session('message566'))
                            <div class="alert alert-icon-right alert-info alert-dismissible mb-2" role="alert">
                                <span class="alert-icon"><i class="fa fa-info"></i></span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <strong>{{ session('message566') }}</strong>
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
                                        <h3 class="content-header-title mb-0 d-inline-block"><i class="fa fa-plus"></i> @lang('admin.message560')</h3>
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
                                              enctype="multipart/form-data" action="{{ route('franchisee.store') }}">
                                            @csrf
                                            <fieldset>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="firstName3">
                                                                @lang("$string_file.service_area")  :
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <select class="form-control" name="area" id="area" required>
                                                                <option value="">--Select Area--</option>
                                                                @foreach($areas as $area)
                                                                    <option value="{{ $area->id }}">{{ $area->CountryAreaName }}</option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('area'))
                                                                <label class="text-danger">{{ $errors->first('area') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="firstName3">
                                                                @lang('admin.message561') :
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control" id="name"
                                                                   name="name"
                                                                   placeholder="@lang('admin.message561')" required>
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
                                                                   placeholder="@lang('admin.message563')" required>
                                                            @if ($errors->has('email'))
                                                                <label class="text-danger">{{ $errors->first('email') }}</label>
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
                                                                   placeholder="@lang('admin.message562')" required>
                                                            @if ($errors->has('contact'))
                                                                <label class="text-danger">{{ $errors->first('contact') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="location3">@lang("$string_file.service_area") :</label>
                                                            <select class="form-control" name="country" id="country"
                                                                    required>
                                                                <option value="">@lang("$string_file.select")</option>
                                                                @foreach($countries  as $country)
                                                                    <option data-min="{{ $country->maxNumPhone }}"
                                                                            data-max="{{ $country->maxNumPhone }}"
                                                                            value="{{ $country->phonecode }}">{{  $country->CountryName }}</option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('country'))
                                                                <label class="text-danger">{{ $errors->first('country') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="lastName3">
                                                                @lang('admin.message564') :
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control" id="user_phone"
                                                                   name="phone"
                                                                   placeholder="@lang('admin.message564')" required>
                                                            @if ($errors->has('phone'))
                                                                <label class="text-danger">{{ $errors->first('phone') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>


                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="emailAddress5">
                                                                @lang('admin.message565') :
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control" id="commission"
                                                                   name="commission"
                                                                   placeholder="@lang('admin.message565')" required>
                                                            @if ($errors->has('contact'))
                                                                <label class="text-danger">{{ $errors->first('contact') }}</label>
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
                                                                   placeholder="@lang("$string_file.password")" required>
                                                            @if ($errors->has('password'))
                                                                <label class="text-danger">{{ $errors->first('password') }}</label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                            </fieldset>
                                            <div class="form-actions float-right">
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
@endsection