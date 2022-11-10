@extends('taxicompany.layouts.main')
@section('content')
    <div class="app-content content">
        <div class="container-fluid ">
        <div class="content-wrapper">
            @if(session('accounts'))
                <div class="box no-border">
                    <div class="box-tools">
                        <p class="alert alert-warning alert-dismissible">
                            {{ session('accounts') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                        </p>
                    </div>
                </div>
            @endif

            <div class="content-body">
                <section id="horizontal">
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow h-100">
							 <div class="card-header py-3">

							  <div class="content-header row">
                <div class="content-header-left col-md-4 col-12 mb-2">
                    <h3 class="content-header-title mb-0 d-inline-block">
						<i class=" fa fa-money-bill-alt" aria-hidden="true"></i>
					<a href="{{ route('driver.show',$driver->id) }}" target="_blank">{{ $driver->fullName }}</a> @lang("$string_file.bill") </h3>
                </div>
                <div class="content-header-right col-md-8 col-12">
                    <div class="btn-group float-md-right">
                        <div class="heading-elements">
                           {{-- <a href="{{ route('accounts.edit',$driver->id) }}">
                                <button class="btn btn-secondary btn-sm" style="position:relative;">
                                    @lang('admin.message483') {{ $driver->fullName }}
                                </button>
                            </a>--}}

                            <a href="{{ url()->previous() }}">
                                <button type="button" class="btn btn-icon btn-success mr-1"><i class="fa fa-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            </div>
                                <div class="card-content collapse show" style="margin:1%">
                                    <div class="" >
                                        <table id="dataTable" class="table table-responsive display nowrap table-striped table-bordered ">
                                            <thead>
                                            <tr>
                                                <th>@lang("$string_file.bill_date") </th>
                                                <th>@lang('admin.message472')</th>
                                                <th>@lang("$string_file.bill_amount")</th>
                                                <th>@lang('admin.message474')</th>
                                                <th>@lang('admin.message473')</th>
                                                <th>@lang("$string_file.reference_no")</th>
                                                <th>@lang('admin.message477')</th>
                                                <th>@lang('admin.message475')</th>
                                                <th>@lang('admin.message476')</th>
                                                <th>@lang('admin.block_date')</th>
                                                <th>@lang('admin.due_date')</th>
                                                <th>@lang('admin.fee_after_grace_period')</th>
                                                {{--<th>@lang("$string_file.action")</th>--}}
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($bills as $bill)
                                                <tr>
                                                    <td>{{ $bill->created_at }}</td>
                                                    <td>
                                                        {{ $bill->from_date }}
                                                        <br>
                                                        To
                                                        <br>
                                                        {{ $bill->from_date }}
                                                    </td>
                                                    <td>{{ $bill->amount }}</td>
                                                    <td>
                                                        @if($bill->status == 1)
                                                            @lang("$string_file.un_settled")
                                                        @else
                                                            @lang("$string_file.settled")
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $bill->CreateBy->merchantFirstName }}
                                                        <br>
                                                        {{ $bill->CreateBy->merchantPhone }}
                                                        <br>
                                                        {{ $bill->CreateBy->email }}
                                                    </td>
                                                    <td>
                                                        @if($bill->referance_number)
                                                            {{ $bill->referance_number }}
                                                        @else
                                                            ------
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($bill->settle_type)
                                                            @if($bill->settle_type == 1)
                                                                @lang("$string_file.cash")
                                                            @else
                                                                @lang("$string_file.non_cash")
                                                            @endif
                                                        @else
                                                            ------
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($bill->settle_by)
                                                            {{ $bill->SettleBy->merchantFirstName }}
                                                            <br>
                                                            {{ $bill->SettleBy->merchantPhone }}
                                                            <br>
                                                            {{ $bill->SettleBy->email }}
                                                        @else
                                                            ------
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($bill->settle_date)
                                                            {{ $bill->settle_date }}
                                                        @else
                                                            ------
                                                        @endif
                                                    </td>
                                                    
                                                    <td>
                                                        @if($bill->block_date)
                                                            {{ $bill->block_date }}
                                                        @else
                                                            ------
                                                        @endif
                                                    </td>
                                                    
                                                    <td>
                                                        @if($bill->due_date)
                                                            {{ $bill->due_date }}
                                                        @else
                                                            ------
                                                        @endif
                                                    </td>
                                                    
                                                    <td>
                                                        @if($bill->fee_after_grace_period)
                                                            {{ $bill->fee_after_grace_period }}
                                                        @else
                                                            ------
                                                        @endif
                                                    </td>
                                                    
                                                   {{--<td>
                                                         @if($bill->status == 1)
                                                            --}}{{--<button type="button" id="{{ $bill->id }}"
                                                                    class="btn btn-primary"
                                                                    data-toggle="modal"
                                                                    data-target="#settlementBill">Settle
                                                            </button>--}}{{--
                                                        @else
                                                            <br>
                                                            <button type="button" id="{{ $bill->id }}"
                                                                    class="btn btn-info btn-sm" onclick="send_email(this);">Send Email
                                                            </button>
                                                            <br> &nbsp;
                                                            <a href="{{ route('merchant.DriverBill',$bill->id) }}" target="_blank">
                                                                <button class="btn btn-secondary btn-sm" style="position:relative;">
                                                                   Invoice
                                                                </button>
                                                            </a>

                                                        @endif
                                                    </td>--}}
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="pagination1">{{ $bills->links() }}</div>
                                </div>
                            </div>

                        </div>
                    </div>

                </section>

            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="settlementBill" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33">@lang('admin.message481')</label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('accounts.store') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-body">
                        <label>@lang("$string_file.reference_no"): </label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="referance_number"
                                   name="referance_number"
                                   placeholder="@lang("$string_file.reference_no")" required>
                            <input type="hidden" id="bill_id"
                                   name="bill_id">

                        </div>

                        <label>@lang('admin.message477'): </label>
                        <div class="form-group">
                            <select class="form-control" name="settle_type"
                                    id="settle_type" required>
                                <option value="1">@lang("$string_file.cash")</option>
                                <option value="2">@lang("$string_file.non_cash")</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" value="@lang("$string_file.close")">
                        <input type="submit" class="btn btn-outline-primary btn-lg" value="Settle">
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('js')
 <script>
        function send_email(data){
            var token = $('[name="_token"]').val();
            console.log(token);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                method: "POST",
                url: "{{ route('merchant.billDriverEmail') }}",
                cache: false,
                data: {
                    param: data.id,
                },
                success: function (data) {
                    console.log('Success');
                    window.location.reload();
                },
                error:function(data){
                    console.log('failed');
                }
            });
        }
    </script>
    @endsection