@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($redirect_route))
                            <div class="btn-group float-md-right">
                                <a href="{{ $redirect_route }}">
                                    <button type="button" class="btn btn-icon btn-success mr-1" style="margin:10px  "><i class="wb-reply"></i></button>
                                </a>
                            </div>
                        @endif
                    </div>
                    <h3 class="panel-title"><i class="fa-user" aria-hidden="true"></i>
                        @lang("$string_file.order_details") # {{$order->merchant_order_id}}
                    </h3>
                </header>
                @include('common-view.order-detail')
            </div>
        </div>
    </div>
@endsection