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
                        @if(Auth::user('merchant')->can('create_countries'))
                            <a href="{{route('country.create')}}">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus"
                                       title="@lang("$string_file.add_country")"></i>
                                </button>
                            </a>
                            <a href="{{route('excel.countriesexport')}}" data-toggle="tooltip">
                                <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                    <i class="wb-download"
                                       title="@lang("$string_file.export_excel")"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.country_management")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="{{ route('country.index') }}" method="GET">
                        <div class="table_search row p-3">
                            <div class="col-md-2 active-margin-top">@lang("$string_file.search_by")
                                :
                            </div>
                            <div class="col-md-4 col-xs-12 form-group active-margin-top">
                                <select class="form-control select2" name="country_id" id="country_id">
                                    <option value="">@lang("$string_file.name")</option>
                                    @foreach($search_countries as $country)
                                        <option value="{{ $country->id }}"
                                                @if(!empty($search_data['country_id']) == $country->id) selected @endif> {{ $country->CountryName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-xs-6 form-group active-margin-top">
                                {!! Form::text('phonecode',isset($search_data['phonecode']) ? $search_data['phonecode'] : NULL,['class'=>'form-control','placeholder'=>trans("$string_file.isd_code")]) !!}
                            </div>
                            <div class="col-md-2 col-xs-6 form-group active-margin-top">
                                {!! Form::text('isoCode',isset($search_data['isoCode']) ? $search_data['isoCode'] : NULL,['class'=>'form-control','placeholder'=>trans("$string_file.iso_code")]) !!}
                            </div>
                            <div class="col-md-1 col-xs-12 form-group active-margin-top">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-search"
                                                                                 aria-hidden="true"></i></button>
                            </div>
                            <div class="col-md-1">
                                <a href="{{ route('country.index') }}">
                                    <button class="btn btn-small btn-success" type="button"><i
                                                class="fa fa-refresh"></i></button>
                                </a>
                            </div>
                        </div>
                    </form>
                    <table class="display nowrap table table-hover table-stripedw-full" id="customDataTable"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.sequence")</th>
                            <th>@lang("$string_file.isd_code")</th>
                            {{--                            <th>@lang("$string_file.currency")</th>--}}
                            <th>@lang("$string_file.iso_code")</th>
                            <th>@lang("$string_file.country_code")</th>
                            <th>@lang("$string_file.distance_unit")</th>
                            <th>@lang("$string_file.phone_length")</th>
                            <th>@lang("$string_file.status")</th>
                            @if(Auth::user('merchant')->can('edit_countries'))
                                <th>@lang("$string_file.action")</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $countries->firstItem() @endphp
                        @foreach($countries as $country)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>@if(empty($country->LanguageCountrySingle))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary">( In {{ isset($country->LanguageCountryAny->LanguageName->name) ? $country->LanguageCountryAny->LanguageName->name : '' }}
                                                            : {{ $country->LanguageCountryAny->name }}
                                                            )</span>
                                    @else
                                        {{ $country->LanguageCountrySingle->name }}
                                    @endif
                                </td>
                                <td>{{ $country->sequance }}</td>
                                <td>{{ $country->phonecode }}</td>
                                <td>{{ $country->isoCode }}</td>
                                <td>{{ $country->country_code }}</td>
                                @switch($country->distance_unit)
                                    @case(1)
                                    <td>@lang("$string_file.km")</td>
                                    @break
                                    @case(2)
                                    <td>@lang("$string_file.miles")</td>
                                    @break
                                @endswitch
                                {{--                                <td>{{ $country->default_language }}</td>--}}
                                <td>@lang("$string_file.min"): {{ $country->minNumPhone }}<br>
                                    @lang("$string_file.max"): {{ $country->maxNumPhone }}</td>
                                <td>
                                    @if($country->country_status  == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                @if(Auth::user('merchant')->can('edit_countries'))
                                    <td style="width:100px; float:left">
                                        <a href="{{ route('country.edit',$country->id) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                            <i class="wb-edit"></i></a>
                                        @if($country->country_status == 1)
                                            <a href="{{ route('merchant.country.active-deactive',['id'=>$country->id,'status'=>2]) }}"
                                               data-original-title="@lang("$string_file.inactive")"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn mr-1"> <i
                                                        class="fa fa-eye-slash"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('merchant.country.active-deactive',['id'=>$country->id,'status'=>1]) }}"
                                               data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                                <i class="wb-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $countries, 'data' => $search_data])
                    {{--                    <div class="pagination1 float-right">{{$countries->links()}}</div>--}}
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection