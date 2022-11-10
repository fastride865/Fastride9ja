@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        @if($price_card_owner_config == 1)
                        <a href="{{route('segment.price_card.add')}}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-plus" title="@lang("$string_file.add_price_card")"></i>
                            </button>
                        </a>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="icon fa-money" aria-hidden="true"></i>
                        @lang("$string_file.handyman_services_price_card")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    @include('merchant.segment-pricecard.search')
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.service_area")</th>
                            @if($price_card_owner_config == 2)
                                <th>@lang("$string_file.driver_details")</th>
                            @endif
                            <th>@lang("$string_file.segment")</th>
{{--                            <th>@lang("$string_file.service_type")</th>--}}
                            <th>@lang("$string_file.type")</th>
                            <th>@lang("$string_file.minimum_booking_amount")</th>
                            @if($price_type_config == "HOURLY" || $price_type_config == "BOTH")
                            <th>@lang("$string_file.hourly_charges")</th>
                            @endif
                            @if($price_type_config == "FIXED" || $price_type_config == "BOTH")
                            <th>@lang("$string_file.service_charges")(@lang("$string_file.fixed"))</th>
                            @endif
                            <th>@lang("$string_file.status")</th>
                            @if($price_card_owner_config == 1)
                            <th>@lang("$string_file.action")</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $arr_price_card->firstItem();
                        @endphp
                        @foreach($arr_price_card as $price_card)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    {{ $price_card->CountryArea->CountryAreaName }}
                                </td>
                                @if($price_card_owner_config == 2)
                                    <td>
                                        @if(!empty($price_card->driver_id))
                                            {{ $price_card->Driver->first_name.' '.$price_card->Driver->last_name }}, <br>
                                            {{ $price_card->Driver->phoneNumber }}
                                         @endif
                                    </td>
                                @endif
                                <td>{{ !empty($price_card->Segment->Name($price_card->merchant_id)) ? $price_card->Segment->Name($price_card->merchant_id) : $price_card->Segment->slag }}</td>
{{--                                <td>{{ $price_card->ServiceType->serviceName }}</td>--}}
                                <td>{{ isset($arr_price_type[$price_card->price_type]) ? $arr_price_type[$price_card->price_type] : "" }}</td>
                                <td>{{ $price_card->minimum_booking_amount }}</td>
                                @if($price_type_config == "HOURLY" || $price_type_config == "BOTH")
                                <td>
                                    @if($price_card->price_type == 2)
                                    @lang("$string_file.per_hour") {{ $price_card->amount }}
                                    @endif
                                </td>
                                @endif
                                @if($price_type_config == "FIXED" || $price_type_config == "BOTH")
                                <td>
                                    @if($price_card->price_type == 1)
                                        @foreach($price_card->SegmentPriceCardDetail as $price_card_details)
                                            {{ !empty($price_card_details->ServiceType->serviceName($price_card->merchant_id)) ? $price_card_details->ServiceType->serviceName($price_card->merchant_id) : $price_card_details->ServiceType->serviceName}}  => {{ $price_card_details->amount }},
                                            <br>
                                        @endforeach
                                    @else
                                        @lang("$string_file.same_for_all_services")
                                    @endif
                                </td>
                                @endif
                                <td>
                                    @if($price_card->status == 1)

                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                @if($price_card_owner_config == 1)
                                <td>
                                <a href="{{ route('segment.price_card.add',$price_card->id) }}"
                                   data-original-title="@lang("$string_file.edit")" data-toggle="tooltip" data-placement="top"
                                   class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                            class="fa fa-edit"></i>
                                </a>
                                </td>
                                @endif
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $arr_price_card, 'data' => $arr_search])
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection

