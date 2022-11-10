<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
           <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="<?php echo e(route('vehiclemodel.index')); ?>">
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
                    <h3 class="panel-title">
                        <i class="wb-edit" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.vehicle_model"); ?> (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)</h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="<?php echo e(route('vehiclemodel.update', $vehicleModel->id)); ?>">
                        <?php echo e(method_field('PUT')); ?>

                        <?php echo csrf_field(); ?>
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.vehicle_type"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" name="vehicletype"
                                                id="vehicletype" required>
                                            <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option <?php if($vehicle->id == $vehicleModel->vehicle_type_id): ?> selected
                                                        <?php endif; ?> value="<?php echo e($vehicle->id); ?>"><?php echo e($vehicle->VehicleTypeName); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php if($errors->has('vehicletype')): ?>
                                            <label class="danger"><?php echo e($errors->first('vehicletype')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.vehicle_make"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" name="vehiclemake"
                                                id="vehiclemake" required>
                                            <?php $__currentLoopData = $vehiclemakes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehiclemake): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option <?php if($vehiclemake->id == $vehicleModel->vehicle_make_id): ?> selected
                                                        <?php endif; ?> value="<?php echo e($vehiclemake->id); ?>"><?php echo e($vehiclemake->VehicleMakeName); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php if($errors->has('vehiclemake')): ?>
                                            <label class="danger"><?php echo e($errors->first('vehiclemake')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.vehicle_model"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="vehicle_model"
                                               name="vehicle_model"
                                               value="<?php if($vehicleModel->LanguageVehicleModelSingle): ?> <?php echo e($vehicleModel->LanguageVehicleModelSingle->vehicleModelName); ?> <?php endif; ?>"
                                               placeholder=""
                                               required>
                                        <?php if($errors->has('vehicle_model')): ?>
                                            <label class="danger"><?php echo e($errors->first('vehicle_model')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.no_of_seat"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="vehicle_seat" name="vehicle_seat"
                                               placeholder="" value="<?php echo e($vehicleModel->vehicle_seat); ?>" required min="1" max="50">
                                        <?php if($errors->has('vehicle_seat')): ?>
                                            <label class="danger"><?php echo e($errors->first('vehicle_seat')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            <?php echo app('translator')->get("$string_file.description"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="description"
                                                  name="description" rows="3"
                                                  placeholder="<?php echo app('translator')->get("$string_file.description"); ?>"><?php if($vehicleModel->LanguageVehicleModelSingle): ?> <?php echo e($vehicleModel->LanguageVehicleModelSingle->vehicleModelDescription); ?> <?php endif; ?></textarea>
                                        <?php if($errors->has('description')): ?>
                                            <label class="danger"><?php echo e($errors->first('description')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <?php if(!$is_demo): ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="icon fa-check-circle"></i> <?php echo app('translator')->get("$string_file.update"); ?>
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
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/vehiclemodel/edit.blade.php ENDPATH**/ ?>