@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content container-fluid">
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
                        <a href="{{route('excel.promocode')}}">
                            <button type="button" class="btn btn-icon btn-primary float-right"
                                    style="margin: 10px;">
                                <i class="wb-download" title="@lang("$string_file.export_excel")"></i>
                            </button>
                        </a>
                        <a href="{{route('promocode.create')}}">
                            <button type="button" class="btn btn-icon btn-success float-right"
                                    style="margin: 10px;">
                                <i class="wb-plus"
                                   title="@lang("$string_file.promo_code")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-percent" aria-hidden="true"></i>
                        @lang("$string_file.promo_code")  @lang("$string_file.management")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.promo_code")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.segment")</th>
                            <th>@lang("$string_file.promo_code_parameter") </th>
                            {{--                                <th>@lang("$string_file.applicable_price_card")</th>--}}
                            <th>@lang("$string_file.description")</th>
                            <th>@lang("$string_file.discount")</th>
                            <th>@lang("$string_file.validity")</th>
                            <th>@lang("$string_file.start_date")</th>
                            <th>@lang("$string_file.end_date")</th>
                            <th>@lang("$string_file.limit")</th>
                            <th>@lang("$string_file.limit_per_user")</th>
                            <th>@lang("$string_file.applicable_for")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $promocodes->firstItem() @endphp
                        @foreach($promocodes as $promocode)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $promocode->promoCode }}</td>
                                <td>{{ !empty($promocode->country_area_id) ? $promocode->CountryArea->CountryAreaName : ""}}</td>
                                <td>{{ ($promocode->segment_id != "") ? $segment_list[$promocode->segment_id] : "---" }}</td>
                                <td>@if(!empty($promocode->LanguageSingle))
                                        {{ $promocode->LanguageSingle->promo_code_name }}
                                    @elseif(!empty($promocode->LanguageAny ))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary">( In {{ $promocode->LanguageAny->LanguageName->name }}
                                                                : {{ $promocode->LanguageAny->promo_code_name }}
                                                                )</span>
                                    @else
                                        <span class="text-primary">------</span>
                                    @endif
                                </td>
                                <?php $a = array(); ?>
                                @foreach($promocode->PriceCard as $pricecard)
                                    <?php $a[] = $pricecard->price_card_name; ?>
                                @endforeach
                                {{--                                    <td>--}}
                                {{--                                        @foreach($a as $applicable)--}}
                                {{--                                            {{ $applicable }}<br>--}}
                                {{--                                        @endforeach--}}
                                {{--                                    </td>--}}
                                <td>
                                    <span class="long_text">{{ $promocode->promo_code_description }}</span>
                                </td>
                                <td>
                                    @if($promocode->promo_code_value_type == 1)
                                        {{ $promocode->CountryArea->Country->isoCode." ".$promocode->promo_code_value }}
                                    @else
                                        {{ $promocode->promo_code_value }} %
                                    @endif
                                </td>
                                <td>
                                    @if($promocode->promo_code_validity == 1)
                                        @lang("$string_file.permanent")
                                    @else
                                        @lang("$string_file.custom")
                                    @endif
                                </td>
                                <td>
                                    @if($promocode->start_date == "")
                                        -----
                                    @else
                                        {{ $promocode->start_date }}
                                    @endif
                                </td>
                                <td>
                                    @if($promocode->end_date == "")
                                        -----
                                    @else
                                        {{ $promocode->end_date }}
                                    @endif
                                </td>
                                <td>{{ $promocode->promo_code_limit }}</td>
                                <td>{{ $promocode->promo_code_limit_per_user }}</td>
                                <td>
                                    @if($promocode->applicable_for == 1)
                                        @lang("$string_file.all_users")
                                    @elseif($promocode->applicable_for == 2)
                                        @lang("$string_file.new_user")
                                    @else
                                        @lang("$string_file.corporate_users")
                                    @endif
                                </td>
                                <td>
                                    @if($promocode->promo_code_status == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                @php $created_at = convertTimeToUSERzone($promocode->created_at, $promocode->CountryArea->timezone, null, $promocode->Merchant, 2); @endphp
                                <td>{!! $created_at !!}</td>
                                <td style="width:200px">
                                    <a href="{{ route('promocode.create',$promocode->id) }}"
                                       data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-warning menu-icon btn_edit action_btn">
                                        <i class="fa fa-edit"></i> </a>
                                    @if($promocode->promo_code_status == 1)
                                        <a href="{{ route('merchant.promocode.active-deactive',['id'=>$promocode->id,'status'=>2]) }}"
                                           data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                            <i class="fa fa-eye-slash"></i> </a>
                                    @else
                                        <a href="{{ route('merchant.promocode.active-deactive',['id'=>$promocode->id,'status'=>1]) }}"
                                           data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                            <i class="fa fa-eye"></i> </a>
                                    @endif
                                    <a href="{{ route('merchant.promocode.delete',$promocode->id) }}"
                                       data-original-title="@lang("$string_file.delete")" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                        <i class="fa fa-trash"></i> </a>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $promocodes, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection