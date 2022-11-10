@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
          @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                    </div>
                    <h4 class="panel-title">
                        <i class=" icon fa-gears" aria-hidden="true"></i>
                        @lang("$string_file.email_configuration")
                    </h4>
                </header>
                <div class="panel-body container-fluid">
                    <h5>
                        <i class="icon fa-user" aria-hidden="true"></i>
                        @lang("$string_file.sender_details")
                    </h5>
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('merchant.emailtemplate.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="host">
                                        @lang("$string_file.host_name")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           name="host"
                                           placeholder=""
                                           value="@if($configuration){{$configuration['host']}}@endif"
                                           required>
                                    @if ($errors->has('host'))
                                        <label class="danger">{{ $errors->first('host') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.email")<span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control"
                                           name="username"
                                           placeholder=""
                                           value="@if($configuration){{$configuration['username']}}@endif"
                                           required>
                                    @if ($errors->has('username'))
                                        <label class="danger">{{ $errors->first('username') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="password">
                                        @lang("$string_file.password")<span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control"
                                           name="password"
                                           placeholder=""
                                           value="@if($configuration){{$configuration['password']}}@endif"
                                           required>
                                    @if ($errors->has('password'))
                                        <label class="danger">{{ $errors->first('password') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="encryption">
                                        @lang("$string_file.encryption")<span class="text-danger">*</span><span
                                                class="text-primary"> ( tls/ssl )</span>
                                    </label>
                                    <select class="form-control" required name="encryption">
                                        <option value="">@lang("$string_file.select")</option>
                                        <option value="ssl"
                                                @if(isset($configuration['encryption']) && $configuration['encryption'] == 'ssl')selected @endif>
                                            SSL
                                        </option>
                                        <option value="tls"
                                                @if(isset($configuration['encryption']) && $configuration['encryption'] == 'tls')selected @endif>
                                            TLS
                                        </option>
                                    </select>
                                    @if ($errors->has('encryption'))
                                        <label class="danger">{{ $errors->first('encryption') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="port">
                                        @lang("$string_file.port_number") <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" required name="port">
                                        <option value="">@lang("$string_file.select")</option>
                                        <option value="465"
                                                @if(isset($configuration['port']) && $configuration['port'] == '465')selected @endif>
                                            465(SSL)
                                        </option>
                                        <option value="587"
                                                @if(isset($configuration['port']) && $configuration['port'] == '587')selected @endif>
                                            587(TLS)
                                        </option>
                                    </select>
                                    @if ($errors->has('port'))
                                        <label class="danger">{{ $errors->first('port') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <h5>
                            <i class="icon fa-envelope" aria-hidden="true"></i>
                            @lang("$string_file.welcome_email_configuration")
                        </h5>
                        <div class="row">
                            {{--<div class="col-md-3">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.logo")<span class="text-danger">*</span>
                                    </label>
                                    <input style="height: 0%" type="file" class="form-control"
                                           name="logo"
                                           placeholder=""
                                           value=""
                                           @if(empty($template['event']['welcome']['logo'])) required @endif>
                                </div>
                            </div>
                            <div class="col-md-3">
                                @if(!empty($template['event']['welcome']['logo']))
                                    <img class="rounded img-bordered img-bordered-primary" width="150" height="150"
                                         src="{{get_image($template['event']['welcome']['logo'],'email')}}"
                                         alt="...">
                                @endif
                            </div>--}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.image")<span class="text-danger">*</span>
                                    </label>
                                    <input style="height: 0%" type="file" class="form-control"
                                           name="image"
                                           placeholder="@lang('admin.message149')"
                                           value=""
                                           @if(empty($template['event']['welcome']['image'])) required @endif>
                                    @if ($errors->has('number_of_driver_user_map'))
                                        <label class="danger">{{ $errors->first('number_of_driver_user_map') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                @if(!empty($template['event']['welcome']['image']))
                                    <img class="rounded img-bordered img-bordered-primary" width="150" height="150"
                                         src="{{get_image($template['event']['welcome']['image'],'email')}}"
                                         alt="...">
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.heading")<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           name="heading"
                                           placeholder="Heading"
                                           value="@if(!empty($welcome->Heading)) {{$welcome->Heading}} @endif"
                                           required>
                                    @if ($errors->has('location_update_timeband'))
                                        <label class="danger">{{ $errors->first('location_update_timeband') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.sub_heading")<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           name="subheading"
                                           placeholder="Sub heading "
                                           value="@if(!empty($welcome->Subheading)) {{$welcome->Subheading}} @endif"
                                           required
                                    >
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.message")<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="textmessage"
                                           name="textmessage"
                                           value="@if(!empty($welcome->Message)) {{$welcome->Message}} @endif"
                                           placeholder="Message"
                                           required
                                    >
                                </div>
                            </div>
                        </div>
                        <h5>
                            <i class="icon fa-file" aria-hidden="true"></i>
                            @lang("$string_file.invoice_email_configuration")
                        </h5>
                        <div class="row">
                            {{--<div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.logo")<span class="text-danger">*</span>
                                    </label>
                                    <input style="height: 0%;" type="file" class="form-control"
                                           name="invoice_logo"
                                           placeholder="@lang('admin.message148')"
                                           value=""
                                           @if(empty($template['event']['invoice']['logo'])) required @endif
                                    >
                                </div>
                            </div>
                            <div class="col-md-4">
                                @if(!empty($template['event']['invoice']['logo']))
                                    <img class="rounded img-bordered img-bordered-primary" width="150" height="150"
                                         src="{{get_image($template['event']['invoice']['logo'],'email')}}"
                                         alt="...">
                                @endif
                            </div>
                            <div class="col-md-4">
                            </div>--}}
                            @php $socialLinks = \Illuminate\Support\Facades\Config::get('custom.social_links'); @endphp
                            @foreach($socialLinks as $key => $socialLink)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3"> {{$socialLink}} Link</label>
                                        <input type="text" name="socialLinks[{{$key}}]"
                                               value="@if(isset($template['event']['invoice']['social_links'])){{$template['event']['invoice']['social_links']->$key}}@endif"
                                               class="form-control" placeholder="Enter {{$socialLink}} Link">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            @if(Auth::user('merchant')->can('edit_email_configurations'))
                            @if(!$is_demo)
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fa fa-check-square-o"></i> @lang("$string_file.save")
                                </button>
                            @else
                                <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                            @endif
                            @endif
                        </div>
                    </form>
                </div>
                {{--                <div class="panel">--}}
                {{--                    <div class="panel-heading">--}}
                {{--                        <div class="panel-actions"></div>--}}
                {{--                                <h3 class="panel-title">--}}
                {{--                                    <i class="icon fa-envelope" aria-hidden="true"></i>--}}
                {{--                                    @lang('admin.welcome_email_template')--}}
                {{--                                </h3>--}}
                {{--                    </div>--}}
                {{--                    <div class="panel-body container-fluid" >--}}

                {{--                    </div>--}}
                {{--                </div>--}}
                {{--                <div class="panel">--}}
                {{--                    <div class="panel-heading">--}}
                {{--                        <div class="panel-actions"></div>--}}
                {{--                                <h3 class="panel-title">--}}
                {{--                                    <i class="fa fa-print" aria-hidden="true"></i>--}}
                {{--                                    @lang('admin.invoice_email_template')</h3>--}}
                {{--                    </div>--}}
                {{--                    <div class="panel-body container-fluid">--}}
                {{--                    </div>--}}
                {{--                </div>--}}
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection