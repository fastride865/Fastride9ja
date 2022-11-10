@extends('taxicompany.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content container-fluid">
            @include('merchant.shared.errors-and-messages')
            @if(session('accounts'))
                <div class="alert dark alert-icon alert-info" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('accounts') }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                    </div>
                    <h3 class="panel-title"><i class="fa fa-money" aria-hidden="true"></i>
                        @lang("$string_file.driver_account")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('taxicompany.account.search') }}" method="GET">
                        @csrf
                        <div class="table_search row">
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                @lang("$string_file.search_by"):
                            </div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="">
                                    <select class="form-control" name="parameter" id="parameter" required>
                                        <option value="1">@lang("$string_file.name")</option>
                                        <option value="2">@lang("$string_file.email")</option>
                                        <option value="3">@lang("$string_file.phone")</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="input-group">
                                    <input type="text" id="" name="keyword"
                                           placeholder="@lang("$string_file.enter_text")"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>
                            <div class="col-sm-2  col-xs-12 form-group active-margin-top">
                                <button class="btn btn-primary" type="submit"><i
                                            class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.area")</th>
                            <th>@lang("$string_file.unbilled_amount")</th>
                            <th>@lang("$string_file.rides")</th>
                            <th>@lang("$string_file.taxi_company_earning")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $drivers->firstItem() @endphp
                        @foreach($drivers as $driver)
                            <tr>
                                <td>{{ $sr }}</td>
                                @if(Auth::user()->demo == 1)
                                    <td>
                                                                <span class="long_text">
                                                                    {{ "********".substr($driver->last_name, -2) }}<br>
                                                                    {{ "********".substr($driver->email, -2) }}<br>
                                                                    {{ "********".substr($driver->phoneNumber, -2) }}
                                                                 </span>
                                    </td>
                                @else
                                    <td><span class="long_text">
                                                        {{ $driver->first_name." ".$driver->last_name }}
                                                        <br>
                                                        {{ $driver->email }}
                                                        <br>
                                                        {{ $driver->phoneNumber }}
                                                        </span>
                                    </td>
                                @endif
                                <td>{{ $driver->CountryArea->CountryAreaName}}</td>
                                <td>
                                    {{ sprintf("%0.2f", $driver->cash_received) }}
                                </td>
                                <td>
                                    @if($driver->total_trips)
                                        {{ $driver->total_trips }}
                                    @else
                                        @lang("$string_file.no_ride")
                                    @endif
                                </td>
                                <td>
                                    @if($driver->total_earnings)
                                        {{ sprintf("%0.2f", $driver->total_earnings)}}
                                    @else
                                        @lang("$string_file.no_ride")
                                    @endif
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1 float-right">{{ $drivers->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection