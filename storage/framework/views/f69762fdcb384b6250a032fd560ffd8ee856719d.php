<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('business-segment.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title">
                        <i class=" icon fa-exchange" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.wallet_transaction"); ?></h3>
                </header>
                <div class="panel-body">
                    <h4><?php echo app('translator')->get("$string_file.wallet_money"); ?> : <?php echo e($business_segment->Country->isoCode.' '.$business_segment->wallet_amount); ?></h4>
                    <?php echo $search_view; ?>

                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.transaction_type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.order_id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.payment"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.amount"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.narration"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $wallet_transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet_transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php if($wallet_transaction->transaction_type == 1): ?>
                                        <?php echo app('translator')->get("$string_file.credit"); ?>
                                    <?php else: ?>
                                       <?php echo app('translator')->get("$string_file.debit"); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($wallet_transaction->order_id)): ?>
                                        <a target="_blank" title="<?php echo app('translator')->get("$string_file.order_details"); ?>"
                                           href="<?php echo e(route('business-segment.order.detail',$wallet_transaction->order_id)); ?>"><?php echo e($wallet_transaction->order_id); ?></a>
                                    <?php else: ?>
                                        --
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
                                    <?php echo e($wallet_transaction->BusinessSegment->Country->isoCode.' '.$wallet_transaction->amount); ?>

                                </td>
                                <td>
                                    <?php echo e(get_narration_value('BUSINESS_SEGMENT',$wallet_transaction->narration,$wallet_transaction->merchant_id,$wallet_transaction->order_id,NULL)); ?>




















                                </td>
                                <td>
                                    <?php echo convertTimeToUSERzone($wallet_transaction->created_at,null, null, $wallet_transaction->BusinessSegment->Merchant); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('business-segment.shared.table-footer', ['table_data' => $wallet_transactions, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/wallet/index.blade.php ENDPATH**/ ?>