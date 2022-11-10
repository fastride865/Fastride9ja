@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content container-fluid">
            @if(session('priceadded'))
                <div class="alert dark alert-icon alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon fa-info" aria-hidden="true"></i>{{ session('priceadded') }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(Auth::user('merchant')->can('create_price_card'))
                            <a href="{{route('pricecard.add')}}">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus" title="@lang("$string_file.add") @lang('admin.pricecard')"></i>
                                </button>
                            </a>
                        @endif
                        @if(Auth::user('merchant')->can('view_price_card'))
                            <a href="{{route('excel.pricecard')}}">
                                <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                    <i class="wb-download" title="@lang("$string_file.export_excel")"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="fa-money" aria-hidden="true"></i>
                        @lang('admin.pricecard')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('order.search') }}" method="GET">
                        @csrf
                        <div class="table_search row p-3">
                            <div class="col-md-2 active-margin-top">@lang('admin.message727') :</div>
                            <div class="col-md-4 col-xs-12 form-group active-margin-top">
                                {!! Form::select('area',add_blank_option([],trans("$string_file.service_area")),old('area'),["class"=>"form-control serviceAreaList","id"=>"area","required"=>true]) !!}
                            </div>
                            <div class="col-md-1 col-xs-12 form-group active-margin-top">
                                <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
                            </div>
                            <div class="col-md-1 col-xs-12 form-group active-margin-top">
                                <a href="{{ route('order.index') }}">
                                    <button type="button" class="btn btn-primary"><i class="wb-reply"></i></button>
                                </a>
                            </div>
                        </div>
                    </form>
                    <table class="display nowrap table table-hover table-stripedw-full" id="customDataTable" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.price_card")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.services")</th>
                            <th>@lang("$string_file.vehicle_type")</th>
                            <th>@lang('admin.message367')</th>
                            <th>@lang('admin.price_type')</th>
                            <th>@lang('admin.message352')</th>
                            <th>@lang("$string_file.payment_method")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                         <tr>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                         </tr>
                        </tbody>
                    </table>
                    <div class="pagination1 float-right"></div>
                </div>
            </div>
        </div>
    </div>
@endsection