<?php $__env->startSection('content'); ?>
    <style>
        #ecommerceRecentbooking .table-row .card-block .table td {
            vertical-align: middle !important;
            height: 15px !important;
            font-size: 14px !important;
            padding: 8px 8px !important;
        }

        .dataTables_filter, .dataTables_info {
            display: none;
        }
    </style>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('merchant.handyman-service.earning.export',$arr_search)); ?>">
                            <button type="button" title="<?php echo app('translator')->get("$string_file.export_bookings"); ?>"
                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-download"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="icon wb-list" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.booking_statistics"); ?>
                        </span>
                    </h3>
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
                                    <span class="ml-15 font-weight-400"><?php echo app('translator')->get("$string_file.bookings"); ?></span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100"><?php echo e($total_bookings); ?></span>
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
                                    <span class="ml-15 font-weight-400"><?php echo app('translator')->get("$string_file.booking_amount"); ?></span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100"><?php echo e(isset($earning_summary['booking_amount']) ? $currency.$earning_summary['booking_amount'] : 0); ?></span>
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
                                        <span class="font-size-20 font-weight-100"><?php echo e(isset($earning_summary['merchant_total']) ? $currency.$earning_summary['merchant_total'] : 0); ?></span>
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
                                    <span class="ml-15 font-weight-400"><?php echo app('translator')->get("$string_file.driver_earning"); ?></span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100"><?php echo e(isset($earning_summary['driver_total']) ? $currency.$earning_summary['driver_total'] : 0); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Third Row -->
                    <!-- Third Left -->
                    <div class="row">
                        <div class="col-lg-12" id="ecommerceRecentbooking">
                            <div class="card card-shadow table-row">
                                <div class="card-block bg-white table-responsive">
                                    <table id="customDataTable"
                                           class="display nowrap table table-hover table-stripedw-full table-bordered report_table"
                                           style="width:100%">
                                        <thead>
                                        <tr class="text-center report_table_row_heading">
                                            <th rowspan="2"><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                            <th rowspan="2"><?php echo app('translator')->get("$string_file.booking_id"); ?></th>
                                            <th rowspan="2"><?php echo app('translator')->get("$string_file.payment_method"); ?></th>
                                            <th rowspan="2"><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                                            <th rowspan="2"><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                                            <th colspan="5"><?php echo app('translator')->get("$string_file.booking_amount"); ?></th>
                                            <th colspan="3"><?php echo app('translator')->get("$string_file.merchant_earning"); ?></th>
                                            <th colspan="3"><?php echo app('translator')->get("$string_file.driver_earning"); ?></th>
                                            <th rowspan="2"><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                                        </tr>
                                        <tr class="report_table_row_heading">
                                            <th><?php echo app('translator')->get("$string_file.service"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.tax"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.discount"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.tip"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.total"); ?></th>


                                            <th><?php echo app('translator')->get("$string_file.earning"); ?> </th>
                                            <th><?php echo app('translator')->get("$string_file.tax"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.total"); ?></th>

                                            <th><?php echo app('translator')->get("$string_file.earning"); ?> </th>
                                            <th><?php echo app('translator')->get("$string_file.tip"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.total"); ?></th>

                                        </tr>
                                        </thead>
                                        <tbody>

                                        <?php if(!empty($arr_bookings)): ?>
                                            <?php $sr = $arr_bookings->firstItem(); $user_name = ''; $user_phone = ''; $user_email = ''; $driver_name = '';$driver_email = ''; ?>
                                            <?php $__currentLoopData = $arr_bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $tax_amount =    !empty($booking->tax) ? $booking->tax : 0;
                                                ?>
                                                <?php if(!empty($booking->HandymanOrderTransaction)): ?>
                                                    <?php $transaction = $booking->HandymanOrderTransaction;
                                                     $currency = $booking->CountryArea->Country->isoCode;
                                                    ?>
                                                <?php endif; ?>
                                                <tr>
                                                    <td><?php echo e($sr); ?></td>
                                                    <td>
                                                        <a href="<?php echo e(route('merchant.handyman.order.detail',$booking->id)); ?>"><?php echo e($booking->merchant_order_id); ?></a>
                                                    </td>
                                                    <td><?php echo e(!empty($booking->PaymentMethod->MethodName($booking->merchant_id)) ? $booking->PaymentMethod->MethodName($booking->merchant_id) : $booking->PaymentMethod->payment_method); ?></td>
                                                    <td><?php echo e(is_demo_data($booking->User->first_name.' '.$booking->User->last_name,$booking->Merchant)); ?></td>
                                                    <td><?php echo e(is_demo_data($booking->Driver->first_name.' '.$booking->Driver->last_name,$booking->Merchant)); ?></td>
                                                    <td>
                                                        <?php echo e(($booking->final_amount_paid - $tax_amount)); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e($tax_amount); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e(!empty($booking->discount_amount) ? $booking->discount_amount : 0); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e(!empty($booking->tip) ? $booking->tip : 0); ?>

                                                    </td>
                                                    <td>
                                                        <b> <?php echo e($currency.' '.$booking->final_amount_paid); ?></b>
                                                    </td>
                                                    <td>
                                                        <?php if(!empty($transaction)): ?>
                                                            <?php echo e($transaction->company_earning - $tax_amount); ?>

                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo e(($tax_amount)); ?>

                                                    </td>
                                                    <td>
                                                        
                                                        <b> <?php echo e($currency.' '.($transaction->company_earning)); ?> </b>
                                                    </td>
                                                    <td>
                                                        <?php if(!empty($transaction)): ?>
                                                            <?php echo e($transaction->driver_earning); ?>

                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo e(!empty($booking->tip) ? $booking->tip : 0); ?>

                                                    </td>
                                                    <td>
                                                        <b> <?php echo e($currency.' '.($transaction->driver_earning)); ?></b>
                                                    </td>
                                                    <td>
                                                        <?php echo convertTimeToUSERzone($booking->created_at, $booking->CountryArea->timezone, null, $booking->Merchant); ?>

                                                    </td>
                                                </tr>
                                                <?php $sr++  ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                        <?php endif; ?>
                                    </table>
                                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $arr_bookings, 'data' => $arr_search], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/report/handyman-services/earning.blade.php ENDPATH**/ ?>