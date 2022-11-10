@extends('merchant.layouts.main')
@section('content')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #section-to-print, #section-to-print * {
                visibility: visible;
            }

            #section-to-print {
                /*position: absolute;*/
                /*left: 0;*/
                /*top: 0;*/
            }
        }
    </style>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#">
                            <button class="btn btn-icon btn-warning float-right" style="margin:2px;width:70px;" onClick="javascript:window.print();"><i class="icon wb-print" aria-hidden="true"></i>
                                @lang("$string_file.print")</button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="wb-flag" aria-hidden="true"></i>
                        @lang('admin.bill_invoice')
                    </h3>
                </header>
{{--                @if(Auth::user()->tax)--}}
{{--                        <div class="panel-heading"> @php $a = json_decode(Auth::user()->tax,true);echo $a['name'] @endphp--}}
{{--                            <strong>@php $a = json_decode(Auth::user()->tax,true);echo $a['tax_number'] @endphp </strong>--}}
{{--                        </div>--}}
{{--                @endif--}}
                <div id="section-to-print" class="panel">
                    <div class="panel-body container-fluid">
                        <h4>@lang('"$string_file.invoice"') <strong>#{{ $BillData->id }}</strong></h4>
                        <br>
                        <div class="row mb-2">
                            <div class="col-sm-4">
                                <h6 class="mb-1">@lang('admin.message307'):</h6>
                                <div><strong>{{ $BillData->Driver->Merchant->BusinessName }}</strong></div>
                                <div>{{ $BillData->Driver->Merchant->merchantFirstName }} {{ $BillData->Driver->Merchant->merchantLastName }}</div>
                                <div>{{ $BillData->Driver->Merchant->merchantAddress }}</div>
                                <div>@lang("$string_file.email"):{{ $BillData->Driver->Merchant->email }}</div>
                                <div>@lang("$string_file.phone"):{{ $BillData->Driver->Merchant->merchantPhone }}</div>
                            </div>
                            <div class="col-sm-3">
                                <h6 class="mb-1">@lang("$string_file.f_cap_to"):</h6>
                                <div><strong>{{ $BillData->Driver->first_name." ".$BillData->Driver->last_name }}</strong></div>
                                <div>@lang("$string_file.email"):{{ $BillData->Driver->email }}</div>
                                <div>@lang("$string_file.phone"):{{ $BillData->Driver->phoneNumber }}</div>
                            </div>
                            <div class="col-sm-5">
                                <table width="100%" border="0">
                                    <tbody>
                                    <tr>
                                        <td width="50%">
                                            <table align="center" width="100%" border="0">
                                                <tbody>
                                                <tr>
                                                    <td align="center">
                                                        <img class="profile_img" style="border-radius: 100%;"
                                                             src="@if ($BillData->Driver->profile_image) {{ get_image($BillData->Driver->profile_image,'driver') }} @endif"
                                                             width="100" height="100">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="4px;"></td>
                                                </tr>
                                                <tr>
                                                    <td align="center"><strong class="profile_name">{{ $BillData->Driver->first_name." ".$BillData->Driver->last_name }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td height="4px;"></td>
                                                </tr>
                                                <tr>
                                                    <td align="center">
                                                        {{ $BillData->Driver->email }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="center">
                                                        {{ $BillData->Driver->phoneNumber }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="center">
                                                        @if ($BillData->Driver->rating == "0.0")
                                                            @lang("$string_file.not_rated_yet")
                                                        @else
                                                            @while($BillData->Driver->rating >0)
                                                                @if($BillData->Driver->rating >0.5)
                                                                    <img src="{{ view_config_image("static-images/star.png") }}"
                                                                         alt='Whole Star'>
                                                                @else
                                                                    <img src="{{ view_config_image('static-images/halfstar.png') }}"
                                                                         alt='Half Star'>
                                                                @endif
                                                                @php $BillData->Driver->rating--; @endphp
                                                            @endwhile
                                                        @endif
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-sm-6">
                                <table class="table table-clear">
                                    <tbody>
                                    <tr>
                                        <td class="left">@lang('admin.from_date')</td>
                                        <td class="right">{{ $BillData->from_date }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left">@lang('admin.to_date')</td>
                                        <td class="right">{{ $BillData->to_date }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <table class="table table-clear">
                                    <tbody>
                                    <tr>
                                        <td class="left"><strong>@lang("$string_file.description")</strong></td>
                                        <td class="right"><strong>@lang("$string_file.price")</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="left">@lang("$string_file.amount")</td>
                                        <td class="right">{{ $BillData->amount }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left">@lang('admin.settle_date')</td>
                                        <td class="right">{{ $BillData->settle_date }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left">@lang("$string_file.reference_no")</td>
                                        <td class="right">{{ $BillData->referance_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"> @lang('admin.total_trips')</td>
                                        <td class="right">{{ $BillData->total_trips }}</td>
                                    </tr>
                                    <!--<tr>-->
                                    <!--    <td class="left"><strong-->
                                    <!--                style="font-size:20px;">@lang("$string_file.total")</strong></td>-->
                                    <!--    <td class="right"><strong-->
                                    <!--                style="font-size:20px;">{{ $BillData->final_amount_paid }}</strong>-->
                                    <!--    </td>-->
                                    <!--</tr>-->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


