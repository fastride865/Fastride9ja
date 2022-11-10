@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" >
                            <a href="{{ route('subadmin.index') }}">
                                <button type="button" class="btn btn-icon btn-success"style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                            @if(!empty($info_setting) && $info_setting->add_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                        @lang("$string_file.sub_admin")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="{{ route('subadmin.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.first_name")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="first_name"
                                           name="first_name"
                                           placeholder="" required>
                                    @if ($errors->has('first_name'))
                                        <label class="text-danger">{{ $errors->first('first_name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.last_name")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="last_name"
                                           name="last_name"
                                           placeholder="" required>
                                    @if ($errors->has('last_name'))
                                        <label class="text-danger">{{ $errors->first('last_name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.phone")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="phone_number"
                                           name="phone_number"
                                           placeholder="" required>
                                    @if ($errors->has('phone_number'))
                                        <label class="text-danger">{{ $errors->first('phone_number') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.email")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="email"
                                           name="email"
                                           placeholder="@lang('admin.message670')" required>
                                    @if ($errors->has('email'))
                                        <label class="text-danger">{{ $errors->first('email') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.password")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control" id="password"
                                           name="password"
                                           placeholder="" required>
                                    @if ($errors->has('password'))
                                        <label class="text-danger">{{ $errors->first('password') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.admin_type")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="admin_type"
                                            id="admin_type" onclick="area(this.value)"
                                            required>
                                        @if(Auth::user('merchant')->parent_id == 0)
                                            <option value="1">@lang("$string_file.all_areas")</option>
                                        @endif
                                        <option value="2">@lang("$string_file.service_area")</option>
                                    </select>
                                    @if ($errors->has('admin_type'))
                                        <label class="text-danger">{{ $errors->first('admin_type') }}</label>
                                        <label class="text-danger">{{ $errors->first('admin_type') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 " id="areaList">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.service_area")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control select2" name="area_list[]"
                                            id="area_list" multiple data-plugin="select2" disabled>
                                        @foreach($areas as $area)
                                            <option value="{{ $area->id }}">@if($area->LanguageSingle) {{ $area->LanguageSingle->AreaName }} @else  {{ $area->LanguageAny->AreaName }} @endif</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('area_list'))
                                        <label class="text-danger">{{ $errors->first('area_list') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 corporate_inr">
                                <div class="form-group">
                                    <label for="location3">@lang("$string_file.role")
                                    </label>
                                    <select class="form-control" name="role_id" id="role_id" required>
                                        <option value="">--@lang("$string_file.role")--</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('role_id'))
                                        <label class="text-danger">{{ $errors->first('role_id') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'])
    <script>
        function area(type) {
            if (type == 2) {
                document.getElementById('area_list').disabled = false;
            } else {
                document.getElementById('area_list').disabled = true;
            }
        }
    </script>
@endsection

