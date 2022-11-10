@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('success'))
                <div class="alert dark alert-icon alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert dark alert-icon alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('info') }}
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(Auth::user('merchant')->can('create_cms'))
                            <a href="{{route('terms.create')}}">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus" title="@lang("$string_file.add_cms_page")"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="fas fa-info" aria-hidden="true"></i>
                        @lang('admin.terms_condition')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="post" action="{{ route('merchant.terms.search') }}">
                        @csrf
{{--                        <div class="table_search">--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-md-2 col-xs-12 form-group ">--}}
{{--                                    <div class="input-group">--}}
{{--                                        <input type="text" id="" name="pagetitle"--}}
{{--                                               placeholder="@lang('admin.message373')"--}}
{{--                                               class="form-control col-md-12 col-xs-12">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-sm-2  col-xs-12 form-group ">--}}
{{--                                    <button class="btn btn-primary" type="submit" name="seabt12"><i--}}
{{--                                                class="fa fa-search" aria-hidden="true"></i>--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.country")</th>
                            <th>@lang("$string_file.application")</th>
                            <th>@lang("$string_file.page_title")</th>
                            <th>@lang("$string_file.description")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $cmspages->firstItem() @endphp
                        @foreach($cmspages as $cmspage)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td> @foreach($countries as $country)

                                        @if(!empty($country)) @if($country->id == $cmspage->country_id) {{ $country->CountryName }}@endif @endif
                                    @endforeach
                                </td>
                                <td>
                                    @if(!empty($cmspage))
                                        @if($cmspage->application == 1)
                                            User
                                        @else
                                            Driver
                                        @endif
                                    @endif
                                </td>
                                <td>@if(!empty($cmspage->LanguageSingle))
                                        @if(empty($cmspage->LanguageSingle))
                                            <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                            <span class="text-primary">( In {{ $cmspage->LanguageAny->LanguageName->name }}
                                                            : {{ $cmspage->LanguageAny->title }}
                                                            )</span>
                                        @else
                                            {{ $cmspage->LanguageSingle->title }}
                                        @endif
                                    @endif
                                </td>
                                <td>@if(!empty($cmspage->LanguageSingle))
                                        @if(empty($cmspage->LanguageSingle))
                                            <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                            <span class="text-primary">( In {{ $cmspage->LanguageAny->LanguageName->name }} :
                                                {{ substr($cmspage->LanguageAny->description, 0, 40) }})</span>
                                        @else
                                            {{ substr($cmspage->LanguageSingle->description, 0, 40) }}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user('merchant')->can('edit_cms'))
                                        <a href="{{ route('terms.edit',$cmspage->id) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn"><i class="wb-edit"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @php $sr++ @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $cmspages, 'data' => []])
{{--                    <div class="pagination1 float-right">{{ $cmspages->links() }}</div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $('.toast').toast('show');
    </script>
@endsection
