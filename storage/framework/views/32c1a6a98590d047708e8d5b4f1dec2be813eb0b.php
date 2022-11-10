<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" >





                            <a href="<?php echo e(route('excel.merchant.orders',$arr_search)); ?>">
                                <button type="button" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"
                                        class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="fa fa-download"></i>
                                </button>
                            </a>
                            <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class="icon wb-list" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.all_orders"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <!-- End First Row -->

                    <!-- second Row -->
                    <div class="row">
                        <!-- First Row -->
                    </div>
                         <?php echo $search_view; ?>

                    <hr>

                    <!-- Third Row -->
                    <!-- Third Left -->
                    <div class="row">
                        <div class="col-lg-12" id="ecommerceRecentOrder">
                            <div class="card card-shadow table-row">
                                <div class="card-block bg-white table-responsive">
                                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.order_id"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.earning_details"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.payment_details"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.product_details"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.store_details"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.deliver_on"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.current_status"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if(!empty($arr_orders)): ?>
                                            <?php $sr = $arr_orders->firstItem(); $user_name = ''; $user_phone = ''; $user_email = '';
                                                        $driver_name = '';$driver_email = '';
                                            ?>
                                            <?php $__currentLoopData = $arr_orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                     $user_name = is_demo_data($order->User->UserName,$order->Merchant);
                                                     $user_phone = is_demo_data($order->User->UserPhone,$order->Merchant);
                                                     $user_email = is_demo_data($order->User->email,$order->Merchant);
                                                     $currency = $order->CountryArea->Country->isoCode;
                                                     $tax_amount =    !empty($order->tax) ? $order->tax : 0;

                                                     $store_name = is_demo_data($order->BusinessSegment->full_name,$order->Merchant);
                                                     $store_phone = is_demo_data($order->BusinessSegment->phone_number,$order->Merchant);
                                                     $store_email = is_demo_data($order->BusinessSegment->email,$order->Merchant);
                                                ?>
                                                <tr>
                                                    <td><?php echo e($sr); ?></td>
                                                    <td>
                                                        <a href="<?php echo e(route('driver.order.detail',$order->id)); ?>"><?php echo e($order->merchant_order_id); ?></a>
                                                    </td>
                                                    <td>
                                                        <?php if(!empty($order->OrderTransaction)): ?>
                                                            <?php $transaction = $order->OrderTransaction;

                                                            ?>
                                                            <?php echo app('translator')->get("$string_file.grand_total"); ?> :  <?php echo e($currency.$order->final_amount_paid); ?> <br>
                                                            <?php echo e(trans("$string_file.store_earning").': '.$currency.$transaction->business_segment_earning); ?> <br>
                                                            <?php echo e(trans("$string_file.merchant_earning").': '.$currency.$transaction->company_earning); ?> <br>
                                                            <?php echo e(trans("$string_file.driver_earning").": ". $currency.$transaction->driver_earning); ?><br>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>

                                                        <?php echo e(trans("$string_file.mode").": ". $order->PaymentMethod->payment_method); ?><br>
                                                        <?php echo e(trans($string_file.".cart_amount"). ': '.$currency.$order->cart_amount); ?> <br>
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
                                                            <?php echo e($product->quantity.' '.$unit.' '.$product->Product->Name($order->merchant_id)); ?>,<br>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo e($store_name); ?> <br>
                                                        <?php echo e($store_phone); ?> <br>
                                                        <?php echo e($store_email); ?> <br>
                                                    </td>
                                                    <td>
                                                        <?php echo e($user_name); ?> <br>
                                                        <?php echo e($user_phone); ?> <br>
                                                        <?php echo e($user_email); ?> <br>
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
                                                    <td>
                                                        <?php echo convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone, null, $order->Merchant); ?>

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
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>





    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/business-segment/orders.blade.php ENDPATH**/ ?>