<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
          <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="<?php echo e(route('merchant.serviceType.index')); ?>">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px">
                                    <i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.service"); ?> (In <?php echo app('translator')->get("$string_file.segment"); ?> : <?php echo e($segment); ?>)</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="<?php echo e(route('merchant.serviceType.update',isset($service->id) ? $service->id : NULL)); ?>">
                        <?php echo e(method_field('PUT')); ?>

                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.service_type"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="service"
                                           name="service" value="<?php if(isset($service->service_locale_name)): ?> <?php echo e($service->service_locale_name); ?> <?php endif; ?>"
                                           placeholder="" required>
                                    <?php if($errors->has('service')): ?>
                                        <label class="danger"><?php echo e($errors->first('service')); ?></label>
                                    <?php endif; ?>
                                    <?php echo Form::hidden('segment_id',$segment_id); ?>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.description"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="description"
                                           name="description" value="<?php if(isset($service->service_locale_description)): ?> <?php echo e($service->service_locale_description); ?> <?php endif; ?>"
                                           placeholder="" required>
                                    <?php if($errors->has('description')): ?>
                                        <label class="danger"><?php echo e($errors->first('description')); ?></label>
                                    <?php endif; ?>
                                    <?php echo Form::hidden('segment_id',$segment_id); ?>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sequence">
                                        <?php echo app('translator')->get("$string_file.sequence"); ?><span class="text-danger">*</span>
                                    </label>
                                    <?php echo Form::number('sequence',old('sequence',isset($service['pivot']->sequence) ? $service['pivot']->sequence : 1),['class'=>'form-control','required'=>true,'id'=>'sequence','min'=>0,'max'=>100]); ?>

                                    <?php if($errors->has('sequence')): ?>
                                        <label class="danger"><?php echo e($errors->first('sequence')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="icon">
                                        <?php echo app('translator')->get("$string_file.icon"); ?>
                                        <?php if(isset($service['pivot']->service_icon) && $service['pivot']->service_icon != ''): ?>
                                            <a href="<?php echo e(get_image($service['pivot']->service_icon,'service')); ?>" target="_blank"><?php echo app('translator')->get("$string_file.view"); ?></a>
                                        <?php endif; ?>
                                    </label>
                                    <input type="file" class="form-control" id="icon" name="icon" placeholder="<?php echo app('translator')->get("$string_file.icon"); ?>">
                                </div>
                            </div>
                        </div>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/service_types/form.blade.php ENDPATH**/ ?>