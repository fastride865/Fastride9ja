@extends('driver-agency.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
           @include('driver-agency.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{ route('driver-agency.driver.index') }}" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="@lang("$string_file.all_drivers")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-user" aria-hidden="true"></i>
                        @lang("$string_file.driver_profile")
                        @if($driver->signupStep < 8)
                            <span style="color:red; font-size: 16px;">@lang("$string_file.mandatory_document_not_uploaded")</span>
                        @endif
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <div id="user-profile">
                        <div class="col-md-12">
                            <h5>@lang("$string_file.personal_details")</h5>
                            <hr>
                            <div class="row">
                                <!-- Column -->
                                <div class="col-md-4 col-xs-12">
                                    <div class="card my-2 shadow  bg-white h-240">
                                        <div class="justify-content-center p-3">
                                            <div class="col-md-12 col-xs-12"
                                                 style="text-align:center;justify-content:center">
                                                <div class="mt-15 mb-15 h-100">
                                                    <img height="100" width="100" class="rounded-circle"
                                                         src="@if ($driver->profile_image) {{ get_image($driver->profile_image,'driver',$driver->merchant_id) }}@endif">
                                                </div>
                                            </div>
                                            <div class="overlay-box">
                                                <div class="user-content " style="text-align:center">
                                                    @if(Auth::user()->demo == 1)
                                                        <h5 class="user-name mb-3">@lang("$string_file.full_name")
                                                            : {{ "********".substr($driver->first_name." ".$driver->last_name, -2) }}</h5>
                                                        <h6 class="user-job mb-3"> @lang("$string_file.email")
                                                            : {{ "********".substr($driver->email, -2) }}</h6>
                                                        <h6 class="user-loaction mb-5"> @lang("$string_file.phone")
                                                            : {{ "********".substr($driver->phoneNumber, -2) }}</h6>
                                                    @else
                                                        <h5 class="user-name mt-5 mb-3">@lang("$string_file.full_name")
                                                            : {{ $driver->first_name." ".$driver->last_name }}</h5>
                                                        <h6 class="user-job mb-3"> @lang("$string_file.email")
                                                            : {{ $driver->email }}</h6>
                                                        <h6 class="user-location mb-5"> @lang("$string_file.phone")
                                                            : {{ $driver->phoneNumber }}</h6>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div class="col-md-8 col-xs-12 mt-20">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4 col-xs-12 py-2 mb-3">
                                            <div class="border-left-success">
                                                <div class="">
                                                    <div class="row no-gutters align-items-center">
                                                        <div class="col mr-2">
                                                            <div class="h6  text-uppercase mb-1">@lang("$string_file.service_area") </div>
                                                            <div class="h6 mb-0 font-weight-400"
                                                                 style="color:#7c8c9a">@if($driver->CountryArea->LanguageSingle) {{ $driver->CountryArea->LanguageSingle->AreaName }} @else  {{ $driver->CountryArea->LanguageAny->AreaName }} @endif</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- display driver wallet number--}}
                                        @if($config->driver_wallet_status == 1)
                                            <div class="col-md-4 col-sm-4 col-xs-12 py-2 mb-3 ">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1">@lang("$string_file.wallet_money")</div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a"> {{ $driver->wallet_money }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if($config->bank_details_enable == 1)
                                            <div class="col-md-4 col-sm-4 col-xs-12 py-2 mb-3">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6  text-uppercase mb-1">@lang("$string_file.account_holder_name")</div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a">{{$driver->account_holder_name}}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                    <div class="row">
                                        @if($config->driver_limit == 1)
                                            <div class="col-md-4 col-sm-4 col-xs-12 py-2 mb-3">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6  text-uppercase mb-1">@lang("$string_file.radius_limit")</div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a">
                                                                    @if(isset($driver_config->radius)){{$driver_config->radius}}
                                                                    <a target="_blank"
                                                                       href="https://www.google.com/maps/place/{{ $driver_config->latitude }},{{  $driver_config->longitude }}"
                                                                       class="ml-2" title="View Map">
                                                                        <i class="fa fa-map" aria-hidden="true"></i>
                                                                    </a>
                                                                    @else -- @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if(!empty(Auth::user('merchant')->ApplicationConfiguration) && Auth::user('merchant')->ApplicationConfiguration->gender == 1)
                                            <div class="col-md-4 col-sm-4 col-xs-12 py-2 mb-3">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1">@lang("$string_file.gender")</div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a">
                                                                    @if($driver->driver_gender == 1) @lang("$string_file.male") @else @lang("$string_file.female")  @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if($config->bank_details_enable == 1)
                                            <div class="col-md-4 col-sm-4 col-xs-12 py-2 mb-3">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1">@lang("$string_file.bank_name")</div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a">{{$driver->bank_name}}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                    <div class="row">
                                        @if($driver->account_type_id)
                                            <div class="col-md-4 col-sm-4 mb-3 col-xs-12 py-2">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1">@lang("$string_file.account_type")</div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a">{{ $driver->AccountType->name }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if($driver->online_code)
                                            <div class="col-md-4 col-sm-4 mb-3 col-xs-12 py-2">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1">@lang("$string_file.transaction_code")</div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a">{{ $driver->online_code }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if(!empty(Auth::user('merchant')->ApplicationConfiguration) && Auth::user('merchant')->ApplicationConfiguration->smoker == 1)
                                            <div class="col-md-4 col-sm-4 mb-3 col-xs-12 py-2">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1"> @lang("$string_file.smoke")</div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a">
                                                                    @if($driver->DriverRideConfig) @if($driver->DriverRideConfig->smoker_type == 1)  @lang("$string_file.smoker") @else  @lang("$string_file.non_smoker") @endif @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        @if($config->bank_details_enable == 1)
                                            <div class="col-md-4 col-sm-4 mb-3 col-xs-12 py-2">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1">@lang("$string_file.account_number")</div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a">{{$driver->account_number}}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if($driver->driver_address)
                                            <div class="col-md-4 col-sm-4 mb-3 col-xs-12 py-2">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1">@lang("$string_file.address")</div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a">{{$driver->driver_address}}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
{{--                                        @if($config->driver_address)--}}
                                            <div class="col-md-4 col-sm-6 mb-3 col-xs-12 py-2">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1">@lang("$string_file.address")</div>
                                                                @php $additionalData = json_decode($driver->driver_additional_data, true); @endphp
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a">
                                                                    @if(!empty($additionalData))
                                                                        @foreach($additionalData as $key => $value)
                                                                            @if(Auth::user()->demo == 1)
                                                                                {{ ucwords( "********".substr($key, -2)) .' : '. ucwords("********".substr($value, -2)) }}
                                                                                <br>
                                                                            @else
                                                                                {{ ucwords($key) .' : '. ucwords($value) }}
                                                                                <br>
                                                                            @endif
                                                                        @endforeach
                                                                    @else
                                                                        @lang("$string_file.data_not_found")
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
{{--                                        @endif--}}
                                        @if(isset($driver->subscription_wise_commission) && $driver->subscription_wise_commission != NULL)
                                            <div class="col-md-4 col-sm-6 mb-3 col-xs-12 py-2">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1">@lang("$string_file.commission_type")</div>
                                                                @if($driver->subscription_wise_commission == 2)
                                                                    <div class="h6 mb-0 font-weight-400"
                                                                         style="color:#7c8c9a">
                                                                        @lang("$string_file.commission_based")
                                                                    </div>
                                                                @elseif($driver->subscription_wise_commission == 1)
                                                                    <div class="h6 mb-0 font-weight-400"
                                                                         style="color:#7c8c9a">
                                                                        @lang("$string_file.subscription_based")<br>
                                                                        @lang("$string_file.current_package")
                                                                        :- {{ isset($package_name) ? $package_name : '---' }}
                                                                    </div>
                                                                @else
                                                                    @lang("$string_file.data_not_found")
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

{{--                                @if($tempDocUploaded == 0)--}}
                                    <div class="col-md-12 mt-20">
                                        <h5>@lang("$string_file.personal_document")</h5>
                                        <hr>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="dataTable">
                                                <thead>
                                                <tr class="text-center">
                                                    <th>@lang("$string_file.sn")</th>
                                                    <th>@lang("$string_file.document_name")</th>
                                                    <th>@lang("$string_file.document") </th>
                                                    <th>@lang("$string_file.expire_date")  </th>
                                                    <th>@lang("$string_file.status")</th>
                                                    <th>@lang("$string_file.uploaded_time") </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php $doc_sn = 1 @endphp
                                                @foreach($driver->DriverDocument as $document)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{$doc_sn}}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $document->Document->DocumentName }}
                                                        </td>
                                                        <td class="text-center">
                                                            @php $p_doc = get_image($document->document_file,'driver_document',$driver->merchant_id); @endphp
                                                            <a target="_blank" href="{{ $p_doc }}">
                                                                <img src="{{ $p_doc }}" alt="avatar" style="width: 100px;height: 100px;">
                                                            </a>
                                                        </td>
                                                        <td class="text-center">{!! convertTimeToUSERzone($document->expire_date, $driver->CountryArea->timezone,null,$driver->Merchant, 2) !!}</td>
                                                        <td class="text-center">
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
                                                        <td class="text-center">
                                                            {!! convertTimeToUSERzone($document->created_at, $driver->CountryArea->timezone,null,$driver->Merchant) !!}
                                                        </td>
                                                    </tr>
                                                    @php $doc_sn = $doc_sn+1; @endphp
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if($tempDocUploaded > 0 && $driver->DriverDocument->where('temp_document_file','!=','')->count()>0)
                                        <h5>@lang("$string_file.temporary_document_verification")  </h5>
                                        <hr>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="dataTable">
                                                <thead>
                                                <tr class="text-center">
                                                    <th>@lang("$string_file.sn")</th>
                                                    <th>@lang("$string_file.document_name")</th>
                                                    <th>@lang("$string_file.document") </th>
                                                    <th>@lang("$string_file.expire_date")  </th>
                                                    <th>@lang("$string_file.status")</th>
                                                    <th>@lang("$string_file.uploaded_time") </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php $doc_sn = 1 @endphp
                                                @foreach($driver->DriverDocument as $document)
                                                    @if(!empty($document->temp_document_file))
                                                    <tr>
                                                        <td class="text-center">
                                                            {{$doc_sn}}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $document->Document->DocumentName }}
                                                        </td>
                                                        <td class="text-center">
                                                            @php $file = get_image($document->temp_document_file,'driver_document',$driver->merchant_id); @endphp
                                                            <a target="_blank"  href="{{ $file }}"><img src="{{ $file }}" alt="avatar" style="width: 100px;height: 100px;"></a>
                                                        </td>
                                                        <td class="text-center">{!! convertTimeToUSERzone($document->temp_expire_date, $driver->CountryArea->timezone,null,$driver->Merchant, 2) !!}</td>
                                                        <td class="text-center">
                                                            @switch($document->temp_doc_verification_status)
                                                                @case(1)
                                                                @lang("$string_file.pending_for_verification") ;
                                                                @break
                                                                @case(2)
                                                                @lang("$string_file.verified")
                                                                @break
                                                                @case(3)
                                                                @lang("$string_file.rejected")
                                                                @break
                                                            @endswitch
                                                        </td>
                                                        <td class="text-center">
                                                            {!! convertTimeToUSERzone($document->created_at, $driver->CountryArea->timezone,null,$driver->Merchant) !!}
                                                        </td>
                                                        @php $doc_sn = $doc_sn+1; @endphp
                                                    </tr>
                                                    @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @endif
                                    </div>
                                    @if($driver->segment_group_id == 2 && !empty($arr_segment))
                                    <div class="col-md-12 mt-20">
                                    <h5>@lang("$string_file.segment_services_with_time_slot")</h5>
                                    <hr>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable">
                                            <thead>
                                            <tr class="text-center">
                                                <th>@lang("$string_file.sn")</th>
                                                <th>@lang("$string_file.segment")</th>
                                                <th>@lang("$string_file.services")</th>
                                                <th>@lang("$string_file.time_slot")</th>
                                                <th>@lang("$string_file.document")</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php $doc_sn = 1 ;  $arr_days = get_days($string_file);@endphp
                                            @foreach($arr_segment as $segment)
                                                    <tr>
                                                        <td class="">
                                                            {{$doc_sn}}
                                                        </td>
                                                        <td class="">
                                                            {{ $segment->Name($driver->merchant_id)}}
                                                        </td>
                                                        <td class="">
                                                            @foreach($segment->ServiceType as $service)
                                                                {{$service->ServiceName($driver->merchant_id)}},
                                                            @endforeach
                                                        </td>
                                                        <td class="">
                                                            @foreach($segment->ServiceTimeSlot as $day_slot)
{{--                                                                {{p($day_slot)}}--}}
                                                                {{isset($arr_days[$day_slot->day]) ? $arr_days[$day_slot->day] : NULL}} => {{implode(',',array_pluck($day_slot->ServiceTimeSlotDetail,'slot_time_text'))}} <br>
                                                            @endforeach
                                                        </td>
                                                        <td class="">
                                                            @if($segment->DriverSegmentDocument->count() > 0)
                                                            <table class="table table-bordered" id="dataTable">
                                                                <thead>
                                                                <tr class="text-center">
                                                                    <th>@lang("$string_file.name")</th>
                                                                    <th>@lang('admin.document')</th>
                                                                    <th>@lang("$string_file.expire_date")  </th>
                                                                    <th>@lang("$string_file.status")</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @php $doc_sn = 1 @endphp
                                                                @foreach($segment->DriverSegmentDocument as $document)
                                                                    <tr>
                                                                        <td class="text-center">
                                                                            {{ $document->Document->DocumentName }}
                                                                        </td>
                                                                        <td class="text-center">
                                                                            @php $p_doc = get_image($document->document_file,'segment_document',$driver->merchant_id); @endphp
                                                                            <a target="_blank" href="{{ $p_doc }}">
                                                                                <img src="{{ $p_doc }}" alt="avatar" style="width: 50px;height: 50px;">
                                                                            </a>
                                                                        </td>
                                                                        <td class="text-center">{!! convertTimeToUSERzone($document->expire_date, $driver->CountryArea->timezone,null,$driver->Merchant, 2) !!}</td>
                                                                        <td class="text-center">
                                                                            @switch($document->document_verification_status)
                                                                                @case(1)
                                                                                @lang("$string_file.pending_for_verification") ;
                                                                                @break
                                                                                @case(2)
                                                                                @lang("$string_file.verified")
                                                                                @break
                                                                                @case(3)
                                                                                @lang("$string_file.rejected")
                                                                                @break
                                                                            @endswitch
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                            @endif
                                                        </td>
                                                        @php $doc_sn = $doc_sn+1; @endphp
                                                    </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    </div>
                                    @else
                                    @if(!empty($vehicle_details))
                                     <div class="col-md-12 mt-20 mb-10">
                                        <h5>@lang("$string_file.vehicle_details")</h5>
                                        <hr>
                                        <div class="row mt-20">
                                            <div class="col-lg-8 mb-30">
                                                <div class="">
                                                    <span class="">@lang("$string_file.vehicle_type") </span> : {{$vehicle_details->VehicleType->VehicleTypeName}}  |
                                                    <span class="">@lang("$string_file.vehicle_model")  </span> : {{$vehicle_details->VehicleModel->VehicleModelName}} |
                                                    <span class="">@lang("$string_file.vehicle_make")  </span> : {{$vehicle_details->VehicleMake->VehicleMakeName}} <br>

                                                    <span class="">@lang("$string_file.vehicle_number") </span> : {{$vehicle_details->vehicle_number}} <br>@if($config->vehicle_model_expire == 1) @lang("$string_file.vehicle_registered_date") : {!! convertTimeToUSERzone($vehicle_details->vehicle_register_date, $driver->CountryArea->timezone,null,$driver->Merchant,2) !!} |  @lang("$string_file.vehicle_expire_date") : {!! convertTimeToUSERzone($vehicle_details->vehicle_expire_date, $driver->CountryArea->timezone,null,$driver->Merchant,2) !!} @endif
                                                    <br>
                                                    <span>@lang("$string_file.services") : {{ implode(',',array_pluck($vehicle_details->ServiceTypes,'serviceName'))}}</span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-5">
                                                        <h6>@lang("$string_file.vehicle_image") </h6>
                                                        <div class="" style="width: 6.5rem;">
                                                            <div class=" bg-light">
                                                                @php $vehicle_image = get_image($vehicle_details->vehicle_image,'vehicle_document',$driver->merchant_id); @endphp
                                                                <a href="{{$vehicle_image}}" target="_blank"><img src="{{ $vehicle_image }}" style="width:100%;height:80px;"></a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 col-sm-7">
                                                        <h6>@lang("$string_file.vehicle")  @lang("$string_file.number_plate") </h6>
                                                        <div class="" style="width: 6.5rem;">
                                                            <div class=" bg-light">
                                                                @php $vehicle_number_plate_image = get_image($vehicle_details->vehicle_number_plate_image,'vehicle_document',$driver->merchant_id); @endphp
                                                                <a href="{{ $vehicle_number_plate_image }}" target="_blank"><img src="{{ $vehicle_number_plate_image }}" style="width:100%;height:80px;"></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="row">
                                             <div class="table-responsive">
                                                 <h5>@lang("$string_file.vehicle_details")</h5>
                                                 <table class="table table-bordered" id="dataTable">
                                                     <thead>
                                                     <tr>
                                                         <th>@lang("$string_file.sn")</th>
                                                         <th>@lang("$string_file.document_name")</th>
                                                         <th>@lang("$string_file.document")</th>
                                                         <th>@lang("$string_file.status")</th>
                                                         <th>@lang("$string_file.expire_date")</th>
                                                         <th>@lang("$string_file.uploaded_time")</th>
                                                     </tr>
                                                     </thead>
                                                     <tbody>
                                                     @php $sn = 1; @endphp
                                                         @foreach($vehicle_details->DriverVehicleDocument as $document)
                                                             <tr>
                                                                 <td>{{$sn}}</td>
                                                                 <td> {{ $document->Document->documentname }}</td>
                                                                 <td>
                                                                     @php $vehicle_file = get_image($document->document,'vehicle_document',$driver->merchant_id); @endphp
                                                                     <a href="{{ $vehicle_file }}" target="_blank"><img src="{{ $vehicle_file }}" style="width:60px;height:60px;border-radius: 10px"></a>
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
                                                                     {!! convertTimeToUSERzone($document->expire_date, $driver->CountryArea->timezone,null,$driver->Merchant,2) !!}
                                                                 </td>
                                                                 <td>
                                                                     {!! convertTimeToUSERzone($document->created_at, $driver->CountryArea->timezone,null,$driver->Merchant) !!}
                                                                 </td>
                                                                 @php $sn = $sn+1; @endphp
                                                             </tr>
                                                         @endforeach
                                                     </tbody>
                                                 </table>
                                                 @if($tempDocUploaded > 0 && $vehicle_details->DriverVehicleDocument->where('temp_document_file','!=','')->count()>0)
                                                     <h5>@lang("$string_file..vehicle_temporary_document_verification")  </h5>
                                                     <table class="table table-bordered" id="dataTable">
                                                         <thead>
                                                         <tr>
                                                             <th>@lang("$string_file.document_name")</th>
                                                             <th>@lang("$string_file.document") </th>
                                                             <th>@lang("$string_file.status")</th>
                                                             <th>@lang("$string_file.expire_date")  </th>
                                                         </tr>
                                                         </thead>
                                                         <tbody>
                                                         @foreach($vehicle_details->DriverVehicleDocument as $document)
                                                             @if(!empty($document->temp_document_file))
                                                             <tr>
                                                                 <td> {{ $document->Document->documentname }}</td>
                                                                 <td>
                                                                     @php $vehicle_file = get_image($document->temp_document_file,'vehicle_document',$driver->merchant_id); @endphp
                                                                     <a href="{{ $vehicle_file }}" target="_blank"><img src="{{ $vehicle_file }}" style="width:60px;height:60px;border-radius: 10px"></a>
                                                                 </td>
                                                                 <td>
                                                                     @switch($document->temp_doc_verification_status)
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
                                                                     {!! convertTimeToUSERzone($document->temp_expire_date, $driver->CountryArea->timezone,null,$driver->Merchant, 2) !!}
                                                                 </td>
                                                             </tr>
                                                             @endif
                                                         @endforeach
                                                         </tbody>
                                                     </table>
                                                 @endif
                                             </div>
                                         </div>
                                    </div>
                                 @endif
                                    @endif
                            </div>
                            @if($driver->signupStep <= 8 && $driver->reject_driver !=2)
                                <div class="float-right mt-10">
                                    @if($driver->signupStep == 8)
                                        <a href="{{ route('merchant.driver-vehicle-verify',[$driver->id,1]) }}">
                                            <button class="btn btn-success float-right">@lang("$string_file.approve")</button>
                                        </a>
                                    @endif
                                    <button class="btn btn-danger float-right mr-2"
                                            data-toggle="modal"
                                            data-target="#exampleModalCenter">@lang("$string_file.reject")
                                    </button>
                                </div>
                            @endif
                        </div>
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
                        <h5 class="modal-title" id="exampleModalCenterTitle">@lang("$string_file.reject_driver")</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
{{--                                <h5>@lang('admin.documentsneed')</h5>--}}
                            </div>
                            <input type="hidden" value="{{ $driver->id }}" name="driver_id">
                            @if(count($driver->DriverDocument) > 0)
                                @foreach($driver->DriverDocument as $document)
                                    <div class="col-md-6">
                                        <input type="checkbox" value="{{ $document->id }}"
                                               name="document_id[]"> {{ $document->Document->documentname }}
                                    </div>
                                @endforeach
                            @endif
                            <hr>
                            @if((count($driver->DriverVehicle) > 0 && $driver->signupStep == 8) && $driver->id == $driver->DriverVehicle[0]->owner_id)
                                <div class="col-md-12">
                                    <h5>@lang("$string_file.vehicle_document")</h5>
                                </div>
                                <input type="hidden" value="{{ $driver->DriverVehicle[0]->id }}"
                                       name="driver_vehicle_id">
                                @foreach($driver->DriverVehicle[0]->DriverVehicleDocument as $document)
                                    <div class="col-md-6">
                                        <input type="checkbox" value="{{ $document->id }}"
                                               name="vehicle_documents[]"> {{ $document->Document->documentname }}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="row">
                            {!! Form::hidden('request_from','driver_profile') !!}
                            <div class="col-md-12">
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