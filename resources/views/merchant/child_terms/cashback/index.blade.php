@extends('merchant.layouts.main')
@section('content')
    <div class="app-content content">
        <div class="container-fluid">
            <div class=" content-wrapper">
                <div class="card shadow mb-4">
                    <div class="card-header py-md-2">
                        <div class="row">
                            @include('merchant.shared.errors-and-messages')
                            <div class="col-md-6">
                                <h3 class="content-header-title mb-0 d-inline-block">
                                    <i class=" fa fa-flag" aria-hidden="true"></i>
                                    @lang('admin.cashback_manages')</h3>
                            </div>
                            {{--@if(Auth::user('merchant')->can('create_cashbacks'))--}}
                                <div class="col-md-6">
                                    <a href="{{route('cashback.create')}}">
                                        <button type="button" title="@lang('admin.createCashback')"
                                                class="btn btn-icon btn-success mr-1 float-right"><i class="fa fa-plus"></i>
                                        </button>
                                    </a>
                                    {{--<a href="{{route('excel.countriesexport')}}" >
                                        <button type="button" data-toggle="tooltip" data-original-title="@lang("$string_file.export")"
                                                class="btn btn-icon btn-primary mr-1 float-right"><i
                                                    class="fa fa-download"
                                                    title="@lang("$string_file.export_excel")"></i>
                                        </button>
                                    </a>--}}
                                </div>
                            {{--@endif--}}
                        </div>

                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table display nowrap table-striped table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>@lang("$string_file.sn")</th>
                                    <th>@lang("$string_file.service_area")</th>
                                    <th>@lang('admin.min_bill_cashback')</th>
                                    <th>@lang("$string_file.status")</th>
                                    <th>@lang("$string_file.action")</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $sr = $cashbacks->firstItem() @endphp
                                @forelse($cashbacks as $cashback)
                                    <tr>
                                        <td>{{ $sr }}</td>
                                        <td>
                                            {{$cashback->CountryArea->CountryAreaName}}
                                        </td>
                                        <td>
                                            {{$cashback->min_bill_amount}}
                                        </td>
                                        <td>
                                            @if($cashback->status  == 1)
                                                <label class="label_success">@lang("$string_file.active")</label>
                                            @else
                                                <label class="label_danger">@lang("$string_file.inactive")</label>
                                            @endif
                                        </td>
                                        <td>
                                            {{--@if(Auth::user('merchant')->can('edit_cashbacks'))--}}
                                                <a href="{{ route('cashback.edit',$cashback->id) }}"
                                                   data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                            class="fa fa-edit"></i> </a>
                                            {{--@endif--}}
                                            @if($cashback->status == 1)
                                                <a href="{{ route('cashback.changestatus',['id'=>$cashback->id,'status'=>0]) }}"
                                                   data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn"> <i
                                                            class="fa fa-eye-slash"></i> </a>
                                            @else
                                                <a href="{{ route('cashback.changestatus',['id'=>$cashback->id,'status'=>1]) }}"
                                                   data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                                   data-placement="top"
                                                   class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                            class="fa fa-eye"></i> </a>
                                            @endif
                                            @csrf
                                            <button onclick="DeleteEvent({{ $cashback->id }})" type="submit"
                                                    data-original-title="@lang("$string_file.delete")"
                                                    data-toggle="tooltip"
                                                    data-placement="top"
                                                    class="btn btn-sm btn-danger menu-icon btn_delete action_btn"><i
                                                        class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    @php $sr++  @endphp
                                @empty
                                    @if(($cashbacks->total() > 0) ||  (isset($_REQUEST['keyword'])))
                                        <p class="alert alert-warning">@lang("$string_file.data_not_found")</p>
                                    @else
                                        <p class="alert alert-warning">{{trans('admin.no_cashback_added')}} <a href="{{ route('cashback.create') }}"><br>@lang('admin.create_one_now')</a></p>
                                    @endif
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <div class="col-sm-12">
                        <div class="pagination1">{{$cashbacks->links()}}</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>

        function DeleteEvent(id) {
            var token = $('[name="_token"]').val();
            swal({
                title: "@lang("$string_file.are_you_sure")",
                text: "@lang('admin.cashback_delete_warning')",
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
                        url: 'cashback/' + id,
                    }).done(function (data) {
                        swal({
                            title: "DELETED!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "{{ route('cashback.index') }}";
                    });
                } else {
                    swal("@lang('admin.cashback_delete_safe')");
                }
            });
        }
    </script>
    

@endsection