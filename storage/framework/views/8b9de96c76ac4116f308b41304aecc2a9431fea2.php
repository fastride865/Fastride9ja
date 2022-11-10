<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                    </div>
                    <h3 class="panel-title"><i class="fa-user" aria-hidden="true"></i>
                        <?php if($driver_not): ?>
                            <?php echo app('translator')->get("$string_file.change_delivery_person"); ?>
                        <?php else: ?>
                            <?php echo app('translator')->get("$string_file.assign_order_to_delivery_candidate"); ?>
                        <?php endif; ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <?php echo Form::open(['name'=>'','id'=>'','url'=>route('business-segment.order-assign-to-driver')]); ?>

                    <?php echo Form::hidden('order_id',$order->id); ?>

                    <?php echo Form::hidden('order_status',$order->order_status); ?>


                    <h5><?php echo app('translator')->get("$string_file.order_details"); ?> : - </h5>

                    <div class="row p-4 mb-2 bg-blue-grey-100 ml-15 mr-15">
                    <div class="col-md-3">
                        <strong>  <?php echo app('translator')->get("$string_file.order_details"); ?> </strong> : <br>
                       <?php echo app('translator')->get("$string_file.order_id"); ?> : #<?php echo e($order->merchant_order_id); ?> <br>
                       <?php echo app('translator')->get("$string_file.product"); ?> :
                        <?php $product_detail = $order->OrderDetail; $products = "";?>
                        <?php $__currentLoopData = $product_detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                             <?php $weight =  isset($product->ProductVariant->weight) ? $product->ProductVariant->weight : "";
                                             $unit = isset($product->weight_unit_id) ? $product->WeightUnit->WeightUnitName : "";
                                             $unit = !empty($weight)  ? $product->quantity.' x '.$weight.' '.$unit : $product->quantity.$unit;
                                        ?>
                            <?php echo e($product->quantity.' '.$unit.' '.$product->Product->Name($order->merchant_id)); ?>,<br>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="col-md-3">
                        <strong><?php echo app('translator')->get("$string_file.payment_details"); ?></strong> : <br>
                        <?php echo e(trans("$string_file.mode").": ". $order->PaymentMethod->payment_method); ?><br>
                        <?php echo e(trans($string_file.".cart_amount").': '.$order->cart_amount); ?> <br>
                        <?php echo e(trans("$string_file.delivery_charge").': '. $order->delivery_amount); ?> <br>
                        <?php echo e(trans("$string_file.tax").': '. ($order->tax)); ?> <br>
                        <?php echo app('translator')->get("$string_file.grand_total"); ?> :  <?php echo e($order->CountryArea->Country->isoCode.' '.$order->final_amount_paid); ?>

                    </div>
                    <div class="col-md-5">
                       <strong> <?php echo app('translator')->get("$string_file.user_details"); ?> </strong> : <?php echo is_demo_data($order->User->first_name,$order->Merchant).' '.is_demo_data($order->User->last_name,$order->Merchant).',<br>'. $order->drop_location; ?>

                    </div>
                    </div>
                    <h5><?php echo app('translator')->get("$string_file.delivery_drivers"); ?> : - </h5>
                    <table id="" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.assign"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.estimate_distance"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.rating"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = 1; $drivers = $arr_driver->count(); ?>
                        <?php if($drivers > 0): ?>
                         <?php $__currentLoopData = $arr_driver; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="driver_id[]" value="<?php echo e($driver->id); ?>" class="assign-driver" driver-id="<?php echo e($driver->id); ?>" order-id="<?php echo e($order->id); ?>">
                                    <?php echo Form::hidden('distance['.$driver->id.']',$driver->distance); ?>

                                </td>
                                <td>
                                    <?php echo e(is_demo_data($driver->first_name,$order->Merchant).' '.is_demo_data($driver->last_name,$order->Merchant)); ?><br>
                                    <?php echo e(is_demo_data($driver->email,$order->Merchant)); ?><br>
                                    <?php echo e(is_demo_data($driver->phoneNumber,$order->Merchant)); ?>

                                </td>
                                <td>
                                    <?php if(!empty($driver->distance)): ?><?php echo e(number_format($driver->distance,2)); ?> <?php else: ?> 0 <?php endif; ?> <?php echo app('translator')->get("$string_file.km"); ?>
                                </td>
                                <td>














                                    <?php if($driver->rating): ?> <?php echo e($driver->rating); ?> <?php else: ?> <?php echo app('translator')->get("$string_file.not_rated_yet"); ?> <?php endif; ?>
                                </td>









                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center"> <?php echo app('translator')->get("$string_file.no_driver_available"); ?></td>
                            </tr>

                        <?php endif; ?>
                        </tbody>
                    </table>
                    <?php if( $drivers > 0): ?>
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i><?php echo app('translator')->get("$string_file.send"); ?>
                        </button>
                    </div>
                    <?php endif; ?>
                    <?php echo Form::close(); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/order/assign.blade.php ENDPATH**/ ?>