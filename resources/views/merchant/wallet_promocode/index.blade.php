@extends('merchant.layouts.main')
@section('content')
    <div class="container-fluid">
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="card shadow ">
                    <div class="card-header py-3 ">

                        <h3 class="content-header-title mb-0 d-inline-block"><i
                                    class="fas fa-money-check-alt"></i> Wallet @lang('admin.couponcode')</h3>
                        @if(!Auth::user('merchant')->can('create_wallet_promo_code'))
                            <div class="btn-group float-md-right">
                                <div class="heading-elements">
                                    <a title="Add Bulk Coupon Code" href="#">
                                        <button href="#" type="button" class="btn btn-icon btn-success" data-toggle="modal" data-target="#myModal">Bulk Coupon Code</button>
                                    </a>
                                    <a title="@lang("$string_file.add") @lang('admin.couponcode')"
                                       href="{{route('walletpromocode.create')}}">
                                        <button class="btn btn-icon btn-success mr-1"><i class="fa fa-plus"></i>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-header py-3">
                        <form method="post" action="">
                            @csrf
                            <div class="table_search row">

                                <div class="col-md-4 col-xs-12 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="code"
                                               placeholder="@lang('admin.couponcode')"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>

                                <div class="col-sm-2  col-xs-12 form-group ">
                                    <button class="btn btn-primary" type="submit" name="seabt12"><i
                                                class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table display nowrap table-striped table-bordered" id="dataTable" width="100%"
                                   cellspacing="0">
                                <thead>
                                <th>@lang("$string_file.sn")</th>
                                <th>@lang("$string_file.service_area")</th>
                                <th>@lang('admin.couponcode')</th>
                                <th>@lang("$string_file.amount")</th>
                                <th>@lang('admin.used_status')</th>
                                <th>@lang("$string_file.action")</th>
                                </thead>
                                <tbody>
                                @php $sr = 1; @endphp
                                @foreach($promocodes as $promocode)
                                    <tr>
                                        <td>{{ $sr }}</td>
                                        <td>{{ $promocode->Country->CountryName }}</td>
                                        <td>{{ $promocode->coupon_code }}</td>
                                        <td>{{ $promocode->amount }}    </td>
                                        <td>
                                            @if($promocode->used_status == 0)
                                                <button type="button" class="btn btn-info">UnUsed</button>
                                            @else
                                                <button type="button" class="btn btn-danger">Used</button>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('walletpromocode.edit',$promocode->id) }}"
                                               data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                        class="fa fa-edit"></i> </a>
                                        </td>
                                    </tr>
                                    @php $sr++  @endphp
                                @endforeach
                                </tbody>
                            </table>
                            @include('merchant.shared.table-footer', ['table_data' => $promocodes, 'data' => []])
                        </div>
{{--                        <div class="col-sm-12">--}}
{{--                            <div class="pagination1">{{ $promocodes->links() }}</div>--}}
{{--                        </div>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal" role="dialog">
<div class="modal-dialog">

<!-- Modal content-->
    <center>
        <div class="modal-content" style="margin-top: 98px;">
            <div class="modal-header">
                <h4 class="modal-title" style="margin-left: 92px;font-size: xx-large;">Bulk Coupon Code</h4>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times-circle"></i></button>
            </div>
            <form method="POST" class="steps-validation wizard-notification"
                enctype="multipart/form-data" action="{{ route('walletpromocode.bulk_code') }}">
                @csrf
                <fieldset><br>
                <div class="col-md-10">
                    <div class="form-group">
                        <label for="firstName3" style="margin-right: 307px;font-size: larger;">
                        @lang("$string_file.service_area")<span class="text-danger">*</span>
                        </label>
                        <select class="form-control" name="country" id="country" required>
                        <option value="" disabled selected>--@lang("$string_file.service_area")--</option>
                        @foreach($countries as $contry)
                        <option id="country"
                        value="{{ $contry->id }}">{{ $contry->CountryName}}</option>
                        @endforeach
                        </select>
                        @if ($errors->has('country'))
                        <label class="danger">{{ $errors->first('country') }}</label>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-10">
                    <div class="form-group">
                        <label for="lastName3" style="margin-right: 180px;font-size: larger;">
                        @lang('admin.coupon_code_quantity')<span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="coupon_code_quantity"
                        name="coupon_code_quantity"
                        placeholder="@lang('admin.coupon_code_quantity')"
                        value="{{ old('coupon_code_quantity') }}" required>
                        @if ($errors->has('coupon_code_quantity'))
                        <label class="danger">{{ $errors->first('coupon_code_quantity') }}</label>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-10">
                    <div class="form-group">
                        <label for="emailAddress5" style="margin-right: 307px;font-size: larger;">
                        @lang("$string_file.amount")<span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control"
                        id="amount"
                        name="amount"
                        placeholder="@lang("$string_file.amount")"
                        value="{{ old('amount') }}" required>
                        @if ($errors->has('amount'))
                        <label class="danger">{{ $errors->first('amount') }}</label>
                        @endif<br>
                        <label for="emailAddress5">
                            Note: One Recharge Coupon Can Be Used Only One Time...
                        </label>
                    </div>
                </div>
                
                </fieldset>
                <div class="form-actions right" style="margin-bottom: 3%">
                    <button style="margin-left: 169px;width: 152px;" onclick="return Validate()" type="submit" class="btn btn-primary float-left">
                    <i class="fa fa-check-circle"></i>
                    Save
                    </button>
                </div><br><br>
            </form>
        </div>
    </center>

</div>
</div>
@endsection