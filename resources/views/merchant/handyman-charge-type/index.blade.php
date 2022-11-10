@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
           @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <a href="{{route("segment.handyman-charge-type.add")}}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"
                                data-toggle="modal" data-target="#inlineForm">
                            <i class="wb-plus" title="@lang("$string_file.charge_types")"></i>
                        </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class=" wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.charge_types")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.charge_types")</th>
                            <th>@lang("$string_file.segment") </th>
                            <th>@lang("$string_file.maximum_amount")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $handyman_charge_types->firstItem() @endphp
                        @foreach($handyman_charge_types as $handyman_charge_type)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>@if(empty($handyman_charge_type->LanguageSingle))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary">( In {{ $handyman_charge_type->LanguageAny->LanguageName->charge_type }}
                                                                : {{ $handyman_charge_type->LanguageAny->charge_type }}
                                                                )</span>
                                    @else
                                        {{ $handyman_charge_type->LanguageSingle->charge_type }}
                                    @endif
                                </td>
                                <td>{{ array_key_exists($handyman_charge_type->segment_id,$merchant_segments) ? $merchant_segments[$handyman_charge_type->segment_id] : '--'}}</td>
                                <td>{{$handyman_charge_type->maximum_amount}}</td>
                                <td>
                                    @if($handyman_charge_type->status == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>

                                <td style="width:100px;float:left">
                                    <a href="{{ route('segment.handyman-charge-type.add',$handyman_charge_type->id) }}"
                                       data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                        <i class="fa fa-edit"></i> </a>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $handyman_charge_types, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
