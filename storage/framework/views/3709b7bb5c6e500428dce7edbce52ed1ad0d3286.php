<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="<?php echo e(route('merchant.driver.cashout_request')); ?>">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
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
                        <?php echo app('translator')->get("$string_file.cashout_request_action"); ?>
                            <span class="long_text">
                                            <?php echo e($driver_cashout_request->Driver->fullName); ?>

                                                ( <?php echo e($driver_cashout_request->Driver->phoneNumber); ?> / <?php echo e($driver_cashout_request->Driver->email); ?> )</span>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="<?php echo e(route('merchant.driver.cashout_status_update', $driver_cashout_request->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.wallet_money"); ?>
                                        </label><br>
                                        <?php echo e($driver_cashout_request->Driver->CountryArea->Country->isoCode.' '.$driver_cashout_request->Driver->wallet_money); ?>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.cashout_amount"); ?>
                                        </label><br>
                                        <?php echo e($driver_cashout_request->Driver->CountryArea->Country->isoCode.' '.$driver_cashout_request->amount); ?>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.requested_at"); ?>
                                        </label><br>
                                        <?php echo e(convertTimeToUSERzone($driver_cashout_request->created_at, $driver_cashout_request->Driver->CountryArea->timezone, $driver_cashout_request->merchant_id, null)); ?>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.action_date"); ?>
                                        </label><br>
                                        <?php if($driver_cashout_request->cashout_status != 0): ?>
                                            <?php echo e(convertTimeToUSERzone($driver_cashout_request->updated_at, $driver_cashout_request->Driver->CountryArea->timezone, $driver_cashout_request->merchant_id, null)); ?>

                                        <?php else: ?>
                                            ---
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.cashout_status"); ?>
                                        </label><br>
                                        <?php switch($driver_cashout_request->cashout_status):
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
                                    </div>
                                </div>
                                <?php if($driver_cashout_request->cashout_status == 0): ?>
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            <?php echo app('translator')->get("$string_file.status"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" id="cashout_status"
                                                name="cashout_status">
                                            <option value="0"
                                                    <?php if($driver_cashout_request->cashout_status == 0): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.pending"); ?></option>
                                            <option value="1"
                                                    <?php if($driver_cashout_request->cashout_status == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.success"); ?></option>
                                            <option value="2"
                                                    <?php if($driver_cashout_request->cashout_status == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.reject"); ?></option>
                                        </select>
                                        <?php if($errors->has('cashout_status')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('cashout_status')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.action_by"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="action_by"
                                               name="action_by"
                                               placeholder="<?php echo app('translator')->get("$string_file.action_by"); ?>"
                                               value="<?php echo e(old('action_by',$driver_cashout_request->action_by)); ?>"
                                               required>
                                        <?php if($errors->has('action_by')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('action_by')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.transaction_id"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="transaction_id"
                                               name="transaction_id"
                                               placeholder="<?php echo app('translator')->get("$string_file.transaction_id"); ?>"
                                               value="<?php echo e(old('transaction_id',$driver_cashout_request->transaction_id)); ?>"
                                               required>
                                        <?php if($errors->has('transaction_id')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('transaction_id')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.comment"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="comment"
                                               name="comment"
                                               placeholder="<?php echo app('translator')->get("$string_file.comment"); ?>"
                                               value="<?php echo e(old('comment',$driver_cashout_request->comment)); ?>" required>
                                        <?php if($errors->has('comment')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('comment')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <?php if($driver_cashout_request->cashout_status == 0): ?>
                            <div class="form-actions d-flex flex-row-reverse p-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.update"); ?>
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/cashout/edit.blade.php ENDPATH**/ ?>