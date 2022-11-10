@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
           @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('hotels.create')}}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i title="@lang("$string_file.add_hotel")" class="wb-plus"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-building-o" aria-hidden="true"></i>
                        @lang("$string_file.hotel_panels")
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable" style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.logo")</th>
                            <th>@lang("$string_file.login_url")</th>
                            <th>@lang("$string_file.address")</th>
                            <th>@lang("$string_file.bank_details")</th>
                            <th>@lang("$string_file.wallet_money")</th>
                            <th>@lang("$string_file.transaction")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $hotels->firstItem() @endphp
                        @foreach($hotels as $hotel)
                            <tr>
                                <td>
                                    {{ $sr }}
                                </td>
                                <td>
                                    @if(Auth::user()->demo == 1) {{ "********".substr($hotel->name, -2) }} @else <b>{{ $hotel->name }}</b> @endif <br>
                                    @if(Auth::user()->demo == 1) {{ "********".substr($hotel->email, -2) }} @else <i>{{ $hotel->email }}</i> @endif <br>
                                    @if(Auth::user()->demo == 1) {{ "********".substr($hotel->phone, -2) }} @else {{ $hotel->phone }} @endif
                                </td>
                                <td>
                                    <img src="{{get_image($hotel->hotel_logo,'hotel_logo')}}" width="50px" height="50px">
                                </td>
                                <td>
                                    <a href="{{ config('app.url') }}hotel/admin/{{Auth::guard('merchant')->user()->alias_name}}/{{$hotel->alias}}/login"
                                       target="_blank" rel="noopener noreferrer" class="btn btn-icon btn-info btn_eye action_btn"><i class="icon fa-sign-in"></i></a>
                                </td>
                                <td>
                                    {{ $hotel->address }}
                                </td>
                                <td>
                                    @if(Auth::user()->demo == 1) {{ "********".substr($hotel->bank_name, -2) }} @else {{ $hotel->bank_name }} @endif,
                                    @if(Auth::user()->demo == 1) {{ "********".substr($hotel->account_holder_name, -2) }} @else {{ $hotel->account_holder_name }} @endif <br>
                                    @if(Auth::user()->demo == 1) {{ "********".substr($hotel->account_number, -2) }} @else {{ $hotel->account_number }} @endif,
                                    @if(Auth::user()->demo == 1) {{ "********".substr((isset($hotel->AccountType->LangAccountTypeSingle->name) ? $hotel->AccountType->LangAccountTypeSingle->name : 'Unknown'), -2) }} @else {{ (isset($hotel->AccountType->LangAccountTypeSingle->name) ? $hotel->AccountType->LangAccountTypeSingle->name : 'Unknown') }} @endif <br>
                                    @if(Auth::user()->demo == 1) {{ "********".substr($hotel->online_transaction, -2) }} @else {{ $hotel->online_transaction }} @endif
                                </td>
                                <td>
                                    {{ isset($hotel->wallet_money) ? $hotel->wallet_money : 0 }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('merchant.hotel.transactions',[$hotel->id])}}"><i class="fa fa-random"></i></a>
                                </td>
                                <td>{{ $hotel->created_at->toformatteddatestring() }}</td>
                                <td>
                                    @if($hotel->status == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
{{--                                <td><img src="{{ get_image($hotel->image,'hotel') }}"--}}
{{--                                         align="center" width="80px" height="80px"--}}
{{--                                         class="img-radius"--}}
{{--                                         alt="Image">--}}
{{--                                </td>--}}

                                <td style="width: 100px;float: left">
                                    <span data-toggle="modal" data-target="#examplePositionCenter">
                                    <a href="#" onclick="AddWalletMoneyMod(this)"
                                       data-ID="{{ $hotel->id }}" data-original-title="@lang("$string_file.add_money")"
                                       data-toggle="tooltip" data-placement="top"
                                       class="btn text-white btn-sm btn-success">
                                        <i class="icon fa-money"></i> </a></span>
                                    <a href="{{ route('merchant.hotel.wallet.show',$hotel->id) }}"
                                       data-original-title="Wallet Transaction" data-toggle="tooltip"
                                       class="btn btn-sm menu-icon btn-primary btn_money action_btn">
                                        <span class="icon fa-window-maximize" title="@lang("$string_file.wallet_transaction")"></span></a>
                                    @if(Auth::user()->demo != 1)
                                        <a href="{{route('hotels.create',$hotel->id)}}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           class="btn btn-sm btn-warning"><i class="icon wb-edit"></i>
                                        </a>
                                    @endif
                                    @if($hotel->status == 1)
                                        <a href="{{ route('merchant.hotel.active-deactive',['id'=>$hotel->id,'status'=>2]) }}"
                                           data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip" data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                            <i class="fa fa-eye-slash"></i> </a>
                                    @else
                                        <a href="{{ route('merchant.hotel.active-deactive',['id'=>$hotel->id,'status'=>1]) }}"
                                           data-original-title="@lang("$string_file.active")" data-toggle="tooltip" data-placement="top"
                                           class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                            <i class="fa fa-eye"></i> </a>
                                    @endif
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $hotels, 'data' => []])
{{--                    <div class="pagination1 float-right">{{$hotels->links()}}</div>--}}
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
                            <input type="text" name="receipt_number" id="receipt_number" placeholder=""
                                   class="form-control" required>
                        </div>
                        <label>@lang("$string_file.amount"): </label>
                        <div class="form-group">
                            <input type="number" name="amount" id="amount" placeholder=""
                                   class="form-control" required min="1">
                            <input type="hidden" name="add_money_hotel_id" id="add_money_hotel_id">
                        </div>
                        <label>@lang("$string_file.description"): </label>
                        <div class="form-group">
                            <textarea class="form-control" id="title1" rows="3" name="description" placeholder=""></textarea>
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
            $(".modal-body #add_money_hotel_id").val(ID);
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
            var hotel_id = document.getElementById('add_money_hotel_id').value;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                type: "POST",
                data: {payment_method_id : payment_method,receipt_number : receipt_number,amount : amount,description : desc,hotel_id : hotel_id},
                url: "{{ route('hotel.AddMoney') }}",
                success: function (data) {
                    console.log(data);
                    if(data.result == 1){
                        $('#myLoader').removeClass('d-flex');
                        $('#myLoader').addClass('d-none');
                        swal({
                            title: "@lang("$string_file.hotel_account")",
                            text: "@lang("$string_file.amount_added_successfully")",
                            icon: "success",
                            buttons: true,
                            dangerMode: true,
                        }).then((isConfirm) => {
                            if (isConfirm) {
                                window.location.href = "{{ route('hotels.index') }}";
                            } else {
                                window.location.href = "{{ route('hotels.index') }}";
                            }
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