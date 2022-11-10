@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <a href="{{route('excel.customersupports')}}">
                            <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-toggle="tooltip">
                                <i class="wb-download" title="@lang("$string_file.export_excel")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.customer_support")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="post" action="{{ route('merchant.customer_support.search') }}">
                        @csrf
                        <div class="table_search row">
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <select class="form-control" name="application"
                                            id="application">
                                        <option value="">--@lang("$string_file.application")--</option>
                                        <option value="2">@lang("$string_file.driver")</option>
                                        <option value="1">@lang("$string_file.user")</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="name"
                                           placeholder="@lang("$string_file.name")"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="date"
                                           placeholder="@lang("$string_file.date")"
                                           class="form-control col-md-12 col-xs-12 datepickersearch"
                                           id="datepickersearch">
                                </div>
                            </div>
                            <div class="col-sm-2  col-xs-12 form-group ">
                                <button class="btn btn-primary" type="submit" name="seabt12"><i
                                            class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <th>@lang("$string_file.sn")</th>
                        <th>@lang("$string_file.application")</th>
                        <th>@lang("$string_file.details")</th>
                        {{--                        <th>@lang("$string_file.name")</th>--}}
                        {{--                        <th>@lang("$string_file.email")</th>--}}
                        {{--                        <th>@lang("$string_file.phone")</th>--}}
                        <th>@lang("$string_file.query")</th>
                        <th>@lang("$string_file.created_at")</th>
                        </thead>
                        <tbody>
                        @php $sr = $customer_supports->firstItem() @endphp
                        @foreach($customer_supports as $customer_support)
                            <tr>
                                <td>
                                    {{ $sr }}
                                </td>
                                <td>
                                    @if($customer_support->application == 1)
                                        @lang("$string_file.user")
                                    @else
                                        @lang("$string_file.driver")
                                    @endif
                                </td>

                                @if(Auth::user()->demo == 1)
                                    <td>
                                        <span class="long_text">
                                            {{ "********".substr($customer_support->name, -2) }}
                                            <br>
                                            {{ "********".substr($customer_support->email, -2) }}
                                            <br>
                                            {{ "********".substr($customer_support->phone, -2) }}
                                        </span>
                                    </td>
                                @else
                                    <td>
                                        <span class="long_text">
                                            {{ $customer_support->name }}
                                            <br>
                                            {{ $customer_support->email }}
                                            <br>
                                            {{ $customer_support->phone }}
                                        </span>
                                    </td>
                                @endif
                                <td>
                                    <span class="long_text">{{ $customer_support->query }}</span>
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($customer_support->created_at, null, null, $customer_support->Merchant) !!}
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1 float-right">{{ $customer_supports->links() }}</div>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection






