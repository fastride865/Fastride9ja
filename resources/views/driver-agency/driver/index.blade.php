@extends('driver-agency.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="mr--10 ml--10">
                        <div class="row " style="margin-right: 0rem;margin-left: 0rem" >
                            <div class="col-md-3 col-sm-3">
                                <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                                    @lang("$string_file.all_drivers")</h3>
                            </div>
                            <div class="col-sm-9 col-md-9">
                                <a href="{{route('driver-agency.driver.add')}}">
                                    <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                        <i class="wb-plus" title="@lang("$string_file.add_driver")"></i>
                                    </button>
                                </a>
                                <a href="{{route('excel.driver')}}" data-toggle="tooltip">
                                    <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                        <i class="wb-download" title="@lang("$string_file.export_excel")"></i>
                                    </button>
                                </a>
                                <a href="{{ route('driver-agency.driver.basic') }}">
                                    <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                        @lang("$string_file.basic_signup_completed")
                                        <span class="badge badge-pill">{{ $basicDriver }}</span>
                                    </button>
                                </a>
{{--                                <a href="{{ route('driver-agency.driver.tempDocPending.show') }}">--}}
{{--                                    <button type="button" class="btn btn-icon btn-warning float-right" style="margin:10px">--}}
{{--                                        @lang("$string_file.temp_doc_approve")--}}
{{--                                        <span class="badge badge-pill">{{ $tempDocUploaded }}</span>--}}
{{--                                    </button>--}}
{{--                                </a>--}}

                                    <a href="{{ route('driver-agency.driver.rejected') }}">
                                        <button type="button" class="btn btn-icon btn-danger float-right" style="margin:10px">
                                            @lang("$string_file.rejected_drivers")
                                            <span  class="badge badge-pill">{{ $rejecteddrivers }}
                                        </span>
                                        </button>
                                    </a>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="panel-body container-fluid">
                    {!! $search_view !!}
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th> @lang("$string_file.id")</th>
                            <th>@lang("$string_file.service_area") </th>
                            <th>@lang("$string_file.driver_details")</th>
                            @if($config->gender == 1)
                                <th>@lang("$string_file.gender")</th>
                            @endif
{{--                            <th>@lang("$string_file.vehicle_number")</th>--}}
                            <th>@lang("$string_file.last_location_updated")  </th>
                            <th>@lang("$string_file.service_statistics")</th>
                            <th>@lang("$string_file.referral_code")</th>
                            <th>@lang("$string_file.transaction_amount")</th>
                            <th>@lang("$string_file.registered_date")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $drivers->firstItem() @endphp
                        @foreach($drivers as $driver)
                            <tr>
                                <td><a href="{{ route('driver-agency.driver.show',$driver->id) }}"
                                       class="hyperLink">{{ $driver->id }}</a></td>
                                <td>{{ $driver->CountryArea->CountryAreaName }}</td>
                                @if(Auth::user()->demo == 1)
                                    <td>
                                                            <span class="long_text">
                                                                {{ "********".substr($driver->last_name, -2) }}<br>
                                                                {{ "********".substr($driver->phoneNumber, -2) }} <br>
                                                                {{ "********".substr($driver->email, -2) }}
                                                            </span>
                                    </td>
                                @else
                                    <td><span class="long_text">
                                                        {{ $driver->first_name." ".$driver->last_name }}<br>
                                                    {{ $driver->phoneNumber }} <br>
                                                    {{ $driver->email }}
                                                    </span>
                                    </td>
                                @endif
                                @if($config->gender == 1)
                                    @switch($driver->driver_gender)
                                        @case(1)
                                        <td>@lang("$string_file.male")</td>
                                        @break
                                        @case(2)
                                        <td>@lang("$string_file.female")</td>
                                        @break
                                        @default
                                        <td>------</td>
                                    @endswitch
                                @endif
                                <td>
                                    @if(!empty($driver->current_latitude))
                                        <a class="map_address hyperLink " target="_blank"
                                           href="https://www.google.com/maps/place/{{ $driver->current_latitude }},{{$driver->current_longitude}}">
                                            {{ $driver->last_location_update_time }}
                                        </a>
                                    @else
                                        ----------
                                    @endif

                                </td>
                                <td>
                                    @if($driver->total_earnings)
                                        {{ $driver->CountryArea->Country->isoCode." ".number_format($driver->total_earnings,2)}}
                                    @else
                                        @lang("$string_file.no_ride")
                                    @endif
                                    <br>
                                        @if ($driver->rating == "0.0")
                                            @lang("$string_file.not_rated_yet")
                                        @else
                                            @while($driver->rating>0)
                                                @if($driver->rating >0.5)
                                                    <img src="{{ asset("star.png") }}"
                                                         alt='Whole Star'>
                                                @else
                                                    <img src="{{ asset('halfstar.png') }}"
                                                         alt='Half Star'>
                                                @endif
                                                @php $driver->rating--; @endphp
                                            @endwhile
                                        @endif
                                </td>
                                <td>
                                    -----
                                </td>
                                @if($config->driver_wallet_status == 1)
                                    <td>
                                        @if($driver->wallet_money)
                                            <a href="{{ route('merchant.driver.wallet.show',$driver->id) }}">{{ $driver->wallet_money }}</a>
                                        @else
                                            ------
                                        @endif

                                    </td>
                                @endif
                                <td>
                                    {!! convertTimeToUSERzone($driver->created_at, $driver->CountryArea->timezone,null,$driver->Merchant) !!}
                                </td>
                                <td>
                                    @if($driver->driver_admin_status == 1)
                                        @if($driver->login_logout == 1)
                                            @if($driver->online_offline == 1)
                                                @if($driver->free_busy == 1)
                                                    <span class="badge badge-info">@lang("$string_file.busy")</span>
                                                @else
                                                    <span class="badge badge-success">@lang("$string_file.free")</span>
                                                @endif
                                            @else
                                                <span class="badge badge-info">@lang("$string_file.offline")</span>
                                            @endif
                                        @else
                                            <span class="badge badge-warning">@lang("$string_file.logout")</span>
                                        @endif
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="button-margin" >
                                        <a href="{{ route('driver-agency.driver.add',$driver->id) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i> </a>
                                        <span data-target="#sendNotificationModel"
                                              data-toggle="modal" id="{{ $driver->id }}"><a
                                                    data-original-title="@lang("$string_file.send_notification")"
                                                    data-toggle="tooltip"
                                                    id="{{ $driver->id }}" data-placement="top"
                                                    class="btn  text-white btn-sm btn-warning menu-icon btn_eye action_btn"> <i
                                                        class="fa fa-bell"></i> </a></span>
                                        <a href="{{ route('driver-agency.driver.show',$driver->id) }}"
                                           class="btn btn-sm btn-info menu-icon btn_detail action_btn"><span
                                                    class="fa fa-user"
                                                    title="View Driver Profile"></span></a>

                                        <a href="{{ route('driver-agency.driver-vehicle',$driver->id) }}"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn"><span
                                                    class="fa fa-car"
                                                    title="View Driver Vehicles"></span></a>
                                    </div><div>
                                        @if($driver->driver_admin_status == 1)
                                            <a href="{{ route('driver-agency.driver.active.deactive',['id'=>$driver->id,'status'=>2]) }}"
                                               data-original-title="@lang("$string_file.inactive")"
                                               data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn"> <i
                                                        class="fa fa-eye-slash"></i> </a>
                                        @else
                                            <a href="{{ route('driver-agency.driver.active.deactive',['id'=>$driver->id,'status'=>1]) }}"
                                               data-original-title="@lang("$string_file.active")"
                                               data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                        class="fa fa-eye"></i>
                                            </a>
                                        @endif
                                        @if($driver->login_logout == 1)
                                            <a href="{{ route('merchant.driver.logout',$driver->id) }}"
                                               data-original-title="Logout"
                                               data-toggle="tooltip" data-placement="top"
                                               class="btn btn-sm btn-secondary menu-icon btn_delete action_btn"> <i
                                                        class="fa fa-sign-out-alt"></i>
                                            </a>
                                        @endif
                                        <button onclick="DeleteEvent({{ $driver->id }})" type="submit"
                                                data-original-title="@lang("$string_file.delete")"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                class="btn btn-sm btn-danger menu-icon btn_delete action_btn"><i
                                                    class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @php $sr++; @endphp
                        @endforeach
                        </tbody>
                    </table>
                    <div class="pagination1 float-right">{{ $drivers->links() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="sendNotificationModel" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33"><b>@lang("$string_file.send_notification_to_driver")</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('driver-agency.sendsingle-driver') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <label>@lang("$string_file.title"): </label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="title"
                                   name="title"
                                   placeholder="@lang("$string_file.title")" required>
                        </div>
                        <label>@lang("$string_file.message"): </label>
                        <div class="form-group">
                           <textarea class="form-control" id="message" name="message"
                                     rows="3"
                                     placeholder="@lang("$string_file.message")"></textarea>
                        </div>
                        <label>@lang("$string_file.image"): </label>
                        <div class="form-group">
                            <input type="file" class="form-control" id="image"
                                   name="image"
                                   placeholder="@lang("$string_file.image")">
                            <input type="hidden" name="persion_id" id="persion_id">
                        </div>
                        <label>@lang("$string_file.show_in_promotion"): </label>
                        <div class="form-group">
                            <input type="checkbox" value="1" name="expery_check"
                                   id="expery_check_two">
                        </div>
                        <label>@lang("$string_file.expire_date"):</label>
                        <div class="form-group">
                            <input type="text" class="form-control datepicker"
                                   id="datepicker-backend" name="date"
                                   placeholder="" disabled readonly>
                        </div>
                        <label>@lang("$string_file.url"): </label>
                        <div class="form-group">
                            <input type="url" class="form-control" id="url"
                                   name="url"
                                   placeholder="@lang("$string_file.url")(@lang("$string_file.optional"))">
                            <label class="danger">@lang("$string_file.example") :  https://www.google.com/</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-outline-primary btn-lg" value="@lang("$string_file.send")">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="addMoneyModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33"><b>@lang("$string_file.add_money_in_driver_wallet")</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('merchant.AddMoney') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <label>@lang("$string_file.payment_method"): </label>
                        <div class="form-group">
                            <select class="form-control" name="payment_method" id="payment_method" required>
                                <option value="1">@lang("$string_file.cash")</option>
                                <option value="2">@lang("$string_file.non_cash")</option>
                            </select>
                        </div>
                        <label>@lang("$string_file.receipt_number"): </label>
                        <div class="form-group">
                            <input type="text" name="receipt_number" id="receipt_number" placeholder="@lang("$string_file.receipt_number")"
                                   class="form-control" required>
                        </div>
                        <label>@lang("$string_file.amount"): </label>
                        <div class="form-group">
                            <input type="number" name="amount" id="amount" placeholder="@lang("$string_file.amount")"
                                   class="form-control" required min="1">
                            <input type="hidden" name="add_money_driver_id" id="add_money_driver_id">
                        </div>
                        <label>@lang("$string_file.description"): </label>
                        <div class="form-group">
                            <textarea class="form-control" id="title1" rows="3" name="description"
                                      placeholder="@lang("$string_file.description")"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-outline-primary btn-lg" value="@lang("$string_file.add")">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function show() {
            if (document.getElementById("expery_check_two").checked = true) {
                document.getElementById('datepicker-backend').disabled = false;
            }
        }

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
                        data:{
                            id: id,
                        },
                        url: "{{route('driver-agency.drivers.delete')}}",
                    }).done(function (data) {
                        swal({
                            title: "DELETED!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "{{ route('driver-agency.driver.index') }}";
                    });
                } else {
                    swal("@lang("$string_file.data_is_safe")");
                }
            });
        }
    </script>
@endsection