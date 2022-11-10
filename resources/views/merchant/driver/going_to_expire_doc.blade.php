@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('driver.index') }}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.all_driver")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-file" aria-hidden="true"></i>
                        @lang("$string_file.docs_going_expire")<span class="text-danger"> ( {{$currentDate}} to {{$reminder_last_date}} )</span></h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                            <thead>
                            <tr>
                                <th><input type="checkbox" name="checkAll" id="checkAll">
                                    <button type="submit" class="btn btn-warning btn-sm" data-original-title="Send Notification To All"
                                            data-toggle="tooltip" data-placement="top"><i class="wb-bell"></i>
                                    </button>
                                </th>
                                <th> @lang("$string_file.id")</th>
                                <th>@lang("$string_file.service_area") </th>
                                <th>@lang("$string_file.driver_details")</th>
                                <th>@lang("$string_file.personal_document")</th>
                                @if($merchant_type == "BOTH" || $merchant_type == "VEHICLE")
                                <th>@lang("$string_file.vehicle_document")</th>
                                @endif
                                @if($merchant_type == "BOTH" || $merchant_type == "HANDYMAN")
                                <th>@lang("$string_file.handyman_segment_documents")</th>
                                @endif
                                <th>@lang("$string_file.action")</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($drivers as $driver)
                                    <tr>
                                        <td><input type="checkbox" name="driver_id[]"
                                                   value="{{$driver->id}}" id="checkItem">
                                        </td>
                                        <td><a href="{{ route('driver.show',$driver->id) }}"
                                               class="hyperLink">{{ $driver->merchant_driver_id }}</a>
                                        </td>
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
                                            <td>
                                                <span class="long_text">
                                                    {{ $driver->first_name." ".$driver->last_name }}<br>
                                                    {{ $driver->phoneNumber }} <br>
                                                    {{ $driver->email }}
                                                </span>
                                            </td>
                                        @endif
                                        <td class="text-center">
                                            @if(count($driver->DriverDocument) > 0)
                                                <span data-target="#PersonalDocumnetExpire{{$driver->id}}"
                                                      data-toggle="modal"
                                                      id="{{ $driver->id }}">
                                                    <a data-original-title=""
                                                            data-toggle="tooltip"
                                                            id="{{ $driver->id }}"
                                                            data-placement="top"
                                                            class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn p-document-upload"> <i
                                                                class="fa fa-file-o"></i>
                                                        @lang("$string_file.view")
                                                    </a>
                                                </span>
                                                <a data-original-title=""
                                                   data-toggle="tooltip"
                                                   href="{{ route('driver.add',$driver->id) }}" target="_blank"
                                                   data-placement="top"
                                                   class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn"> <i
                                                            class="fa fa-upload"></i>
                                                    @lang("$string_file.upload")
                                                </a>

                                            @else
                                                ----------
                                            @endif
                                        </td>
                                        @if($merchant_type == "BOTH" || $merchant_type == "VEHICLE")
                                        <td class="text-center">
                                            @if(count($driver->DriverVehicles) > 0)
                                                <span data-target="#VehicleDocumnetExpire{{$driver->id}}"
                                                      data-toggle="modal"
                                                      id="{{ $driver->id }}">
                                                    <a data-original-title="@lang("$string_file.vehicle_document")"
                                                            data-toggle="tooltip"
                                                            id="{{ $driver->id }}"

                                                            data-placement="top"
                                                            class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn"> <i
                                                                class="fa fa-file-o"></i>
                                                        @lang("$string_file.view")
                                                    </a>
                                                </span>
                                                @foreach($driver->DriverVehicles as $vehicle)
                                                    <a data-original-title=""
                                                       data-toggle="tooltip"
                                                       href="{{ route('merchant.driver.vehicle.create',[$driver->id,$vehicle->id]) }}" target="_blank"
                                                       data-placement="top"
                                                       class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn v-document-upload"> <i
                                                                class="fa fa-upload"></i>
                                                        @lang("$string_file.vehicle_number"):
                                                        {{$vehicle->vehicle_number}}
                                                    </a>
                                                    <br>
                                                @endforeach
                                            @else
                                                ----------
                                            @endif
                                        </td>
                                        @endif
                                        @if($merchant_type == "BOTH" || $merchant_type == "HANDYMAN")
                                        <td class="text-center">
                                            @if(count($driver->DriverSegmentDocument) > 0)
                                                <span data-target="#HandymanDocumnetExpire{{$driver->id}}"
                                                      data-toggle="modal"
                                                      id="{{ $driver->id }}">
                                                    <a
                                                            data-original-title="@lang("$string_file.handyman_segment_documents")"
                                                            data-toggle="tooltip"
                                                            id="{{ $driver->id }}"
                                                            data-placement="top"
                                                            class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn"> <i
                                                                class="fa fa-file-o"></i>
                                                         @lang("$string_file.view")
                                                    </a>
                                                </span>
                                                <a data-original-title=""
                                                   data-toggle="tooltip"
                                                   href="{{ route('merchant.driver.handyman.segment',$driver->id) }}" target="_blank"
                                                   data-placement="top"
                                                   class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn s-document-upload"> <i
                                                            class="fa fa-upload"></i>
                                                        @lang("$string_file.upload")
                                                </a>
                                            @else
                                                ----------
                                            @endif
                                        </td>
                                        @endif
{{--                                        <td>{{ $driver->created_at }}</td>--}}
                                        <td>
                                            <a href="{{route('goingToExpireDocuments.sendNotification',$driver->id)}}"
                                               class="btn btn-warning" data-toggle="tooltip"
                                               data-original-title="@lang("$string_file.send_notification")"><i
                                                        class="fa fa-bell"></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @include('merchant.shared.table-footer', ['table_data' => $drivers, 'data' => []])
{{--                    </form>--}}
                </div>
            </div>
        </div>
    </div>
    @foreach($drivers as $driver)
        <div class="modal fade text-left" id="PersonalDocumnetExpire{{$driver->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <label class="modal-title text-text-bold-600" id="myModalLabel33">@lang("$string_file.name") : <b>{{ $driver->first_name." ".$driver->last_name }}</b> |  @lang("$string_file.title") : @lang("$string_file.personal_docs_going_expire")</label>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    @csrf
                    <div class="modal-body">
                        <div class="container col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="font-weight-bold">@lang("$string_file.document_name")</label>
                                </div>

                                <div class="col-md-3">
                                    <label class="font-weight-bold">@lang("$string_file.expire_date")</label>
                                </div>

                                <div class="col-md-3">
                                    <label class="font-weight-bold">@lang("$string_file.document")</label>
                                </div>
{{--                                <div class="col-md-3">--}}
{{--                                    <label class="font-weight-bold">@lang('$string_file.upload')</label>--}}
{{--                                </div>--}}
{{--                                <div class="col-md-2">--}}
{{--                                    <label class="font-weight-bold">@lang("$string_file.expire_date")  </label>--}}
{{--                                </div>--}}
                            </div>
{{--                            <form method="post" action="{{route('merchant.driver.uploadDriverExpireDocs')}}"--}}
{{--                                  enctype="multipart/form-data">--}}
{{--                                @csrf--}}
                                @foreach($driver->DriverDocument as $driverDocs)
                                    <div class="row">
                                        <div class="col-md-3">{{$driverDocs->Document->DocumentName}}</div>
                                        <div class="col-md-3">{{$driverDocs->expire_date}}</div>
                                        <div class="col-md-3"><a target="_blank"
                                                                 href="{{get_image($driverDocs->document_file, 'driver_document', $driver->merchant_id)}}"><img
                                                        src="{{get_image($driverDocs->document_file, 'driver_document', $driver->merchant_id)}}"
                                                        height="50px" width="50px"></a></div>
{{--                                        <input type="hidden" name="driver_id"--}}
{{--                                               value="{{$driverDocs->driver_id}}">--}}
{{--                                        <input type="hidden" name="doc_type" value="1">--}}
{{--                                        <div class="col-md-3"><input class="form-control" type="file"--}}
{{--                                                                     name="uploadDocs[{{$driverDocs->document_id}}]">--}}
{{--                                        </div>--}}
{{--                                        <div class="col-md-2">--}}
{{--                                            <input class="form-control docs_datepicker" type="text"--}}
{{--                                                   name="expireDate[{{$driverDocs->document_id}}]">--}}
{{--                                        </div>--}}
                                    </div><br>
                                @endforeach
{{--                                <div class="d-flex w-100 justify-content-end">--}}
{{--                                    <button type="submit" class="btn btn-sm btn-success">@lang("$string_file.upload") <i--}}
{{--                                                class="fa fa-upload"></i></button>--}}
{{--                                </div><br>--}}
{{--                            </form>--}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="@lang("$string_file.close")">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade text-left" id="VehicleDocumnetExpire{{$driver->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <label class="modal-title text-text-bold-600" id="myModalLabel33">@lang("$string_file.name") : <b>{{ $driver->first_name." ".$driver->last_name }}</b> |  @lang("$string_file.title") : @lang("$string_file.vehicle_docs_going_expire")</label>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    @csrf
                    <div class="modal-body">
                        <div class="container col-md-12">
                            @foreach($driver->DriverVehicles as $driverVehicle)
                                <div class="row">
                                    <div class="col-md-12 text-center text-danger"><b>@lang("$string_file.vehicle_number")
                                            : {{$driverVehicle->vehicle_number}}</b></div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="font-weight-bold">@lang("$string_file.document")@lang("$string_file.name")</label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-weight-bold">@lang("$string_file.expire_date")</label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-weight-bold">@lang("$string_file.document")</label>
                                    </div>
{{--                                    <div class="col-md-3">--}}
{{--                                        <label class="font-weight-bold">@lang('$string_file.upload')</label>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-2">--}}
{{--                                        <label class="font-weight-bold">@lang("$string_file.expire_date")  </label>--}}
{{--                                    </div>--}}
                                </div>
{{--                                <form method="post" action="{{route('merchant.driver.uploadVehicleExpireDocs')}}"--}}
{{--                                      enctype="multipart/form-data">--}}
{{--                                    @csrf--}}
                                    @foreach($driverVehicle->DriverVehicleDocument as $vehicleDocs)
                                        <div class="row">
                                            <div class="col-md-3">{{$vehicleDocs->Document->DocumentName}}</div>
                                            <div class="col-md-3">{{$vehicleDocs->expire_date}}</div>
                                            <div class="col-md-3"><a target="_blank"
                                                                     href="{{get_image($vehicleDocs->document, 'vehicle_document', $driver->merchant_id)}}"><img
                                                            src="{{get_image($vehicleDocs->document, 'vehicle_document', $driver->merchant_id)}}"
                                                            height="50px" width="50px"></a></div>
{{--                                            <input type="hidden" name="driver_vehicle_id"--}}
{{--                                                   value="{{$vehicleDocs->driver_vehicle_id}}">--}}
{{--                                            <input type="hidden" name="doc_type" value="1">--}}
{{--                                            <div class="col-md-3"><input class="form-control" type="file"--}}
{{--                                                                         name="uploadDocs[{{$vehicleDocs->document_id}}]">--}}
{{--                                            </div>--}}
{{--                                            <div class="col-md-2">--}}
{{--                                                <input class="form-control docs_datepicker" type="text"--}}
{{--                                                       name="expireDate[{{$vehicleDocs->document_id}}]">--}}
{{--                                            </div>--}}
                                        </div>
                                        <br>
                                    @endforeach
                                    <hr>
{{--                                    <div class="d-flex w-100 justify-content-end">--}}
{{--                                        <button type="submit" class="btn btn-sm btn-success">@lang("$string_file.upload") <i--}}
{{--                                                    class="fa fa-upload"></i></button>--}}
{{--                                    </div>--}}
{{--                                    <hr>--}}
{{--                                </form>--}}
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="@lang("$string_file.close")">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade text-left" id="HandymanDocumnetExpire{{$driver->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <label class="modal-title text-text-bold-600" id="myModalLabel33">@lang("$string_file.name") : <b>{{ $driver->first_name." ".$driver->last_name }}</b> |  @lang("$string_file.title") :  @lang("$string_file.handyman_segment_docs_going_expire")</label>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
{{--                    <form method="post" action="{{route('merchant.driver.handyman-document-upload')}}"--}}
{{--                          enctype="multipart/form-data">--}}
{{--                        @csrf--}}
                    <div class="modal-body">
                        <div class="container col-md-12">
                            @foreach($driver->DriverSegmentDocument as $seg_doc)
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="font-weight-bold">@lang("$string_file.segment")</label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-weight-bold">@lang("$string_file.name")</label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-weight-bold">@lang("$string_file.expired_at")</label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-weight-bold">@lang("$string_file.document")</label>
                                    </div>
{{--                                    <div class="col-md-2">--}}
{{--                                        <label class="font-weight-bold">@lang('$string_file.upload')</label>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-2">--}}
{{--                                        <label class="font-weight-bold">@lang("$string_file.expire_date")  </label>--}}
{{--                                    </div>--}}
                                </div>
                                        <div class="row">
                                            <div class="col-md-3">{{ $seg_doc->Segment->Name($merchant_id) }}</div>
                                            <div class="col-md-3">{{$seg_doc->Document->DocumentName}}</div>
                                            <div class="col-md-3">{{$seg_doc->expire_date}}</div>
                                            <div class="col-md-3"><a target="_blank"
                                                                     href="{{get_image($seg_doc->document, 'segment_document', $driver->merchant_id)}}"><img
                                                            src="{{get_image($seg_doc->document, 'vehicle_document', $driver->merchant_id)}}"
                                                            height="50px" width="50px"></a></div>
{{--                                            <input type="hidden" name="driver_vehicle_id"--}}
{{--                                                   value="{{$seg_doc->segment_id}}">--}}
{{--                                            <input type="hidden" name="doc_type" value="1">--}}
{{--                                            <div class="col-md-2"><input class="form-control" type="file"--}}
{{--                                                                         name="uploadDocs[{{$seg_doc->document_id}}]">--}}
{{--                                            </div>--}}
{{--                                            <div class="col-md-2">--}}
{{--                                                <input class="form-control docs_datepicker" type="text"--}}
{{--                                                       name="expireDate[{{$seg_doc->document_id}}]">--}}
{{--                                            </div>--}}
                                        </div>
                                        <br>
                                    <hr>
{{--                                    <div class="d-flex w-100 justify-content-end">--}}
{{--                                        <button type="submit" class="btn btn-sm btn-success">@lang("$string_file.upload") <i--}}
{{--                                                    class="fa fa-upload"></i></button>--}}
{{--                                    </div>--}}
{{--                                    <hr>--}}
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="@lang("$string_file.close")">
                    </div>
{{--                    </form>--}}
                </div>
            </div>
        </div>
    @endforeach
@endsection
@section('js')
    <script>

        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });

        $(".p-document-upload").click(function () {
          {{Session::flash('personal-document-expire-warning', trans("$string_file.document_expire_warning"))}}
        });
        $(".v-document-upload").click(function () {
          {{Session::flash('vehicle-document-expire-warning', trans("$string_file.document_expire_warning"))}}
        });
        $(".s-document-upload").click(function () {
          {{Session::flash('handyman-document-expire-warning', trans("$string_file.document_expire_warning"))}}
        });
        // $('.toast').toast('show');
    </script>
@endsection