@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('configurationadded'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('configurationadded') }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title">
                        <i class=" icon fa-gears" aria-hidden="true"></i>
                        @lang('admin.delivery_config')
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('configuration.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.delivery_radius')<span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="radius"
                                           name="radius"
                                           placeholder="@lang('admin.delivery_radius')"
                                           value="{{ $configuration->radius }}"
                                           required>
                                    @if ($errors->has('radius'))
                                        <label class="danger">{{ $errors->first('radius') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.delivery_request_drivers')<span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="request_drivers"
                                           name="request_drivers"
                                           placeholder="@lang('admin.delivery_request_drivers')"
                                           value="{{ $configuration->request_drivers }}"
                                           required>
                                    @if ($errors->has('request_drivers'))
                                        <label class="danger">{{ $errors->first('request_drivers') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.later_request_type')<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="later_request_type" name="later_request_type" required>
                                        <option value="1" @if($configuration->later_request_type == 1) selected @endif>Send To All Driver</option>
                                        <option value="2" @if($configuration->later_request_type == 2) selected @endif>Cron Job</option>
                                    </select>
                                    @if ($errors->has('later_request_type'))
                                        <label class="danger">{{ $errors->first('later_request_type') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.delivery_later_radius')<span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="later_radius"
                                           name="later_radius"
                                           placeholder="@lang('admin.delivery_later_radius')"
                                           value="{{ $configuration->later_radius }}"
                                           required>
                                    @if ($errors->has('later_radius'))
                                        <label class="danger">{{ $errors->first('later_radius') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.delivery__later_request_drivers')<span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="later_request_drivers"
                                           name="later_request_drivers"
                                           placeholder="@lang('admin.delivery__later_request_drivers')"
                                           value="{{ $configuration->later_request_drivers }}"
                                           required>
                                    @if ($errors->has('later_request_drivers'))
                                        <label class="danger">{{ $errors->first('later_request_drivers') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            @if(Auth::user('merchant')->can('edit_configuration'))
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fa fa-check-circle"></i> @lang("$string_file.save")
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


