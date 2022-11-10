<div class="panel-body container-fluid">
    <div class="row">
        <div class="col-lg-6 col-xs-12">
            <h5><?php echo app('translator')->get("$string_file.user_details"); ?> : - </h5>
            <div class="card my-2 shadow  bg-white h-240">
                <div class="row p-3 mt-30 ml-30">
                    <div class="col-md-4 col-xs-6">
                        <img height="100" width="100" class="rounded-circle"
                             src="<?php if($order->User->UserProfileImage): ?> <?php echo e(get_image($order->User->UserProfileImage,'user',$order->merchant_id)); ?><?php else: ?><?php echo e(get_image()); ?><?php endif; ?>">
                    </div>
                    <div class="col-md-8 col-xs-6">
                        <?php if(!empty($calling_from_bs) && $hide_user_info_from_store == 1): ?>
                        <?php else: ?>
                            <p>
                                <span class="font-size-20"><?php echo e(is_demo_data($order->User->first_name.' '.$order->User->last_name,$order->Merchant)); ?></span>
                                <br>
                                <span title="Phone"><?php echo app('translator')->get("$string_file.phone"); ?>:</span>&nbsp;&nbsp;<?php echo e(is_demo_data($order->User->UserPhone,$order->Merchant)); ?>

                                <span title="Email"><?php echo app('translator')->get("$string_file.email"); ?>:</span>&nbsp;&nbsp;<?php echo e(is_demo_data($order->User->email,$order->Merchant)); ?>

                                <br>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row mt-10 ml-20">
                    <div class="col-md-4 col-xs-4 pt-20 pl-30">
                        <?php echo app('translator')->get("$string_file.address"); ?> :
                    </div>
                    <div class="col-md-8 col-xs-8">
                        <address>
                            <?php if($order->drop_location): ?>
                                <?php echo e($order->drop_location); ?>

                            <?php else: ?>
                                <?php echo e($order->UserAddress->house_name); ?>,
                                <?php echo e($order->UserAddress->floor); ?>

                                <?php echo e($order->UserAddress->building); ?>

                                <br>
                                <?php echo e($order->UserAddress->address); ?>

                            <?php endif; ?>
                        </address>
                        <br>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="col-lg-6 col-xs-12">
            <h5><?php echo app('translator')->get("$string_file.other_details"); ?> : - </h5>
            <div class="card my-2 shadow  bg-white h-240">
                <div class="row p-3 mt-30 ml-30">
                    <div class="col-md-4 col-xs-6 text-info">
                        <i class="icon fa-money"></i> <?php echo app('translator')->get("$string_file.payment"); ?>
                    </div>
                    <div class="col-md-8 col-xs-6">
                        <p>
                            <span class="font-size-20"><?php echo e($order->PaymentMethod->payment_method); ?></span>
                            <br>
                            <?php $currency = $order->CountryArea->Country->isoCode; ?>
                            <span title=""><?php echo app('translator')->get("$string_file.grand_total"); ?> : </span><?php echo e($currency.$order->final_amount_paid); ?>

                        </p>
                    </div>
                </div>
                <div class="row p-3 mt-30 ml-30">
                    <div class="col-md-4 col-xs-6 text-warning">
                        <i class="icon fa-comments fa-2x text-gray-300"></i> <?php echo app('translator')->get("$string_file.current_status"); ?>
                    </div>
                    <div class="col-md-8 col-xs-6">
                        <p>
                            <?php echo e($arr_status[$order->order_status]); ?>

                            <br>
                        </p>
                    </div>
                </div>
                <div class="row p-3 ml-30">
                    <div class="col-md-4 col-xs-6 text-success">
                        <i class="icon fa-calendar fa-2x text-gray-300"></i><?php echo app('translator')->get("$string_file.created_at"); ?>
                    </div>
                    <div class="col-md-8 col-xs-6">
                        <p>
                            <?php echo convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone, null, $order->Merchant); ?>


                            <br>
                        </p>
                    </div>
                </div>
                 <div class="row p-3 ml-30">
                    <div class="col-md-4 col-xs-6 text-success">
                        <i class="icon fa-calendar fa-2x text-gray-300"></i><?php echo app('translator')->get("$string_file.deliver_on"); ?>
                    </div>
                    <div class="col-md-8 col-xs-6">
                        <?php if(!empty($order->service_time_slot_detail_id)): ?>
                            <?php echo e($order->ServiceTimeSlotDetail->slot_time_text); ?>,
                        <?php endif; ?>
                        
                            <?php echo convertTimeToUSERzone($order->order_date, $order->CountryArea->timezone, null, $order->Merchant,2); ?>

                    </div>
                </div>
                <div class="row mt-10 ml-20">
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <?php if(!empty($order->driver_id)): ?>
        <div class="col-lg-6 col-xs-12">
            <h5><?php echo app('translator')->get("$string_file.driver_details"); ?> : - </h5>
            <div class="card my-2 shadow  bg-white h-240">
                <div class="row p-3 mt-30 ml-30">
                    <div class="col-md-4 col-xs-6">
                        <img height="100" width="100" class="rounded-circle"
                             src="<?php if($order->driver_id): ?> <?php echo e(get_image($order->Driver->profile_image,'drive',
                             $order->merchant_id)); ?><?php else: ?><?php echo e(get_image()); ?><?php endif; ?>">
                    </div>
                    <div class="col-md-8 col-xs-6">
                            <p>
                                <span class="font-size-20"><?php echo e(is_demo_data($order->Driver->first_name.' '.$order->Driver->last_name,$order->Merchant)); ?></span>
                                <br>
                                <span title="Phone"><?php echo app('translator')->get("$string_file.phone"); ?>:</span>&nbsp;&nbsp;<?php echo e(is_demo_data($order->Driver->phoneNumber,$order->Merchant)); ?>

                                <span title="Email"><?php echo app('translator')->get("$string_file.email"); ?>:</span>&nbsp;&nbsp;<?php echo e(is_demo_data($order->Driver->email,$order->Merchant)); ?>

                                <br>
                            </p>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <?php if($order->reassign == 1): ?>
            <div class="col-lg-6 col-xs-12">
                <h5><?php echo app('translator')->get("$string_file.first_driver_details"); ?> : - </h5>
                <div class="card my-2 shadow  bg-white h-240">
                    <div class="row p-3 mt-30 ml-30">
                        <div class="col-md-4 col-xs-6">
                            <img height="100" width="100" class="rounded-circle"
                                 src="<?php if($order->OldDriver->profile_image): ?> <?php echo e(get_image($order->OldDriver->profile_image,'drive',$order->merchant_id)); ?><?php else: ?><?php echo e(get_image()); ?><?php endif; ?>">
                        </div>
                        <div class="col-md-8 col-xs-6">
                            <p>
                                <span class="font-size-20"><?php echo e(is_demo_data($order->OldDriver->first_name.' '.$order->OldDriver->last_name,$order->Merchant)); ?></span>
                                <br>
                                <span title="Phone"><?php echo app('translator')->get("$string_file.phone"); ?>:</span>&nbsp;&nbsp;<?php echo e(is_demo_data($order->OldDriver->phoneNumber,$order->Merchant)); ?>

                                <span title="Email"><?php echo app('translator')->get("$string_file.email"); ?>:</span>&nbsp;&nbsp;<?php echo e(is_demo_data($order->OldDriver->email,$order->Merchant)); ?>

                                <br>
                            </p>
                        </div>
                    </div>
                    <div class="row p-3 mt-30 ml-30">
                        <div class="col-md-4 col-xs-6">
                            <?php echo app('translator')->get("$string_file.reassign_reason"); ?>
                        </div>
                        <div class="col-md-8 col-xs-6">
                            <p>
                                <?php echo e($order->reassign_reason); ?>

                            </p>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
    <hr>
    <h5><?php echo app('translator')->get("$string_file.product_details"); ?> : - </h5>
    <div class="page-invoice-table table-responsive">
        <table class="table table-hover text-right">
            <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-center"><?php echo app('translator')->get("$string_file.product_name"); ?></th>
                <th class="text-center"><?php echo app('translator')->get("$string_file.product_variant"); ?></th>
                <th class="text-center"><?php echo app('translator')->get("$string_file.description"); ?></th>
                <th class="text-right"><?php echo app('translator')->get("$string_file.quantity"); ?></th>
                <th class="text-right"><?php echo app('translator')->get("$string_file.price"); ?></th>
                <?php if($order->Segment->slag =="FOOD"): ?>
                    <th class="text-right"><?php echo app('translator')->get("$string_file.option_amount"); ?></th>
                <?php endif; ?>
                <?php if($order->Segment->slag =="PHARMACY"): ?>
                    <th class="text-right"><?php echo app('translator')->get("$string_file.prescription"); ?></th>
                <?php endif; ?>
                <th class="text-right"><?php echo app('translator')->get("$string_file.discount"); ?></th>
                <th class="text-right"><?php echo app('translator')->get("$string_file.total_amount"); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php $sn = 1;$col_span = 7;$arr_option_amount = [];
                                 $tax =  !empty($order->tax) ? $order->tax : 0.0;
                                 $tip =  !empty($order->tip_amount) && $order->tip_amount > 0 ? $order->tip_amount : 0.0;
                                 $time_charges =  !empty($order->time_charges) && $order->time_charges > 0 ? $order->time_charges : 0.0;
                                 $discount_amount =  !empty($order->discount_amount) && $order->discount_amount > 0 ? $order->discount_amount : 0.0;
            ?>
            <?php if($order->Segment->slag =="FOOD"): ?>
                <?php $col_span = 8;?>
            <?php endif; ?>
            <?php $__currentLoopData = $order->OrderDetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $lang = $product->Product->langData($order->merchant_id); $option_amount = [];
                ?>
                <tr>
                    <td class="text-center">
                        <?php echo e($sn); ?>

                    </td>
                    <td class="text-center">
                        <?php echo e($lang->name); ?>

                        <?php if(!empty($product->options)): ?>
                            <?php echo e('('); ?>

                            <?php  $arr_cart_option = !empty($product->options) ? json_decode($product->options,true) : []; ?>
                            <?php $__currentLoopData = $arr_cart_option; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                
                                
                                
                                <?php $arr_option_amount[] = $option['amount'];
                                                $option_amount[] = $option['amount'];
                                ?>
                                <?php echo e($option['option_name']); ?>,

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e(')'); ?>

                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php echo e($product->ProductVariant->Name($order->merchant_id)); ?>

                    </td>
                    <td class="text-center">
                        <?php echo e($lang->description); ?>

                    </td>
                    <td>
                        <?php echo e($product->quantity); ?> <?php if(isset($product->WeightUnit) && !empty($product->WeightUnit)): ?> <?php echo e($product->WeightUnit->WeightUnitName); ?> <?php endif; ?>
                    </td>
                    <td>
                        <?php echo e($product->price); ?>

                    </td>
                    <?php if($order->Segment->slag =="FOOD"): ?>
                        <td>
                            <?php echo e(array_sum($option_amount)); ?>

                        </td>
                    <?php endif; ?>
                    <?php if($order->Segment->slag =="PHARMACY"): ?>
                        <td>
                            <?php if(!empty($order->prescription_image)): ?>
                                <a href="<?php echo e(get_image($order->prescription_image,'prescription_image',$order->merchant_id)); ?>"> <?php echo app('translator')->get("$string_file.view"); ?>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?php echo e($product->ProductVariant->discount); ?>

                    </td>
                    <td>
                        <?php echo e($product->total_amount); ?>

                    </td>
                </tr>
                <?php $sn = $sn+1; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td colspan="<?php echo e($col_span); ?>"><?php echo app('translator')->get("$string_file.cart_amount"); ?></td>
                <td><?php echo e($currency.$order->cart_amount); ?></td>
            </tr>
            <tr>
                <td colspan="<?php echo e($col_span); ?>"><?php echo app('translator')->get("$string_file.delivery_charge"); ?></td>
                <td><?php echo e($currency.$order->delivery_amount); ?></td>
            </tr>
            <tr>
                <td colspan="<?php echo e($col_span); ?>"><?php echo app('translator')->get("$string_file.tax"); ?></td>
                <td><?php echo e($currency.$tax); ?></td>
            </tr>
            <tr>
                <td colspan="<?php echo e($col_span); ?>"><?php echo app('translator')->get("$string_file.tip"); ?></td>
                <td><?php echo e($currency.$tip); ?></td>
            </tr>
            <?php if($order->Segment->slag =="DELIVERY"): ?>
                <tr>
                    <td colspan="<?php echo e($col_span); ?>"><?php echo app('translator')->get("$string_file.time_charges"); ?></td>
                    <td><?php echo e($currency.$time_charges); ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td colspan="<?php echo e($col_span); ?>"><?php echo app('translator')->get("$string_file.discount_amount"); ?></td>
                <td><?php echo e($currency.$discount_amount); ?></td>
            </tr>
            <tr>
                <td colspan="<?php echo e($col_span); ?>"><?php echo app('translator')->get("$string_file.grand_total"); ?></td>
                <td><?php echo e($currency.$order->final_amount_paid); ?></td>
            </tr>
            </tbody>
            <tfoot>
            <?php if(isset($cancel_receipt) && $cancel_receipt['cancel_receipt_visibility'] == true): ?>
                <tr>
                    <td class="text-left" colspan="<?php echo e($col_span+1); ?>">
                        <b><?php echo app('translator')->get("$string_file.other_action"); ?></b></td>
                </tr>
                <tr>
                    <td class="text-left" colspan="3"><?php echo e($cancel_receipt['cancelled_tital']); ?>

                        <br> <?php echo e($cancel_receipt['cancelled_bottom_text']); ?></td>
                    <td colspan="<?php echo e($col_span-3); ?>"></td>
                    <td><?php echo e($currency.$cancel_receipt['cancelled_charges']); ?></td>
                </tr>
            <?php endif; ?>
            </tfoot>
        </table>
    </div>
</div>
<?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/common-view/order-detail.blade.php ENDPATH**/ ?>