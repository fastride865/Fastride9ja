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
                        <a href="{{ route('weightunit.add') }}">
                            <button type="button" title="@lang("$string_file.add_unit")" data-toggle="modal"
                                    data-target="#myModal"
                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-plus"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                        {{--                        @lang('admin.weightunits_list')--}}
                        @lang("$string_file.weight_unit")
                    </h3>
                </header>
                <div class="panel-body">
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.description")</th>
                            <th>@lang("$string_file.segment")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1 @endphp
                        @foreach($weightunits as $weightunit)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    {{ isset($weightunit->LanguageSingle)?$weightunit->LanguageSingle->name:'' }}
                                </td>
                                <td>
                                    {{ isset($weightunit->LanguageSingle)?$weightunit->LanguageSingle->description:'' }}
                                </td>
                                <td>
                                    @foreach($weightunit->Segment as $segment)
                                        {{$segment->Name($weightunit->merchant_id)}},
                                    @endforeach
                                </td>
                                <td>
{{--                                    Its edit option--}}
                                    <form method="POST" action="{{ route('weightunit.destroy',$weightunit['id']) }}"
                                          onsubmit="return confirm('@lang("$string_file.are_you_sure")')">
                                        @csrf
                                        {{method_field('DELETE')}}
                                        <a href="{{ route('weightunit.add',$weightunit['id']) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i> </a>

                                    </form>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection

