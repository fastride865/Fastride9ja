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
                        <a href="{{route('segment.commission.add')}}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-plus" title="@lang("$string_file.add_commission")"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="icon fa-money" aria-hidden="true"></i>
                        @lang("$string_file.handyman_driver_commission_setup")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    @include('merchant.segment-pricecard.search')
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.segment")</th>
                            <th>@lang("$string_file.commission_from_driver")</th>
                            <th>@lang("$string_file.tax")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $arr_commission->firstItem();
                        @endphp
                        @foreach($arr_commission as $commission)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>
                                    {{ $commission->CountryArea->CountryAreaName }}
                                </td>
                                <td>{{ !empty($commission->Segment->Name($commission->merchant_id)) ? $commission->Segment->Name($commission->merchant_id) : $commission->Segment->slag }}</td>
                                <td>
                                    @lang("$string_file.commission_value") :
                                    @if($commission->commission_method == 1)
                                        {{ $commission->CountryArea->Country->isoCode }}
                                    @endif
                                    {!! $commission->commission !!}
                                    @if($commission->commission_method == 2)
                                        %
                                    @endif
                                </td>
                                <td>{{ !empty($commission->tax) ? $commission->tax.' %' : '---'}}</td>
                                <td>
                                    @if($commission->status == 1)

                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('segment.commission.add',$commission->id) }}"
                                       data-original-title="@lang("$string_file.edit")" data-toggle="tooltip" data-placement="top"
                                       class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                class="fa fa-edit"></i> </a>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $arr_commission, 'data' => $arr_search])
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection

