@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include("merchant.shared.errors-and-messages")
{{--            @if(session('success'))--}}
{{--                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">--}}
{{--                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--                        <span aria-hidden="true">×</span>--}}
{{--                    </button>--}}
{{--                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('success') }}--}}
{{--                </div>--}}
{{--            @endif--}}
{{--            @if(session('error'))--}}
{{--                <div class="alert dark alert-icon alert-info alert-dismissible" role="alert">--}}
{{--                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--                        <span aria-hidden="true">×</span>--}}
{{--                    </button>--}}
{{--                    <i class="icon wb-info" aria-hidden="true"></i>{{ session('error') }}--}}
{{--                </div>--}}
{{--            @endif--}}
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('business-segment.product.index')}}">
                            <button type="button" title="@lang("$string_file.product")"
                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class=" icon wb-shopping-cart" aria-hidden="true"></i>@lang("$string_file.product_inventory")</h3>
                </header>
                <div class="panel-body">
                    <div class="example-wrap">
                        <form class="form-inline"
                              type="post" action="{{ route('business-segment.product.inventory.index') }}"
                        >
                            @csrf
                            <div class="form-group">
                                <label class="sr-only" for="inputUnlabelUsername">@lang("$string_file.product")</label>
                                {!! Form::select('id',add_blank_option($product_list,trans("$string_file.select_product")),old('id'),['id'=>'id','class'=>'form-control']) !!}
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="inputUnlabelPassword">@lang("$string_file.product_variant") </label>
                                {!! Form::select('product_variant_cid',add_blank_option($product_variant_list,trans("$string_file.select_variant")),old('id'),['id'=>'product_variant_id','class'=>'form-control']) !!}
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-outline">@lang("$string_file.search")</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="panel">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="exampleFooEditing" class="table table-bordered table-hover toggle-circle"
                               data-paging="true" data-filtering="false" data-sorting="true" data-dropdown-toggle="false"
                               data-editing-allow-delete="false" data-editing-allow-add="false">
                            <thead>
                            <tr>
                                <th data-name="id" data-type="number"
                                    data-breakpoints="xs">@lang("$string_file.ride_id")</th>
                                <th data-name="product">@lang("$string_file.product")</th>
                                <th data-name="product_variant">@lang("$string_file.product_variant") </th>
                                <th data-name="current_stock" data-type="string">@lang("$string_file.current_stock") </th>
                                <th data-name="product_cost" data-type="number">@lang("$string_file.product_cost")</th>
                                <th data-name="product_selling_price"
                                    data-type="number">@lang("$string_file.selling_price")</th>
                                <th data-name="action" data-visible="false"
                                    data-filterable="false">@lang("$string_file.action")
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $sr = 1 @endphp
                            @if(!empty($product_variants))
                                @foreach($product_variants as $product_variant)
                                    <tr>
                                        <td>{{$product_variant->id}}</td>
                                        <td>{{$product_variant->Product->Name($product_variant->Product->merchant_id).' ('.$product_variant->Product->sku_id.')'}}</td>
                                        <td>{{$product_variant->product_title.' ('.$product_variant->sku_id.')'}}</td>
                                        <td>{{ isset($product_variant->ProductInventory->current_stock) ? $product_variant->ProductInventory->current_stock : trans("$string_file.no").' '.trans($string_file.".stock")}}</td>
                                        <td>{{ isset($product_variant->ProductInventory->product_cost) ? $product_variant->ProductInventory->product_cost : 0}}</td>
                                        <td>{{isset($product_variant->ProductInventory->product_selling_price) ? $product_variant->ProductInventory->product_selling_price : $product_variant->product_price}}</td>
                                        <td>
                                            <div class="badge badge-table badge-success">Paid</div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        <!-- End Panel Editing Rows -->
                        <div class="modal fade" id="editor-modal" tabindex="-1" role="dialog"
                             aria-labelledby="editor-title">
                            <div class="modal-dialog modal-simple" role="document">
                                <form class="modal-content form-horizontal" id="editor" type="post">
                                    @csrf
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title"
                                            id="editor-title">
                                            @lang("$string_file.edit_product_inventory")
                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <input type="number" id="id" name="id" class="hidden" style="display:none;"/>
                                        <dl class="dl-horizontal row">
                                            <dt class="col-sm-3">@lang("$string_file.product") :</dt>
                                            <dd class="col-sm-3" id="product"></dd>
                                            <dt class="col-sm-3">@lang("$string_file.product_variant")  :</dt>
                                            <dd class="col-sm-3" id="product_variant"></dd>
                                        </dl>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="price"
                                                           class=" control-label">@lang("$string_file.current_stock") </label>
                                                    <input type="number" class="form-control" id="current_stock"
                                                           name="current_stock"
                                                           placeholder="@lang("$string_file.current_stock") "
                                                           required readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="price"
                                                           class=" control-label"> @lang("$string_file.new_stock")</label>
                                                    <input type="number" class="form-control" id="new_stock"
                                                           name="new_stock"
                                                           placeholder="" value=""
                                                           required>
                                                    <input type="hidden" id="last_new_stock" value="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="price"
                                                           class=" control-label"> @lang("$string_file.updated_current_stock")</label>
                                                    <input type="number" class="form-control" id="updated_current_stock"
                                                           name="updated_current_stock"
                                                           placeholder=""
                                                           required readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="discount"
                                                           class=" control-label">@lang("$string_file.product_cost")</label>
                                                        <input type="number" class="form-control" id="product_cost" step="any"
                                                               name="product_cost"
                                                               placeholder="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="discount"
                                                           class=" control-label">@lang("$string_file.selling_price")</label>
                                                        <input type="number" class="form-control" id="product_selling_price"
                                                               name="product_selling_price" step="any"
                                                               placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                     @if(!$is_demo)
                                        <button type="submit" class="btn btn-primary">@lang("$string_file.save") </button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang("$string_file.cancel") </button>
                                        @else
                                            <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('global/vendor/footable/footable.core.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/examples/css/tables/footable.css') }}">
