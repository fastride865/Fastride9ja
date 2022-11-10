@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title"><i class="wb-flag"></i>
                        @lang("$string_file.application_string")</h3>
                </div>
                <div class="panel-body container-fluid">
                    <form method="post" action="{{route('exportString')}}">
                        @csrf
                        <div class="row">
                            @if(Auth::user('merchant')->can('edit_language_strings'))
                            <div class="col-md-2">
                                    <a href="{{route('customEdit')}}" class="btn btn-success float-right"
                                       title=""> @lang("$string_file.customize_string")
                                    </a>
                            </div>
                            @endif
                            <div class="col-md-2">
                                <select class="form-control" name="platform" id="platform">
                                    <option value=""> -- @lang("$string_file.application") --</option>
                                    <option value="android"> @lang("$string_file.android") </option>
                                    <option value="ios"> @lang("$string_file.ios") </option>
                                </select>
                                @if ($errors->has('platform'))
                                    <label class="text-danger">{{ $errors->first('platform') }}</label>
                                @endif
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="app" id="app" onchange="getKeyVal(this)">
                                    <option value=""> -- @lang("$string_file.app") --</option>
                                    <option value="USER"> @lang("$string_file.user") </option>
                                    <option value="DRIVER"> @lang("$string_file.driver") </option>
                                </select>
                                @if ($errors->has('app'))
                                    <label class="text-danger">{{ $errors->first('app') }}</label>
                                @endif
                            </div>
{{--                                @if(Auth::user('merchant')->can('edit_language_strings'))--}}
{{--                                    <div class="col-md-1">--}}
{{--                                        <button type="submit" class="btn btn-primary mr-1" title="@lang("$string_file.export")">--}}
{{--                                            @lang("$string_file.export")--}}
{{--                                        </button>--}}
{{--                                    </div>--}}
{{--                                 @endif--}}
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.plateform")</th>
                            <th>@lang("$string_file.application")</th>
                            <th>@lang("$string_file.string_value")</th>
                            <th>@lang("$string_file.string_translation")</th>
                            <th>@lang("$string_file.group_name")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1; @endphp
                        @if(!empty($application_string))
                            @foreach($application_string as $general_string)
                                <tr>
                                    <td>{{ $sr }}</td>
                                    <td> {{$general_string->platform}} </td>
                                    <td> {{$general_string->application}} </td>
                                    <td> {{$general_string->ApplicationStringLanguage[0]->string_value}} </td>
                                    <td> {{isset($general_string->ApplicationMerchantString[0]) ? $general_string->ApplicationMerchantString[0]->string_value : "--"}} </td>
                                    <td> {{$general_string->string_group_name}} </td>
                                </tr>
                                @php $sr++; $key=0;  @endphp
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    @if(!empty($application_string))
                    @include('merchant.shared.table-footer', ['table_data' => $application_string, 'data' => []])
                   @endif
                </div>
            </div>
        </div>
    </div>
@endsection



