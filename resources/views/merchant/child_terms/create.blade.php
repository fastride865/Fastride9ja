@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                        <div class="panel-actions">
                            <div class="btn-group float-right" style="margin:10px">
                                <a href="{{ route('child-terms-conditions.index') }}">
                                    <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                    </button>
                                </a>
                            </div>
                        </div>
                    <h3 class="panel-title"><i class="wb-user-plus" aria-hidden="true"></i>
                      @lang("$string_file.add_child_t_n_c")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data" action="{{ route('child-terms-conditions.store') }}">
                            @csrf
                            <fieldset>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="emailAddress5">
                                                @lang("$string_file.service_area")<span
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
                                            @if ($errors->has('country'))
                                                <label class="danger">{{ $errors->first('country') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lastName3">
                                                @lang("$string_file.page_title")<span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="title"
                                                   name="title" value="{{old('title')}}" placeholder="@lang("$string_file.page_title")"
                                                   required>
                                            @if ($errors->has('title'))
                                                <label class="danger">{{ $errors->first('title') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="emailAddress5">
                                                @lang("$string_file.description")<span class="text-danger">*</span>
                                            </label>
                                            <textarea id="summernote" class="form-control"
                                                      name="description" rows="5"
                                                      placeholder="@lang("$string_file.description")" data-plugin="summernote">
                                       {{old('title')}}</textarea>
                                            @if ($errors->has('description'))
                                                <label class="danger">{{ $errors->first('description') }}</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="form-actions right" style="margin-bottom: 3%">
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
