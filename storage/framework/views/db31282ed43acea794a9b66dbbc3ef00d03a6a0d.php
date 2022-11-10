<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('business-segment.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <button type="button" class="btn btn-icon btn-success float-right"
                                title="<?php echo app('translator')->get("$string_file.add_vehicle"); ?> "
                                data-toggle="modal"
                                data-target="#cashout-request" style="margin:10px">
                            <i class="wb-plus"></i>
                        </button>
                    </div>
                    <h3 class="panel-title">
                        <i class="icon fa-taxi" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.cashout_request"); ?>  </h3>
                </header>
                <div class="panel-body container-fluid">
                    <h4><?php echo app('translator')->get("$string_file.wallet_money"); ?> : <?php echo e($business_segment->Country->isoCode.' '.$business_segment->wallet_amount); ?></h4>
                    <?php echo $search_view; ?>

                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.cashout_amount"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action_by"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.transaction_id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.comment"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.updated_at"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $cashout_requests->firstItem() ?>
                        <?php $__currentLoopData = $cashout_requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cashout_request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php echo e($cashout_request->BusinessSegment->Country->isoCode.' '.$cashout_request->amount); ?></td>
                                <td>
                                    <?php switch($cashout_request->cashout_status):
                                        case (0): ?>
                                        <small class="badge badge-round badge-warning float-left"><?php echo app('translator')->get("$string_file.pending"); ?></small>
                                        <?php break; ?>;
                                        <?php case (1): ?>
                                        <small class="badge badge-round badge-info float-left"><?php echo app('translator')->get("$string_file.success"); ?></small>
                                        <?php break; ?>;
                                        <?php case (2): ?>
                                        <small class="badge badge-round badge-danger float-left"><?php echo app('translator')->get("$string_file.rejected"); ?></small>
                                        <?php break; ?>;
                                        <?php default: ?>
                                        ----
                                    <?php endswitch; ?>
                                </td>
                                <td><?php echo e(($cashout_request->action_by != '') ? $cashout_request->action_by : '---'); ?></td>
                                <td><?php echo e(($cashout_request->transaction_id) ? $cashout_request->transaction_id : '---'); ?></td>
                                <td><?php echo e(($cashout_request->comment != '') ? $cashout_request->comment : '---'); ?></td>
                                <td>
                                    <?php echo convertTimeToUSERzone($cashout_request->created_at,null, null, $cashout_request->BusinessSegment->Merchant); ?>

                                </td>
                                <td>
                                    <?php echo convertTimeToUSERzone($cashout_request->updated_at,null, null, $cashout_request->BusinessSegment->Merchant); ?>

                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('business-segment.shared.table-footer', ['table_data' => $cashout_requests, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade show" id="cashout-request" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b> <?php echo app('translator')->get("$string_file.register_cashout_request"); ?> </b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?php echo e(route('business-segment.cashout.request')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo app('translator')->get("$string_file.amount"); ?> <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="text" id="amount" name="amount" min="1" class="form-control" placeholder="" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn" data-dismiss="modal"
                               value="<?php echo app('translator')->get("$string_file.close"); ?>">
                        <input type="submit" class="btn btn-outline-primary btn" value="<?php echo app('translator')->get("$string_file.add"); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/wallet/cashout.blade.php ENDPATH**/ ?>