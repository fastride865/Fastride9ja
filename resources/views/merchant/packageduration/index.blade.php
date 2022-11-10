@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            @if($durations)
                <div class="panel panel-bordered">
                    <header class="panel-heading">
                        <div class="panel-actions">
                            @if(!empty($info_setting) && $info_setting->view_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                            <a href="{{ url('merchant/admin/duration/add/') }}">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"
                                        data-toggle="modal" data-target="#inlineForm">
                                    <i class="wb-plus" title="@lang('admin.add_package_duration')"></i>
                                </button>
                            </a>
                        </div>
                        <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                            @lang("$string_file.duration_management")
                        </h3>
                    </header>
                    <div class="panel-body container-fluid">
                        <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                               style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang("$string_file.sn")</th> {{--S.no--}}
                                <th>@lang("$string_file.name")</th>
                                <th>@lang("$string_file.period")</th>
                                <th>@lang("$string_file.action")</th>
                            </tr>
                            </thead>
                            @php $sr = $durations->firstItem() @endphp
                            <tbody>
                            @forelse($durations as $duration)
                                @if(!empty($duration->id))
                                    <tr>
                                        <td>{{ $sr  }}</td>
                                        <td>@if(!empty($duration->NameAccMerchant))
                                                @if(empty($duration->LangPackageDurationAccMerchantSingle))
                                                    <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                                    <span class="text-primary">( In {{ $duration->LangPackageDurationAccMerchantAny->LanguageName->name }}
                                                                        : {{ $duration->LangPackageDurationAccMerchantAny['name'] }}
                                                                        )</span>
                                                @else
                                                    {{ $duration->LangPackageDurationAccMerchantSingle['name'] }}
                                                @endif
                                            @else
                                                -----------
                                            @endif
                                        </td>
                                        <td>
                                            {{ isset($duration['sequence']) ? $duration['sequence'].' '.trans("$string_file.day") : '--' }}
                                        </td>

                                        <td>
                                            <div class="button-margin">
                                                <a href="{{ url('merchant/admin/duration/add/'.$duration->id) }}"
                                                   data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </div>
                                            @csrf
                                        </td>
                                    </tr>
                                @endif
                                @php $sr++  @endphp
                            @empty
                                @if(($durations->total() > 0) ||  (isset($_REQUEST['keyword'])))
                                    <p class="alert alert-warning">{{trans("$string_file.data_not_found")}}</p>
                                @else
                                @endif
                            @endforelse
                            </tbody>
                        </table>
                        @include('merchant.shared.table-footer', ['table_data' => $durations, 'data' => []])
                        {{--                        <div class="pagination1 float-right">{{$durations->links()}}</div>--}}
                    </div>
                </div>
            @endif
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>

        function DeleteEvent(id) {
            var token = $('[name="_token"]').val();
            swal({
                title: "@lang("$string_file.are_you_sure")",
                text: "@lang("$string_file.delete_warning")",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        type: "DELETE",
                        url: 'subscription/' + id,
                    }).done(function (data) {
                        swal({
                            title: "DELETED!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "{{ route('subscription.index') }}";
                    });
                } else {
                    swal("@lang("$string_file.data_is_safe")");
                }
            });
        }
    </script>
@endsection