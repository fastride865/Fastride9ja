@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('notification'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>@lang('admin.message423')
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('promotions.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-bell"></i>
                        @lang('admin.message422')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{route('promotions.update', $promotion->id)}}">
                        {{method_field('PUT')}}
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.title")<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="title"
                                           name="title"
                                           placeholder="@lang("$string_file.title")" value="{{ $promotion->title }}" required>
                                    @if ($errors->has('title'))
                                        <label class="danger">{{ $errors->first('title') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="profile_image">
                                        @lang("$string_file.image")<span class="text-danger">*</span>
                                    </label>
                                    <input style="height: 0%" type="file" class="form-control" id="image"
                                           name="image"
                                           placeholder="@lang("$string_file.image")">
                                    @if ($errors->has('image'))
                                        <label class="danger">{{ $errors->first('image') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.message")<span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="message" name="message"
                                              rows="3"
                                              placeholder="@lang("$string_file.message")">{{ $promotion->message }}</textarea>
                                    @if ($errors->has('message'))
                                        <label class="danger">{{ $errors->first('message') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.url")<span class="text-danger">*</span>
                                    </label>
                                    <input type="url" class="form-control" id="url"
                                           name="url"
                                           placeholder="@lang("$string_file.url")" value="{{ $promotion->url }}">
                                    @if ($errors->has('url'))
                                        <label class="danger">{{ $errors->first('url') }}</label>
                                    @endif
                                </div>
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
@endsection