@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content container-fluid">
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
                        @if(Auth::user('merchant')->can('create_refer'))
                            <a href="{{route('referral-system.create')}}">
                                <button type="button" data-toggle="tooltip" class="btn btn-icon btn-success float-right"
                                        style="margin: 10px;">
                                    <i class="wb-plus"
                                       title="@lang("$string_file.add_referral_system")"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="fa-television" aria-hidden="true"></i>
                        @lang("$string_file.referral_system")
                    </h3>
                </header>

                <div class="panel-body container-fluid">
                    {{--                    <div class="nav-tabs-horizontal" data-plugin="tabs">--}}
                    {{--                        <ul class="nav nav-tabs nav-tabs-line tabs-line-top" id="myTab" role="tablist">--}}
                    {{--                            <li class="nav-item" role="presentation">--}}
                    {{--                                <a class="nav-link active" id="base-tab11" data-toggle="tab" href="#exampleTabsLineTopOne"--}}
                    {{--                                   aria-controls="#exampleTabsLineTopOne" role="tab">--}}
                    {{--                                    @lang("$string_file.referral_code")</a></li>--}}
                    {{--                            <li class="nav-item" role="presentation">--}}
                    {{--                                <a class="nav-link" id="base-tab12" data-toggle="tab" href="#exampleTabsLineTopTwo"--}}
                    {{--                                   aria-controls="#exampleTabsLineTopTwo" role="tab">--}}
                    {{--                                    @lang('admin.default_referral')</a></li>--}}
                    {{--                        </ul>--}}
                    {{--                        <div class="tab-content pt-20">--}}
                    {{--                            <div class="tab-pane active" id="exampleTabsLineTopOne" role="tabpanel">--}}
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.country")</th>
                            <th>@lang("$string_file.service_area")</th>
                            <th>@lang("$string_file.application")</th>
                            <th>@lang("$string_file.start_date")</th>
                            <th>@lang("$string_file.end_date")</th>
                            <th>@lang("$string_file.discount_applicable")</th>
                            <th>@lang("$string_file.offer_type")</th>
                            <th>@lang("$string_file.offer_value")</th>
                            <th>@lang("$string_file.offer_condition")</th>
                            <th>@lang("$string_file.status")</th>
                            @if(Auth::user('merchant')->can('edit_refer') || Auth::user('merchant')->can('delete_refer'))
                                <th>@lang("$string_file.action")</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $referral_systems->firstItem() @endphp
                        @foreach($referral_systems as $refer)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ !empty($refer->country_id) ? $refer->Country->CountryName : "" }}</td>
                                <td>{{ !empty($refer->country_area_id) ? $refer->CountryArea->CountryAreaName : "" }}</td>
                                <td>
                                    @switch($refer->application)
                                        @case(1)
                                        @lang("$string_file.user")
                                        @break
                                        @case(2)
                                        @lang("$string_file.driver")
                                        @break
                                    @endswitch
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($refer->start_date, null, null, $refer->Merchant, 2) !!}
                                </td>
                                <td>
                                    {!! convertTimeToUSERzone($refer->end_date, null, null, $refer->Merchant, 2) !!}
                                </td>
                                <td>
                                    @switch($refer->offer_applicable)
                                        @case(1)
                                        @lang("$string_file.sender")
                                        @break
                                        @case(2)
                                        @lang("$string_file.receiver")
                                        @break
                                        @case(3)
                                        @lang("$string_file.both")
                                        @break
                                    @endswitch
                                </td>
                                <td>
                                    @switch($refer->offer_type)
                                        @case(1)
                                        @lang("$string_file.fixed_amount")
                                        @break
                                        @case(2)
                                        @lang("$string_file.discount")
                                        @break
                                        @case(3)
                                        @lang("$string_file.according_to_commission_fare")
                                        @break
                                    @endswitch
                                </td>
                                <td>
                                    @switch($refer->offer_type)
                                        @case(1)
                                        {{ $refer->Country->isoCode." ".$refer->offer_value }}
                                        @break
                                        @case(2)
                                        {{ $refer->offer_value }} %
                                        @break
                                    @endswitch
                                </td>
                                <td>
                                    @php $offer_condition = getReferralSystemOfferCondition($string_file) @endphp
                                    {{ $offer_condition[$refer->offer_condition] }}
                                </td>
                                <td>
                                    @if($refer->status == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @elseif($refer->status == 2)
                                        <span class="badge badge-default">@lang("$string_file.inactive")</span>
                                    @elseif($refer->status == 3)
                                        <span class="badge badge-dark">@lang("$string_file.expired")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.deleted")</span>
                                    @endif
                                </td>
                                @if(Auth::user('merchant')->can('edit_refer') || Auth::user('merchant')->can('delete_refer'))
                                    <td>
                                        @if(Auth::user('merchant')->can('edit_refer') && in_array($refer->status,[1,2]))
                                            @if($refer->status == 1)
                                                <a href="{{ route('referral-system.change-status',['id'=>$refer->id,'status'=>2]) }}"
                                                   data-original-title="@lang("$string_file.inactive")"
                                                   data-toggle="tooltip" data-placement="top"
                                                   class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn"> <i
                                                            class="fa fa-eye-slash"></i> </a>
                                            @else
                                                <a href="{{ route('referral-system.change-status',['id'=>$refer->id,'status'=>1]) }}"
                                                   data-original-title="@lang("$string_file.active")"
                                                   data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                            class="fa fa-eye"></i> </a>
                                            @endif
                                            <a href="{{ route('referral-system.create',['id'=>$refer->id]) }}"
                                               data-original-title="@lang("$string_file.edit")"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                        class="fa fa-edit"></i> </a>
                                        @endif
                                        @if(Auth::user('merchant')->can('delete_refer') && $refer->status != 4)
                                            <a href="#" class="btn btn-sm btn-danger menu-icon"
                                               data-original-title="Delete"
                                               data-toggle="tooltip"
                                               data-placement="top" data-Id="{{$refer->id}}" onclick="EditDoc(this)"> <i
                                                        class="fa fa-trash"></i>
                                            </a>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $referral_systems, 'data' => []])
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="EditDOc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b> @lang("$string_file.referral_system")</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{ route('referral-system.delete') }}">
                    @csrf
                    <div class="modal-body text-center">
                        <label><b class="text-danger">@lang("$string_file.delete_warning")</b></label>
                        <input type="hidden" id="referral_system_id" name="referral_system_id">
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-sm btn-secondary" data-dismiss="modal"
                               value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-sm btn-danger" value="@lang("$string_file.delete")">
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
@section('js')
    <script>
        function EditDoc(obj) {
            let ID = obj.getAttribute('data-ID');
            $(".modal-body #referral_system_id").val(ID);
            $('#EditDOc').modal('show');
        }

        $(document).ready(function () {
            $('#dataTable2').DataTable({
                searching: false,
                paging: false,
                info: false,
                "bSort": false,
            });
        });
    </script>
@endsection
