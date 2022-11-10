@extends('taxicompany.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('taxicompany.driver.index') }}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.all_drivers")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-user-plus" aria-hidden="true"></i>
                        @lang("$string_file.basic_signup_completed")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('taxicompany.driver.basic.search') }}" method="GET">
                        @csrf
                        <div class="table_search row p-3 ">
                            <div class="col-md-2 col-xs-6 active-margin-top"> @lang("$string_file.search_by") :</div>
                            <div class="col-sm-2 col-xs-12 form-group active-margin-top">
                                <div class="input-group">
                                    <select class="form-control form-control" name="parameter" id="parameter" required>
                                        <option value="1">@lang("$string_file.name")</option>
                                        <option value="2">@lang("$string_file.email")</option>
                                        <option value="3">@lang("$string_file.phone")</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2 col-xs-12 form-group active-margin-top">
                                <select class="form-control form-control" name="area_id" id="area_id" >
                                    <option value="">@lang("$string_file.service_area")</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}"
                                                @if(request()->get('area_id') == $area->id) selected @endif> {{ $area->CountryAreaName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                                <input id="keyword" name="keyword" placeholder="@lang("$string_file.enter_text")" class="form-control" type="text">
                            </div>
                            <div class="col-sm-2 col-xs-6 form-group active-margin-top">
                                <button class="btn btn-primary" type="submit"
                                        name="seabt12"><i class="fa fa-search"
                                                          aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.service_area") </th>
                            <th>@lang("$string_file.profile_image")</th>
                            <th>@lang("$string_file.driver_details")</th>
                            <th>@lang("$string_file.registered_date")</th>
                            <th>@lang("$string_file.updated_at")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($drivers as $driver)
                            <tr>
                                <td>{{ $driver->CountryArea->CountryAreaName }}</td>
                                <td class="text-center">
                                    @isset($driver->profile_image)
                                    <img
                                            src="{{ get_image($driver->profile_image,'driver',$driver->merchant_id) }}"
                                            alt="avatar" style="width: 100px;height: 100px;">
                                    @endisset
                                </td>
                                @if(Auth::user()->demo == 1)
                                    <td>
                                                            <span class="long_text">
                                                                {{ "********".substr($driver->last_name, -2) }}<br>
                                                                {{ "********".substr($driver->phoneNumber, -2) }} <br>
                                                                {{ "********".substr($driver->email, -2) }}
                                                            </span>
                                    </td>
                                @else
                                    <td>{{ $driver->first_name." ".$driver->last_name }}<br>
                                        {{ $driver->phoneNumber }}<br>
                                        {{ $driver->email }}
                                    </td>
                                @endif
                                <td>
                                    {!! convertTimeToUSERzone($driver->created_at, $driver->CountryArea->timezone,null,$driver->Merchant) !!}
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($driver->updated_at, $driver->CountryArea->timezone,null,$driver->Merchant) !!}
                                </td>
                                <td>
                                    <a href="{{ route('taxicompany.driver.document.show',$driver->id) }}"
                                       data-original-title="Complete Signup"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                        <i class="fa fa-edit"></i> </a>
                                <!--<button onclick="DeleteEvent({{ $driver->id }})"
                                                                    type="submit"
                                                                    data-original-title="@lang("$string_file.delete")"
                                                                    data-toggle="tooltip"
                                                                    data-placement="top"
                                                                    class="btn menu-icon btn-danger action_btn"><i
                                                                        class="fa fa-trash"></i></button>-->
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1 float-right">{{ $drivers->links() }}</div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function DeleteEvent(id) {
            var token = $('[name="_token"]').val();
            swal({
                title: "@lang("$string_file.are_you_sure")",
                text: "@lang("$string_file.delete_warning")",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        type: "POST",
                        // url: "{{ route('Driver_Delete') }}",
                        url: '{{ URL::route('Driver_Delete') }}',
                        data:
                            {
                                id
                            }
                    }).done(function (data) {
                        console.log((data))
                        swal({
                            title: "DELETED!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "{{ route('merchant.driver.basic') }}";
                    });
                } else {
                    swal("@lang("$string_file.data_is_safe")");
                }
            });
        }
    </script>
@endsection