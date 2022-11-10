@extends('merchant.layouts.main')
@section('content')
    {{dd($signupRecharges)}}
    <div class="container-fluid">
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="card shadow ">
                    <div class="card-header py-3 ">

                        <h3 class="content-header-title mb-0 d-inline-block"><i
                                    class="fas fa-code"></i>@lang('admin.wallet_rech_onSignup')</h3>
                        <div class="btn-group float-md-right">
                            <div class="heading-elements">
                                <div class="btn-group float-md-left">
                                    <div class="heading-elements">
                                        <a href="{{route('excel.promocode')}}">
                                            <button type="button" class="btn btn-icon btn-primary mr-1"
                                                    data-original-title="@lang("$string_file.export_excel")"
                                                    data-toggle="tooltip"><i
                                                        class="fa fa-download"></i>
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(Auth::user('merchant')->can('create_promo_code'))
                            <div class="btn-group float-md-right">
                                <div class="heading-elements">
                                    <a title="@lang("$string_file.add_promo_code")"
                                       href="{{route('promocode.create')}}">
                                        <button class="btn btn-icon btn-success mr-1"><i class="fa fa-plus"></i>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-header py-3">
                        <form method="post" action="{{ route('promocode.search') }}">
                            @csrf
                            <div class="table_search row">

                                <div class="col-md-4 col-xs-12 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="code"
                                               placeholder="@lang('admin.message378')"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>

                                <div class="col-sm-2  col-xs-12 form-group ">
                                    <button class="btn btn-primary" type="submit" name="seabt12"><i
                                                class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table display nowrap table-striped table-bordered" id="dataTable" width="100%"
                                   cellspacing="0">
                                <thead>
                                <tr>
                                    <th>@lang("$string_file.sn")</th>
                                    <th>@lang("$string_file.promo_code")</th>
                                    <th>@lang("$string_file.service_area")</th>
                                    <th>@lang("$string_file.price_card")</th>
                                    <th>@lang('admin.applicableFor')</th>
                                    <th>@lang("$string_file.description")</th>
                                    <th>@lang("$string_file.discount")</th>
                                    <th>@lang("$string_file.validity")</th>
                                    <th>@lang("$string_file.start")  @lang("$string_file.date") </th>
                                    <th>@lang("$string_file.end")  @lang("$string_file.date")</th>
                                    <th>@lang("$string_file.limit")</th>
                                    <th>@lang("$string_file.limit_per_user")</th>
                                    <th>@lang("$string_file.applicable")</th>
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
                                        <td>{{ $promocode->CountryArea->CountryAreaName}}</td>
                                        <td>@if(!empty($promocode->LanguageSingle))
                                                {{ $promocode->LanguageSingle->promo_code_name }}
                                            @elseif(!empty($promocode->LanguageAny ))
                                                <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                                <span class="text-primary">( In {{ $promocode->LanguageAny->LanguageName->name }}
                                                                : {{ $promocode->LanguageAny->promo_code_name }}
                                                                )</span>
                                            @else
                                                <span class="text-primary">@lang('admin.noRecord')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="long_text">{{ implode(',',array_pluck($promocode->PriceCard,'price_card_name')) }}</span>
                                        </td>
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
                                                @lang("$string_file.all_users")
                                            @else
                                                @lang("$string_file.corporate_user")
                                            @endif
                                        </td>
                                        <td>
                                            @if($promocode->promo_code_status == 1)
                                                <label class="label_success">@lang("$string_file.active")</label>
                                            @else
                                                <label class="label_danger">@lang("$string_file.inactive")</label>
                                            @endif
                                        </td>
                                        <td>{{ $promocode->created_at->toformatteddatestring() }}</td>
                                        <td>
                                            @if(Auth::user('merchant')->can('edit_promo_code'))
                                                <a href="{{ route('promocode.edit',$promocode->id) }}"
                                                   data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                            class="fa fa-edit"></i> </a>


                                                @if($promocode->promo_code_status == 1)
                                                    <a href="{{ route('merchant.promocode.active-deactive',['id'=>$promocode->id,'status'=>2]) }}"
                                                       data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                                       data-placement="top"
                                                       class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                                        <i
                                                                class="fa fa-eye-slash"></i> </a>
                                                @else
                                                    <a href="{{ route('merchant.promocode.active-deactive',['id'=>$promocode->id,'status'=>1]) }}"
                                                       data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                                       data-placement="top"
                                                       class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                                class="fa fa-eye"></i> </a>
                                                @endif
                                            @endif

                                            @if(Auth::user('merchant')->can('delete_promo_code'))
                                                <a href="{{ route('merchant.promocode.delete',$promocode->id) }}"
                                                   data-original-title="@lang("$string_file.delete")"
                                                   data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn btn-sm btn-danger menu-icon btn_delete action_btn"> <i
                                                            class="fa fa-trash"></i> </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @php $sr++  @endphp
                                @endforeach
                                </tbody>
                            </table>
                            @include('merchant.shared.table-footer', ['table_data' => $promocodes, 'data' => []])
                        </div>
                        {{--                        <div class="col-sm-12">--}}
                        {{--                            <div class="pagination1">{{ $promocodes->links() }}</div>--}}
                        {{--                        </div>--}}
                    </div>
                </div>
            </div>
            <br>
        </div>
    </div>
@endsection