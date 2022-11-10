@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('business-segment.product.index')}}">
                            <button type="button" title="Back"
                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class=" icon wb-shopping-cart" aria-hidden="true"></i>{{ $data->Name($data->merchant_id) }}
                        - @lang("$string_file.product_variant") </h3>
                </header>
                <div class="panel-body">
                    <table id="exampleFooEditing" class="table table-bordered table-hover toggle-circle"
                           data-paging="true" data-filtering="false" data-sorting="true">
                        <thead>
                        <tr>
                            <th data-name="sn" data-type="number" data-breakpoints="xs">@lang("$string_file.sn")</th>
                            <th data-name="id" data-type="number" data-breakpoints="xs"> @lang("$string_file.id")</th>
                            <th data-name="sku_id" data-breakpoints="xs">@lang("$string_file.sku_no")</th>
                            <th data-name="title">@lang("$string_file.title")</th>
                            <th data-name="price">@lang("$string_file.price")</th>
                            <th data-name="discount">@lang("$string_file.discount")</th>
                            <th data-name="weight_unit">@lang("$string_file.weight_unit") </th>
                            <th data-name="weight_unit_value" data-visible="false">@lang("$string_file.weight_unit") </th>
                            <th data-name="weight">@lang("$string_file.weight")</th>
                            <th data-name="is_title_show">@lang("$string_file.is_title_show")</th>
                            <th data-name="is_title_show_value" data-visible="false">@lang("$string_file.is_title_show")</th>
                            <th data-name="status">@lang("$string_file.status")</th>
                            <th data-name="status_value" data-visible="false">@lang("$string_file.status")</th>
                            <th data-name="variant_inventory">@lang("$string_file.inventory_status")</th>
                            <th data-name="action" data-visible="false"
                                data-filterable="false">@lang("$string_file.action")
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1 @endphp
                        @foreach($product_variants as $product_variant)
                            <tr>
                                <td>{{ $sr }}</td>
                                <td>{{ $product_variant->id}}</td>
                                <td>{{ $product_variant->sku_id}}</td>
                                <td>{{  $product_variant->is_title_show == 1 ? $product_variant->Name($merchant_id) : $product_variant->Product->Name($merchant_id)}}</td>
                                <td>{{ custom_number_format($product_variant->product_price,$trip_calculation_method) }}</td>
                                <td>{{ custom_number_format($product_variant->discount,$trip_calculation_method) }}</td>
                                <td>@if(!empty($product_variant->weight_unit_id)){{$product_variant->WeightUnit->WeightUnitName}}@endif</td>
                                <td>{{$product_variant->weight_unit_id}}</td>
                                <td>{{ $product_variant->weight }}</td>
                                <td>@if($product_variant->is_title_show == 1)
                                        <span class="badge badge-success">@lang("$string_file.yes")</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.no")</span>
                                    @endif
                                </td>
                                <td>{{$product_variant->is_title_show}}</td>
                                <td>@if($product_variant->status == 1)
                                        <span class="badge badge-success">{{$product_status[$product_variant->status]}}</span>
                                    @else
                                        <span class="badge badge-danger">{{$product_status[$product_variant->status]}}</span>
                                    @endif
                                </td>
                                <td>{{$product_variant->status}}</td>
                                <td>@if(isset($product_variant->ProductInventory) && $product_variant->ProductInventory->count() > 0)
                                        <span class="badge badge-success">{{$product_status[1]}}</span>
                                    @else
                                        <span class="badge badge-danger">{{$product_status[2]}}</span>
                                    @endif
                                </td>
                                <td></td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        @if(isset($data->manage_inventory) && $data->manage_inventory == 1)
                            <a href="{{ route('business-segment.product.inventory.index',['id' => $data->id]) }}">
                                <button class="btn btn-primary" @if($product_variants->count() == 0) disabled @endif>
                                    <i class="fa fa-check-circle"></i>
                                    @lang("$string_file.continue_to_product_inventory")
                                </button>
                            </a>
                        @else
                            <a href="{{ route('business-segment.product.index') }}">
                                <button class="btn btn-primary">
                                    <i class="fa fa-check-circle"></i>
                                    @lang("$string_file.finish")
                                </button>
                            </a>
                        @endif
                    </div>
                    @if(!empty($arr_option_type) && $arr_option_type->count() > 0)
                    <hr>
                    @php $sn = 1; @endphp
                    <h4>@lang("$string_file.option_management") : </h4>
                        {!! Form::open(["name"=>"","url"=>route("business-segment.product.options.save")]) !!}
                        <input type="hidden" name="product_id" value="{{ $data->id }}">
                        @foreach($arr_option_type as $option_type)
                            @php $form_field_type = $option_type->charges_type == 2 ? "text" : "checkbox"; @endphp
                            <div class="row">
                                <div class="col-md-12">
                                 <h5>
                                {{$sn.'. '.$option_type->Type($merchant_id)}}
                                 </h5>
                                    <div class="row">
                                        @foreach($option_type->Option as $option)
                                            @php $checked = false; $amount = NULL; $disabled= true; @endphp
                                            @foreach($option->Product as $product_pivot)
                                            @if(!empty($product_pivot->pivot->product_id) && ($product_pivot->pivot->product_id == $data->id))
                                                @php $disabled =false; $checked = true; $amount = $product_pivot->pivot->option_amount; @endphp
                                            @endif
                                            @endforeach
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="sku_id">
                                                        {{$option->Name($bs_id)}}
                                                        <span class="text-danger"></span>
                                                    </label>
                                                    @if($form_field_type == "text")
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                {!! Form::checkbox('arr_option['.$option->id.']',NULL,$checked,['id'=>$option->id,'class'=>'option_checkbox']) !!}
                                                            </span>
                                                            {!! Form::text('option_amount['.$option->id.']',old('option_amount',$amount),['id'=>'','class'=>'form-control option'.$option->id,'placeholder'=>trans("$string_file.amount"),'disabled'=>$disabled]) !!}
                                                        </div>
                                                    @else
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                {!! Form::checkbox('arr_option['.$option->id.']',NULL,$checked,['id'=>$option->id,'class'=>'option_checkbox']) !!}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @php $sn++; @endphp
                        @endforeach
                        @if(!$is_demo)
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            @if(isset($data->manage_inventory) && $data->manage_inventory == 1)
                                <a href="{{ route('business-segment.product.inventory.index',['id' => $data->id]) }}">
                                    <button class="btn btn-primary" @if($product_variants->count() == 0) disabled @endif>
                                        <i class="fa fa-check-circle"></i>
                                        @lang("$string_file.save") & @lang("$string_file.continue_to_product_inventory")
                                    </button>
                                </a>
                            @else
                                <a href="{{ route('business-segment.product.index') }}">
                                    <button class="btn btn-primary">
                                        <i class="fa fa-check-circle"></i>
                                        @lang("$string_file.save")
                                    </button>
                                </a>
                            @endif
                        </div>
                        @else
                        <span style="color: red" class="float-right">@lang("$string_file.demo_warning_message")</span>
                        @endif
                        {!! Form::close() !!}
                    @endif


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
                                    <h4 class="modal-title" id="editor-title">@lang("$string_file.edit_product_variant")</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="number" id="id" name="id" class="hidden"
                                                   style="display:none;"/>
                                            <input type="hidden" name="product_id" value="{{ $data->id }}">
                                            <div class="form-group required">
                                                <label for="price"
                                                       class="control-label">@lang("$string_file.sku_no")</label>
                                                <input type="text" class="form-control" id="sku_id" name="sku_id"
                                                       placeholder="@lang("$string_file.sku_no")"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="hidden" name="product_variant_id" id="product_variant_id"
                                                   value="">
                                            <div class="form-group">
                                                <label for="dob"
                                                       class=" control-label">@lang("$string_file.status")</label>
                                                {!! Form::select('status',$product_status,old('status'),['id'=>'status','class'=>'form-control','autocomplete'=>'off','required']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group required">
                                                <label for="title"
                                                       class="control-label">@lang("$string_file.product_name")</label>
                                                <input type="text" class="form-control" id="title" name="title" readonly
                                                       placeholder="@lang("$string_file.title")" value="{{ $data->Name($data->merchant_id) }}"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="checkbox-custom checkbox-default">
                                                    <input type="checkbox" id="is_title_show" onclick="ShowHideDiv(this)" name="is_title_show" autocomplete="off">
                                                    <label for="is_title_show">@lang("$string_file.is_title_show")</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group required">
                                                <label for="price"
                                                       class="control-label">@lang("$string_file.price")</label>
                                                <input type="number" class="form-control" id="price" name="price" step="{{$step_value}}" min="0"
                                                       placeholder="@lang("$string_file.price")"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="discount"
                                                       class=" control-label">@lang("$string_file.discount")</label>
                                                <input type="number" class="form-control" id="discount" name="discount" step="{{$step_value}}" min="0"
                                                       placeholder="@lang("$string_file.discount")">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="weight_unit"
                                                       class=" control-label">@lang("$string_file.weight_unit") </label>
                                                {!! Form::select('weight_unit',$arr_weight_unit,old('weight_unit'),['id'=>'weight_unit','class'=>'form-control','autocomplete'=>'off']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="discount"
                                                       class=" control-label">@lang("$string_file.weight")</label>
                                                <input type="text" class="form-control" id="weight" name="weight"
                                                       placeholder="@lang("$string_file.weight")">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    @if(!$is_demo)
                                    <button type="submit" class="btn btn-primary">@lang("$string_file.save") </button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang("$string_file.cancel")</button>
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

@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('global/vendor/footable/footable.core.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/examples/css/tables/footable.css') }}">
@endsection
@section('js')
    <script src="{{ asset('global/vendor/footable/footable.min.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript">
        function ShowHideDiv(ss) {
            var checkValue = ss.checked ? 1 : 0;
            console.log(checkValue);
            if(checkValue == 1){
                $('#title').attr('readonly', false);
            }else{
                $('#title').attr('readonly', true);
            }
        }
        $(document).ready(function () {

            var $modal = $('#editor-modal'),
                $editor = $('#editor'),
                $editorTitle = $('#editor-title'),
                ft = FooTable.init('#exampleFooEditing', {
                    editing: {
                        enabled: true,
                        addRow: function () {
                            $modal.removeData('row');
                            $editor[0].reset();
                            $editor.find('#is_title_show').prop("checked", false );
                            $editor.find('#title').prop('readonly',true);
                            $editorTitle.text('@lang("$string_file.product_variant")');
                            $modal.modal('show');
                        },
                        editRow: function (row) {
                            var values = row.val();
                            console.log(values);
                            $editor.find('#id').val(values.id);
                            $editor.find('#sku_id').val(values.sku_id);
                            $editor.find('#title').val(values.title);
                            $editor.find('#price').val(values.price);
                            $editor.find('#discount').val(values.discount);
                            $editor.find('#weight_unit').val(values.weight_unit_value);
                            $editor.find('#weight').val(values.weight);
                            $editor.find('#status').val(values.status_value);
                            if(values.is_title_show_value == 1){
                                $editor.find('#is_title_show').prop("checked", true );
                                $editor.find('#title').prop('readonly',false);
                            }else{
                                $editor.find('#is_title_show').prop("checked", false );
                                $editor.find('#title').prop('readonly',true);
                            }

                            $modal.data('row', row);
                            $editorTitle.text('Edit row #' + values.id);
                            $modal.modal('show');
                        },
                        deleteRow: function (row) {
                            if (confirm('Are you sure you want to delete the row?')) {
                                var values = row.val();
                                $.get("{{ route('business-segment.product.variant.destroy') }}", {id: values.id}, function (data, status) {
                                    if (data.result == 'success') {
                                        row.delete();
                                    } else {
                                        alert(data.data);
                                    }
                                });
                            }
                        }
                    }
                }),
                uid = 10;

            $editor.on('submit', function (e) {
                if (this.checkValidity && !this.checkValidity()) return;
                e.preventDefault();
                $.ajax({
                    url: "{{ route('business-segment.product.variant.save') }}",
                    data: $editor.serialize(),
                    type: "POST",
                }).done(function (result) {
                    if (typeof (result.success) != "undefined" && result.success !== null) {
                        var row = $modal.data('row'),
                            values = {
                                id: $editor.find('#id').val(),
                                sku_id: $editor.find('#sku_id').val(),
                                title: $editor.find('#title').val(),
                                price: $editor.find('#price').val(),
                                discount: $editor.find('#discount').val(),
                                weight_unit: $editor.find('#weight_unit').val(),
                                weight: $editor.find('#weight').val(),
                                status: $editor.find('#status').val()
                            };
                        if (row instanceof FooTable.Row) {
                            row.val(values);
                        } else {
                            values.id = uid++;
                            ft.rows.add(values);
                        }
                        $modal.modal('hide');
                        window.location.href = result.route;
                    } else {
                        alert('error : ' + result.error);
                    }
                });
            });
        });
        $(document).ready(function () {
            $(".option_checkbox").click(function () {
               var id = $(this).attr("id");
               if($(this).is(':checked'))
               {
                 $(".option"+id).prop("disabled",false);
                 // $(".option"+id).prop("required",true);
               }
               else
               {
                   $(".option"+id).prop("disabled",true);
                   // $(".option"+id).prop("required",false);
               }
            });
        });
    </script>
@endsection