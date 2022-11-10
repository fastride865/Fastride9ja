<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                    </div>
                    <h3 class="panel-title">
                        <i class="icon wb-list" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.product_order_statistics"); ?>
                        </span>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <!-- First Row -->
                    <div class="row">
                        <div class="col-xl-3 col-md-6 info-panel">
                            <div class="card card-shadow">
                                <div class="card-block bg-grey-100 p-20">
                                    <button type="button" class="btn btn-floating btn-sm btn-success">
                                        <i class="icon wb-info"></i>
                                    </button>
                                    <span class="ml-15 font-weight-400"><?php echo app('translator')->get("$string_file.products"); ?></span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100"><?php echo e($business_summary['products']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 info-panel">
                            <div class="card card-shadow">
                                <div class="card-block bg-grey-100 p-20">
                                    <button type="button" class="btn btn-floating btn-sm btn-warning">
                                        <i class="icon wb-shopping-cart"></i>
                                    </button>
                                    <span class="ml-15 font-weight-400"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                    <div class="content-text text-center mb-0">
                                        
                                        
                                        <span class="font-size-20 font-weight-100"><?php echo e($business_summary['orders']); ?></span>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6 info-panel">
                            <div class="card card-shadow">
                                <div class="card-block bg-grey-100 p-20">
                                    <button type="button" class="btn btn-floating btn-sm btn-danger">
                                        <i class="icon fa-dollar"></i>
                                    </button>
                                    <span class="ml-15 font-weight-400 p-lg-60"><?php echo app('translator')->get("$string_file.orders_amount"); ?> : <?php echo e(isset($business_summary['income']['order_amount']) ? $currency.$business_summary['income']['order_amount'] : 0); ?></span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-14 font-weight-100 green-800">
                                            <?php echo e($title.' '.trans("$string_file.earning").': '); ?> <?php echo e(!empty($business_summary['income']['store_earning']) ? $currency.$business_summary['income']['store_earning'] : 0); ?> | </span>
                                        <span class="font-size-14 font-weight-100 blue-800">
                                            <?php echo e($merchant_name.' '.trans("$string_file.earning").': '); ?> <?php echo e(!empty($business_summary['income']['store_earning']) ? $currency.$business_summary['income']['merchant_earning'] : 0); ?>  <br></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <!-- Third Row -->
                    <!-- Third Left -->
                    <div class="row">
                        <div class="col-lg-12" id="ecommerceRecentOrder">
                            <div class="card card-shadow table-row">
                                <h4 class="example-title"><?php echo app('translator')->get("$string_file.all_orders"); ?> </h4>
                                <div class="card-block bg-white table-responsive">
                                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.order_id"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.earning_details"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.payment_details"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.product_details"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.deliver_on"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.current_status"); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if(!empty($arr_orders)): ?>
                                            <?php $sr = $arr_orders->firstItem(); $user_name = ''; $user_phone = ''; $user_email = '';
                                                        $driver_name = '';$driver_email = '';
                                                         $tax_amount =    !empty($order->tax) ? $order->tax : 0;
                                            ?>
                                            <?php $__currentLoopData = $arr_orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                     $user_name = is_demo_data($order->User->UserName,$order->Merchant);
                                                     $user_phone = is_demo_data($order->User->UserPhone,$order->Merchant);
                                                     $user_email = is_demo_data($order->User->email,$order->Merchant);
                                                    $currency = $order->CountryArea->Country->isoCode;
                                                ?>
                                                <tr>
                                                    <td><?php echo e($sr); ?></td>
                                                    <td>
                                                        <a href="<?php echo e(route('business-segment.order.detail',$order->id)); ?>"><?php echo e($order->merchant_order_id); ?></a>
                                                    </td>
                                                    <td>
                                                        <?php if(!empty($order->OrderTransaction)): ?>
                                                            <?php $transaction = $order->OrderTransaction;

                                                            ?>
                                                            <?php echo app('translator')->get("$string_file.grand_total"); ?> :  <?php echo e($currency.$order->final_amount_paid); ?> <br>
                                                            <?php echo e($order->BusinessSegment->full_name.' '.trans("$string_file.earning").': '.$currency.$transaction->business_segment_earning); ?> <br>
                                                            <?php echo e(trans("$string_file.merchant_earning").': '.$currency.$transaction->company_earning); ?> <br>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo e(trans("$string_file.mode").": ". $order->PaymentMethod->payment_method); ?><br>
                                                        <?php echo e(trans($string_file.".cart_amount").': '.$currency.$order->cart_amount); ?> <br>
                                                        <?php echo e(trans("$string_file.delivery_charge").': '. $currency.$order->delivery_amount); ?> <br>
                                                        <?php echo e(trans("$string_file.tax").': '.$currency.$tax_amount); ?> <br>
                                                        <?php echo e(trans("$string_file.tip_amount").': '.$currency.$order->tip_amount); ?> <br>
                                                        <?php echo e(trans("$string_file.discount").': '.$currency.$order->discount_amount); ?> <br>
                                                        <?php echo app('translator')->get("$string_file.grand_total"); ?> :  <?php echo e($currency.$order->final_amount_paid); ?>

                                                        <br>
                                                    </td>
                                                    <td>
                                                        <?php $product_detail = $order->OrderDetail; $products = "";?>
                                                        <?php $__currentLoopData = $product_detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                             <?php $weight =  isset($product->ProductVariant->weight) ? $product->ProductVariant->weight : "";
                                             $unit = isset($product->weight_unit_id) ? $product->WeightUnit->WeightUnitName : "";
                                             $unit = !empty($weight)  ? $product->quantity.' x '.$weight.' '.$unit : $product->quantity.$unit;
                                        ?>
                                                            <?php echo e($unit .' - '.$product->Product->Name($order->merchant_id)); ?>

                                                            (<?php echo e($product->ProductVariant->Name($order->merchant_id)); ?>})
                                                            ,<br>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo e($user_name); ?> <br>
                                                        <?php echo e($user_phone); ?> <br>
                                                        <?php echo e($user_email); ?> <br>
                                                    </td>
                                                    <td>
                                                        <?php echo convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone, null, $order->Merchant); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo convertTimeToUSERzone($order->order_date, $order->CountryArea->timezone, null, $order->Merchant,2); ?>

                                                    </td>
                                                    <td style="text-align: center">
                                                        <?php if($order->order_status == 11): ?>
                                                            <span class="badge badge-success font-weight-100"><?php echo e($arr_status[$order->order_status]); ?></span>
                                                        <?php elseif(in_array($order->order_status,[1,6,7,9,10])): ?>
                                                            <span class="badge btn-info font-weight-100"><?php echo e($arr_status[$order->order_status]); ?></span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger font-weight-100"><?php echo e($arr_status[$order->order_status]); ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php $sr++  ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                        <?php endif; ?>
                                    </table>
                                        <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $arr_orders, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/statics.blade.php ENDPATH**/ ?>