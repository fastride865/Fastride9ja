<?php $__env->startSection('content'); ?>
    <style>
        #ecommerceRecentOrder .table-row .card-block .table td {
            vertical-align: middle !important;
            height: 15px !important;
            font-size: 14px !important;
            padding: 8px 8px !important;
        }
        .dataTables_filter, .dataTables_info { display: none; }
    </style>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                    </div>
                    <h3 class="panel-title">
                        <i class="icon wb-list" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.order_earning_statistics"); ?>
                        </span>
                    </h3>
                    <div class="panel-actions">
                        <a href="<?php echo e(route('business-segment.earning.export')); ?>">
                            <button type="button" title="<?php echo app('translator')->get("$string_file.export_orders"); ?>"
                                    class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-download"></i>
                            </button>
                        </a>
                    </div>
                </header>
                <div class="panel-body container-fluid">
                <?php echo $search_view; ?>

                    <hr>
                    <!-- First Row -->
                    <div class="row">
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
                        <div class="col-xl-3 col-md-3 info-panel">
                            <div class="card card-shadow">
                                <div class="card-block bg-grey-100 p-20">
                                    <button type="button" class="btn btn-floating btn-sm btn-danger">
                                        <i class="icon fa-dollar"></i>
                                    </button>
                                    <span class="ml-15 font-weight-400"><?php echo app('translator')->get("$string_file.orders_amount"); ?></span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100"><?php echo e(isset($business_summary['income']['store_earning']) ? $currency.$business_summary['income']['order_amount'] : 0); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-3 info-panel">
                            <div class="card card-shadow">
                                <div class="card-block bg-grey-100 p-20">
                                    <button type="button" class="btn btn-floating btn-sm btn-danger">
                                        <i class="icon fa-percent"></i>
                                    </button>
                                    <span class="ml-15 font-weight-400"><?php echo app('translator')->get("$string_file.merchant_earning"); ?></span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100"><?php echo e(isset($business_summary['income']['merchant_earning']) ? $currency.$business_summary['income']['merchant_earning'] : 0); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-3 info-panel">
                            <div class="card card-shadow">
                                <div class="card-block bg-grey-100 p-20">
                                    <button type="button" class="btn btn-floating btn-sm btn-danger">
                                        <i class="icon fa-dollar"></i>
                                    </button>
                                    <span class="ml-15 font-weight-400"><?php echo app('translator')->get("$string_file.total_earning"); ?></span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100"><?php echo e(isset($business_summary['income']['store_earning']) ? $currency.$business_summary['income']['store_earning'] : 0); ?></span>
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

                                <div class="card-block bg-white table-responsive">
                                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.order_id"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.store_earning"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.merchant_earning"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.order_amount"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.cart_amount"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.tax"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.delivery_charge"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.other_charges"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if(!empty($arr_orders)): ?>
                                            <?php $sr = $arr_orders->firstItem(); $user_name = ''; $user_phone = ''; $user_email = '';
                                                        $driver_name = '';$driver_email = '';
                                                         $tax_amount =    !empty($order->tax) ? $order->tax : 0;
                                            ?>
                                            <?php $__currentLoopData = $arr_orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(!empty($order->OrderTransaction)): ?>
                                                    <?php $transaction = $order->OrderTransaction;
                                                    ?>
                                                <?php endif; ?>
                                                <tr>
                                                    <td><?php echo e($sr); ?></td>
                                                    <td>
                                                        <a href="<?php echo e(route('business-segment.order.invoice',$order->id)); ?>"><?php echo e($order->merchant_order_id); ?></a>
                                                    </td>
                                                    <td>
                                                        <?php if(!empty($transaction)): ?>
                                                            <?php echo e($transaction->business_segment_earning); ?>

                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if(!empty($transaction)): ?>
                                                            <?php echo e($transaction->company_earning); ?>

                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo e($order->final_amount_paid); ?>

                                                    </td>
                                                    <td>
                                                      <?php echo e($order->cart_amount); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e($order->tax); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e($order->delivery_amount); ?>

                                                    </td>
                                                    <td>
                                                        <?php if(!empty($order->tip_amount)): ?>
                                                         <?php echo app('translator')->get("$string_file.tip"); ?> : <?php echo e($order->tip_amount); ?>

                                                        <?php endif; ?>
                                                    </td>

                                                    <td>
                                                        <?php echo convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone,null,$order->Merchant); ?>

                                                    </td>
                                                </tr>
                                                <?php $sr++  ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                        <?php endif; ?>
                                    </table>
                                        <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $arr_orders, 'data' => $arr_search], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/report/earning.blade.php ENDPATH**/ ?>