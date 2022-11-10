@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('terms.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class=" wb-user-plus" aria-hidden="true"></i>
                        @lang("$string_file.add") @lang('admin.terms_condition')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="{{ route('terms.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.select")<span
                                                class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="country" id="country"
                                            required>
                                        <option value="">@lang("$string_file.select")</option>
                                        @foreach($countries  as $country)
                                            <option data-min="{{ $country->maxNumPhone }}"
                                                    data-max="{{ $country->maxNumPhone }}"
                                                    value="{{ $country->id }}">{{  $country->CountryName }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('page'))
                                        <label class="danger">{{ $errors->first('page') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.page_type")<span
                                                class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="page" id="page"
                                            required>
                                        <option value="">--@lang("$string_file.select")--
                                        </option>
                                        @foreach($pages as $page)
                                            <option value="{{ $page->slug }}">{{ $page->page }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('page'))
                                        <label class="danger">{{ $errors->first('page') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.application")<span
                                                class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="application"
                                            id="application" required>
                                        <option value="2">@lang("$string_file.driver")</option>
                                        <option value="1">@lang("$string_file.user")</option>
                                    </select>
                                    @if ($errors->has('application'))
                                        <label class="danger">{{ $errors->first('application') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.page_title")<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="title"
                                           name="title"
                                           placeholder="@lang("$string_file.page_title")"
                                           required>
                                    @if ($errors->has('title'))
                                        <label class="danger">{{ $errors->first('title') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.description")<span
                                                class="text-danger">*</span>
                                    </label>
                                    <textarea id="summernote" class="form-control"
                                              name="description" rows="5"
                                              placeholder="@lang("$string_file.description")" data-plugin="summernote">
                                        </textarea>

                                    @if ($errors->has('description'))
                                        <label class="danger">{{ $errors->first('description') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection