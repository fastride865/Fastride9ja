@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="{{ route('navigation-drawer.index') }}" style="margin:10px">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                            @if(!empty($info_setting) && $info_setting->edit_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                       @lang("$string_file.navigation_drawer")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('navigation-drawer.update', $edit->id) }}">
                        @csrf
                        <input type="hidden" name="_method" value="put">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">
                                        @lang("$string_file.name")<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="name" name="name"
                                           placeholder="@lang("$string_file.name")"
                                           @if(!empty($edit->LanguageAppNavigationDrawersOneViews)) value="{!! $edit->LanguageAppNavigationDrawersOneViews->name !!}" @endif
                                           required>
                                    @if ($errors->has('name'))
                                        <label class="danger">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sequence">
                                        @lang("$string_file.sequence")<span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control"
                                           id="sequence"
                                           name="sequence" min="1"
                                           placeholder="@lang("$string_file.sequence")"
                                           value="{{ $edit->sequence }}"
                                           required>
                                    @if ($errors->has('sequence'))
                                        <label class="danger">{{ $errors->first('sequence') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
{{--                                <div class="form-group">--}}
{{--                                    @if(isset($edit->image))--}}
{{--                                        <div class="col-md-3">--}}
{{--                                            <div class="row">--}}
{{--                                                <img src="{{ asset($edit->image) }}" alt="" class="img-responsive"> <br />--}}
{{--                                                <label for="image" class="btn btn-danger btn-sm btn-block">@lang('admin.change_image')?</label><br />--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        @if ($errors->has('image'))--}}
{{--                                            <span class="help-block">--}}
{{--                                                                        <strong>{{ $errors->first('image') }}</strong>--}}
{{--                                                                    </span>--}}
{{--                                        @endif--}}
{{--                                    @endif--}}
{{--                                </div>--}}
                                <div class="row"></div>
                                <div class="form-group">
                                    <label for="image">@lang("$string_file.icon")</label>
                                    <input type="file" name="image" id="image" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            @if(!Auth::user('merchant')->can('edit_navigation_drawer'))
                                @if(!$is_demo)
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fa fa-check-circle"></i> {{ trans("$string_file.update") }}
                                </button>
                                @else
                                    <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                                @endif
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'])
@endsection