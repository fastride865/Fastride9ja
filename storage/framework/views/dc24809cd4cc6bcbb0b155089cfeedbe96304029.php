<?php
    $name = isset($arr_search['name']) ? $arr_search['name'] : "";
    $sku_number = isset($arr_search['sku_id']) ? $arr_search['sku_id'] : "";
    $category = isset($arr_search['category']) ? $arr_search['category'] : "";
    $inventory = isset($arr_search['manage_inventory']) ? $arr_search['manage_inventory'] : "";
    $search_route = isset($arr_search['search_route']) ? $arr_search['search_route'] : "";
    $status = isset($arr_search['status']) ? $arr_search['status'] : "";
    $arr_inventory = add_blank_option(inventory_status($string_file),trans("$string_file.inventory"));
    $arr_status = add_blank_option(get_product_status("web",$string_file),trans("$string_file.status"));
?>
<?php echo Form::open(['name'=>'','url'=>$search_route,'method'=>'GET']); ?>

<div class="table_search row">
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="sku_id" value="<?php echo e($sku_number); ?>"
                   placeholder="<?php echo app('translator')->get("$string_file.sku_no"); ?>"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="name" value="<?php echo e($name); ?>"
                   placeholder="<?php echo app('translator')->get("$string_file.product_name"); ?>"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="category" value="<?php echo e($category); ?>"
                   placeholder="<?php echo app('translator')->get("$string_file.category"); ?>"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <?php echo Form::select('status',$arr_status,$status,['class'=>'form-control']); ?>

        </div>
    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <?php echo Form::select('manage_inventory',$arr_inventory,$inventory,['class'=>'form-control']); ?>

        </div>
    </div>
    <div class="col-sm-2  col-xs-12 form-group active-margin-top">
        <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
        <a href="<?php echo e($search_route); ?>" ><button class="btn btn-success" type="button"><i class="fa fa-refresh" aria-hidden="true"></i></button></a>
    </div>
</div>
<?php echo Form::close(); ?>

<hr><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/product/search.blade.php ENDPATH**/ ?>