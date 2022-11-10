<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->edit_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('merchant.paymentMethod.index')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.edit_payment_method"); ?> </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="<?php echo e(route('merchant.paymentMethod.update',$payment->id)); ?>">
                        <?php echo e(method_field('PUT')); ?>

                        <?php $required = false; ?>
                        <?php echo csrf_field(); ?>
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.name"); ?><span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="payment_name"
                                               name="payment_name"
                                               value="<?php if(!empty($payment->MethodName($merchant->id))): ?> <?php echo e($payment->MethodName($merchant->id)); ?> <?php else: ?> <?php echo e($payment->payment_method); ?> <?php endif; ?>"
                                               placeholder="" required>
                                        <?php if($errors->has('payment_name')): ?>
                                            <label class="danger"><?php echo e($errors->first('payment_name')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="" for=""><?php echo app('translator')->get("$string_file.image"); ?>
                                            <span class="text-danger">*</span>
                                            <?php if(!empty($icon)): ?>
                                                <a href="<?php echo e($icon); ?>"
                                                   target="_blank"><?php echo app('translator')->get("$string_file.view"); ?></a>
                                            <?php endif; ?>
                                        </label>
                                        <input type="file" class="form-control" id="image" name="p_icon_image"
                                                <?php echo e($required); ?>/>
                                        <?php if($errors->has('image')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('image')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-actions float-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> <?php echo app('translator')->get("$string_file.update"); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/payment_methods/edit.blade.php ENDPATH**/ ?>