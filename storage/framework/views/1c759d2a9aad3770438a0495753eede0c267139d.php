<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php if(session('rideradded')): ?>
                <div class="alert dark alert-icon alert-success" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i> <?php echo app('translator')->get('admin.rideradded'); ?>
                </div>
            <?php endif; ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <?php echo e(session('errors')); ?>

                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('users.index')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.add_user"); ?></h3>
                </header>
                <div class="panel-body container-fluid" id="validation">
                    <?php if(Auth::user()->demo != 1): ?>
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data" action="<?php echo e(route('users.store')); ?>" autocomplete="false">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3"><?php echo app('translator')->get("$string_file.user_type"); ?></label>
                                        <select class="form-control" name="rider_type"
                                                id="rider_type" onclick="RideType(this.value)"
                                                required>
                                            <option value="">--Select Rider Type--</option>
                                            <?php if($config->corporate_admin == 1): ?>
                                                <option value="1">Corporate</option>
                                            <?php endif; ?>
                                            <option value="2">Retail</option>
                                        </select>

                                        <?php if($errors->has('rider_type')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('rider_type')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.first_name"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="first_name"
                                               name="first_name"
                                               placeholder=" <?php echo app('translator')->get("$string_file.first_name"); ?>"
                                               value="<?php echo e(old('first_name')); ?>" required>
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
                                               placeholder="<?php echo app('translator')->get("$string_file.last_name"); ?>"
                                               value="<?php echo e(old('last_name')); ?>" required>
                                        <?php if($errors->has('last_name')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('last_name')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row custom-hidden" id="corporate_div">
                                <div class="col-md-4 corporate_inr">
                                    <div class="form-group">
                                        <label for="location3"><?php echo app('translator')->get('admin.corporate_name'); ?>
                                        </label>
                                        <select class="form-control" name="corporate_id"
                                                id="corporate_id">
                                            <option value="">--Select Corporate--</option>
                                            <?php $__currentLoopData = $corporates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $corporate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($corporate->id); ?>"><?php echo e($corporate->corporate_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>

                                        <?php if($errors->has('rider_type')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('rider_type')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-4 corporate_inr">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get('admin.corporateemail'); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="corporate_email"
                                               name="corporate_email" value="<?php echo e(old('corporate_email')); ?>"
                                               placeholder="<?php echo app('translator')->get('admin.corporateemail'); ?>">
                                        <?php if($errors->has('corporate_email')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('corporate_email')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3"><?php echo app('translator')->get("$string_file.country"); ?>
                                            <span class="text-danger">*</span></label>
                                        <select class="form-control" name="country" id="country"
                                                required>
                                            <option value=""><?php echo app('translator')->get("$string_file.select"); ?></option>
                                            <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option data-min="<?php echo e($country->minNumPhone); ?>"
                                                        data-max="<?php echo e($country->maxNumPhone); ?>"
                                                        data-ISD="<?php echo e($country->phonecode); ?>"
                                                        value="<?php echo e($country->id); ?>|<?php echo e($country->phonecode); ?>"><?php echo e($country->CountryName); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php if($errors->has('country')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('country')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.code"); ?> :
                                        </label>
                                        <input type="text" class="form-control" id="isd"
                                               name="isd" value="<?php echo e(old('isd')); ?>"
                                               placeholder="<?php echo app('translator')->get("$string_file.isd_code"); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            <?php echo app('translator')->get("$string_file.phone"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" autocomplete="false" class="form-control" id="user_phone"
                                               name="user_phone" value="<?php echo e(old('user_phone')); ?>"
                                               placeholder="<?php echo app('translator')->get("$string_file.phone"); ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.email"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control" id="user_email"
                                               name="user_email"
                                               placeholder="<?php echo app('translator')->get("$string_file.email"); ?>"
                                               value="<?php echo e(old('user_email')); ?>" required>
                                        <?php if($errors->has('user_email')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('user_email')); ?></label>
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
                                               placeholder="<?php echo app('translator')->get("$string_file.password"); ?>" required>
                                        <?php if($errors->has('password')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('password')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if($appConfig->gender == 1): ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="location3"><?php echo app('translator')->get("$string_file.gender"); ?>
                                                :</label>
                                            <select class="form-control" name="driver_gender"
                                                    id="driver_gender"
                                                    required>
                                                <option value="1"><?php echo app('translator')->get("$string_file.male"); ?></option>
                                                <option value="2"><?php echo app('translator')->get("$string_file.female"); ?></option>
                                            </select>
                                            <?php if($errors->has('driver_gender')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('driver_gender')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="row">
                                <?php if($appConfig->smoker == 1): ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="location3"> <?php echo app('translator')->get("$string_file.smoke"); ?>
                                                :</label>
                                            <br>
                                            <label class="radio-inline"
                                                   style="margin-left: 5%;margin-right: 10%;margin-top: 1%;">
                                                <input type="radio" value="1"
                                                       checked id="smoker_type"
                                                       name="smoker_type"
                                                       required> <?php echo app('translator')->get("$string_file.smoker"); ?>
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" value="2" id="smoker_type"
                                                       name="smoker_type"
                                                       required> <?php echo app('translator')->get("$string_file.non_smoker"); ?>
                                            </label>
                                            <br>
                                            <br>
                                            <label class="checkbox-inline"
                                                   style="margin-left: 5%;">
                                                <input type="checkbox" name="allow_other_smoker"
                                                       id="allow_other_smoker"
                                                       value="1"> <?php echo app('translator')->get("$string_file.allow_other_to_smoke"); ?>
                                            </label>

                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="profile_image">
                                            <?php echo app('translator')->get("$string_file.profile_image"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="profile"
                                               name="profile"
                                               placeholder="<?php echo app('translator')->get("$string_file.profile_image"); ?>" required>
                                        <?php if($errors->has('profile')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('profile')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions d-flex flex-row-reverse p-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check-circle"></i> Save
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="card bg-danger text-white shadow">
                            <div class="card-body">
                                Alert
                                <div class="large"><?php echo app('translator')->get("$string_file.demo_user_cant_edited"); ?>.</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script>
        function RideType(val) {
            if (val == "1") {
                document.getElementById('corporate_div').style.display = 'block';
            } else {
                document.getElementById('corporate_div').style.display = 'none';
            }
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/user/create.blade.php ENDPATH**/ ?>