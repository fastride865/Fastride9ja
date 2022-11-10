@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(Auth::user()->demo == 1)
                            <a href="">
                                <button type="button" class="btn btn-icon btn-primary mr-1 float-right" style="margin:10px" data-original-title="@lang("$string_file.export_excel")" data-toggle="tooltip"><i
                                            class="fa fa-download"></i>
                                </button>
                            </a>
                        @else
                            <a href="{{route('excel.driveracceptancereport',$data)}}">
                                <button type="button" class="btn btn-icon btn-primary mr-1 float-right" style="margin:10px" data-original-title="@lang("$string_file.export_excel")" data-toggle="tooltip"><i
                                            class="fa fa-download"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.ride_acceptance_report")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('report.driver.acceptance.search') }}" method="get">
                        <div class="table_search">
                            <div class="row">
                                <div class="col-md-2 col-xs-4 form-group ">
                                    <div class="input-group">
                                        <select class="form-control" name="parameter" id="parameter">
                                            <option value>@lang("$string_file.search_by")</option>
                                            <option value="1">@lang("$string_file.name")</option>
                                            <option value="2">@lang("$string_file.email")</option>
                                            <option value="3">@lang("$string_file.phone")</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2 col-xs-6 form-group ">
                                    <div class="input-group">
                                        <input type="text" name="keyword" value=""
                                               placeholder="@lang("$string_file.enter_text")"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-6 form-group ">
                                    <div class="input-daterange" data-plugin="datepicker">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                          <span class="input-group-text">
                                            <i class="icon wb-calendar" aria-hidden="true"></i>
                                          </span>
                                            </div>
                                            <input type="text" class="form-control customDatePicker2" name="from" value="{{ !empty($_GET['from']) ? $_GET['from'] : date('Y-m-01', strtotime(date('Y-m-d'))) }}"/>
                                        </div>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">to</span>
                                            </div>
                                            <input type="text" class="form-control customDatePicker2" name="to" value="{{ !empty($_GET['to']) ? $_GET['to'] : date('Y-m-d') }}" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-2  col-xs-12 form-group ">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.total_rides")</th>
                            <th>@lang("$string_file.accepted_rides")</th>
                            <th>@lang("$string_file.not_responded")</th>
                            <th>@lang("$string_file.rejected_rides")</th>
                            <th>@lang("$string_file.acceptance_rate")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $drivers->firstItem() @endphp
                        @foreach($drivers as $driver)
                            <tr>
                                <td>{{ $sr  }}</td>
                                @if(Auth::user()->demo == 1)
                                    <td>
                                        {{ "********".substr($driver->fullName, -2) }}
                                        <br>
                                        {{ "********".substr($driver->phoneNumber, -2) }}
                                        <br>
                                        {{ "********".substr($driver->email, -2) }}
                                    </td>
                                @else
                                    <td>
                                        {{ $driver->fullName }}
                                        <br>
                                        {{ $driver->phoneNumber }}
                                        <br>
                                        {{ $driver->email }}
                                    </td>
                                @endif
                                <td>@isset($driver->BookingRequestDriver[0]->total_trip){{ $driver->BookingRequestDriver[0]->total_trip  }}@endisset</td>
                                <td>@isset($driver->BookingRequestDriver[0]->accepted){{ $driver->BookingRequestDriver[0]->accepted  }}@endisset</td>
                                <td>@isset($driver->BookingRequestDriver[0]->no_response) {{ $driver->BookingRequestDriver[0]->no_response  }}@endisset</td>
                                <td>@isset($driver->BookingRequestDriver[0]->reject){{ $driver->BookingRequestDriver[0]->reject  }}@endisset</td>
                                <td>@isset($driver->BookingRequestDriver[0]->accepted){{  round(($driver->BookingRequestDriver[0]->accepted / $driver->BookingRequestDriver[0]->total_trip ) * 100) }}
                                    %
                                    @endisset</td>

                            </tr>
                            @php $sr++ ; @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $drivers, 'data' => $data])
{{--                    <div class="pagination1 float-right">{{ $drivers->appends($data)->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
{{--    <script>--}}
{{--        $(document).ready(function() {--}}
{{--            $('input[name="daterange"]').daterangepicker({--}}
{{--                opens: 'left',--}}
{{--                locale: {--}}
{{--                    format: 'YYYY-MM-DD'--}}
{{--                },--}}
{{--            }, function(start, end, label) {--}}
{{--                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
@endsection