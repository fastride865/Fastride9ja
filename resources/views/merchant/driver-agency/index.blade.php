@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        {{--@if(Auth::user('merchant')->can('driver_agency'))--}}
                                <a href="{{route('merchant.driver-agency.add')}}">
                                    <button type="button" title="@lang("$string_file.add_driver_agency")"
                                            class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-plus"></i>
                                    </button>
                                </a>
                        {{--@endif--}}
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-building" aria-hidden="true"></i>
                        @lang("$string_file.driver_agency")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang("$string_file.sn")</th>
                                <th>@lang("$string_file.driver_agency")</th>
                                <th>@lang("$string_file.logo")</th>
                                <th>@lang("$string_file.country")</th>
                                <th>@lang("$string_file.login_url")</th>
                                <th>@lang("$string_file.address")</th>
                                <th>@lang("$string_file.bank_details")</th>
                                <th>@lang("$string_file.no_of_driver")</th>
                                <th>@lang("$string_file.wallet_money")</th>
                                <th>@lang("$string_file.transactions")</th>
                                <th>@lang("$string_file.status")</th>
                                <th>@lang("$string_file.action")</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $sr = 1 @endphp
                            @foreach($driver_agency as $agency)
                                <tr>
                                    <td>{{ $sr }}</td>
                                    <td>
                                        @if(Auth::user()->demo == 1) {{ "********".substr($agency->name, -2) }} @else <b>{{ $agency->name }}</b> @endif <br>
                                        @if(Auth::user()->demo == 1) {{ "********".substr($agency->email, -2) }} @else <i>{{ $agency->email }}</i> @endif <br>
                                        @if(Auth::user()->demo == 1) {{ "********".substr($agency->phone, -2) }} @else {{ $agency->phone }} @endif
                                    </td>
                                    <td>
                                        <img src="{{get_image($agency->logo,'agency_logo',$agency->merchant_id)}}" width="50px" height="50px">
                                    </td>
                                    <td>
                                        @if(Auth::user()->demo == 1) {{ "********".substr($agency->country->CountryName, -2) }} @else {{ $agency->country->CountryName }} @endif
                                    </td>
                                    <td>
                                        <a href="{{ config('app.url') }}driver-agency/admin/{{$merchant->alias_name}}/{{ $agency->alias_name }}/login"
                                           target="_blank" rel="noopener noreferrer"  class="btn btn-icon btn-info btn_eye action_btn"><i class="icon fa-sign-in"></i></a>
                                    </td>
                                    <td>
                                        @if(Auth::user()->demo == 1) {{ "********".substr($agency->address, -2) }} @else {{ $agency->address }} @endif
                                    </td>
                                    <td>
                                        @if(Auth::user()->demo == 1) {{ "********".substr($agency->bank_name, -2) }} @else {{ $agency->bank_name }} @endif,
                                        @if(Auth::user()->demo == 1) {{ "********".substr($agency->account_holder_name, -2) }} @else {{ $agency->account_holder_name }} @endif <br>
                                        @if(Auth::user()->demo == 1) {{ "********".substr($agency->account_number, -2) }} @else {{ $agency->account_number }} @endif,
                                        @if(Auth::user()->demo == 1) {{ "********".substr((isset($agency->AccountType->LangAccountTypeSingle->name) ? $agency->AccountType->LangAccountTypeSingle->name : 'Unknown'), -2) }} @else {{ (isset($agency->AccountType->LangAccountTypeSingle->name) ? $agency->AccountType->LangAccountTypeSingle->name : 'Unknown') }} @endif <br>
                                        @if(Auth::user()->demo == 1) {{ "********".substr($agency->online_transaction, -2) }} @else {{ $agency->online_transaction }} @endif
                                    </td>
                                    <td> {{ isset($agency->Driver) ? $agency->Driver->count() : 0 }} </td>
                                    <td>
                                        {{ isset($agency->wallet_balance) ? $agency->wallet_balance : 0 }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('merchant.driver-agency.transactions',[$agency->id])}}"><i class="fa fa-random"></i> </a>
                                    </td>
                                    <td>
                                        @if($agency->status == 1)
                                            <span class="badge badge-success">@lang("$string_file.active")</span>
                                        @else
                                            <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                        @endif
                                    </td>
                                    <td  style="width: 100px;float: left">
                                        {{--@if(Auth::user('merchant')->can('driver_agency'))--}}
                                            <span data-toggle="modal"
                                            data-target="#examplePositionCenter">
                                                <a href="#"
                                               onclick="AddWalletMoneyMod(this)"
                                               data-ID="{{ $agency->id }}"
                                               data-original-title="@lang("$string_file.add_money")"
                                               data-toggle="tooltip"

                                               data-placement="top"
                                               class="btn text-white btn-sm btn-success">
                                                <i class="icon fa-money"></i>
                                                </a></span>
                                            <a href="{{ route('merchant.driver-agency.wallet.show',$agency->id) }}"
                                               data-original-title="Wallet Transaction" data-toggle="tooltip"
                                               class="btn btn-sm menu-icon btn-primary btn_money action_btn">
                                                <i class="icon fa-window-maximize">
                                                         </i>
                                            </a>
                                            @if(Auth::user()->demo != 1)
                                                <a href="{{route('merchant.driver-agency.add',$agency->id)}}"
                                                   data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                                   class="btn btn-sm btn-warning">
                                                    <i class="wb-edit"></i>
                                                </a>
                                            @endif

                                                @if($agency->status == 1)
                                                    <a href="{{ route('driver-agency.status',['id'=>$agency->id,'status'=>2]) }}"
                                                       data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                                       data-placement="top"
                                                       class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn"> <i
                                                                class="fa fa-eye-slash"></i> </a>
                                                @else
                                                    <a href="{{ route('driver-agency.status',['id'=>$agency->id,'status'=>1]) }}"
                                                       data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                                       data-placement="top"
                                                       class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                                class="icon fa-eye"></i> </a>
                                                @endif

                                    </td>
                                </tr>
                                @php $sr++  @endphp
                            @endforeach
                            </tbody>
                        </table>
                    @include('merchant.shared.table-footer', ['table_data' => $driver_agency, 'data' => []])
{{--                    <div class="pagination1" style="float:right;">{{$driver_agency->links()}}</div>--}}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="examplePositionCenter" aria-hidden="true" aria-labelledby="examplePositionCenter"
         role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple modal-center">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33"><b>@lang("$string_file.add_money_in_wallet")</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>
                    @csrf
                    <div class="modal-body">
                        <label>@lang("$string_file.payment_method"): </label>
                        <div class="form-group">
                            <select class="form-control" name="payment_method" id="payment_method" required>
                                <option value="1">@lang("$string_file.cash")</option>
                                <option value="2">@lang("$string_file.no_cash")</option>
                            </select>
                        </div>

                        <label>@lang("$string_file.receipt_number"): </label>
                        <div class="form-group">
                            <input type="text" name="receipt_number" id="receipt_number"
                                   class="form-control" required>
                        </div>

                        <label>@lang("$string_file.amount"): </label>
                        <div class="form-group">
                            <input type="number" name="amount" id="amount" placeholder=""
                                   class="form-control" required min="1">
                            <input type="hidden" name="add_money_driver_agency_id" id="add_money_driver_agency_id">
                        </div>

                        <label>@lang("$string_file.description"): </label>
                        <div class="form-group">
                            <textarea class="form-control" id="title1" rows="3" name="description"
                                      placeholder=""></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="@lang("$string_file.close")">
                        <input type="button" id="add_money_button" class="btn btn-primary" value="@lang("$string_file.add")">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        function AddWalletMoneyMod(obj) {
            let ID = obj.getAttribute('data-ID');
            $(".modal-body #add_money_driver_agency_id").val(ID);
            $('#addWalletMoneyModel').modal('show');
        }
        $('#add_money_button').on('click', function () {
            $('#add_money_button').prop('disabled',true);
            $('#myLoader').removeClass('d-none');
            $('#myLoader').addClass('d-flex');
            var token = $('[name="_token"]').val();
            var payment_method = document.getElementById('payment_method').value;
            var receipt_number = document.getElementById('receipt_number').value;
            var amount = document.getElementById('amount').value;
            var desc = document.getElementById('title1').value;
            var driver_agency_id = document.getElementById('add_money_driver_agency_id').value;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                type: "POST",
                data: {payment_method_id : payment_method,receipt_number : receipt_number,amount : amount,description : desc,driver_agency_id : driver_agency_id},
                url: "{{ route('driver-agency.add-wallet') }}",
                success: function (data) {
                    console.log(data);
                    if(data.result == 1){
                        $('#myLoader').removeClass('d-flex');
                        $('#myLoader').addClass('d-none');
                        swal({
                            title: "@lang("$string_file.driver_agency_account")",
                            text: "@lang("$string_file.amount_added_successfully")",
                            icon: "success",
                            buttons: true,
                            dangerMode: true,
                        }).then((isConfirm) => {
                            window.location.href = "{{ route('merchant.driver-agency') }}";

                        });
                    }
                }, error: function (err) {
                    $('#myLoader').removeClass('d-flex');
                    $('#myLoader').addClass('d-none');
                }
            });
        });
    </script>
@endsection