@extends('merchant.layouts.main')
@section('content')
    <div class="app-content content">
        <div class="container-fluid ">
            <div class="content-wrapper">
                <div class="content-body">
                    <section id="validation">
                        <div class="row">
                            @include('merchant.shared.errors-and-messages')
                            <div class="col-12">
                                <div class="card shadow h-100">
                                    <div class="card-header py-3">
                                        <div class="content-header row">
                                            <div class="content-header-left col-md-8 col-12 mb-2 breadcrumb-new">
                                                <h3 class="content-header-title mb-0 d-inline-block">
                                                    <i class=" fa fa-user-plus" aria-hidden="true"></i>
                                                    @lang('admin.website_headings')</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="">
                                        <a class="heading-elements-toggle"><i
                                                    class="ft-ellipsis-h font-medium-3"></i></a>
                                        <div class="heading-elements">
                                            <ul class="list-inline mb-0">
                                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-content collapse show">
                                        <div class="card-body">
                                            <form method="POST" class="steps-validation wizard-notification"
                                                  enctype="multipart/form-data" action="{{ route('website-user-home-headings.store') }}">
                                                @csrf
                                                <fieldset>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="banner_image">
                                                                    @lang('admin.website_banner_image'):
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="file" class="form-control" id="banner_image"
                                                                       name="banner_image"
                                                                       placeholder="@lang('admin.website_banner_image')">
                                                                @if ($errors->has('banner_image'))
                                                                    <label class="text-danger">{{ $errors->first('banner_image') }}</label>
                                                                @endif
                                                            </div>
                                                            <img src="{{asset($details['user_banner_image'])}}" alt="" style="height:130px;">
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="start_address_hint">
                                                                    @lang('admin.website_start_time') :
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="start_address_hint"
                                                                       name="start_address_hint"
                                                                       placeholder="Start/Pickup Location" value="{{$details['LanguageSingle']['start_address_hint']}}" required>
                                                                @if ($errors->has('start_address_hint'))
                                                                    <label class="text-danger">{{ $errors->first('start_address_hint') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="end_address_hint">
                                                                    End/Drop Location :
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="end_address_hint"
                                                                       name="end_address_hint"
                                                                       placeholder="End/Drop Location" value="{{$details['LanguageSingle']['end_address_hint']}}" required>
                                                                @if ($errors->has('end_address_hint'))
                                                                    <label class="text-danger">{{ $errors->first('end_address_hint') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="book_btn_title">
                                                                    Book Button Title :
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="book_btn_title"
                                                                       name="book_btn_title"
                                                                       placeholder="Book Button Title" value="{{$details['LanguageSingle']['book_btn_title']}}" required>
                                                                @if ($errors->has('book_btn_title'))
                                                                    <label class="text-danger">{{ $errors->first('book_btn_title') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="book_btn_title">
                                                                    Estimate Button Title :
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="estimate_btn_title"
                                                                       name="estimate_btn_title"
                                                                       placeholder="Book Button Title" value="{{$details['LanguageSingle']['estimate_btn_title']}}" required>
                                                                @if ($errors->has('estimate_btn_title'))
                                                                    <label class="text-danger">{{ $errors->first('estimate_btn_title') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="estimate_description">
                                                                    Estimate Description:
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <textarea class="form-control" id="estimate_description" name="estimate_description"
                                                                          rows="4"
                                                                          placeholder="@lang("$string_file.description")" required>{{$details['LanguageSingle']['estimate_description'] }}</textarea>
                                                                @if ($errors->has('estimate_description'))
                                                                    <label class="text-danger">{{ $errors->first('estimate_description') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                        <div class="form-group text-center">
                                                            <strong for="estimate_description">
                                                                Features
                                                            </strong>
                                                        </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="features_title[{{$features[0]['id']}}]">
                                                                    Section One Title :
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="features_title[{{$features[0]['id']}}]"
                                                                       name="features[{{$features[0]['id']}}][title]"
                                                                       placeholder="Book Button Title" value="{{$features[0]['LanguageSingle']['title']}}" required>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="features_description[{{$features[0]['id']}}]">
                                                                    Section One Description:
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <textarea class="form-control" id="features_description[{{$features[0]['id']}}]" name="features[{{$features[0]['id']}}][description]"
                                                                          rows="4"
                                                                          placeholder="@lang("$string_file.description")" required>{{$features[0]['LanguageSingle']['description'] }}</textarea>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="features_title[{{$features[1]['id']}}]">
                                                                    Section Two Title :
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="features_title[{{$features[1]['id']}}]"
                                                                       name="features[{{$features[1]['id']}}][title]"
                                                                       placeholder="Book Button Title" value="{{$features[1]['LanguageSingle']['title']}}" required>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="features_description[{{$features[1]['id']}}]">
                                                                    Section Two Description:
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <textarea class="form-control" id="features_description[{{$features[0]['id']}}]"
                                                                          name="features[{{$features[1]['id']}}][description]"
                                                                          rows="4"
                                                                          placeholder="@lang("$string_file.description")" required>{{$features[1]['LanguageSingle']['description'] }}</textarea>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="features_title[{{$features[2]['id']}}]">
                                                                    Section Three Title :
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="features_title[{{$features[2]['id']}}]"
                                                                       name="features[{{$features[2]['id']}}][title]"
                                                                       placeholder="Book Button Title" value="{{$features[2]['LanguageSingle']['title']}}" required>
                                                                @if ($errors->has('estimate_btn_title'))
                                                                    <label class="text-danger">{{ $errors->first('estimate_btn_title') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="features_description[{{$features[2]['id']}}]">
                                                                    Section Three Description:
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <textarea class="form-control" id="features_description[{{$features[1]['id']}}]"
                                                                          name="features[{{$features[2]['id']}}][description]"
                                                                          rows="4"
                                                                          placeholder="@lang("$string_file.description")" required>{{$features[2]['LanguageSingle']['description'] }}</textarea>
                                                                @if ($errors->has('estimate_description'))
                                                                    <label class="text-danger">{{ $errors->first('estimate_description') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                </fieldset>
                                                <div class="form-actions right">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa fa-check-square-o"></i> Save
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