@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
           @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    @if(Auth::user('merchant')->can('create_cms'))
                        <div class="panel-actions">
                            @if(!empty($info_setting) && $info_setting->view_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                            <a href="{{route('cms.create')}}">
                                <button type="button" title="@lang("$string_file.add_cms_page")"
                                        class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-plus"></i>
                                </button>
                            </a>
                        </div>
                    @endif
                    <h3 class="panel-title"><i class="wb-copy" aria-hidden="true"></i>
                        @lang("$string_file.cms_pages_management")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang("$string_file.sn")</th>
                                <th>@lang("$string_file.country")</th>
                                <th>@lang("$string_file.application")</th>
                                <th>@lang("$string_file.name")</th>
                                <th>@lang("$string_file.page_title")</th>
                                <th>@lang("$string_file.action")</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $sr = $cmspages->firstItem() @endphp
                            @foreach($cmspages as $cmspage)
                                <tr>
                                    <td>{{ $sr }}</td>
                                    <td> @if($cmspage->country_id != '')
                                            {{ $cmspage->Country->CountryName }}
                                        @else
                                             ----
                                        @endif
                                    </td>
                                    <td>
                                        @if($cmspage->application == 1)
                                            @lang("$string_file.user")
                                        @else
                                            @lang("$string_file.driver")
                                        @endif
                                    </td>
                                    <td> {{ $cmspage->Page->page }} </td>
                                    <td>@if(empty($cmspage->LanguageSingle))
                                            <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                            <span class="text-primary">( In {{ $cmspage->LanguageAny->LanguageName->name }}
                                                                : {{ $cmspage->LanguageAny->title }}
                                                                )</span>
                                        @else
                                            {{ $cmspage->LanguageSingle->title }}
                                        @endif
                                    </td>
{{--                                    <td><span class="long_text">--}}
{{--                                            @if(empty($cmspage->LanguageSingle))--}}
{{--                                                <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>--}}
{{--                                                <span class="text-primary">( In {{ $cmspage->LanguageAny->LanguageName->name }}--}}
{{--                                                                : {{ substr($cmspage->LanguageAny->description, 0, 50) }}--}}
{{--                                                                )</span>--}}
{{--                                            @else--}}
{{--                                                {{ substr($cmspage->LanguageSingle->description, 0, 50) }}--}}
{{--                                            @endif--}}
{{--                                        </span>--}}
{{--                                    </td>--}}
                                    <td>
                                        @if(Auth::user('merchant')->can('edit_cms'))
                                            <a href="{{ route('cms.edit',$cmspage->id) }}"
                                               data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
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
                        @include('merchant.shared.table-footer', ['table_data' => $cmspages, 'data' => []])
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection