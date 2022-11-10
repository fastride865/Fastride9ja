@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                        <div class="panel-actions">
                            @if(!empty($info_setting) && $info_setting->edit_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                            <div class="btn-group float-right">
                                <a href="{{ route('promotions.index') }}">
                                    <button type="button" class="btn btn-icon btn-success" style="margin:10px">
                                        <i class="wb-reply"></i>
                                    </button>
                                </a>
                            </div>
                        </div>
                    <h3 class="panel-title"><i class="wb-edit"></i>
                        @lang("$string_file.notification")
                    </h3>
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
                                        <label class="text-danger">{{ $errors->first('title') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ProfileImage">
                                        @lang("$string_file.image")<span class="text-danger">*</span>
                                    </label>
                                    <input style="height: 0%" type="file" class="form-control" id="image"
                                           name="image"
                                           placeholder="@lang("$string_file.image")">
                                    @if ($errors->has('image'))
                                        <label class="text-danger">{{ $errors->first('image') }}</label>
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
                                        <label class="text-danger">{{ $errors->first('message') }}</label>
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
                                        <label class="text-danger">{{ $errors->first('url') }}</label>
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
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'])
@endsection