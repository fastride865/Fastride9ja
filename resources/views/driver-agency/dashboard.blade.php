@extends('driver-agency.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="mr--10 ml--10">
                <div class="row" style="margin-right: 0rem;margin-left: 0rem">
                    <!-- First Row -->
{{--                    @if(Auth::user('merchant')->can('view_drivers') && Auth::user('merchant')->can('view_rider') && Auth::user('merchant')->can('active_ride'))--}}
                        <div class="col-12 col-md-12 col-sm-12">
                            <!-- Example Panel With Heading -->
                            <div class="panel panel-bordered">
                                <div class="panel-heading">
                                    <div class="panel-actions"></div>
                                    <h3 class="panel-title">@lang("$string_file.site_statistics")  </h3>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="{{ route('driver-agency.driver.index') }}">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-success"
                                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="icon wb-users"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$string_file.active_drivers")</span>
                                                        <div class="content-text text-center mb-0">
                                                            <span class="font-size-18 font-weight-100">{{$drivers}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="{{ route('driver-agency.wallet') }}">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-warning"
                                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="fa fa-window-maximize"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400">@lang("$string_file.wallet_money")</span>
                                                        <div class="content-text text-center mb-0">
                                                            <span class="font-size-18 font-weight-100">{{$wallet_money}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection