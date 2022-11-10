@php
    $name = isset($arr_search['name']) ? $arr_search['name'] : "";
    $sku_number = isset($arr_search['sku_id']) ? $arr_search['sku_id'] : "";
    $category = isset($arr_search['category']) ? $arr_search['category'] : "";
    $inventory = isset($arr_search['manage_inventory']) ? $arr_search['manage_inventory'] : "";
    $search_route = isset($arr_search['search_route']) ? $arr_search['search_route'] : "";
    $status = isset($arr_search['status']) ? $arr_search['status'] : "";
    $arr_inventory = add_blank_option(inventory_status($string_file),trans("$string_file.inventory"));
    $arr_status = add_blank_option(get_product_status("web",$string_file),trans("$string_file.status"));
@endphp
{!! Form::open(['name'=>'','url'=>$search_route,'method'=>'GET']) !!}
<div class="table_search row">
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="sku_id" value="{{$sku_number}}"
                   placeholder="@lang("$string_file.sku_no")"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="name" value="{{$name}}"
                   placeholder="@lang("$string_file.product_name")"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="category" value="{{$category}}"
                   placeholder="@lang("$string_file.category")"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            {!! Form::select('status',$arr_status,$status,['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            {!! Form::select('manage_inventory',$arr_inventory,$inventory,['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="col-sm-2  col-xs-12 form-group active-margin-top">
        <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
        <a href="{{$search_route}}" ><button class="btn btn-success" type="button"><i class="fa fa-refresh" aria-hidden="true"></i></button></a>
    </div>
</div>
{!! Form::close() !!}
<hr>