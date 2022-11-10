<?php $__env->startSection('content'); ?>
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
                    </div>
                    <h3 class="panel-title">
                        <i class="icon fa-gears" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.driver_configuration"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              action="<?php echo e(route('merchant.driver_configuration.store')); ?>">
                            <?php echo csrf_field(); ?>
                            <fieldset>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize"><?php echo e(trans("$string_file.auto_verify")); ?></label>
                                            <select name="auto_verify" id="auto_verify" class="form-control">
                                                <option value="1"
                                                        <?php if($configuration['auto_verify'] == 1): ?> selected <?php endif; ?>><?php echo e(trans("$string_file.active")); ?></option>
                                                <option value="0"
                                                        <?php if($configuration['auto_verify'] == 0): ?> selected <?php endif; ?>><?php echo e(trans("$string_file.inactive")); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize"><?php echo e(trans("$string_file.inactive_time")); ?>

                                                <span class="text-danger">*</span>(minutes)</label>
                                            <?php echo Form::number('inactive_time',old('inactive_time',isset($configuration['inactive_time']) ? $configuration['inactive_time'] : 15),['class'=>'form-control', 'id'=>'','placeholder'=>'Time in minutes','required'=>true]); ?>

                                        </div>
                                    </div>
                                    <?php if($merchant->Configuration && $merchant->Configuration->driver_suspend_penalty_enable == 1): ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="text-capitalize"><?php echo app('translator')->get("$string_file.penalty_enable"); ?></label>
                                                <select class="form-control" name="driver_penalty_enable"
                                                        onchange="handleDiv(this.value , 'driver-penalty')">
                                                    <option value="2"><?php echo app('translator')->get("$string_file.disable"); ?></option>
                                                    <option value="1" <?php echo e(($configuration['driver_penalty_enable'] == 1 || old('driver_penalty_enable') == 1) ? 'selected' : ''); ?>><?php echo app('translator')->get("$string_file.enable"); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 driver-penalty <?php echo e(($configuration['driver_penalty_enable'] != 1 && old('driver_penalty_enable') != 1) ? 'd-none' : ''); ?>">
                                            <div class="form-group">
                                                <label class="text-capitalize"><?php echo app('translator')->get("$string_file.driver_cancel_count"); ?></label>
                                                <input name="driver_cancel_count" class="form-control"
                                                       value="<?php echo e($configuration['driver_cancel_count']); ?>">
                                                <?php if($errors->first('driver_cancel_count')): ?>
                                                    <span class="text-danger"><?php echo e($errors->first('driver_cancel_count')); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 driver-penalty <?php echo e(($configuration['driver_penalty_enable'] != 1 && old('driver_penalty_enable') != 1) ? 'd-none' : ''); ?>">
                                            <div class="form-group">
                                                <label class="text-capitalize"><?php echo app('translator')->get("$string_file.penalty_period"); ?></label>
                                                <input name="driver_penalty_period" class="form-control"
                                                       value="<?php echo e($configuration['driver_penalty_period']); ?>">
                                                <?php if($errors->first('driver_penalty_period')): ?>
                                                    <span class="text-danger"><?php echo e($errors->first('driver_penalty_period')); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 driver-penalty <?php echo e(($configuration['driver_penalty_enable'] != 1 && old('driver_penalty_enable') != 1) ? 'd-none' : ''); ?>">
                                            <div class="form-group">
                                                <label class="text-capitalize"><?php echo app('translator')->get("$string_file.penalty_period_text"); ?></label>
                                                <input name="driver_penalty_period_next" class="form-control"
                                                       value="<?php echo e($configuration['driver_penalty_period_next']); ?>">
                                                <?php if($errors->first('driver_penalty_period_next')): ?>
                                                    <span class="text-danger"><?php echo e($errors->first('driver_penalty_period_next')); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <br>
                                <h5 class="form-section"><i
                                            class="fa fa-taxi"></i> <?php echo app('translator')->get("$string_file.cashout_configuration"); ?>
                                </h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize">
                                                <?php echo app('translator')->get("$string_file.cashout_minimum_amount"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   name="driver_cashout_min_amount"
                                                   placeholder=""
                                                   value="<?php echo e($configuration['driver_cashout_min_amount']); ?>"
                                                   required>
                                            <?php if($errors->has('driver_cashout_min_amount')): ?>
                                                <label class="danger"><?php echo e($errors->first('driver_cashout_min_amount')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                            </fieldset>
                            <div class="form-actions right" style="margin-bottom: 3%">
                                <?php if(Auth::user('merchant')->can('edit_configuration')): ?>
                                    <?php if(!$is_demo): ?>
                                    <button type="submit" class="btn btn-primary float-right">
                                        <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?>
                                    </button>
                                    <?php else: ?>
                                        <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        function handleDiv(value, cls) {
            if (parseInt(value) == 1) {
                $('.' + cls).removeClass('d-none');
                return
            }

            $('.' + cls).addClass('d-none')
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/random/driverconfiguration.blade.php ENDPATH**/ ?>