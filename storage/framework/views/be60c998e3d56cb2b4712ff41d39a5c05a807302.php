<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="<?php echo e(route('vehiclemake.index')); ?>">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                            <?php if(!empty($info_setting) && $info_setting->edit_text != ""): ?>
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.vehicle_make"); ?> (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="<?php echo e(route('vehiclemake.update', $vehiclemake->id)); ?>">
                        <?php echo e(method_field('PUT')); ?>

                        <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.vehicle_make"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="vehicle_make"
                                               name="vehicle_make"
                                               value="<?php if(!empty($vehiclemake->LanguageVehicleMakeSingle)): ?> <?php echo e($vehiclemake->LanguageVehicleMakeSingle->vehicleMakeName); ?> <?php endif; ?>"
                                               placeholder=""
                                               required>
                                        <?php if($errors->has('vehicle_make')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('vehicle_make')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.logo"); ?>
                                            <span class="text-danger">*</span>
                                        </label><span style="color: blue">(<?php echo app('translator')->get("$string_file.size"); ?>)</span><i
                                                class="fa fa-info-circle fa-1"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                title=""></i>
                                        <input type="file" class="form-control" id="vehicle_make_logo" name="vehicleMakeLogo"
                                               placeholder="<?php echo app('translator')->get("$string_file.logo"); ?>">
                                        <?php if($errors->has('vehicleMakeLogo')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('vehicleMakeLogo')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            <?php echo app('translator')->get("$string_file.description"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="description"
                                                  name="description" rows="3"
                                                  placeholder=""><?php if(!empty($vehiclemake->LanguageVehicleMakeSingle)): ?> <?php echo e($vehiclemake->LanguageVehicleMakeSingle->vehicleMakeDescription); ?> <?php endif; ?></textarea>
                                        <?php if($errors->has('description')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('description')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <?php if(!$is_demo): ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.update"); ?>
                            </button>
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

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/vehiclemake/edit.blade.php ENDPATH**/ ?>