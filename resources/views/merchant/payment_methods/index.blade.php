@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        @lang("$string_file.payment_method_management")</h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.type")</th>
                            <th>@lang("$string_file.name")</th>
                            <th>@lang("$string_file.icon")</th>
                            @if(Auth::user('merchant')->can('edit_payment_methods'))
                                <th>@lang("$string_file.action")</th>
                            @endif
                        </tr>
                        </thead>
                        <tfoot></tfoot>
                        <tbody>
                        @php $sr = 1 @endphp
                        @foreach($payment as $value)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $value->payment_method }}</td>
                                <td> @if(!empty($value->PaymentMethodTranslation)) {{ $value->PaymentMethodTranslation->name }} @else
                                        ---  @endif </td>
                                <td>
                                    @php
                                        $icon = get_image($value->payment_icon,'payment_icon',$merchant->id,false);
                                        $merchant_payment = $value->Merchant->where('id',$merchant->id);
                                        $merchant_payment = collect($merchant_payment->values());
                                        if(isset($merchant_payment) && !empty($merchant_payment[0]->pivot['icon']))
                                        {
                                            $icon = get_image($merchant_payment[0]->pivot['icon'],'p_icon',$merchant->id);
                                        }
                                    @endphp
                                    <img src="{{$icon}}" height="50" width="50">
                                </td>
                                @if(Auth::user('merchant')->can('edit_payment_methods'))
                                    <td>
                                        <a href="{{ route('merchant.paymentMethod.edit',$value->id) }}"
                                           data-original-title="@lang("$string_file.edit")" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn">
                                            <i class="fa fa-edit"></i> </a>
                                    </td>
                                @endif
                                @php $sr++ @endphp
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
