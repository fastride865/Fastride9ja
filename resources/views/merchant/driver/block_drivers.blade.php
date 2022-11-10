@extends('merchant.layouts.main')
@section('content')
    @csrf
    <div class="app-content content">
        <div class="container-fluid ">
            <div class="content-wrapper">
                <div class="content-header row">
                    <div class="content-header-left col-md-4 col-12 mb-2">
                    </div>
                    <div class="col-md-6 col-12">
                        @if(session('moneyAdded'))
                            <div class="col-md-8 alert alert-icon-right alert-info alert-dismissible mb-2" role="alert">
                                <span class="alert-icon"><i class="fa fa-info"></i></span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <strong>
                                    {{ session('moneyAdded') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="content-body">
                    <section id="horizontal">
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow">
                                    <div class="card-header py-3">
                                        <div class="content-header row">
                                            <div class="content-header-left col-md-10 col-12 mb-2">
                                                <h3 class="content-header-title mb-0 d-inline-block">
                                                    <i class=" fa fa-car" aria-hidden="true"></i>
                                                    @lang('admin.message2851')</h3>
                                            </div>
                                            <div class="btn-group float-md-right">
                                                <a href="{{route('excel.blockeddrivers')}}"
                                                   data-original-title="@lang("$string_file.export")" data-toggle="tooltip">
                                                    <button type="button" class="btn btn-icon btn-primary mr-1"><i
                                                                class="fa fa-download"
                                                                title="@lang("$string_file.export_excel")"></i>
                                                    </button>
                                                </a>

                                                <a href="{{route('merchant.driver.cronblock')}}"
                                                   data-original-title="@lang('admin.BlockDriver')"
                                                   data-toggle="tooltip">
                                                    <button type="button" class="btn btn-icon btn-success mr-1">
                                                        BlockDriver
                                                    </button>
                                                </a>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table display nowrap table-striped table-bordered"
                                                   id="dataTable" width="100%" cellspacing="0">
                                                <thead>
                                                <tr>
                                                    <th>@lang("$string_file.sn")</th>
                                                    <th>@lang("$string_file.driver_details")</th>
                                                    <th>@lang("$string_file.wallet_money")</th>
                                                    <th>@lang('admin.outstand_amount')</th>
                                                    <th>@lang('admin.Block_Ststus')</th>
                                                    <th>@lang("$string_file.action")</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php $sr = 1; @endphp
                                                @foreach($drivers as $value)

                                                    <tr>
                                                        <td>{{ $sr }}</td>
                                                        @if(Auth::user()->demo == 1)
                                                            <td>
                                                                {{ "********".substr($value->last_name, -2) }}<br>
                                                                {{ "********".substr($value->phoneNumber, -2) }}
                                                                <br>
                                                                {{ "********".substr($value->email, -2) }}
                                                            </td>
                                                        @else
                                                            <td>
                                                                {{ $value->first_name." ".$value->last_name }}
                                                                <br>
                                                                {{ $value->phoneNumber }}
                                                                <br>
                                                                {{ $value->email }}
                                                            </td>
                                                        @endif
                                                        <td class="text-center">
                                                            {{ $value->wallet_money }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ $value->outstand_amount }}
                                                        </td>
                                                        <td class="text-center">
                                                            @if($value->driver_block_status == 1)
                                                                Blocked
                                                            @else
                                                                Unblocked
                                                            @endif
                                                        </td>

                                                        <td class="text-center">
                                                            <button onclick="DeleteEvent({{ $value->id }})"
                                                                    type="submit"
                                                                    data-original-title="@lang('admin.Unblock_Driver')"
                                                                    data-toggle="tooltip"
                                                                    data-placement="top"
                                                                    class="btn btn-warning btn_delete action_btn"><i
                                                                        class="fa fa-exclamation"></i></button>
                                                        </td>
                                                    </tr>
                                                    @php $sr++  @endphp
                                                @endforeach
                                                </tbody>
                                            </table>
                                            @include('merchant.shared.table-footer', ['table_data' => $drivers, 'data' => []])
                                        </div>
                                    </div>
{{--                                    <div class="col-sm-12">--}}
{{--                                        <div class="pagination1">{{ $drivers->links() }}</div>--}}
{{--                                    </div>--}}
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <br>
        </div>
    </div>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function DeleteEvent(id) {
            var token = $('[name="_token"]').val();

            swal({
                title: "@lang('admin.Unblock_Driver_sure')",
                text: "",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        type: "POST",
                        url: '{{ URL::route('Driver_unblock') }}',
                        data:
                            {
                                id
                            }
                    }).done(function (data) {
                        console.log((data))

                        swal({
                            title: "UNBLOCKED !!",
                            text: data,
                            type: "success",
                        });
                        window.location.href = "{{ route('driver.index') }}";
                    });
                } else {
                    swal("@lang('admin.driver_unblock')");
                }
            });
        }
    </script>
@endsection





