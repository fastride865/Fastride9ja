@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('notification'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <span class="alert-icon"><i class="fa fa-info"></i></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ (session('notification')) }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('promotionsms.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fas fa-sms" aria-hidden="true"></i>
                        @lang('admin.sms')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <div class="nav-tabs-horizontal" data-plugin="tabs">
                        <ul class="nav nav-tabs nav-tabs-line tabs-line-top" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="base-tab11" data-toggle="tab" href="#exampleTabsLineTopOne"
                                   aria-controls="#exampleTabsLineTopOne" role="tab">
                                    <i class="icon fa-cab"></i>@lang("$string_file.all")</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="base-tab12" data-toggle="tab" href="#exampleTabsLineTopTwo"
                                   aria-controls="#exampleTabsLineTopTwo" role="tab">
                                    <i class="icon fa-clock-o"></i>@lang('admin.single')</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="base-tab13" data-toggle="tab" href="#exampleTabsLineTopThree"
                                   aria-controls="#exampleTabsLineTopThree" role="tab">
                                    <i class="icon fa-clock-o"></i>@lang("$string_file.service_area")</a>
                            </li>
                        </ul>
                        <div class="tab-content pt-20">
                            <div class="tab-pane active" id="exampleTabsLineTopOne" role="tabpanel">
                                <form method="POST" class="steps-validation wizard-notification"
                                      enctype="multipart/form-data"
                                      action="{{ route('promotionsms.store') }}">
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
                                                    <option value="">
                                                        --@lang("$string_file.application")--
                                                    </option>
                                                    <option value="1">@lang("$string_file.driver")</option>
                                                    <option value="2">@lang("$string_file.user")</option>
                                                </select>

                                                @if ($errors->has('application'))
                                                    <label class="danger">{{ $errors->first('application') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emailAddress5">
                                                    @lang('admin.sms_text')<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <textarea class="form-control" id="message"
                                                          name="message"
                                                          rows="2" required
                                                          placeholder="@lang('admin.enter_sms_text')"></textarea>
                                                @if ($errors->has('message'))
                                                    <label class="danger">{{ $errors->first('message') }}</label>
                                                @endif
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
                                      action="{{ route('merchant.promotionsms.storeUserDriver') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="location3">@lang("$string_file.application")
                                                    :</label>
                                                <select class="form-control"
                                                        name="application"
                                                        id="application"
                                                        onchange="UserDriver(this.value)"
                                                        required>
                                                    <option value="">
                                                        --@lang("$string_file.application")--
                                                    </option>
                                                    <option value="1">@lang("$string_file.driver")</option>
                                                    <option value="2">@lang("$string_file.user")</option>
                                                </select>

                                                @if ($errors->has('application'))
                                                    <label class="danger">{{ $errors->first('application') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="location3">@lang("$string_file.name")
                                                    :</label>
                                                <select class="form-control"
                                                        name="user_driver_id"
                                                        id="driver_user_name"
                                                        onchange="UserDriver(this.value)"
                                                        required>
                                                </select>
                                                @if ($errors->has('driver_user_name'))
                                                    <label class="danger">{{ $errors->first('driver_user_name') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emailAddress5">
                                                    @lang('admin.sms_text')<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <textarea class="form-control" id="message"
                                                          name="message"
                                                          rows="2" required
                                                          placeholder="@lang('admin.enter_sms_text')"></textarea>
                                                @if ($errors->has('message'))
                                                    <label class="danger">{{ $errors->first('message') }}</label>
                                                @endif
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
                            <div class="tab-pane" id="tab12" aria-labelledby="base-tab12">
                                <form method="POST" class="steps-validation wizard-notification"
                                      enctype="multipart/form-data"
                                      action="{{ route('merchant.areawise-notification') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="firstName3">
                                                    @lang('admin.AreaName')<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <select class="form-control" name="area"
                                                        id="area" required>
                                                    <option value="">--@lang("$string_file.area")--
                                                    </option>
                                                    @foreach($areas as $area)
                                                        <option value="{{ $area->id }}">@if($area->LanguageSingle) {{ $area->LanguageSingle->AreaName }} @else  {{ $area->LanguageAny->AreaName }} @endif</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('area'))
                                                    <label class="danger">{{ $errors->first('area') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emailAddress5">
                                                    @lang('admin.sms_text')<span
                                                            class="text-danger">*</span>
                                                </label>
                                                <textarea class="form-control" id="message"
                                                          name="message"
                                                          rows="3" required
                                                          placeholder="@lang('admin.enter_sms_text')"></textarea>
                                                @if ($errors->has('message'))
                                                    <label class="danger">{{ $errors->first('message') }}</label>
                                                @endif
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
    <script>
        function UserDriver(val) {
            $.ajax({
                method: 'GET',
                url: "{{ route('merchant.promotionsms.userdriver') }}",
                data: {user_driver: val},
                success: function (data) {
                    $('#driver_user_name').html(data);
                }
            });
        }

        function show() {
            if (document.getElementById("expery_check").checked = true) {
                document.getElementById('datepicker-end').disabled = false;
            }
        }

        function show1() {
            if (document.getElementById("expery_check_two").checked = true) {
                document.getElementById('datepicker-backend').disabled = false;
            }
        }
    </script>
@endsection