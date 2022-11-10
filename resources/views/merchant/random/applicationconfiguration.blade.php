@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('configuration'))
                <div class="alert dark alert-icon alert-success alert-dismissible"
                     role="alert">
                    <button type="button" class="close" data-dismiss="alert"
                            aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message110')
                </div>
            @endif
            @if ($errors->has('driver_wallet'))
                {!! $errors !!}
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title">
                        <i class="icon fa-gears" aria-hidden="true"></i>
                        @lang('admin.message748')
                    </h3>
                    </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              action="{{ route('merchant.application_configuration.store') }}">
                            @csrf
                            <div class="row">
{{--                                <div class="col-md-4">--}}
{{--                                    <div class="form-group">--}}
{{--                                        @if(isset($configuration->banner_image_user))--}}
{{--                                            <div class="col-md-3">--}}
{{--                                                <div class="row">--}}
{{--                                                    <img src="{{ get_image($configuration->banner_image_user,'splash') }}" alt="" class="config-imgage"> <br />--}}
{{--                                                    <label for="image" class="btn btn-danger btn-sm btn-block">Change image ?</label><br />--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            @if ($errors->has('banner_image_user'))--}}
{{--                                                <span class="help-block">--}}
{{--                                                                        <strong>{{ $errors->first('banner_image_user') }}</strong>--}}
{{--                                                                    </span>--}}
{{--                                            @endif--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                    <div class="row"></div>--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="image">@lang('admin.banner_image_user')</label>--}}
{{--                                        <input type="file" name="banner_image_user" id="banner_image_user" class="form-control">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                            </div>
                            <div class="form-actions right" style="margin-bottom: 3%">
                                @if(Auth::user('merchant')->can('edit_configuration'))
                                    <button type="submit" class="btn btn-primary float-right">
                                        <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                                    </button>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

