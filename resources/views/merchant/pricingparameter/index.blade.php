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
                        @if(Auth::user('merchant')->can('create_pricing_parameter'))
                            <a href="{{route('priceparameter.add')}}">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus"
                                       title="@lang("$string_file.add_pricing_parameter")"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="icon fa-money" aria-hidden="true"></i>
                        @lang("$string_file.pricing_parameter_management")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.segment")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.application_name")</th>
                            <th>@lang("$string_file.type")</th>
                            <th>@lang("$string_file.sequence")</th>
                            <th>@lang("$string_file.applicable_for")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $parameters->firstItem();
                             $arr_price_type = get_price_parameter($string_file, "edit");
                        @endphp
                        @foreach($parameters as $parameter)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    @if(!empty($parameter->Segment))
                                        {!! implode(',',array_pluck($parameter->Segment,'slag')) !!}
                                    @endif
                                </td>
                                <td>
                                    {{--@if(empty($parameter->LanguageSingle))--}}
                                        {{--<span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>--}}
                                        {{--<span class="text-primary">( In {{ $parameter->LanguageAny->LanguageName->name }}--}}
                                                                {{--: {{ $parameter->LanguageAny->parameterName }}--}}
                                                                {{--)</span>--}}
                                    {{--@else--}}
                                        {{--{{ $parameter->LanguageSingle->parameterName }}--}}
                                    {{--@endif--}}

                                    {{$parameter->ParameterName}}
                                </td>
                                <td>
                                    {{$parameter->ParameterApplication}}
                                    {{--@if(empty($parameter->LanguageSingle))--}}
                                        {{--<span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>--}}
                                        {{--<span class="text-primary">( In {{ $parameter->LanguageAny->LanguageName->name }} : {{ $parameter->LanguageAny->parameterNameApplication }})</span>--}}
                                    {{--@else--}}
                                        {{--{{ $parameter->LanguageSingle->parameterNameApplication }}--}}
                                    {{--@endif--}}
                                </td>
                                <td>
                                    {!! isset($arr_price_type[$parameter->parameterType]) ? $arr_price_type[$parameter->parameterType] : '' !!}
                                </td>
                                <td>{{ $parameter->sequence_number }}</td>
                                <td>
                                    @foreach($parameter->PricingType as $value)
                                        @switch($value->price_type)
                                            @case(1)
                                            @lang("$string_file.variable"),
                                            @break
                                            @case(2)
                                            @lang("$string_file.fixed_price"),
                                            @break
                                            @case(3)
                                            @lang("$string_file.input_by_driver"),
                                            @break
                                        @endswitch
                                    @endforeach
                                </td>
                                <td>
                                    @if($parameter->parameterStatus == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user('merchant')->can('edit_pricing_parameter'))
                                        <a href="{{ route('priceparameter.add',$parameter->id) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i>
                                        </a>
                                    @endif

                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $parameters, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection

