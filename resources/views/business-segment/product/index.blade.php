@extends('business-segment.layouts.main')
@section('content')
    <div class="page">
        <div class="page-content">
        <div class="panel panel-bordered">
            <!--<header class="panel-heading">-->
            <!--    <div class="row">-->
            <!--        <div class="col-md-3">-->
            <!--        <h3 class="panel-title">-->
            <!--            @lang("$string_file.import_bulk_products")-->
            <!--        </h3>-->
            <!--        </div>-->
            <!--        <div class="col-md-9">-->
            <!--            @if(!empty($info_setting) && $info_setting->view_text != "")-->
            <!--                <button class="btn btn-icon btn-primary float-right" style="margin:10px"-->
            <!--                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">-->
            <!--                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>-->
            <!--                </button>-->
            <!--            @endif-->
            <!--        </div>-->
            <!--    </div>-->
            <!--    <div class="row">-->
            <!--        <div class="col-md-4">-->
            <!--        <a href="{{route('business-segment.category.export',$arr_category_search)}}">-->
            <!--            <button type="button" title="@lang("$string_file.export_category")"-->
            <!--                    class="btn btn-icon btn-success" style="margin:10px"> 1. @lang("$string_file.export_category")-->
            <!--                @lang("$string_file.categories") <i class="wb-upload"></i>-->
            <!--            </button>-->
            <!--        </a>-->
            <!--        </div>-->
            <!--        <div class="col-md-4">-->
            <!--        <a href="{{asset('basic-images/product_import_sheet.xlsx')}}">-->
            <!--            <button type="button" title="@lang("$string_file.download_product_excel_sample")"-->
            <!--                    class="btn btn-icon btn-info" style="margin:10px">-->
            <!--                2. @lang("$string_file.download_product_excel_sample") <i class="wb-upload"></i>-->
            <!--            </button>-->
            <!--        </a>-->
            <!--        </div>-->
            <!--        <div class="col-md-4">-->
            <!--        <button type="button" class="btn btn-icon btn-success"-->
            <!--                title="@lang("$string_file.import_bulk_products")"-->
            <!--                data-toggle="modal"-->
            <!--                data-target="#importProduct" style="margin:10px"> 3. @lang("$string_file.import_bulk_products") <i class="wb-download"></i>-->
            <!--        </button>-->
            <!--        </div>-->
            <!--        <div class="col-md-4">-->
            <!--            <a href="{{route('business-segment.weight.export')}}">-->
            <!--                <button type="button" title="@lang("$string_file.export_weight_unit")"-->
            <!--                        class="btn btn-icon btn-info" style="margin:10px">-->
            <!--                    4. @lang ("$string_file.export_weight_unit") <i class="wb-upload"></i>-->
            <!--                </button>-->
            <!--            </a>-->
            <!--        </div>-->
            <!--        <div class="col-md-4">-->
            <!--        <a href="{{route('business-segment-product-export-variant')}}">-->
            <!--            <button type="button" title="@lang("$string_file.export_product_variant")"-->
            <!--                    class="btn btn-icon btn-info" style="margin:10px">-->
            <!--                5. @lang("$string_file.export_product_variant") <i class="wb-upload"></i>-->
            <!--            </button>-->
            <!--        </a>-->
            <!--        </div>-->
            <!--        <div class="col-md-4">-->
            <!--        <button type="button" class="btn btn-icon btn-success"-->
            <!--                title="@lang("$string_file.import_product_variants")"-->
            <!--                data-toggle="modal"-->
            <!--                data-target="#importProductVariant" style="margin:10px">-->
            <!--            6. @lang("$string_file.import_product_variants") <i class="wb-download"></i>-->
            <!--        </button>-->
            <!--        </div>-->




            <!--        <a href="{{route('business-segment-product-import')}}">-->
            <!--        <button type="button" title="@lang("$string_file.download_product_excel_sample")"-->
            <!--        class="btn btn-icon btn-info float-right" style="margin:10px"><i-->
            <!--        class="wb-upload"></i>-->
            <!--        </button>-->
            <!--        </a>-->
            <!--    </div>-->

            <!--</header>-->
        </div>
        </div>
            <div class="page-content">
            @include('merchant.shared.errors-and-messages')
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="{{route('business-segment.product.basic.add')}}">
                            <button type="button" title="@lang("$string_file.add_product")"
                                    class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-plus"></i>
                            </button>
                        </a>

                    </div>
                    <h3 class="panel-title">
                        <i class=" icon wb-shopping-cart" aria-hidden="true"></i>@lang("$string_file.products")
                    </h3>
                </header>
                <div class="panel-body">
                    {!! $search_view !!}
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable2"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>@lang("$string_file.sn")</th>
                            <th>@lang("$string_file.sku_no")</th>
                            <th>@lang("$string_file.product_name")</th>
                            <th>@lang("$string_file.description")</th>
                            <th>@lang("$string_file.ingredients")</th>
                            <th>@lang("$string_file.category")</th>
                            <th>@lang("$string_file.sequence")</th>
                            <th>@lang("$string_file.cover_image")</th>
                            @if($segment->Segment->slag == 'FOOD')
                                <th>@lang("$string_file.preparation_time")</th>
                            @endif
                            <th>@lang("$string_file.status")</th>
                            <th>@lang("$string_file.variant_status")</th>
                            <th>@lang("$string_file.inventory_status")</th>
                            <th>@lang("$string_file.action")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $sr = 1; @endphp
                        @foreach($data as $product)

                            <tr>
                                @php $lang_data = $product->langData($product->merchant_id); @endphp
                                <td>{{ $sr }}</td>
                                <td>{{ $product->sku_id }}</td>
                                <td>{{ $lang_data->name}}</td>
                                <td>{{ $lang_data->description }}</td>
                                <td>{{ $lang_data->ingredients }}</td>
                                <td>{{$product->Category->Name($product->merchant_id)}}</td>
                                <td>{{$product->sequence}}</td>
                                <td>
                                    <img src="{{ get_image($product->product_cover_image,'product_cover_image',$product->merchant_id)}}"
                                         width="80px" height="80px">
                                </td>
                                @if($segment->Segment->slag == 'FOOD')
                                    <td>{{$product->product_preparation_time}}</td>
                                @endif
                                <td>@if($product->status == 1)
                                        <span class="badge badge-success">{{isset($product_status[$product->status]) ? $product_status[$product->status] : ""}}</span>
                                    @else
                                        <span class="badge badge-danger">{{isset($product_status[$product->status]) ? $product_status[$product->status] : ""}}</span>
                                    @endif
                                </td>
                                @php $product_variant_count = $product->ProductVariant->count(); @endphp
                                <td>@if($product_variant_count > 0)
                                        <span class="badge badge-success">@lang("$string_file.added") ({{$product_variant_count}} - @lang("$string_file.variants"))</span>
                                    @else
                                        <span class="badge badge-danger">@lang("$string_file.not_added")</span>
                                    @endif
                                </td>
                                <td>@php
                                        $inventory_available_status = 0;
                                        if($product->manage_inventory == 1){
                                            $product_variant_inventory_count = 0;
                                            foreach($product->ProductVariant as $product_variant){
                                                if(isset($product_variant->ProductInventory) && $product_variant->ProductInventory->count() > 0){
                                                    $product_variant_inventory_count++;
                                                }
                                            }
                                            if($product_variant_count > 0){
                                                if($product_variant_count == $product_variant_inventory_count){
                                                    $inventory_available_status = 1;
                                                }elseif($product_variant_inventory_count > 0){
                                                    $inventory_available_status = 2;
                                                }
                                            }
                                        }
                                    @endphp
                                    @if($product->manage_inventory == 1)
                                        <span class="badge badge-success">{{isset($inventory_status[$product->manage_inventory]) ? $inventory_status[$product->manage_inventory] : ""}}</span>
                                        @switch($inventory_available_status)
                                            @case(0)
                                            <span class="badge badge-danger">@lang("$string_file.not_added")</span>
                                            @break;
                                            @case(1)
                                            <span class="badge badge-success">@lang("$string_file.added")</span>
                                            @break;
                                            @case(2)
                                            <span class="badge badge-info">@lang("$string_file.add") : {{$product_variant_inventory_count}} - @lang("$string_file.variant") | @lang("$string_file.inventory")</span>
                                            @break;
                                        @endswitch
                                    @else
                                        <span class="badge badge-danger">{{isset($inventory_status[$product->manage_inventory]) ? $inventory_status[$product->manage_inventory] : ""}}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{!! route('business-segment.product.basic.add',$product->id) !!}"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn" title="@lang("$string_file.edit")"><i
                                                class="wb-edit"></i></a>
                                    @csrf
                                    <button onclick="DeleteEvent({{ $product->id }})"
                                            type="button"
                                            data-original-title="@lang("$string_file.delete")"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                        <i class="fa fa-trash"></i></button>
                                    <a href="{!! route('business-segment.product.variant.index',$product->id) !!}"
                                       title="Manage Variant"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn"><i
                                                class="wb-eye"></i></a>
                                    @if(isset($product->manage_inventory) && $product->manage_inventory == 1)
                                        <a href="{!! route('business-segment.product.inventory.index',['id' => $product->id]) !!}"
                                           title="Manage Product Inventory"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn"><i
                                                    class="wb-book"></i></a>
                                    @endif
                                </td>
                            </tr>
                            @php $sr++  @endphp
                        @endforeach
                        </tbody>
                    </table>
                    @include('merchant.shared.table-footer', ['table_data' => $data, 'data' => $arr_search])
                    {{--                    <div class="pagination1" style="float:right;">{{$data->links()}}</div>--}}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="importProduct" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b> @lang("$string_file.import_bulk_products")</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{ route('business-segment-product-import') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label>@lang("$string_file.product_excel") <span class="text-danger">*</span> </label>
                                <i class="fa fa-info-circle fa-1" data-toggle="tooltip" data-placement="top" title=""></i>
                                <div class="form-group">
                                    <input style="height: 0%" type="file" class="form-control" id="product_import_sheet"
                                           name="product_import_sheet" placeholder="" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn" data-dismiss="modal"
                               value="@lang("$string_file.reset")">
                        <input type="submit" class="btn btn-outline-primary btn" value="@lang("$string_file.submit")">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="importProductVariant" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b> @lang("$string_file.import_product_variants")</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="{{ route('business-segment-product-variant-import') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label>@lang("$string_file.product_excel") <span class="text-danger">*</span> </label>
                                <i class="fa fa-info-circle fa-1" data-toggle="tooltip" data-placement="top" title=""></i>
                                <div class="form-group">
                                    <input style="height: 0%" type="file" class="form-control" id="product_variant_import_sheet"
                                           name="product_variant_import_sheet" placeholder="" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn" data-dismiss="modal"
                               value="@lang("$string_file.reset")">
                        <input type="submit" class="btn btn-outline-primary btn" value="@lang("$string_file.submit")">
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'])
@endsection
@section('js')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function DeleteEvent(id) {
            var token = $('[name="_token"]').val();
            swal({
                title: "@lang("$string_file.are_you_sure")",
                text: "@lang("$string_file.delete_warning")",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        type: "POST",
                        data: {
                            id: id,
                        },
                        url: "{{ route('business-segment.product.destroy') }}",
                    })
                        .done(function (data) {
                            swal({
                                title: "DELETED!",
                                text: data,
                                type: "success",
                            });
                            window.location.href = "{{ route('business-segment.product.index') }}";
                        });
                } else {
                    swal("@lang("$string_file.data_is_safe")");
                }
            });
        };
    </script>
@endsection