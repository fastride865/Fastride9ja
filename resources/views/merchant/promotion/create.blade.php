@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->add_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <div class="btn-group float-right" >
                            <a href="{{ route('promotions.index') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        @lang("$string_file.notification")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <div class="nav-tabs-horizontal" data-plugin="tabs">
                        <ul class="nav nav-tabs nav-tabs-line tabs-line-top" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="base-tab11" data-toggle="tab" href="#exampleTabsLineTopOne"
                                   aria-controls="#exampleTabsLineTopOne" role="tab">
                                    <i class="icon fa-cab"></i>@lang("$string_file.all")</a></li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="base-tab12" data-toggle="tab" href="#exampleTabsLineTopTwo"
                                   aria-controls="#exampleTabsLineTopTwo" role="tab">
                                    <i class="icon fa-clock-o"></i>@lang("$string_file.area_wise")</a></li>
                        </ul>
                        <div class="tab-content pt-20">
                            <div class="tab-pane active" id="exampleTabsLineTopOne" role="tabpanel">
                                <form method="POST" class="steps-validation wizard-notification"
                                      enctype="multipart/form-data"
                                      action="{{ route('promotions.store') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="location3">@lang("$string_file.application")
                                                    :</label>
                                                <select class="form-control"
                                                        name="application"
                                                        id="application"
                                                        onchange="DriverNotification(this.value)"
                                                        required>
                                                    <option value="">--@lang("$string_file.application")--
                                                    </option>
                                                    <option value="1">@lang("$string_file.driver")</option>
                                                    <option value="2">@lang("$string_file.user")</option>
                                                </select>
                                                @if ($errors->has('application'))
                                                    <label class="text-danger">{{ $errors->first('application') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="lastName3">
                                                    @lang("$string_file.title")<span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="title" name="title" placeholder="" required>
                                                    @if ($errors->has('title'))
                                                        <label class="text-danger">{{ $errors->first('title') }}</label>
                                                    @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emailAddress5">
                                                    @lang("$string_file.description")<span class="text-danger">*</span>
                                                </label>
                                                <textarea class="form-control" id="message"
                                                          name="message"
                                                          rows="3" required
                                                          placeholder=""></textarea>
                                                @if ($errors->has('message'))
                                                    <label class="text-danger">{{ $errors->first('message') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="profile_image">
                                                    @lang("$string_file.image")
                                                </label>
                                                <input style="height: 0%;" type="file" class="form-control"
                                                       id="image"
                                                       name="image"
                                                       placeholder="@lang("$string_file.image")">
                                                @if ($errors->has('image'))
                                                    <label class="text-danger">{{ $errors->first('image') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emailAddress5">
                                                    @lang("$string_file.url")
                                                </label>
                                                <input type="url" class="form-control"
                                                       id="url"
                                                       name="url"
                                                       placeholder="">
                                                @if ($errors->has('url'))
                                                    <label class="text-danger">{{ $errors->first('url') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emailAddress5">
                                                    @lang("$string_file.expire_date")
                                                </label>
                                                <input type="text" class="form-control customDatePicker1" name="date" placeholder="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions right" style="margin-bottom: 3%">
                                        <button type="submit" class="btn btn-primary float-right">
                                            <i class="fa fa-check-circle"></i> @lang("$string_file.send")
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="exampleTabsLineTopTwo" role="tabpanel">
                                <form method="POST" class="steps-validation wizard-notification"
                                      enctype="multipart/form-data"
                                      action="{{ route('merchant.areawise-notification') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="firstName3">
                                                    @lang("$string_file.service_area") <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control" name="area"
                                                        id="area" required>
                                                    <option value="">--Select Area--
                                                    </option>
                                                    @foreach($areas as $area)
                                                        <option value="{{ $area->id }}">@if($area->LanguageSingle) {{ $area->LanguageSingle->AreaName }} @else  {{ $area->LanguageAny->AreaName }} @endif</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('area'))
                                                    <label class="text-danger">{{ $errors->first('area') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="lastName3">
                                                    @lang("$string_file.title")<span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="title"
                                                       name="title"
                                                       placeholder=""
                                                       required>
                                                @if ($errors->has('title'))
                                                    <label class="text-danger">{{ $errors->first('title') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emailAddress5">
                                                    @lang("$string_file.description")<span class="text-danger">*</span>
                                                </label>
                                                <textarea class="form-control" id="message"
                                                          name="message"
                                                          rows="3" required
                                                          placeholder=""></textarea>
                                                @if ($errors->has('message'))
                                                    <label class="text-danger">{{ $errors->first('message') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="profile_image">
                                                    @lang("$string_file.image")
                                                </label>
                                                <input style="height: 0%" type="file" class="form-control"
                                                       id="image"
                                                       name="image"
                                                       placeholder="@lang("$string_file.image")">
                                                @if ($errors->has('image'))
                                                    <label class="text-danger">{{ $errors->first('image') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emailAddress5">
                                                    @lang("$string_file.url")
                                                </label>
                                                <input type="url" class="form-control"
                                                       id="url"
                                                       name="url"
                                                       placeholder="">
                                                @if ($errors->has('url'))
                                                    <label class="text-danger">{{ $errors->first('url') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emailAddress5">
                                                    @lang("$string_file.expire_date")
                                                </label>
                                                <input type="text" class="form-control customDatePicker1" name="date" placeholder="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions right" style="margin-bottom: 3%">
                                        <button type="submit" class="btn btn-primary float-right">
                                            <i class="fa fa-check-circle"></i> @lang("$string_file.send")
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
@endsection

