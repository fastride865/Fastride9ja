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
                        @if(Auth::user('merchant')->can('add_banner'))
                            <a href="{{route('advertisement.create')}}">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus"
                                       title="@lang("$string_file.add_banner")"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.banner_management")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.sequence")</th>
                            <th>@lang("$string_file.url")</th>
                            <th>@lang("$string_file.validity")</th>
                            <th>@lang("$string_file.activate_date")</th>
                            <th>@lang("$string_file.expire_date")</th>
                            <th>@lang("$string_file.banner_for")</th>
                            <th>@lang("$string_file.is_display_on_home_screen")</th>
                            <th>@lang("$string_file.segment")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.image")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $banners->firstItem() @endphp
                        @foreach($banners as $banner)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $banner->name }}</td>
                                <td>{{ $banner->sequence }}</td>
                                <td>{{ $banner->redirect_url }}</td>
                                <td>
                                    @if($banner->validity == 1)
                                        @lang("$string_file.unlimited")
                                    @elseif($banner->validity == 2)
                                        @lang("$string_file.limited")
                                    @else
                                        ----
                                    @endif
                                </td>
                                @php $activate_date = convertTimeToUSERzone($banner->activate_date, null, null, $banner->Merchant,2); @endphp
                                <td>{!! $activate_date !!}</td>
                                <td>
                                    @if($banner->validity == 2)
                                        @php $expire_date = convertTimeToUSERzone($banner->expire_date, null, null, $banner->Merchant,2); @endphp
                                        {!! $expire_date !!}
                                    @else
                                        ----
                                    @endif
                                </td>
                                <td>
                                    @if($banner->banner_for == 1)
                                        @lang("$string_file.user")
                                    @elseif($banner->banner_for == 2)
                                        @lang("$string_file.driver")
                                    @else
                                        @lang("$string_file.both")
                                    @endif
                                </td>
                                <td>
                                    {{$status[$banner->home_screen]}}
                                </td>
                                <td>
                                    @if($banner->segment_id)
                                        {{$arr_segment[$banner->segment_id]}}
                                    @endif
                                </td>
                                @php $created_at = convertTimeToUSERzone($banner->created_at, null, null, $banner->Merchant,2); @endphp
                                <td>{!! $created_at !!}</td>
                                {{--                                <th>{{date_format($banner->created_at,'H:i a')}}--}}
                                {{--                                    <br>--}}
                                {{--                                    {{date_format($banner->created_at,'D, M d, Y')}}--}}
                                {{--                                </th>--}}
                                <td>
                                    @if($banner->status  == 1)
                                        <label class="label_success">@lang("$string_file.active")</label>
                                    @else
                                        <label class="label_danger">@lang("$string_file.inactive")</label>
                                    @endif
                                </td>
                                <td><a href="{{ get_image($banner->image,'banners',$banner->merchant_id) }}"
                                       target="_blank"><img
                                                src="{{ get_image($banner->image,'banners',$banner->merchant_id) }}"
                                                height="60" width="60" class="img-responsive"/></a></td>
                                <td>
                                    @if(Auth::user('merchant')->can('add_banner'))
                                        @if(empty($banner->expire_date) || ($banner->expire_date >= date('Y-m-d')))
                                            <a href="{{ route('advertisement.create',$banner->id) }}"
                                               data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                        class="fa fa-edit"></i></a>
                                        @endif
                                    @endif
                                    @if($banner->status == 1)
                                        <a href="{{ route('advertisement.active.deactive',['id'=>$banner->id,'status'=>2]) }}"
                                           data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn"> <i
                                                    class="fa fa-eye-slash"></i> </a>
                                    @else
                                        <a href="{{ route('advertisement.active.deactive',['id'=>$banner->id,'status'=>1]) }}"
                                           data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                    class="fa fa-eye"></i> </a>
                                    @endif
                                    @if(Auth::user('merchant')->can('delete_banner'))
                                        <button onclick="DeleteEvent({{ $banner->id }})"
                                                type="submit"
                                                data-original-title="@lang("$string_file.delete")"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $banners, 'data' => []])
                    {{--                    <div class="pagination1 float-right">{{$banners->links()}}</div>--}}
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
    <script>
        function DeleteEvent(id) {
            var token = $('[name="_token"]').val();
            swal({
                title: "@lang("$string_file.are_you_sure")",
                text: "@lang("$string_file.delete_banner")",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        type: "GET",
                        data: {
                            id: id,
                        },
                        url: "{{ route('advertisement.delete') }}",
                    }).done(function (data) {
                        swal({
                            title: "DELETED!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "{{ route('advertisement.index') }}";
                    });
                } else {
                    swal("@lang("$string_file.data_is_safe")");
                }
            });
        }
    </script>
@endsection