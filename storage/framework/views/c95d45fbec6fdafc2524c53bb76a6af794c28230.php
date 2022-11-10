<?php
    $order_id = isset($arr_search['order_id']) ? $arr_search['order_id'] : "";
    $rider = isset($arr_search['rider']) ? $arr_search['rider'] : "";
    $start = isset($arr_search['start']) ? $arr_search['start'] : "";
    $product = isset($arr_search['product']) ? $arr_search['product'] : "";
    $end = isset($arr_search['end']) ? $arr_search['end'] : "";
    $driver = isset($arr_search['driver']) ? $arr_search['driver'] : "";
    $arr_segment = isset($arr_search['arr_segment']) ? $arr_search['arr_segment'] : [];
    $arr_bs = isset($arr_search['arr_bs']) ? $arr_search['arr_bs'] : [];
    $segment_id = isset($arr_search['segment_id']) ? $arr_search['segment_id'] : NULL;
    $driver_id = isset($arr_search['driver_id']) ? $arr_search['driver_id'] : NULL;
    $business_segment_id = isset($arr_search['business_segment_id']) ? $arr_search['business_segment_id'] : NULL;
    $calling_view = isset($arr_search['calling_view']) ? $arr_search['calling_view'] : "";
    $search_route = isset($arr_search['search_route']) ? $arr_search['search_route'] : "";
?>
<?php echo Form::open(['name'=>'','url'=>$search_route,'method'=>'GET']); ?>

<div class="table_search row">
    <?php if(!empty($arr_segment)): ?>
        <div class="col-md-2 col-xs-12 form-group active-margin-top">
            <div class="input-group">
                <?php echo Form::select('segment_id',add_blank_option($arr_segment,trans("$string_file.segment")),$segment_id,['class'=>'form-control']); ?>

            </div>
        </div>
    <?php endif; ?>
        <?php if(!empty($arr_bs)): ?>
            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                <div class="input-group">
                    <?php echo Form::select('business_segment_id',add_blank_option($arr_bs,trans("$string_file.store")),$business_segment_id,['class'=>'form-control']); ?>

                </div>
            </div>
        <?php endif; ?>
    <?php if($calling_view == "earning"): ?>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="order_id" value="<?php echo e($order_id); ?>"
                   placeholder="<?php echo app('translator')->get("$string_file.order_id"); ?>"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    <?php else: ?>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="product" value="<?php echo e($product); ?>"
                   placeholder="<?php echo app('translator')->get("$string_file.product_name"); ?>"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="rider" value="<?php echo e($rider); ?>"
                   placeholder="<?php echo app('translator')->get("$string_file.user_details"); ?>"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="driver" value="<?php echo e($driver); ?>"
                   placeholder="<?php echo app('translator')->get("$string_file.driver_details"); ?>"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
    <?php endif; ?>
    <div class="col-md-4 col-xs-12 form-group active-margin-top">
        <div class="input-daterange" data-plugin="datepicker">
            <div class="input-group">
                <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="icon wb-calendar" aria-hidden="true"></i>
                          </span>
                </div>
                <input type="text" class="form-control" name="start" value="<?php echo e($start); ?>" />
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">to</span>
                </div>
                <input type="text" class="form-control" name="end" value="<?php echo e($end); ?>" />
            </div>
        </div>
        <?php echo Form::hidden('driver_id',$driver_id,['class'=>'form-control']); ?>








    </div>
    <div class="col-sm-2  col-xs-12 form-group active-margin-top">
        <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
        <a href="<?php echo e($search_route); ?>" ><button class="btn btn-success" type="button"><i class="fa fa-refresh" aria-hidden="true"></i></button></a>
    </div>
</div>
<?php echo Form::close(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/order/order-search.blade.php ENDPATH**/ ?>