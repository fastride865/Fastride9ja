@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <h3 class="panel-title">
                        <i class=" wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.promo_code_reports")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang("$string_file.sn")</th>
                                <th>@lang("$string_file.promo_code")</th>
                                <th>@lang("$string_file.service_area")</th>
                                <th>@lang("$string_file.price_card")</th>
                                <th>@lang("$string_file.discount")</th>
                                <th>@lang("$string_file.validity")</th>
                                <th>@lang("$string_file.start_date")</th>
                                <th>@lang("$string_file.end_date")</th>
                                <th>@lang("$string_file.limit")</th>
                                <th>@lang("$string_file.limit_per_user")</th>
                                <th>@lang("$string_file.applicable")</th>
                                <th>@lang("$string_file.created_at")</th>
                                <th>@lang("$string_file.total_usage")</th>
                                <th>@lang("$string_file.details")</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $sr = $promo_codes->firstItem() @endphp
                            @foreach($promo_codes as $promo_code)
                                <tr>
                                    <td>{{ $sr  }}</td>
                                    <td>{{ $promo_code->promoCode }}</td>
                                    <td>{{ $promo_code->CountryArea->CountryAreaName}}</td>
                                    <td>@if(!empty($promo_code->LanguageSingle))
                                            {{ $promo_code->LanguageSingle->promo_code_name }}
                                        @elseif(!empty($promo_code->LanguageAny ))
                                            <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                            <span class="text-primary">( In {{ $promo_code->LanguageAny->LanguageName->name }}
                                                                : {{ $promo_code->LanguageAny->promo_code_name }}
                                                                )</span>
                                        @else
                                            <span class="text-primary">---</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($promo_code->promo_code_value_type == 1)
                                            {{ $promo_code->CountryArea->Country->isoCode." ".$promo_code->promo_code_value }}
                                        @else
                                            {{ $promo_code->promo_code_value }} %
                                        @endif
                                    </td>
                                    <td>
                                        @if($promo_code->promo_code_validity == 1)
                                            @lang("$string_file.permanent")
                                        @else
                                            @lang("$string_file.custom")
                                        @endif
                                    </td>
                                    <td>
                                        @if($promo_code->start_date == "")
                                            -----
                                        @else
                                            {{ $promo_code->start_date }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($promo_code->end_date == "")
                                            -----
                                        @else
                                            {{ $promo_code->end_date }}
                                        @endif
                                    </td>
                                    <td>{{ $promo_code->promo_code_limit }}</td>
                                    <td>{{ $promo_code->promo_code_limit_per_user }}</td>
                                    <td>
                                        @if($promo_code->applicable_for == 1)
                                            @lang("$string_file.all_users")
                                        @elseif($promo_code->applicable_for == 2)
                                            @lang("$string_file.new_user")
                                        @else
                                            @lang("$string_file.corporate_user")
                                        @endif
                                    </td>
                                    <td>{{ $promo_code->created_at->todatestring() }}
                                    <br>
                                    {{ $promo_code->created_at->toTimeString() }}</td>
                                    <td>{{ $promo_code->TotalUses }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('report.promocode.details',[$promo_code->id])}}">@lang("$string_file.details")</a>
                                    </td>
                                </tr>
                                @php $sr++  @endphp
                            @endforeach
                            </tbody>
                        </table>
                    @include('merchant.shared.table-footer', ['table_data' => $promo_codes, 'data' => []])
                </div>
            </div>
        </div>
    </div>
@endsection