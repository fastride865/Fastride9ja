<?php $__env->startSection('content'); ?>
    <style>
        #ecommerceRecentride .table-row .card-block .table td {
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
            <div class="panel panel-brideed">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('merchant.taxi.earning.export',$arr_search)); ?>">
                            <button type="button" title="<?php echo app('translator')->get("$string_file.export_rides"); ?>"
                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-download"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="icon wb-list" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.ride_statistics"); ?>
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
                                    <span class="ml-15 font-weight-400"><?php echo app('translator')->get("$string_file.rides"); ?></span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100"><?php echo e($total_rides); ?></span>
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
                                    <span class="ml-15 font-weight-400"><?php echo app('translator')->get("$string_file.ride_amount"); ?></span>
                                    <div class="content-text text-center mb-0">
                                        <span class="font-size-20 font-weight-100"><?php echo e(isset($earning_summary['ride_amount']) ? $currency.$earning_summary['ride_amount'] : 0); ?></span>
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
                                        <span class="font-size-20 font-weight-100"><?php echo e(isset($earning_summary['merchant_earning']) ? $currency.$earning_summary['merchant_earning'] : 0); ?></span>
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
                                        <span class="font-size-20 font-weight-100"><?php echo e(isset($earning_summary['driver_earning']) ? $currency.$earning_summary['driver_earning'] : 0); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <!-- Third Row -->
                    <!-- Third Left -->
                    <div class="row">
                        <div class="col-lg-12" id="ecommerceRecentride">
                            <div class="card card-shadow table-row">
                                <div class="card-block bg-white table-responsive">
                                    <table id="customDataTable" class="display nowrap table table-hover table-bordered report_table"
                                           style="width:100%">
                                        <thead>
                                      <?php
                                          $col_span =  $arr_parameter->count();$extra_col_span = 0; $merchant_extra_col_span = 0;?>
                                        <tr class="text-center report_table_row_heading">
                                            <th rowspan="2"><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                            <th rowspan="2"><?php echo app('translator')->get("$string_file.ride_id"); ?></th>
                                            <th rowspan="2"><?php echo app('translator')->get("$string_file.payment_method"); ?></th>
                                            <th rowspan="2"><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                                            <th rowspan="2"><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                                            <th rowspan="2"><?php echo app('translator')->get("$string_file.pickup_location"); ?></th>
                                            <th colspan=<?php echo e(($col_span + 8 + $extra_col_span)); ?>><?php echo app('translator')->get("$string_file.ride_amount"); ?></th>
                                            <th colspan="<?php echo e($merchant_extra_col_span + 5); ?>"><?php echo app('translator')->get("$string_file.merchant_earning"); ?></th>
                                            <th colspan="5"><?php echo app('translator')->get("$string_file.driver_earning"); ?></th>
                                            <th rowspan="2"><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                                        </tr>
                                        <tr class="report_table_row_heading">
                                            <th><?php echo app('translator')->get("$string_file.base_fare"); ?></th>

                                            <?php $__currentLoopData = $arr_parameter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($param['parameterType'] != 13): ?>
                                                <th><?php echo e(!empty($param['name']) ? $param['name'] : ""); ?></th>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                            <th><?php echo app('translator')->get("$string_file.extra_charges"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.sub_total_before_discount"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.discount"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.sub_total"); ?></th>

                                            <?php $__currentLoopData = $arr_parameter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($param['parameterType'] == 13): ?>
                                                    <th><?php echo e(!empty($param['name']) ? $param['name'] : ""); ?></th>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                            <th><?php echo app('translator')->get("$string_file.tip"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.toll"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.paid_amount"); ?></th>

                                            <th><?php echo app('translator')->get("$string_file.earning"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.tax"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.cancellation_charges"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.discount"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.total"); ?></th>

                                            <th><?php echo app('translator')->get("$string_file.earning"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.tip"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.toll"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.discount"); ?></th>
                                            <th><?php echo app('translator')->get("$string_file.total"); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if(!empty($arr_rides_details)): ?>
                                            <?php $sr = $arr_rides->firstItem(); $user_name = ''; $user_phone = ''; $user_email = '';
                                                        $driver_name = '';$driver_email = ''; $currency = "";
                                                        $tax_amount =    !empty($ride->tax) ? $ride->tax : 0;
                                            ?>
                                            <?php $__currentLoopData = $arr_rides_details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ride): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                               $arr_invoice = array_column($ride->invoice,NULL,'id');
                                               $tip = isset($arr_invoice['Tip']) ? $arr_invoice['Tip']['value'] : 0;
                                               $toll = isset($arr_invoice['TollCharges']) ? $arr_invoice['TollCharges']['value'] : 0;
                                               $cancellation_amount = isset($arr_invoice['Cancellation fee']) ? $arr_invoice['Cancellation fee']['value'] : 0;
                                               $additional_mover_amount = isset($arr_invoice['Additional Mover Charger']) ? $arr_invoice['Additional Mover Charger']['value'] : 0;
                                               // peak time charges is storing in extra charges column
                                               //$peak_time_charge = isset($arr_invoice['Peak Time Charges']) ? $arr_invoice['Peak Time Charges']['value'] : 0;
                                               $discount = isset($arr_invoice['promo_code']) ? $arr_invoice['promo_code']['value'] : 0;
                                               $ride_total = 0;
                                                     $currency = $ride->CountryArea->Country->isoCode;
                                                ?>
                                                <?php if(!empty($ride->BookingTransaction)): ?>
                                                    <?php $transaction = $ride->BookingTransaction;
                                                    $ride_total = $transaction->sub_total_before_discount;
                                                    ?>
                                                <?php endif; ?>
                                                <tr>
                                                    <td><?php echo e($sr); ?></td>
                                                    <td>
                                                        <a href="<?php echo e(route('merchant.booking.invoice',$ride->id)); ?>"><?php echo e($ride->merchant_booking_id); ?></a>
                                                    </td>
                                                   
                                                    <td>
                                                        <?php if(!empty($ride->PaymentMethod)): ?>
                                                        <?php echo e($ride->PaymentMethod->MethodName($ride->merchant_id) ? $ride->PaymentMethod->MethodName($ride->merchant_id) : $ride->PaymentMethod->payment_method); ?>

                                                        <?php else: ?>
                                                            --
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e(is_demo_data($ride->User->first_name.' '.$ride->User->last_name,$ride->Merchant)); ?></td>
                                                    <td><?php echo e(is_demo_data($ride->Driver->first_name.' '.$ride->Driver->last_name,$ride->Merchant)); ?></td>
                                                    <td><?php echo e($ride->pickup_location); ?></td>
                                                    <td>
                                                    
                                                        <?php $__currentLoopData = $arr_invoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if(isset($invoice['parameterType']) && $invoice['parameterType'] == 10): ?>
                                                                 <?php echo e(!empty($invoice) ? $invoice['value'] : 0); ?>

                                                            <?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </td>
                                                    <?php $__currentLoopData = $arr_parameter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($param['parameterType'] != 13): ?>
                                                        <td><?php echo e(isset($arr_invoice[$param['id']]) ? $arr_invoice[$param['id']]['value'] : 0); ?></td>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <td>

                                                        <?php $__currentLoopData = $arr_invoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if($invoice['id'] == "Peak Time Charges" || $invoice['id'] == "Additional Mover Charger" || $invoice['id'] == "Cancellation"): ?>
                                                             <?php echo e($invoice['name']); ?> : <?php echo e($invoice['value']); ?> <br>
                                                            <?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                           <?php echo app('translator')->get("$string_file.total"); ?> : <?php echo e($transaction->extra_charges + $cancellation_amount + $additional_mover_amount); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e($transaction->sub_total_before_discount); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e($discount); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e(($transaction->sub_total_before_discount - $discount)); ?>

                                                    </td>
                                                    <?php $__currentLoopData = $arr_parameter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        
                                                        <?php if($param['parameterType'] == 13): ?>
                                                            <td><?php echo e(isset($arr_invoice[$param['id']]) ? $arr_invoice[$param['id']]['value'] : 0); ?></td>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <td>
                                                        <?php echo e($tip); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e($toll); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e($currency.' '.$ride->final_amount_paid); ?>

                                                    </td>
                                                    <td>
                                                        <?php if(!empty($transaction)): ?>
                                                            <?php echo e($transaction->company_earning); ?>

                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e($transaction->tax_amount); ?></td>
                                                    <td><?php echo e($transaction->cancellation_charge_received); ?></td>
                                                    <td>
                                                      -<?php echo e($discount); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e($currency.' '.($transaction->company_gross_total)); ?>

                                                    </td>
                                                    <td>
                                                        
                                                        <?php if(!empty($transaction)): ?>
                                                            <?php echo e(($transaction->driver_earning)); ?>

                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo e($tip); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e($toll); ?>

                                                    </td>
                                                    <td>
                                                        <?php echo e($discount); ?>

                                                    </td>
                                                    <td>
                                                        <?php if(!empty($transaction)): ?>
                                                            <?php echo e($currency.' '.($transaction->driver_total_payout_amount)); ?>

                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo convertTimeToUSERzone($ride->created_at, $ride->CountryArea->timezone, null, $ride->Merchant); ?>

                                                    </td>
                                                 </tr>
                                                <?php $sr++  ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                        <?php endif; ?>
                                    </table>
                                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $arr_rides, 'data' => $arr_search], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/report/taxi-services/earning.blade.php ENDPATH**/ ?>