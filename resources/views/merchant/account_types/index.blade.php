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
                        @if(Auth::user('merchant')->can('create-account-types'))
                            <a href="{{route('account-types.create')}}">
                                <button type="button"
                                        title="@lang("$string_file.add_account_type")"
                                        class="btn btn-icon btn-success float-right" style="margin-top:10px">
                                    <i class="wb-plus"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.account_type_management")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.account_type")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $account_types->firstItem() @endphp
                        @foreach($account_types as $account_type)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>@if(empty($account_type->LangAccountTypeSingle))
                                        <span style="color:red">{{ trans("$string_file.not_added_in_english") }}</span>
                                        <span class="text-primary">@if(!empty($account_type->LangAccountTypeAny->LanguageName->name))
                                                ( In {{ $account_type->LangAccountTypeAny->LanguageName->name }}
                                                : {{ $account_type->LangAccountTypeAny->name }}
                                                )@endif </span>
                                    @else
                                        {{ $account_type->LangAccountTypeSingle->name }}
                                    @endif
                                </td>
                                <td>
                                    @if($account_type->status  == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user('merchant')->can('edit-account-types'))
                                        <a href="{{ route('account-types.edit',$account_type->id) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i> </a>

                                        @if($account_type->status == 1)
                                            <a href="{{ route('account-types.changestatus',['id'=>$account_type->id,'status'=>0]) }}"
                                               data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn"> <i
                                                        class="fa fa-eye-slash"></i> </a>
                                        @else
                                            <a href="{{ route('account-types.changestatus',['id'=>$account_type->id,'status'=>1]) }}"
                                               data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                        class="fa fa-eye"></i> </a>
                                        @endif
                                    @endif
                                    @csrf

                                    @if(Auth::user('merchant')->can('delete-account-types'))
                                        <button onclick="DeleteEvent({{ $account_type->id }})" type="submit"
                                                data-original-title="@lang("$string_file.delete")"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                class="btn btn-sm btn-dangeu-icon btn_delete action_btn"><i
                                                    class="fa fa-trash"></i></button>
                                    @endif
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $account_types, 'data' => []])
                    {{--                    <div class="pagination1 float-right">{{$account_types->links()}}</div>--}}
                </div>
            </div>
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
                        url: 'account-types/' + id,
                    }).done(function (data) {
                        swal({
                            title: "DELETED!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "{{ route('account-types.index') }}";
                    });
                } else {
                    swal("@lang("$string_file.data_is_safe")");
                }
            });
        }
    </script>
@endsection
