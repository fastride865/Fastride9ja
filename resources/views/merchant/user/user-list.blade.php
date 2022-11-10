@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('success') || session('error'))
                <div aria-live="polite" aria-atomic="true">
                    <div class="toast" style="position: absolute; top: 5px; right: 5px; z-index: 999 !important;"
                         data-delay="5000" data-autohide="true">
                        <div class="toast-header">
                            <i class="fa fa-cog fa-spin"></i>
                            <strong class="mr-auto"> @lang("$string_file.corporate_user")</strong>
                            <small>@lang('admin.just_now')</small>
                            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="toast-body @if(session('success')) text-success @else text-danger @endif bg-white">
                            @if(session('success'))
                                <h6>{{ session('success') }}</h6>
                            @else
                                <h6>{{ session('error') }}</h6>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title"><i class="fa fa-taxi" aria-hidden="true"></i> @lang("$string_file.all_users") </h3>
                    {!! Form::open(['name'=>'otp','url'=>route('user.otp-verification')]) !!}
                    <div class="table_search row m-2">
                        <div class="col-md-4 form-group active-margin-top">
                            <input type="text" class="form-control" id="" name="otp" placeholder="Enter OTP" autocomplete="off">
                            <small id="emailHelp" class="form-text text-muted"> @lang('admin.enter_otp')</small>
                        </div>
                        <div class="col-md-4 form-group active-margin-top">
                            <button type="submit" class="btn btn-success" name ="submit" >@lang('admin.submit')</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
                <div class="panel-body container-fluid">
                    <table class="display nowrap table table-hover table-stripedw-full" id="customDataTable" style="width: 100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.email")</th>
                            <th>@lang('admin.merchantPhone')</th>
                            <th>@lang("$string_file.registered_date")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1 @endphp
                        @foreach($drivers as $driver)
                            <tr>
                                <td> {!! $sr !!} </td>
                                <td>{!! $driver->first_name.' '.$driver->last_name !!}</td>
                                <td>{!! $driver->email !!}</td>
                                <td>{!! $driver->UserPhone !!}</td>
                                <td>{{ $driver->created_at }}</td>
                            </tr>
                            @php $sr++; @endphp
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


