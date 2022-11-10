<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" >
                            <a href="<?php echo e(route('subadmin.index')); ?>">
                                <button type="button" class="btn btn-icon btn-success"style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                            <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.sub_admin"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="<?php echo e(route('subadmin.store')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.first_name"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="first_name"
                                           name="first_name"
                                           placeholder="" required>
                                    <?php if($errors->has('first_name')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('first_name')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.last_name"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="last_name"
                                           name="last_name"
                                           placeholder="" required>
                                    <?php if($errors->has('last_name')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('last_name')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        <?php echo app('translator')->get("$string_file.phone"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="phone_number"
                                           name="phone_number"
                                           placeholder="" required>
                                    <?php if($errors->has('phone_number')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('phone_number')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.email"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="email"
                                           name="email"
                                           placeholder="<?php echo app('translator')->get('admin.message670'); ?>" required>
                                    <?php if($errors->has('email')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('email')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.password"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control" id="password"
                                           name="password"
                                           placeholder="" required>
                                    <?php if($errors->has('password')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('password')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.admin_type"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="admin_type"
                                            id="admin_type" onclick="area(this.value)"
                                            required>
                                        <?php if(Auth::user('merchant')->parent_id == 0): ?>
                                            <option value="1"><?php echo app('translator')->get("$string_file.all_areas"); ?></option>
                                        <?php endif; ?>
                                        <option value="2"><?php echo app('translator')->get("$string_file.service_area"); ?></option>
                                    </select>
                                    <?php if($errors->has('admin_type')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('admin_type')); ?></label>
                                        <label class="text-danger"><?php echo e($errors->first('admin_type')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4 " id="areaList">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.service_area"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control select2" name="area_list[]"
                                            id="area_list" multiple data-plugin="select2" disabled>
                                        <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($area->id); ?>"><?php if($area->LanguageSingle): ?> <?php echo e($area->LanguageSingle->AreaName); ?> <?php else: ?>  <?php echo e($area->LanguageAny->AreaName); ?> <?php endif; ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php if($errors->has('area_list')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('area_list')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4 corporate_inr">
                                <div class="form-group">
                                    <label for="location3"><?php echo app('translator')->get("$string_file.role"); ?>
                                    </label>
                                    <select class="form-control" name="role_id" id="role_id" required>
                                        <option value="">--<?php echo app('translator')->get("$string_file.role"); ?>--</option>
                                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($role->id); ?>"><?php echo e($role->display_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>

                                    <?php if($errors->has('role_id')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('role_id')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script>
        function area(type) {
            if (type == 2) {
                document.getElementById('area_list').disabled = false;
            } else {
                document.getElementById('area_list').disabled = true;
            }
        }
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/subadmin/create.blade.php ENDPATH**/ ?>