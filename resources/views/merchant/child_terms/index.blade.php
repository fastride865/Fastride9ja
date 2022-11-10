@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                        @if(Auth::user('merchant')->can('create_child_terms'))
                            <div class="panel-actions">
                                <a href="{{route('child-terms-conditions.create')}}">
                                    <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                        <i class="wb-plus" title="@lang('admin.add_child_terms')"></i>
                                    </button>
                                </a>
                            </div>
                        @endif
                            <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                                @lang("$string_file.child_t_n_c")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.page_title")</th>
                            <th>@lang("$string_file.description")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $child_termpages->firstItem() @endphp
                        @foreach($child_termpages as $child_termpage)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $child_termpage->Country->CountryName }}</td>
                                <td>@if(empty($child_termpage->LangTermsConditionSingle))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary">( In {{ $child_termpage->LangTermsConditionAny->LanguageName->name }}
                                            : {{ $child_termpage->LangTermsConditionAny->name }}
                                            )</span>
                                    @else
                                        {{ $child_termpage->LangTermsConditionSingle->name }}
                                    @endif
                                </td>
                                <td>
                                    <span class="long_text">
                                        @if(empty($child_termpage->LangTermsConditionSingle))
                                            <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                            <span class="text-primary">( In {{ $child_termpage->LangTermsConditionAny->LanguageName->name }}
                                            : {{ substr($child_termpage->LangTermsConditionAny->field_three, 0, 50) }}
                                            )</span>
                                        @else
                                            {{ substr($child_termpage->LangTermsConditionSingle->field_three, 0, 50) }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if(Auth::user('merchant')->can('edit_child_terms'))
                                        <a href="{{ route('child-terms-conditions.edit',$child_termpage->id) }}"
                                           data-original-title="{{trans("$string_file.edit")}}" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i> </a>
                                    @endif
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $child_termpages, 'data' => []])
                </div>
            </div>
        </div>
    </div>
@endsection