<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
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
                        <?php echo app('translator')->get("$string_file.segment"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification" enctype="multipart/form-data" action="<?php echo e(route('merchant.segment.update',$segment->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.name"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="service" name="segment" value="<?php echo e($segment->segment_locale_name); ?>" placeholder="" required>
                                    <?php if($errors->has('segment')): ?>
                                        <label class="danger"><?php echo e($errors->first('segment')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sequence">
                                        <?php echo app('translator')->get("$string_file.sequence"); ?><span class="text-danger">*</span>
                                    </label>
                                    <?php echo Form::number('sequence',old('sequence',isset($segment['pivot']->sequence) ? $segment['pivot']->sequence : 1),['class'=>'form-control','required'=>true,'id'=>'sequence','min'=>0,'max'=>100]); ?>

                                    <?php if($errors->has('sequence')): ?>
                                        <label class="danger"><?php echo e($errors->first('sequence')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sequence">
                                        <?php echo app('translator')->get("$string_file.is_coming_soon"); ?><span class="text-danger">*</span>
                                    </label>

                                    <?php echo Form::select('is_coming_soon',[2=>trans("$string_file.no"),1 =>trans("$string_file.yes")],old('is_coming_soon',isset($segment['pivot']->is_coming_soon) ? $segment['pivot']->is_coming_soon : 2),['class'=>'form-control']); ?>

                                    <?php if($errors->has('is_coming_soon')): ?>
                                        <label class="danger"><?php echo e($errors->first('is_coming_soon')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="profile_image">
                                        <?php echo app('translator')->get("$string_file.icon"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="icon" name="icon" placeholder="">
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


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/service_types/segment.blade.php ENDPATH**/ ?>