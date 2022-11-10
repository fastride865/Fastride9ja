@extends('merchant.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content container-fluid">
            @include("merchant.shared.errors-and-messages")
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        @if(!empty($info_setting) && $info_setting->view_text != "")
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        @endif
                        <button type="button" title="@lang("$string_file.add_product")"
                                class="btn btn-icon btn-success float-right" style="margin:10px" data-toggle="modal"
                                data-target="#exampleModal">
                            <i class="wb-plus"></i>
                        </button>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-product-hunt" aria-hidden="true"></i>
                        @lang("$string_file.delivery_product")
                    </h3>
                </header>
                <div class="panel-body">
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable"
                           style="width:100%"
                           cellspacing="0">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.product_name")</th>
                            <th>@lang("$string_file.weight_unit")</th>
                            <th>@lang("$string_file.created_at")</th>
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = $delivery_products->firstItem() @endphp
                        @foreach($delivery_products as $delivery_product)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $delivery_product->ProductName }}</td>
                                <td>{{$delivery_product->WeightUnit->WeightUnitName}}</td>
                                <td>{!! convertTimeToUSERzone($delivery_product->created_at, null,null,$delivery_product->Merchant, 2) !!}</td>
                                <td>
                                    @if($delivery_product->status == 1)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Deactivate</span>
                                    @endif
                                </td>
                                <td>
                                    @if($delivery_product->status == 1)
                                        <a class="btn btn-sm btn-danger"
                                           href="{{route('delivery_product.change_status',[$delivery_product->id,2])}}"><i
                                                    class="fa fa-eye-slash"></i></a>
                                    @else
                                        <a class="btn btn-sm btn-success"
                                           href="{{route('delivery_product.change_status',[$delivery_product->id,1])}}"><i
                                                    class="fa fa-eye"></i></a>
                                    @endif
                                    <a href="{!! route('delivery_product.edit',$delivery_product->id) !!}"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                        <i class="wb-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $delivery_products, 'data' => $data])
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="exampleModalLabel">@lang("$string_file.add_product")</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('delivery_product.store')}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <span>@lang("$string_file.product_name")</span>
                                    <input type="text" name="product_name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <span>@lang("$string_file.weight_unit")</span>
                                    <select name="weight_unit" class="form-control" required>
                                        <option value="">@lang("$string_file.select")</option>
                                        @foreach($weight_units as $weight_unit)
                                            <option value="{{$weight_unit->id}}">{{$weight_unit->WeightUnitName}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">@lang("$string_file.reset")</button>
                        <button type="submit" class="btn btn-success">@lang("$string_file.save")</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection

