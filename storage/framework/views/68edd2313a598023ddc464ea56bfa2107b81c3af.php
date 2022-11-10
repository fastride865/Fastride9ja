<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                    </div>
                    <h3 class="panel-title"><i class="fa-user" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.ongoing_orders"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <?php echo $search_view; ?>

                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.order_id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.product_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.payment_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.current_status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.deliver_on"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.pickup_drop"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $arr_orders->firstItem();$user_name = ''; $user_phone = ''; $user_email = '';
                        $driver_name = '';$driver_email = '';
                        ?>
                        <?php $__currentLoopData = $arr_orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                if($hide_user_info_from_store == 2)
                                    {
                                     $user_name = is_demo_data($order->User->first_name.' '.$order->User->last_name,$order->Merchant);
                                     $user_phone = is_demo_data($order->User->UserPhone,$order->Merchant);
                                     $user_email = is_demo_data($order->User->email,$order->Merchant);
                                    }
                            ?>
                            <?php if(!empty($order->driver_id)): ?>
                                <?php
                                     $driver_name = is_demo_data($order->Driver->first_name.' '.$order->Driver->last_name,$order->Merchant);
                                    $driver_phone = is_demo_data($order->Driver->phoneNumber,$order->Merchant);
                                    $driver_email = is_demo_data($order->Driver->email,$order->Merchant);
                                ?>
                            <?php endif; ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php echo e($order->merchant_order_id); ?></td>
                                <td>
                                    <?php $product_detail = $order->OrderDetail; $products = "";?>
                                    <?php $__currentLoopData = $product_detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                         <?php $weight =  isset($product->ProductVariant->weight) ? $product->ProductVariant->weight : "";
                                             $unit = isset($product->weight_unit_id) ? $product->WeightUnit->WeightUnitName : "";
                                             $unit = !empty($weight)  ? $product->quantity.' x '.$weight.' '.$unit : $product->quantity.$unit;
                                        ?>
                                        <?php echo e($unit .' - '.$product->Product->Name($order->merchant_id)); ?> <br>
                                        <?php echo e($product->ProductVariant->Name($order->merchant_id)); ?>

                                        <?php if(!empty($product->options)): ?>
                                            <b><?php echo e('|'); ?></b>
                                            <?php  $arr_cart_option = !empty($product->options) ? json_decode($product->options,true) : []; ?>
                                            <?php $__currentLoopData = $arr_cart_option; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php echo e($option['option_name']); ?>,
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <br>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <br>
                                    <?php if($order->Segment->slag == "PHARMACY" && !empty($order->prescription_image)): ?>
                                        <?php echo app('translator')->get("$string_file.prescription"); ?> : <a href="<?php echo e(get_image($order->prescription_image,'prescription_image',$order->merchant_id)); ?>"> <?php echo app('translator')->get("$string_file.view"); ?></a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e(trans("$string_file.mode").": ". $order->PaymentMethod->payment_method); ?><br>
                                    <?php echo e(trans($string_file.".cart_amount").': '.$order->cart_amount); ?> <br>
                                    <?php echo e(trans("$string_file.delivery_charge").': '. $order->delivery_amount); ?> <br>
                                    <?php echo e(trans("$string_file.tax").': '. ($order->tax)); ?> <br>
                                    <?php echo e(trans("$string_file.tip").': '. (!empty($order->tip_amount) ? $order->tip_amount : 0.0)); ?> <br>
                                    <?php echo app('translator')->get("$string_file.grand_total"); ?>
                                    : <?php echo e($order->CountryArea->Country->isoCode.' '.$order->final_amount_paid); ?>

                                    <br>
                                </td>
                                <td style="text-align: center">
                                    <?php echo e($arr_status[$order->order_status]); ?>

                                </td>
                                <td> <?php echo e($order->CountryArea->CountryAreaName); ?></td>
                                <td>
                                    <?php echo e($user_name); ?> <br>
                                    <?php echo e($user_phone); ?> <br>
                                    <?php echo e($user_email); ?> <br>
                                </td>
                                <td>
                                    <?php if(!empty($driver_name)): ?>
                                        <?php echo e($driver_name); ?> <br>
                                        <?php echo e($driver_phone); ?> <br>
                                        <?php echo e($driver_email); ?> <br>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.not_assigned_yet"); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php $slot_time = ""; $current_time_stamp = strtotime(date('Y-m-d H:i:s')); ?>
                                    <?php if(!empty($order->service_time_slot_detail_id)): ?>
                                        <?php
                                            $slot_time = $order->ServiceTimeSlotDetail->to_time;
                                            $start = $order->ServiceTimeSlotDetail->from_time;
                                            $start = strtotime($start);
                                            $start = $time_format == 2  ? date("H:i",$start) : date("h:i a",$start);
                                            $end = strtotime($slot_time);
                                            $end =  $time_format == 2  ? date("H:i",$end) : date("h:i a",$end);
                                            $time = $start."-".$end;
                                            echo $time.'<br>';
                                        ?>
                                    <?php endif; ?>
                                    <?php echo convertTimeToUSERzone($order->order_date, $order->CountryArea->timezone,null,$order->Merchant, 2); ?>

                                </td>
                                <td>
                                    <?php echo convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone,null,$order->Merchant); ?>

                                </td>
                                <td>
                                    <a title="<?php echo e($order->drop_location); ?>" target="_blank"
                                       href="https://www.google.com/maps/place/<?php echo e($order->drop_location); ?>"
                                       class="btn btn-icon btn-danger ml-20">
                                        <i class="icon fa-tint"></i>
                                    </a>
                                </td>
                                <td>
                                    <a target="_blank" title=""
                                       href="<?php echo e(route('business-segment.order.detail',$order->id)); ?>"
                                       class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                        <span class="fa fa-info-circle" title="<?php echo app('translator')->get("$string_file.order_details"); ?>"></span>
                                    </a>
                                    <a target="_blank" title="<?php echo app('translator')->get("$string_file.cancel"); ?>"
                                       href="<?php echo e(route('business-segment.order-cancel',$order->id)); ?>"
                                       class="btn btn-sm btn-warning menu-icon btn_eye action_btn"><span
                                                class="fa fa-eye-slash"></span>
                                    </a>
                                    <?php if($order->reassign !=1): ?>
                                        <a target="_blank" title=""
                                           href="<?php echo e(route('business-segment.order.reassign',$order->id)); ?>" class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                            <span class="fa fa-check" title="<?php echo app('translator')->get("$string_file.change_delivery_person"); ?>"></span>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $arr_orders, 'data' => $arr_search], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="pickup_otp_verify" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="myModalLabel33"><b><?php echo app('translator')->get("$string_file.order_pickup_verification"); ?></b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('business-segment.order.pickup.verify')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <label><?php echo app('translator')->get("$string_file.otp"); ?></label>
                        <div class="form-group">
                            <input type="number" name="otp" placeholder="Enter Driver App OTP"
                                   class="form-control" required>
                            <input type="hidden" name="order_id" id="order_id">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="<?php echo app('translator')->get("$string_file.close"); ?>">
                        <input type="submit" id="sub" class="btn btn-primary" value="<?php echo app('translator')->get("$string_file.save"); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        $(document).on("click", ".pickup_otp_verify", function () {
            var order_id = $(this).data('id');
            $(".modal-body #order_id").val( order_id );
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/order/order-ontheway.blade.php ENDPATH**/ ?>