@endsection
@section('js')
    <script src="{{ asset('global/vendor/footable/footable.min.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).ready(function () {
            var $modal = $('#editor-modal'),
                $editor = $('#editor'),
                $editorTitle = $('#editor-title'),
                ft = FooTable.init('#exampleFooEditing', {
                    editing: {
                        enabled: true,
                        editRow: function (row) {
                            var values = row.val();
                            $editor.find('#id').val(values.id);
                            $editor.find('#product').text(values.product);
                            $editor.find('#product_variant').text(values.product_variant);
                            if(values.current_stock > 0){
                                $editor.find('#current_stock').val(values.current_stock);
                                $editor.find('#updated_current_stock').val(values.current_stock);
                            }else{
                                $editor.find('#current_stock').val(0);
                                $editor.find('#updated_current_stock').val(0);
                            }
                            $editor.find('#product_cost').val(values.product_cost);
                            $editor.find('#product_selling_price').val(values.product_selling_price);

                            $modal.data('row', row);
                            $editorTitle.text('Edit row #' + values.id);
                            $modal.modal('show');
                        },
                    }
                }),
                uid = 10;
            $editor.on('submit', function (e) {
                if (this.checkValidity && !this.checkValidity()) return;
                e.preventDefault();
                $.ajax({
                    url: "{{ route('business-segment.product.inventory.save') }}",
                    data: $editor.serialize(),
                    type: "POST",
                }).done(function (result) {
                    if (typeof (result.success) != "undefined" && result.success !== null) {
                        var row = $modal.data('row'),
                            values = {
                                id: $editor.find('#id').val(),
                                product: $editor.find('#product').val(),
                                product_variant: $editor.find('#product_variant').val(),
                                current_stock: $editor.find('#current_stock').val(),
                                product_cost: $editor.find('#product_cost').val(),
                                product_selling_price: $editor.find('#product_selling_price').val(),
                                new_stock: $editor.find('#new_stock').val(),
                            };
                        if (row instanceof FooTable.Row) {
                            row.val(values);
                        } else {
                            values.id = uid++;
                            ft.rows.add(values);
                        }
                        $modal.modal('hide');
                        alert('success : ' + result.success);
                        window.location.href = result.route;

                    } else {
                        alert('error : ' + result.error);
                    }
                });
            });
            $('#new_stock').change(function(){
                if($('#new_stock').val() > 0){
                    var updated_current_stock = (isNaN(parseInt($('#updated_current_stock').val()))) ? 0 : parseInt($('#updated_current_stock').val());
                    var new_stock = (isNaN(parseInt($('#new_stock').val()))) ? 0 : parseInt($('#new_stock').val());
                    var last_new_stock = (isNaN(parseInt($('#last_new_stock').val()))) ? 0 : parseInt($('#last_new_stock').val());
                    $('#last_new_stock').val(new_stock);
                    var total_stock = (updated_current_stock - last_new_stock) + new_stock;
                    $('#updated_current_stock').val(total_stock);
                }else{
                    var last_new_stock = (isNaN(parseInt($('#last_new_stock').val()))) ? 0 : parseInt($('#last_new_stock').val());
                    var updated_current_stock = (isNaN(parseInt($('#updated_current_stock').val()))) ? 0 : parseInt($('#updated_current_stock').val());
                    var last_stock = updated_current_stock - last_new_stock;
                    $('#updated_current_stock').val(last_stock);
                    $('#last_new_stock').val(0);
                }
            });
        });
    </script>
@endsection