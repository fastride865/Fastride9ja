@extends('merchant.layouts.main')
@section('content')
    <div class="container-fluid">
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row">
                    @if(session('couponcode'))
                        <div class="col-md-12 alert alert-icon-right alert-info alert-dismissible mb-2" role="alert">
                            <span class="alert-icon"><i class="fa fa-info"></i></span>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <strong>@lang('admin.couponcode') Updated</strong>
                        </div>
                    @endif
                </div>
                <div class="content-body">
                    <section id="validation">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="content-header-title mb-0 d-inline-block">@lang("$string_file.edit") @lang('admin.couponcode')</h3>
                                        <div class="btn-group float-md-right">
                                            <a href="{{ route('walletpromocode.index') }}">
                                                <button type="button" class="btn btn-icon btn-success mr-1"><i class="fa fa-reply"></i>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-content collapse show">
                                        <div class="card-body">
                                            <form method="POST" class="steps-validation wizard-notification"
                                                  enctype="multipart/form-data" action="{{ route('walletpromocode.update',$wallet_code->id) }}">
                                                @csrf
                                                {{method_field('PUT')}}
                                                <fieldset>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="firstName3">
                                                                    @lang("$string_file.service_area")<span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-control" name="country" id="country" required>
                                                                    <option value="" disabled selected>--@lang("$string_file.service_area")--</option>
                                                                    @foreach($countries as $contry)
                                                                        <option id="country" value="{{ $contry->id }}" @if($wallet_code->country_id == $contry->id) selected @endif>{{ $contry->CountryName}}</option>
                                                                    @endforeach
                                                                </select>
                                                                @if ($errors->has('country'))
                                                                    <label class="danger">{{ $errors->first('country') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>"
                                               placeholder="Driver Footer Image">
                                        @if ($errors->has('driver_footer_image'))
                                            <label class="text-danger">{{
                                                    </div>
                                                    <div class="row">
"
                                               placeholder="Driver Footer Image">
                                        @if ($errors->has('driver_footer_image'))
                                            <label class="text-danger">{{
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="lastName3">
                                                                    @lang('admin.couponcode')<span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="couponcode"
                                                                       name="couponcode"
                                                                       placeholder="@lang('admin.couponcode')"
                                                                       value="{{$wallet_code->coupon_code}}" required>
                                                                @if ($errors->has('couponcode'))
                                                                    <label class="danger">{{ $errors->first('couponcode') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group"><br>
                                                                <button style="border-radius: 5px;margin-top:11px;background-color: darkcyan;" type="button" class="btn-primary" onclick="return codegenerator();">Code Generator</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="emailAddress5">
                                                                    @lang("$string_file.amount")<span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                       id="amount"
                                                                       name="amount"
                                                                       placeholder="@lang("$string_file.amount")"
                                                                       value="{{$wallet_code->amount}}" required>
                                                                @if ($errors->has('amount'))
                                                                    <label class="danger">{{ $errors->first('amount') }}</label>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                </fieldset>
                                                <div class="form-actions right" style="margin-bottom: 3%">
                                                    <button style="margin-right: 500px;"  onclick="return Validate()" type="submit" class="btn btn-primary float-right">
                                                        <i class="fa fa-check-circle"></i>
                                                        @lang("$string_file.update")
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>

    <script>
        function Validate() {
            var country = document.getElementById('country').value;
            var couponcode = document.getElementById('couponcode').value;
            var amount = document.getElementById('amount').value;
            if (country == "") {
                alert('Please Choose Country');
                return false;
            }else if (couponcode == "") {
                alert('Please Enter Coupon Code');
                return false;
            }else if (amount == "") {
                alert('Please Enter Amount Of Coupon Code');
                return false;
            }

        }

        function codegenerator() {
            var chars = "ABCDEFGHIJKLMNOPQRSTUVWXTZ";
            var string_length = 8;
            var randomstring = '';
            for (var i=0; i<string_length; i++) {
                var rnum = Math.floor(Math.random() * chars.length);
                randomstring += chars.substring(rnum,rnum+1);
            }
            document.getElementById('couponcode').value = randomstring;

        }
    </script>
@endsection
