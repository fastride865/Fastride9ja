<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                       <a href="<?php echo e(route("business-segment.start-order-process",$order->id)); ?>"> <button type="submit" class="btn btn-primary">
                            <i class="fa fa-spinner"></i>&nbsp;<?php echo app('translator')->get("$string_file.start_processing"); ?>
                        </button>
                       </a>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-product-hunt" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.order_details"); ?> #<?php echo e($order->merchant_order_id); ?>

                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <h5><?php echo app('translator')->get("$string_file.product_details"); ?> : - </h5>
                    <div class="page-invoice-table table-responsive">
                        <table class="table table-hover text-right">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th  class="text-center"><?php echo app('translator')->get("$string_file.product_name"); ?></th>
                                <th class="text-center"><?php echo app('translator')->get("$string_file.product_variant"); ?></th>
                                <th  class="text-center"><?php echo app('translator')->get("$string_file.product_description"); ?></th>
                                <th class="text-right"><?php echo app('translator')->get("$string_file.quantity"); ?></th>
                                <th class="text-right"><?php echo app('translator')->get("$string_file.price"); ?></th>
                                <?php if($order->Segment->slag =="FOOD"): ?>
                                    <th class="text-right"><?php echo app('translator')->get("$string_file.option_amount"); ?></th>
                                <?php endif; ?>
                                <?php if($order->Segment->slag == "PHARMACY"): ?>
                                    <th  class="text-center"><?php echo app('translator')->get("$string_file.prescription"); ?></th>
                                <?php endif; ?>
                                <th class="text-right"><?php echo app('translator')->get("$string_file.discount"); ?></th>
                                <th class="text-right"><?php echo app('translator')->get("$string_file.total_amount"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $sn = 1 ?>
                            <?php $__currentLoopData = $order->OrderDetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $lang = $product->Product->langData($order->merchant_id); $option_amount = []; ?>
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
                                        <?php echo e($product->quantity); ?>

                                    </td>
                                    <td>
                                        <?php echo e($product->price); ?>

                                    </td>
                                    <?php if($order->Segment->slag =="FOOD"): ?>
                                        <td>
                                            <?php echo e(array_sum($option_amount)); ?>

                                        </td>
                                    <?php endif; ?>
                                    <?php if($order->Segment->slag == "PHARMACY"): ?>
                                    <td>
                                        <?php if(!empty($order->prescription_image)): ?>
                                        <a href="<?php echo e(get_image($order->prescription_image,'prescription_image',$order->merchant_id)); ?>"> <?php echo app('translator')->get("$string_file.view"); ?></a>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                    <td>
                                        <?php echo e($product->discount); ?>

                                    </td>
                                    <td>
                                        <?php echo e($product->total_amount); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <h5><?php echo app('translator')->get("$string_file.additional_notes"); ?> : - </h5>
                    <div class="row">
                        <div class="col-md-12">
                            <p>
                              <?php echo e($order->additional_notes); ?>

                            </p>
                        </div>
                    </div>
                    <hr>
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
                                            <p>
                                                <span class="font-size-20"><?php echo e(is_demo_data($order->User->first_name,$order->Merchant)); ?></span>
                                                <br>
                                                <span title="Phone"><?php echo app('translator')->get("$string_file.phone"); ?>:</span>&nbsp;&nbsp;<?php echo e(is_demo_data($order->User->UserPhone,$order->Merchant)); ?>

                                                <span title="Phone"><?php echo app('translator')->get("$string_file.email"); ?>:</span>&nbsp;&nbsp;<?php echo e(is_demo_data($order->User->email,$order->Merchant)); ?>

                                                <br>
                                            </p>
                                        </div>
                                </div>
                                <div class="row mt-10 ml-20">
                                    <div class="col-md-4 col-xs-4 pt-20 pl-30">
                                        <?php echo app('translator')->get("$string_file.address"); ?> :
                                    </div>
                                    <div class="col-md-8 col-xs-8">
                                            <address>
                                                <?php if(!empty($order->drop_location)): ?>
                                                    <?php echo e($order->drop_location); ?>

                                                <?php elseif(!empty($order->user_address_id)): ?>
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
                                    <div class="col-md-4 col-xs-6">
                                      <?php echo app('translator')->get("$string_file.payment"); ?>
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            <span class="font-size-20"><?php echo e($order->PaymentMethod->payment_method); ?></span>
                                            <br>
                                            <span title=""><?php echo app('translator')->get("$string_file.grand_total"); ?>:</span>&nbsp;&nbsp;<?php echo e($order->CountryArea->Country->isoCode.$order->final_amount_paid); ?>

                                        </p>
                                    </div>
                                </div>
                                <div class="row p-3 mt-30 ml-30">
                                    <div class="col-md-4 col-xs-6">
                                        <?php echo app('translator')->get("$string_file.payment_status"); ?>
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                           <?php echo e($order->payment_status == 1 ? trans("$string_file.paid") : trans("$string_file.pending")); ?>

                                            <br>
                                        </p>
                                    </div>
                                </div>
                                <div class="row p-3 mt-30 ml-30">
                                    <div class="col-md-4 col-xs-6">
                                        <?php echo app('translator')->get("$string_file.current_status"); ?>
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            <?php echo e($arr_status[$order->order_status]); ?>

                                            <br>
                                        </p>
                                    </div>
                                </div>
                                <div class="row mt-10 ml-20">
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/order/process-order.blade.php ENDPATH**/ ?>