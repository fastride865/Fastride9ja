<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('excel.driverwalletreport',['driver_id' => $driver->id])); ?>" >
                            <button type="button" data-toggle="tooltip" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                <i class="wb-download" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                            </button>
                        </a>
                        <a href="<?php echo e(route('driver.index')); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="<?php echo app('translator')->get("$string_file.all_drivers"); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-google-wallet" aria-hidden="true"></i>
                        <?php echo e($driver->first_name." ".$driver->last_name); ?> <?php echo app('translator')->get("$string_file.wallet_transaction"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.amount"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.transaction_type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.payment_method"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.narration"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.receipt_number"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.date"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sn = 1; ?>
                        <?php $__currentLoopData = $wallet_transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet_transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sn); ?></td>
                                <td>
                                    <?php if($wallet_transaction->transaction_type == 1): ?>
                                        <span class="green-500">
                                             <?php echo e($wallet_transaction->amount); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="red-500">
                                             <?php echo e($wallet_transaction->amount); ?>

                                        </span>
                                    <?php endif; ?>

                                </td>
                                <td>
                                    <?php if($wallet_transaction->transaction_type == 1): ?>
                                        <?php echo app('translator')->get("$string_file.credit"); ?>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.debit"); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($wallet_transaction->payment_method == 1): ?>
                                        <?php echo app('translator')->get("$string_file.cash"); ?>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.non_cash"); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php $id = NULL; ?>
                                    <?php if(!empty($wallet_transaction->narration)): ?>
                                        <?php
                                             $booking_id = !empty($wallet_transaction->Booking) ? $wallet_transaction->Booking->merchant_booking_id : NULL;
                                             $order_id = !empty($wallet_transaction->Order) ? $wallet_transaction->Order->merchant_order_id: NULL;
                                             $handyman_order_id = !empty($wallet_transaction->HandymanOrder) ? $wallet_transaction->HandymanOrder->merchant_order_id: NULL;
                                        ?>
                                    <?php if(!empty($booking_id)): ?>
                                            <?php
                                                $id = $booking_id;
                                            ?>
                                    <?php elseif($order_id): ?>
                                        <?php
                                            $id = $order_id;
                                        ?>
                                    <?php elseif($handyman_order_id): ?>
                                    <?php
                                        $id = $handyman_order_id;
                                    ?>
                                    <?php endif; ?>
                                    <?php echo e(get_narration_value("DRIVER",$wallet_transaction->narration,$driver->merchant_id,$id)); ?>

                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e($wallet_transaction->receipt_number); ?>

                                </td>
                                <td>
                                    <?php echo convertTimeToUSERzone($wallet_transaction->created_at, $wallet_transaction->Driver->CountryArea->timezone,null,$wallet_transaction->Driver->Merchant); ?>

                                </td>
                            </tr>
                            <?php $sn++; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $wallet_transactions, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/wallet.blade.php ENDPATH**/ ?>