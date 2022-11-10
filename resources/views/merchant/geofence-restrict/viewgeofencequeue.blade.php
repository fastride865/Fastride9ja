@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title">
                        <i class="wb-flag" aria-hidden="true"></i>
                        {{$area->CountryAreaName}}'s @lang("$string_file.restrict_queue_management")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('geofence.restrict.viewgeofencequeue.search',['id'=>$area->id]) }}" method="post">
                        @csrf
                        <div class="table_search row">
                            <div class="col-md-1 col-xs-12 form-group active-margin-top">
                                @lang("$string_file.search_by"):
                            </div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <div class="input-group">
                                    <input type="text" id="" name="driver" value="{{ old('driver') }}"
                                           placeholder="@lang("$string_file.driver_name")"
                                           class="form-control col-md-12 col-xs-12">
                                </div>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group ">
                                <div class="input-group">
                                    <input type="text" id="" name="date"
                                           placeholder="@lang("$string_file.date")" readonly
                                           class="form-control col-md-12 col-xs-12 customDatePicker2 bg-this-color"
                                           id="datepickersearch">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="checkbox" id="active_queue" name="active_queue" data-plugin="switchery" data-target="">
                                    <label class="pt-3" for="active_queue">@lang("$string_file.active_queue")</label>
                                </div>
                            </div>
                            <div class="col-sm-1 col-xs-12 form-group active-margin-top">
                                <button class="btn btn-primary" type="submit" name="search"><i
                                            class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.queue_number")</th>
                            <th>@lang("$string_file.driver_name")</th>
                            <th>@lang("$string_file.entry_time")</th>
                            <th>@lang("$string_file.exit_time")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $queue_managements->firstItem() @endphp
                        @foreach($queue_managements as $queue_management)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $queue_management->queue_no }}</td>
                                <td>
                                    <span class="long_text">
                                        {{ $queue_management->Driver->first_name.' '.$queue_management->Driver->last_name }}
                                    </span>
                                </td>
                                <td>{{ $queue_management->entry_time }}</td>
                                <td>{{ $queue_management->exit_time }}</td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1 float-right">{{ $queue_managements->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
