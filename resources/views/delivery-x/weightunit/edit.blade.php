@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('success'))
                <div class="alert dark alert-icon alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('success') }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('weightunit.index') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px">
                                    <i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang('admin.message888') (@lang('admin.message460') {{ strtoupper(Config::get('app.locale')) }})
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          action="{{route('weightunit.update', $weightunit->id)}}">
                        {{method_field('PUT')}}
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.weightunit')
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="vehicle_make"
                                           name="name"
                                           value="@if(!empty($weightunit->LanguageSingle)) {{ $weightunit->LanguageSingle->name }} @endif"
                                           placeholder="@lang('admin.message189')"
                                           required>
                                    @if ($errors->has('name'))
                                        <label class="text-danger">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang('admin.description')
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="description"
                                              name="description" rows="2"
                                              placeholder="@lang('admin.message648')">@if(!empty($weightunit->LanguageSingle)) {{ $weightunit->LanguageSingle->description }} @endif</textarea>
                                    @if ($errors->has('description'))
                                        <label class="text-danger">{{ $errors->first('description') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> @lang('admin.update')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

