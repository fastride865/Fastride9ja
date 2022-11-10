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
                            <a href="{{route('food-grocery.price_card.add',[$price_card_for])}}">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus" title="@lang("$string_file.add_price_card")"></i>
                                </button>
                            </a>
                    </div>
                    <h3 class="panel-title"><i class="icon fa-money" aria-hidden="true"></i>
                       @if($price_card_for == 1)
                           @php $prefix = trans($string_file.'.driver');@endphp
                       @elseif($price_card_for == 2)
                            @php $prefix = trans("$string_file.user");@endphp
                       @else
                            @php $prefix = "";@endphp
                       @endif
                        {{$prefix}} @lang("$string_file.price_card")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.segment")</th>
                            <th>@lang("$string_file.service_type")</th>
                            @if($price_card_for == 1)
                                <th>@lang("$string_file.pickup_amount")</th>
                                <th>@lang("$string_file.drop_off_amount")</th>
                            @endif
                            @if($price_card_for == 2)
                                <th>@lang("$string_file.tax")</th>
                            @endif
                            <th>@lang("$string_file.slab")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
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
                                <td>{{ !empty($price_card->Segment->Name($price_card->merchant_id)) ? $price_card->Segment->Name($price_card->merchant_id) : $price_card->Segment->slag }}</td>
                                <td>{{ $price_card->ServiceType->serviceName }}</td>
                                @if($price_card_for == 1)
                                    <td>{{$price_card->pick_up_fee}}</td>
                                    <td>{{$price_card->drop_off_fee}}</td>
                                @endif
                                @if($price_card_for == 2)
                                    <td>{{!empty($price_card->tax) ? $price_card->tax .'%': NULL}}</td>
                                @endif
                                <td>
                                    @foreach($price_card->PriceCardDetail as $key=> $detail)
                                        @php $country = $price_card->CountryArea->Country;
                                              $unit = $country->distance_unit ==1 ? trans("$string_file.km") : trans("$string_file.miles")
                                        @endphp
                                        {{($key+1).') '. $detail->distance_from.'-'.$detail->distance_to.$unit.'=>'.$country->isoCode.$detail->slab_amount}}
                                        <br>
                                    @endforeach
                                </td>
                                <td>
                                    @if($price_card->status == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('food-grocery.price_card.add',[$price_card->price_card_for,$price_card->id]) }}"
                                       data-original-title="@lang("$string_file.edit")" data-toggle="tooltip" data-placement="top"
                                       class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                class="fa fa-edit"></i> </a>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $arr_price_card, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection

