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
                        <div class="btn-group float-right">
                            <a href="<?php echo e(route('vehicletype.index')); ?>">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <?php echo app('translator')->get("$string_file.vehicle_type"); ?>
                        (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="<?php echo e(route('vehicletype.update', $vehicle->id)); ?>">
                        <?php echo e(method_field('PUT')); ?>

                        <?php echo csrf_field(); ?>
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.vehicle_type"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="vehicle_name"
                                               name="vehicle_name"
                                               value="<?php if($vehicle->LanguageVehicleTypeSingle): ?> <?php echo e($vehicle->LanguageVehicleTypeSingle->vehicleTypeName); ?> <?php endif; ?>"
                                               placeholder=""
                                               required>
                                        <?php if($errors->has('vehicle_name')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('vehicle_name')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                             <?php echo app('translator')->get("$string_file.vehicle_rank"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="vehicle_rank"
                                               name="vehicle_rank"
                                               value="<?php echo e($vehicle->vehicleTypeRank); ?>"
                                               placeholder=""
                                               min="1"
                                               required>
                                        <?php if($errors->has('vehicle_rank')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('vehicle_rank')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.sequence"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="sequence"
                                               name="sequence"
                                               value="<?php echo e($vehicle->sequence); ?>"
                                               placeholder=""
                                               min="1"
                                               required>
                                        <?php if($errors->has('sequence')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('sequence')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if($vehicle_model_expire == 1): ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <label><?php echo app('translator')->get("$string_file.model_expire_year"); ?> <span class="text-danger">*</span></label>
                                        </label>
                                        <input type="number" class="form-control" id="model_expire_year"
                                               name="model_expire_year" value="<?php echo e($vehicle->model_expire_year); ?>" placeholder="" min="1" max="50" required>
                                        <?php if($errors->has('model_expire_year')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('model_expire_year')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            <?php echo app('translator')->get("$string_file.description"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" id="description"
                                                  name="description"
                                                  placeholder=""
                                                  rows="3"><?php if($vehicle->LanguageVehicleTypeSingle): ?> <?php echo e($vehicle->LanguageVehicleTypeSingle->vehicleTypeDescription); ?> <?php endif; ?></textarea>
                                        <?php if($errors->has('description')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('description')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.image"); ?>
                                            <span class="text-danger">*</span>
                                        </label><span
                                                style="color: blue">(<?php echo app('translator')->get("$string_file.size"); ?> 100*100 px)</span><i
                                                class="fa fa-info-circle fa-1"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                title=""></i>
                                        <input style="    height: 0%;" type="file" class="form-control" id="vehicle_image"
                                               name="vehicle_image"
                                               placeholder="">
                                        <?php if($errors->has('vehicle_image')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('vehicle_image')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <img src="<?php echo e(get_image($vehicle->vehicleTypeImage, 'vehicle')); ?>" style="width:50%; height:100%; ">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.map_image"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <span
                                                style="color: blue">(<?php echo app('translator')->get("$string_file.size"); ?> 60*60 px)
                                                        </span>
                                        <i
                                                class="fa fa-info-circle fa-1"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                title=""></i>
                                        <div class="row">
                                            <?php $__currentLoopData = get_config_image('map_icon'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <br>
                                                <div class="col-md-4 col-sm-6">
                                                    <input type="radio" name="vehicleTypeMapImage"
                                                           value="<?php echo e($path); ?>"
                                                           id="male-radio-<?php echo e($path); ?>" <?php if($vehicle['vehicleTypeMapImage'] == $path): ?> checked <?php endif; ?>>                                            &nbsp;
                                                    <label for="male-radio-<?php echo e($path); ?>"><img
                                                                src="<?php echo e(view_config_image($path)); ?>"
                                                                style="width:10%; height:10%; margin-right:3%;"><?php echo e(explode_image_path($path)); ?>

                                                    </label>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($errors->has('vehicleTypeMapImage')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('vehicleTypeMapImage')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="checkbox-custom checkbox-primary">
                                                <input type="checkbox" value="1" name="ride_now"
                                                       id="ride_now" <?php if($vehicle->ride_now == 1): ?> checked=""  <?php endif; ?>>
                                                <label class="font-weight-400"><?php echo app('translator')->get("$string_file.request_now"); ?></label>
                                                <br>
                                                <input type="checkbox" value="1" name="ride_later"
                                                       id="ride_later" <?php if($vehicle->ride_later == 1): ?> checked="" <?php endif; ?>>
                                                <label class="font-weight-400"><?php echo app('translator')->get("$string_file.request_later"); ?></label>
                                                <br>
                                                <?php if(in_array(5,$merchant->Service)): ?>
                                                <input type="checkbox" value="1" name="pool_enable"
                                                       id="pool_enable"
                                                       <?php if($vehicle->pool_enable == 1): ?> checked=""  <?php endif; ?>>
                                                <label class="font-weight-400"><?php echo app('translator')->get("$string_file.pool_enable"); ?></label>
                                                    <br>
                                                    <?php endif; ?>
                                            </div><br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
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
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/vehicletype/edit.blade.php ENDPATH**/ ?>