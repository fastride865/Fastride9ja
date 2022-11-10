@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="{{ route('delivery_product.index') }}">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px">
                                    <i class="wb-reply"></i>
                                </button>
                            </a>
                            @if(!empty($info_setting) && $info_setting->edit_text != "")
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        @lang("$string_file.delivery_product")
                        (@lang("$string_file.you_are_adding_in") {{ strtoupper(Config::get('app.locale')) }})
                    </h3>
                </div>
                @php $id = isset($delivery_product->id) ? $delivery_product->id : NULL; @endphp
                <div class="panel-body container-fluid">
                    <form action="{{route('delivery_product.update',$id)}}" method="POST">
                        {{method_field('PUT')}}
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        @lang("$string_file.product_name")
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="vehicle_make"
                                           name="product_name"
                                           value="@if(!empty($delivery_product->ProductName))  {{$delivery_product->ProductName}} @endif"
                                           placeholder=""
                                           required>
                                    @if ($errors->has('name'))
                                        <label class="text-danger">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <span>@lang("$string_file.weight_unit")</span>
                                    <select name="weight_unit" class="form-control" required>
                                        <option value="">@lang("$string_file.select")</option>
                                        @foreach($weight_units as $weight_unit)
                                            <option value="{{$weight_unit->id}}"
                                                    @if($delivery_product->weight_unit_id == $weight_unit->id) selected @endif>{{$weight_unit->WeightUnitName}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            @if(!$is_demo)
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check-circle"></i> @lang("$string_file.update")
                                </button>
                            @else
                                <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'])
@endsection

