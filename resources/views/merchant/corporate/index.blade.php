@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(Auth::user('merchant')->can('corporate'))
                            <a href="{{route('corporate.create')}}">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus" title="@lang("$string_file.add_corporate")"></i>
                                </button>
                            </a>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="fa fa-building" aria-hidden="true"></i>
                    @lang("$string_file.corporate_panels")
                </header>
                <div class="panel-body container-fluid">
                    <table class="display nowrap table table-hover table-stripedw-full" id="customDataTable" style="width:100%">
                        <thead>
                        <tr>
{{--                            <th>@lang("$string_file.sn")</th>--}}
{{--                            <th>@lang('admin.corporate_logo')</th>--}}
{{--                            <th>@lang('admin.corporate_name')</th>--}}
{{--                            <th>@lang('admin.corporateemail')</th>--}}
{{--                            <th>@lang("$string_file.phone")</th>--}}
{{--                            <th>@lang("$string_file.url")</th>--}}
{{--                            <th>@lang("$string_file.service_area")</th>--}}
{{--                            <th>@lang('admin.corporate_address')</th>--}}
{{--                            <th>@lang("$string_file.wallet_money")</th>--}}
{{--                            <th>@lang("$string_file.status")</th>--}}
{{--                            @if(Auth::user('merchant')->can('edit_corporate'))--}}
{{--                                <th>@lang("$string_file.action")</th>--}}
{{--                            @endif--}}
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.name")</th>
{{--                            <th>@lang("$string_file.email")</th>--}}
{{--                            <th>@lang("$string_file.phone")</th>--}}

                            <th>@lang("$string_file.country")</th>
                            <th>@lang("$string_file.address")</th>

                            <th>@lang("$string_file.wallet_money")</th>
{{--                            <th>@lang("$string_file.transaction")</th>--}}
{{--                            <th>@lang("$string_file.created_at")</th>--}}
                            <th>@lang("$string_file.logo")</th>
                            <th>@lang("$string_file.login_url")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $corporates->firstItem() @endphp
                        @foreach($corporates as $corporate)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $corporate->corporate_name }}<br>
                                {{ $corporate->email }}<br>
                                {{ $corporate->corporate_phone }}</td>

                                @if($corporate->country)
                                    <td>{{ $corporate->country->CountryName }}</td>
                                @else
                                    <td> ----</td>
                                @endif
                                <td><span class="long_text">{{ $corporate->corporate_address }} </span></td>
                                <td>
                                    @if($corporate->wallet_balance)
                                        <a href="{{ route('corporate.wallet.show',$corporate->id) }}">{{ $corporate->wallet_balance }}</a>
                                    @else
                                        ----
                                    @endif
                                </td>
                                <td>
                                    <img src="{{get_image($corporate->corporate_logo,'corporate_logo')}}"
                                         width="50px" height="50px">
                                </td>
                                <td>
                                    <a href="{{ config('app.url') }}corporate/admin/{{$merchant->alias_name}}/{{ $corporate->alias_name }}/login"
                                       target="_blank" class="btn btn-icon btn-info btn_eye action_btn"><i class="icon fa-sign-in"></i></a></td>
                                <td>
                                    @if($corporate->status == 1)
                                        <span class="badge badge-success">@lang("$string_file.active")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.inactive")</span>
                                    @endif
                                </td>
                                <td style="width: 100px;float: left">
                                    @if(Auth::user('merchant')->can('corporate'))
                                        <a href="{{route('corporate.create',$corporate->id)}}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           class="btn btn-sm btn-warning">
                                            <i class="wb-edit"></i>
                                        </a>

                                        <span data-target="#addMoneyModel"
                                              data-toggle="modal"
                                              id="{{ $corporate->id }}"><a
                                                    href="#"
                                                    data-original-title="Wallet Recharge" data-toggle="tooltip"
                                                    id="{{ $corporate->id }}"
                                                    class="btn btn-sm menu-icon btn-success btn_money action_btn">
                                                <i class="icon fa-money"></i>
                                            </a></span>
                                        <a href="{{ route('corporate.wallet.show',$corporate->id) }}"
                                           data-original-title="Wallet Transaction" data-toggle="tooltip"
                                           class="btn btn-sm menu-icon btn-primary btn_money action_btn">
                                            <span class="icon fa-window-maximize" title="@lang("$string_file.wallet_transaction")"></span></a>

                                        @if($corporate->status == 1)
                                            <a href="{{ route('merchant.corporate.status',['id'=>$corporate->id,'status'=>2]) }}"
                                               data-original-title="@lang("$string_file.inactive")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                                <i
                                                        class="fa fa-eye-slash"></i> </a>
                                        @else
                                            <a href="{{ route('merchant.corporate.status',['id'=>$corporate->id,'status'=>1]) }}"
                                               data-original-title="@lang("$string_file.active")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                        class="fa fa-eye"></i> </a>
                                        @endif
                                    @endif

                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $corporates, 'data' => []])
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addMoneyModel" aria-hidden="true" aria-labelledby="addMoneyModel"
         role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple modal-center">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33"><b>@lang("$string_file.add_money")</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('corporate.AddMoney') }}" method="post">
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
                            <input type="text" name="receipt_number" placeholder=""
                                   class="form-control" required>
                        </div>
                        <label>@lang("$string_file.amount"): </label>
                        <div class="form-group">
                            <input type="number" name="amount" placeholder=""
                                   class="form-control" required min="1">
                            <input type="hidden" name="add_money_driver_id" id="add_money_driver_id">
                        </div>

                        <label>@lang("$string_file.description"): </label>
                        <div class="form-group">
                            <textarea class="form-control" id="title1" rows="3" name="description"
                                      placeholder=""></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="@lang("$string_file.close")">
                        <input type="submit" id="sub" class="btn btn-primary" value="@lang("$string_file.add")">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $('#sub').on('click', function () {
            $('#myLoader').removeClass('d-none');
            $('#myLoader').addClass('d-flex');
        });
    </script>
@endsection