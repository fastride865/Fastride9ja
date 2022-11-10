@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @if(session('promoadded'))
                <div class="alert dark alert-icon alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{session('promoadded')}}
                </div>
            @endif
            <div class="panel panel-bordered">
                <header class="panel-heading">
                        <div class="panel-actions">
                            <div class="btn-group float-right" style="margin:10px">
                                <a href="{{ route('promocode.index') }}">
                                    <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                    </button>
                                </a>
                            </div>
                        </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang('admin.message729')</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('promocode.update',$promocode->id) }}">
                        @csrf
                        {{method_field('PUT')}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang('admin.selectPricard')<span class="text-danger">*</span>
                                    </label>
                                    <select class="select2 form-control"
                                            name="price_card_ids[]"
                                            id="price_card_ids"
                                            data-placeholder="@lang("$string_file.service_area") "
                                            multiple data-plugin="select2">
                                        @foreach($pricecards as $pricecard)
                                            <option value="{{ $pricecard->id }}"
                                                    @if(in_array($pricecard->id, array_pluck($promocode->PriceCard,'id'))) selected @endif>{{ $pricecard->price_card_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('price_card_ids'))
                                        <label class="text-danger">{{ $errors->first('price_card_ids') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang("$string_file.promo_code")<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="promocode"
                                           name="promocode"
                                           placeholder="@lang('admin.message645')"
                                           value="{{ $promocode->promoCode }}" required>
                                    @if ($errors->has('promocode'))
                                        <label class="text-danger">{{ $errors->first('promocode') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang('admin.promo_code_value_type')<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control"
                                            name="promo_code_value_type"
                                            id="promo_code_value_type"
                                            onchange="changeText1(this.value)"
                                            required>
                                        <option id="1" value="1"
                                                @if($promocode->promo_code_value_type == 1) selected @endif>
                                            Flat Rate
                                        </option>
                                        <option id="2" value="2"
                                                @if($promocode->promo_code_value_type == 2) selected @endif>
                                            Percentage
                                        </option>
                                    </select>
                                    @if ($errors->has('promo_code_value_type'))
                                        <label class="text-danger">{{ $errors->first('promo_code_value_type') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.discount")<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="number" step=0.01 min=0 class="form-control"
                                           id="promo_code_value" name="promo_code_value"
                                           placeholder="@lang('admin.message646')"
                                           value="{{ $promocode->promo_code_value }}"
                                           required>
                                    @if ($errors->has('promo_code_value'))
                                        <label class="text-danger">{{ $errors->first('promo_code_value') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang('admin.applicable_for')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="applicable_for"
                                            id="applicable_for"
                                            onchange="UserType(this.value)"
                                            required>
                                        <option value="1"
                                                @if($promocode->applicable_for == 1) selected @endif>@lang("$string_file.all_users")</option>
                                        <option value="2"
                                                @if($promocode->applicable_for == 2) selected @endif>@lang("$string_file.new_user")</option>
                                        @if($config->corporate_admin == 1)
                                            <option value="3"
                                                    @if($promocode->applicable_for == 3) selected @endif>@lang("$string_file.corporate_user")</option>
                                        @endif
                                    </select>
                                    @if ($errors->has('applicable_for'))
                                        <label class="text-danger">{{ $errors->first('applicable_for') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang('admin.message649')<span
                                                class="text-danger">*</span>
                                    </label>
                                    <div class="icheckbox_minimal checked hover active"
                                         style="position: relative;">
                                        <input type="radio"
                                               id="promo_code_validity_permanent" value="1"
                                               name="promo_code_validity"
                                               onclick="javascript:yesnoCheck()"
                                               @if($promocode->promo_code_validity == 1) checked @endif>
                                        <label for="input-5"
                                               class="">@lang("$string_file.permanent")</label>
                                        <input type="radio" id="promo_code_validity_custom"
                                               value="2" name="promo_code_validity"
                                               onclick="javascript:yesnoCheck()"
                                               style="margin-left: 20px;"
                                               @if($promocode->promo_code_validity == 2) checked @endif>
                                        <label for="input-5"
                                               class="">@lang("$string_file.custom")</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group @if($promocode->promo_code_validity == 1) custom-hidden @endif"
                                     id="start-div">
                                    <label for="emailAddress5">
                                        @lang("$string_file.start")  @lang("$string_file.date") <span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control datepicker"
                                           id="datepicker" name="start_date"
                                           placeholder="@lang("$string_file.start")  @lang("$string_file.date") "
                                           value="{{ $promocode->start_date }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group @if($promocode->promo_code_validity == 1) custom-hidden @endif"
                                     id="end-div">
                                    <label for="emailAddress5">
                                        @lang("$string_file.end")  @lang("$string_file.date")<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control datepicker"
                                           id="datepicker-end" name="end_date"
                                           placeholder="@lang("$string_file.end")  @lang("$string_file.date")"
                                           value="{{ $promocode->end_date }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.limit")<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="promo_code_limit"
                                           name="promo_code_limit"
                                           placeholder="@lang('admin.message650')"
                                           value="{{ $promocode->promo_code_limit }}"
                                           required>
                                    @if ($errors->has('promo_code_limit'))
                                        <label class="text-danger">{{ $errors->first('promo_code_limit') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.limit_per_user")<span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="promo_code_limit_per_user"
                                           name="promo_code_limit_per_user"
                                           placeholder="@lang('admin.promo_code_limit_per_user')"
                                           value="{{ $promocode->promo_code_limit_per_user }}"
                                           required>
                                    @if ($errors->has('promo_code_limit_per_user'))
                                        <label class="text-danger">{{ $errors->first('promo_code_limit_per_user') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row @if($promocode->applicable_for != 3) custom-hidden @endif"
                             id="corporate_div">
                            <div class="col-md-4 corporate_inr">
                                <div class="form-group">
                                    <label for="location3">@lang('admin.corporate_name')</label>
                                    <select class="form-control" name="corporate_id"
                                            id="corporate_id">
                                        <option value="">--Select Corporate--</option>
                                        @foreach($corporates as $corporate)
                                            <option value="{{ $corporate->id }}">{{ $corporate->corporate_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('rider_type'))
                                        <label class="text-danger">{{ $errors->first('rider_type') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        @lang("$string_file.description")<span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control"
                                              id="promo_code_description"
                                              name="promo_code_description"
                                              placeholder="@lang('admin.message648')"
                                              required>{{ $promocode->promo_code_description }}</textarea>
                                    @if ($errors->has('promo_code_description'))
                                        <label class="text-danger">{{ $errors->first('promo_code_description') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang('admin.order_minimum_amount')
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="order_minimum_amount"
                                           name="order_minimum_amount"
                                           placeholder="@lang('admin.order_minimum_amount')"
                                           value="{{ $promocode->order_minimum_amount }}">
                                    @if ($errors->has('order_minimum_amount'))
                                        <label class="text-danger">{{ $errors->first('order_minimum_amount') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang('admin.promo_percentage_maximum_discount')
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="promo_percentage_maximum_discount"
                                           name="promo_percentage_maximum_discount"
                                           placeholder="@lang('admin.promo_percentage_maximum_discount')"
                                           value="{{ $promocode->promo_percentage_maximum_discount }}"
                                           @if($promocode->promo_code_value_type == 1) disabled @endif>
                                    @if($errors->has('promo_percentage_maximum_discount'))
                                        <label class="text-danger">{{ $errors->first('promo_percentage_maximum_discount') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        @lang('admin.promocodeParamter')
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="promo_code_name"
                                           name="promo_code_name"
                                           placeholder="@lang('admin.enterpromocodeParamter')"
                                           value="@if($promocode->LanguageSingle){{$promocode->LanguageSingle->promo_code_name}} @endif">
                                    @if ($errors->has('promo_code_name'))
                                        <label class="text-danger">{{ $errors->first('promo_code_name') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="fa fa-check-circle" onclick="return Validate()"></i>
                               @lang("$string_file.update")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function Validate() {
            var promo_code_value_type = document.getElementById('promo_code_value_type').value;
            var promo_code_value = document.getElementById('promo_code_value').value;
            if (promo_code_value_type == 2 && promo_code_value > 100) {
                alert('Enter Value Less Then 100');
                return false;
            }

        }

        function changeText(val) {
            let firstmsg = "";
            let firstmsg2 = "";
            if (val == 2) {
                $('#promo_code_value').attr("placeholder", firstmsg2);
            } else {
                $('#promo_code_value').attr("placeholder", firstmsg);
            }
        }

        function UserType(val) {
            if (val == "3") {
                document.getElementById('corporate_div').style.display = 'block';
            } else {
                document.getElementById('corporate_div').style.display = 'none';
            }
        }

        function yesnoCheck() {
            if (document.getElementById('promo_code_validity_permanent').checked) {
                document.getElementById('start-div').style.display = 'none';
                document.getElementById('end-div').style.display = 'none';
            } else {
                document.getElementById('start-div').style.display = 'block';
                document.getElementById('end-div').style.display = 'block';
            }
        }

        function getServices(val) {
            if (val != "") {
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "<?php echo route('merchant.area.services')  ?>",
                    data: {area_id: val},
                    success: function (data) {
                        $("#service_type_id").html(data);
                    }
                });
            }
        }

        function changeText1(val) {
            let firstmsg = "@lang('admin.message646')";
            let firstmsg2 = "@lang('admin.message647')";
            if (val == 2) {
                $('#promo_percentage_maximum_discount').prop("disabled", false);
                $('#promo_code_value').attr("placeholder", firstmsg2);
            } else {
                $('#promo_percentage_maximum_discount').prop("disabled", true);
                $('#promo_code_value').attr("placeholder", firstmsg);
            }
        }
    </script>
@endsection

