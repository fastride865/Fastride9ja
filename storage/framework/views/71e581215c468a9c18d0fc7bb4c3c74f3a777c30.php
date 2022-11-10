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
                    <h3 class="panel-title"><i class="wb-user" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.application"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              action="<?php echo e(route('merchant.application.store')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.ios_user_app_url"); ?><span class="text-danger">*</span>
                                        </label>
                                        <input type="url" class="form-control"
                                               id="ios_user_link" name="ios_user_link"
                                               placeholder="<?php echo app('translator')->get("$string_file.ios_user_app_url"); ?>"
                                               value="<?php if(!empty($application)): ?> <?php echo e($application->ios_user_link); ?> <?php endif; ?>"
                                               required>
                                        <?php if($errors->has('ios_user_link')): ?>
                                            <label class="danger"><?php echo e($errors->first('ios_user_link')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.ios_driver_app_url"); ?><span class="text-danger">*</span>
                                        </label>
                                        <input type="url" class="form-control" id="ios_driver_link"
                                               name="ios_driver_link"
                                               placeholder="<?php echo app('translator')->get("$string_file.ios_driver_app_url"); ?>"
                                               value="<?php if(!empty($application)): ?> <?php echo e($application->ios_driver_link); ?> <?php endif; ?>"
                                               required>
                                        <?php if($errors->has('ios_driver_link')): ?>
                                            <label class="danger"><?php echo e($errors->first('ios_driver_link')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.android_user_app_url"); ?><span class="text-danger">*</span>
                                        </label>
                                        <input type="url" class="form-control"
                                               id="android_user_link"
                                               name="android_user_link"
                                               placeholder="<?php echo app('translator')->get("$string_file.android_user_app_url"); ?>"
                                               value="<?php if(!empty($application)): ?> <?php echo e($application->android_user_link); ?> <?php endif; ?>"
                                               required>
                                        <?php if($errors->has('android_user_link')): ?>
                                            <label class="danger"><?php echo e($errors->first('android_user_link')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.android_driver_app_url"); ?><span class="text-danger">*</span>
                                        </label>
                                        <input type="url" class="form-control"
                                               id="android_driver_link"
                                               name="android_driver_link"
                                               placeholder="<?php echo app('translator')->get("$string_file.android_driver_app_url"); ?>"
                                               value="<?php if(!empty($application)): ?> <?php echo e($application->android_driver_link); ?> <?php endif; ?>"
                                               required>
                                        <?php if($errors->has('android_driver_link')): ?>
                                            <label class="danger"><?php echo e($errors->first('android_driver_link')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.ios_user_app_id"); ?><span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control"
                                               id="ios_user_appid"
                                               name="ios_user_appid"
                                               placeholder="<?php echo app('translator')->get("$string_file.ios_user_app_id"); ?>"
                                               value="<?php if(!empty($application)): ?> <?php echo e($application->ios_user_appid); ?> <?php endif; ?>">
                                        <?php if($errors->has('ios_user_appid')): ?>
                                            <label class="danger"><?php echo e($errors->first('ios_user_appid')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.ios_driver_app_id"); ?>
                                        </label>
                                        <input type="text" class="form-control"
                                               id="ios_driver_appid"
                                               name="ios_driver_appid"
                                               placeholder="<?php echo app('translator')->get("$string_file.ios_driver_app_id"); ?>"
                                               value="<?php if(!empty($application)): ?> <?php echo e($application->ios_driver_appid); ?> <?php endif; ?>">
                                        <?php if($errors->has('ios_driver_appid')): ?>
                                            <label class="danger"><?php echo e($errors->first('ios_driver_appid')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php if(Auth::user('merchant')->can('edit_applications_url')): ?>
                                <div class="form-actions right" style="margin-bottom: 3%">
                                    <?php if(Auth::user('merchant')->can('edit_configuration')): ?>
                                        <?php if(!$is_demo): ?>
                                            <button type="submit" class="btn btn-primary float-right">
                                                <i class="fa fa-check-square-o"></i> Save
                                            </button>
                                        <?php else: ?>
                                            <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                            <?php endif; ?>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/application/index.blade.php ENDPATH**/ ?>