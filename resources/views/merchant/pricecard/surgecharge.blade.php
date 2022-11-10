@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
           @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title"><i class="fa-money" aria-hidden="true"></i>
                        @lang("$string_file.surcharge")
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.service_type")</th>
                            <th>@lang("$string_file.vehicle_type")</th>
                            <th>@lang("$string_file.package")</th>
                            @if($config->sub_charge == 1)
                                <th>@lang("$string_file.surcharge_status")</th>
                                <th>@lang("$string_file.surcharge_type")</th>
                                <th>@lang("$string_file.surcharge_value")</th>
                            @endif
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $pricecards->firstItem() @endphp
                        @foreach($pricecards as $pricecard)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    {{ $pricecard->CountryArea->CountryAreaName }}
                                </td>

                                <td>{{ isset($pricecard->ServiceType->serviceName) ? $pricecard->ServiceType->serviceName : '' }}</td>
                                <td>
                                    {{ $pricecard->VehicleType->VehicleTypeName }}
                                </td>
                                <td>
                                    @if(empty($pricecard->package_id))
                                        ------
                                    @else
                                        @if($pricecard->service_type_id == 4)
                                            {{ $pricecard->OutstationPackage->PackageName }}
                                        @else
                                            {{ $pricecard->Package->PackageName }}
                                        @endif
                                    @endif

                                </td>
                                @if($config->sub_charge == 1)
                                    <td>
                                        @if($pricecard->sub_charge_status == 1)
                                            <span class="badge badge-success">
                                                @lang("$string_file.on")
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                @lang("$string_file.off")
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pricecard->sub_charge_type == 1)
                                            @lang("$string_file.nominal")
                                        @else
                                            @lang("$string_file.multiplier")
                                        @endif
                                    </td>
                                    <td>
                                        {{ $pricecard->CountryArea->Country->isoCode." ".$pricecard->sub_charge_value }}
                                    </td>
                                @endif
                                <td>
                                    <form method="post" action="{{ route('pricecard.surgecharge.update',$pricecard->id) }}">
                                        @csrf
                                        <a onclick="EditDet(this)" data-toggle="modal" data-target="#EditDOc" data-ID = "{{$pricecard->id}}" data-chargeType="{{$pricecard->sub_charge_type}}"
                                           data-value="{{$pricecard->sub_charge_value}}" class="btn btn-sm btn-warning"> <i class="fa fa-edit"></i> </a>
                                        @if($pricecard->sub_charge_status == 1)
                                            <button type="submit" name="status" value="1" class="btn btn-sm btn-success">@lang("$string_file.on")</button>
                                        @else
                                            <button type="submit" name="status" value="2" class="btn btn-sm btn-danger">@lang("$string_file.off")</button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $pricecards, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="EditDOc" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">@lang("$string_file.edit_surcharge")</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('pricecard.surgecharge.value.update')}}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="docId" name="docId">
                        <div class="col-md-12">
                            <div class="form-group" id="edit_selDiv">
                                <label for="emailAddress5">
                                    @lang("$string_file.surcharge_type")
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control"
                                        name="sub_charge_type"
                                        id="sub_charge_type">
                                    <option value="">@lang("$string_file.select")</option>
                                    <option value="1">@lang("$string_file.nominal")</option>
                                    <option value="2">@lang("$string_file.multiplier")</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    @lang("$string_file.surcharge_value")
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control"
                                       id="sub_charge_value"
                                       name="sub_charge_value"
                                       placeholder=""
                                       value="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang("$string_file.close")</button>
                        <button type="submit" name="submit" class="btn btn-primary">@lang("$string_file.save")</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function EditDet(obj) {
            let ID = obj.getAttribute('data-ID');
            let Type = obj.getAttribute('data-chargeType');
            let amount = obj.getAttribute('data-value');
            $("div#edit_selDiv select").val(Type);
            $(".modal-body #sub_charge_value").val(amount);
            $(".modal-body #docId").val(ID);
            $('#EditDOc').modal('show');
        }
    </script>
@endsection
