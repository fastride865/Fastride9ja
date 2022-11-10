<?php
    $order_id = isset($arr_search['booking_id']) ? $arr_search['booking_id'] : "";
    $booking_type = isset($arr_search['booking_type']) ? $arr_search['booking_type'] : "";
    $rider = isset($arr_search['rider']) ? $arr_search['rider'] : "";
    $booking_status = isset($arr_search['booking_status']) ? $arr_search['booking_status'] : "";
    $arr_booking_status = isset($arr_search['arr_booking_status']) ? $arr_search['arr_booking_status'] : [];
    $date = isset($arr_search['booking_status']) ? $arr_search['booking_status'] : "";
    $driver = isset($arr_search['driver']) ? $arr_search['driver'] : "";
    $start = isset($arr_search['start']) ? $arr_search['start'] : "";
    $end = isset($arr_search['end']) ? $arr_search['end'] : "";
    $segment_id = isset($arr_search['segment_id']) ? $arr_search['segment_id'] : "";
    $arr_segment = isset($arr_search['arr_segment']) ? $arr_search['arr_segment'] : "";
    $request_from = isset($arr_search['request_from']) ? $arr_search['request_from'] : "";
    $search_route = isset($arr_search['search_route']) ? $arr_search['search_route'] : "";
    $driver_id = isset($arr_search['driver_id']) ? $arr_search['driver_id'] : NULL;
?>
<?php echo Form::open(['name'=>'','url'=>$search_route,'method'=>'GET']); ?>

<?php echo Form::hidden('driver_id',$driver_id,['class'=>'form-control']); ?>

<div class="table_search row">
    <?php if(!empty($arr_segment) && $request_from == "ride_earning"): ?>
        <div class="col-md-2 col-xs-12 form-group active-margin-top">
            <div class="input-group">
                <?php echo Form::select('segment_id',add_blank_option($arr_segment,trans("$string_file.segment")),$segment_id,['class'=>'form-control']); ?>

            </div>
        </div>
    <?php endif; ?>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <input type="text" id="" name="booking_id" value="<?php echo e($order_id); ?>"
                   placeholder="<?php echo app('translator')->get("$string_file.ride_id"); ?>"
                   class="form-control col-md-12 col-xs-12">
        </div>
    </div>
       <?php if($request_from != "ride_earning"): ?>
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
    <?php if($request_from == "ACTIVE"): ?>
    <div class="col-md-2 col-xs-12 form-group active-margin-top">
        <div class="input-group">
            <select class="form-control" name="booking_status" id="booking_status">
                <option value=""><?php echo app('translator')->get("$string_file.ride_status"); ?></option>
                <option value="1001"><?php echo app('translator')->get("$string_file.new_ride"); ?></option>
                <option value="1002"><?php echo app('translator')->get("$string_file.accepted"); ?></option>
                <option value="1012"><?php echo app('translator')->get("$string_file.partial_accepted"); ?></option>
                <option value="1003"><?php echo app('translator')->get("$string_file.arrived_at_pickup"); ?></option>
                <option value="1004"><?php echo app('translator')->get("$string_file.started"); ?></option>
            </select>
        </div>
    </div>
    <?php elseif($request_from == "ALL"): ?>
        <div class="col-md-2 col-xs-12 form-group active-margin-top">
            <div class="input-group">
        <?php echo Form::select('booking_status',add_blank_option($arr_booking_status,trans("$string_file.status")),$booking_status,['class'=>'form-control']); ?>

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
            </div>
        <?php if($request_from != "ride_earning"): ?>
            <div class="col-md-2 col-xs-12 form-group active-margin-top">
                <div class="input-group">
                    <?php echo Form::select('booking_type',[''=>trans($string_file.".ride_type"),'1'=>trans($string_file.".ride_now"),'2'=>trans($string_file.".ride_later")],$booking_type,['class'=>'form-control']); ?>

                </div>
            </div>
        <?php endif; ?>
    <div class="col-sm-2  col-xs-12 form-group active-margin-top">
        <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search" aria-hidden="true"></i></button>
        <a href="<?php echo e($search_route); ?>" ><button class="btn btn-success" type="button"><i class="fa fa-refresh" aria-hidden="true"></i></button></a>
    </div>
</div>
<?php echo Form::close(); ?>

<hr><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/booking/ride-search.blade.php ENDPATH**/ ?>