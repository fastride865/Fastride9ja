@extends('taxicompany.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('taxicompany.driver.allvehicles') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.all_vehicles") "></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-taxi" aria-hidden="true"></i>
                        @lang("$string_file.vehicle_details_of") {{ $vehicle->vehicle_number }}
                        @if(!$result)
                            <span style="color:red; font-size: 14px;">@lang("$string_file.mandatory_document_not_uploaded")</span>
                        @endif
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <div class="card-body">
                        <h5>@lang("$string_file.vehicle")  @lang("$string_file.details")</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <span class="">@lang("$string_file.vehicle_type") </span> : {{$vehicle->VehicleType->VehicleTypeName}}
                            </div>
                            <div class="col-md-4">
                                <span class="">@lang("$string_file.vehicle_model")  </span> : {{$vehicle->VehicleModel->VehicleModelName}}
                            </div>
                            <div class="col-md-4">
                                <span class="">@lang("$string_file.vehicle_make")  </span> : {{$vehicle->VehicleMake->VehicleMakeName}}
                            </div>
                            <div class="col-md-4">
                                <span class="">@lang("$string_file.vehicle_number") </span> : {{$vehicle->vehicle_number}}
                            </div>
                            @if($vehicle_model_expire == 1)
                            <div class="col-md-4">
                                @lang("$string_file.vehicle_registered_date")   : {!! convertTimeToUSERzone($vehicle->vehicle_register_date, $vehicle->Driver->CountryArea->timezone,null,$vehicle->Driver->Merchant, 2) !!}
                            </div>
                            <div class="col-md-4">
                                @lang("$string_file.vehicle_expire_date")   : {!! convertTimeToUSERzone($vehicle->vehicle_expire_date, $vehicle->Driver->CountryArea->timezone,null,$vehicle->Driver->Merchant, 2) !!}
                            </div>
                            @endif
                            @if(!empty($baby_seat_enable))
                            <div class="col-md-4">
                                @lang("$string_file.baby_seat_enable")   : {{$vehicle->baby_seat == 1 ? trans("$string_file.yes") : trans("$string_file.no")}}
                            </div>
                            @endif
                            @if(!empty($wheel_chair_enable))
                                <div class="col-md-4">
                                    @lang("$string_file.wheel_chair_enable")   : {{$vehicle->wheel_chair == 1 ? trans("$string_file.yes") : trans("$string_file.no")}}
                                </div>
                            @endif
                            @if(!empty($vehicle_ac_enable))
                                <div class="col-md-4">
                                    @lang("$string_file.ac_enable")   : {{$vehicle->ac_nonac == 1 ? trans("$string_file.yes") : trans("$string_file.no")}}
                                </div>
                            @endif
                        </div>

                        <strong>@lang("$string_file.services") </strong>: {{ implode(',',array_pluck($vehicle->ServiceTypes,'serviceName')) }}
{{--                        <hr>--}}
                        <div class="row">
                            <div class="col-md-5">
                                <h5>@lang("$string_file.vehicle_image") </h5>
                               @php $vehicle_image = get_image($vehicle->vehicle_image,'vehicle_document',$merchant_id); @endphp
                                <div class="" style="width: 6.5rem;">
                                    <div class=" bg-light">
                                        <a href="{{ $vehicle_image }}" target="_blank">
                                            <img src="{{ $vehicle_image }}" class="rounded" alt="@lang("$string_file.vehicle_image") " width="100" height="100">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <h5>@lang("$string_file.vehicle")  @lang("$string_file.number_plate") </h5>
                                @php $number_plate = get_image($vehicle->vehicle_number_plate_image,'vehicle_document',$merchant_id); @endphp
                                <div class="" style="width: 6.5rem;">
                                    <div class=" bg-light">
                                        <a href="{{ $number_plate }}" target="_blank">
                                            <img src="{{ $number_plate }}" class="rounded" alt="@lang("$string_file.vehicle_image") " width="100" height="100">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable">
                                <thead>
                                <tr>
                                    <th>@lang("$string_file.sn")</th>
                                    <th>@lang("$string_file.document_name")</th>
                                    <th>@lang("$string_file.document") </th>
                                    <th>@lang("$string_file.status")</th>
                                    <th>@lang("$string_file.expire_date")  </th>
                                    <th>@lang("$string_file.uploaded_time") </th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $sn= 1; @endphp
                                @foreach($vehicle->DriverVehicleDocument as $document)
                                    <tr>
                                        <td>{{$sn}}</td>
                                        <td> {{ $document->Document->documentname }}</td>
                                        <td>
                                            <a href="{{ get_image($document->document,'vehicle_document',$merchant_id) }}"
                                               target="_blank"><img
                                                        src="{{ get_image($document->document,'vehicle_document',$merchant_id) }}"
                                                        style="width:60px;height:60px;border-radius: 10px"></a>
                                        </td>
                                        <td>
                                            @switch($document->document_verification_status)
                                                @case(1)
                                                @lang("$string_file.pending_for_verification")
                                                @break
                                                @case(2)
                                                @lang("$string_file.verified")
                                                @break
                                                @case(3)
                                                @lang("$string_file.rejected")
                                                @break
                                            @endswitch
                                        </td>
                                        <td>
                                            {!! convertTimeToUSERzone($document->expire_date, $vehicle->Driver->CountryArea->timezone,null,$vehicle->Driver->Merchant, 2) !!}
                                        </td>
                                        <td>
                                            {!! convertTimeToUSERzone($document->created_at, $vehicle->Driver->CountryArea->timezone,null,$vehicle->Driver->Merchant) !!}
                                        </td>
                                        @php $sn = $sn+1; @endphp
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="text-right m-3">
                        @if($result && $vehicle->vehicle_verification_status != 2)
                            <a href="{{ route('merchant.driver-vehicle-verify',[$vehicle->id,2]) }}">
                                <button class="btn btn-md btn-success" style="width: 80px">@lang("$string_file.approve") </button>
                            </a>
                        @endif
                       @if($vehicle->vehicle_verification_status != 2)
                        <a href="#">
                            <button class="btn btn-md btn-danger" style="width: 80px"
                                    data-toggle="modal"
                                    data-target="#exampleModalCenter">@lang("$string_file.reject")
                            </button>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-group" action="{{ route('merchant.driver-vehicle-reject') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">@lang("$string_file.reject_vehicle") </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>@lang("$string_file.vehicle_document")</h5>
                            </div>
                            <input type="hidden" value="{{ $vehicle->driver_id }}" name="driver_id">
                            <input type="hidden" value="{{ $vehicle->id }}"
                                   name="driver_vehicle_id">
                            @foreach($vehicle->DriverVehicleDocument as $document)
                                <div class="col-md-6">
                                    <input type="checkbox" value="{{ $document->id }}"
                                           name="vehicle_documents[]"> {{ $document->Document->documentname }}
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {!! Form::hidden('request_from','vehicle_details') !!}
                                <textarea class="form-control" placeholder="@lang("$string_file.comments")" name="comment" required></textarea>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">@lang("$string_file.close")</button>
                        <button type="submit" class="btn btn-primary">@lang("$string_file.reject") </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection



