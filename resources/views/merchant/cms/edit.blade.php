@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->edit_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <a href="{{ route('cms.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang("$string_file.cms_page")
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('cms.update',$cmspage->id) }}">
                        {{method_field('PUT')}}
                        @csrf
                        <fieldset>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            @lang("$string_file.page_title")<span
                                                    class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="title"
                                               name="title"
                                               value="@if($cmspage->LanguageSingle){{ $cmspage->LanguageSingle->title }} @else {{$cmspage->LanguageAny->title}} @endif"
                                               placeholder="@lang("$string_file.page_title")"
                                               required>
                                        @if ($errors->has('title'))
                                            <label class="text-danger">{{ $errors->first('title') }}</label>
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
                                                 @if($cmspage->LanguageSingle){{ $cmspage->LanguageSingle->description }}
                                            @else {{$cmspage->LanguageAny->description}}
                                            @endif
                                             </textarea>
                                        @if ($errors->has('description'))
                                            <label class="text-danger">{{ $errors->first('description') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </fieldset>
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
