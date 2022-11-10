<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->edit_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('packages.index')); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="<?php echo app('translator')->get("$string_file.view_package"); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.edit_package"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification" enctype="multipart/form-data"
                          action="<?php echo e(route('packages.update', $package->id)); ?>"> <?php echo e(method_field('PUT')); ?>

                        <?php echo csrf_field(); ?>
                        <?php echo Form::hidden('service_type_id',$package->service_type_id); ?>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="emailAddress5"><?php echo app('translator')->get("$string_file.package_name"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="<?php if($package->LanguagePackageSingle): ?> <?php echo e($package->LanguagePackageSingle->name); ?> <?php endif; ?>"
                                           placeholder="" required/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="lastName3"><?php echo app('translator')->get("$string_file.description"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="description" rows="3" name="description"
                                              placeholder=""><?php if($package->LanguagePackageSingle): ?> <?php echo e($package->LanguagePackageSingle->description); ?> <?php endif; ?></textarea>
                                    <?php if($errors->has('description')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('description')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label" for="lastName3"><?php echo app('translator')->get("$string_file.terms_conditions"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="terms_conditions" rows="3"
                                              name="terms_conditions"
                                              placeholder=""><?php if($package->LanguagePackageSingle): ?> <?php echo e($package->LanguagePackageSingle->terms_conditions); ?> <?php endif; ?></textarea>
                                    <?php if($errors->has('terms_conditions')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('terms_conditions')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions float-right" style="margin-bottom: 1%">
                            <?php if(!$is_demo): ?>
                            <button type="submit" class="btn btn-primary"><i
                                        class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?> </button>
                            <?php else: ?>
                                <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/service-package/edit.blade.php ENDPATH**/ ?>