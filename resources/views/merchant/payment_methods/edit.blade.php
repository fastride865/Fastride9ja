@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->edit_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <a href="{{ route('merchant.paymentMethod.index') }}">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.payment_method") </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="{{ route('merchant.paymentMethod.update',$payment->id) }}">
                        {{method_field('PUT')}}
                        @php $required = false; @endphp
                        @csrf
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            @lang("$string_file.name")<span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="payment_name"
                                               name="payment_name"
                                               value="@if(!empty($payment->MethodName($merchant->id))) {{ $payment->MethodName($merchant->id) }} @else {{ $payment->payment_method }} @endif"
                                               placeholder="" required>
                                        @if ($errors->has('payment_name'))
                                            <label class="danger">{{ $errors->first('payment_name') }}</label>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="" for="">@lang("$string_file.image")
                                            <span class="text-danger">*</span>
                                            @if(!empty($icon))
                                                <a href="{{$icon}}"
                                                   target="_blank">@lang("$string_file.view")</a>
                                            @endif
                                        </label>
                                        <input type="file" class="form-control" id="image" name="p_icon_image"
                                                {{$required}}/>
                                        @if ($errors->has('image'))
                                            <label class="text-danger">{{ $errors->first('image') }}</label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-actions float-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> @lang("$string_file.update")
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'])
@endsection
