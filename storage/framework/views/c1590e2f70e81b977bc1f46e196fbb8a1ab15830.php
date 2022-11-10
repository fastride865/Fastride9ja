<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                            <div class="btn-group float-right">
                                <a href="<?php echo e(route('merchant.dashboard')); ?>">
                                    <button type="button" class="btn btn-icon btn-success" style="margin:10px">
                                        <i class="wb-reply"></i>
                                    </button>
                                </a>
                            </div>
                    </div>
                    <h3 class="panel-title">
                       <?php echo app('translator')->get("$string_file.edit_profile"); ?> </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="<?php echo e(route('merchant.profile.update')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.first_name"); ?> :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="merchantFirstName"
                                           name="merchantFirstName"
                                           value="<?php echo e(Auth::user()->merchantFirstName); ?>"
                                           placeholder="<?php echo app('translator')->get("$string_file.first_name"); ?>" required>
                                    <?php if($errors->has('merchantFirstName')): ?>
                                        <label class="danger"><?php echo e($errors->first('merchantFirstName')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3">
                                        <?php echo app('translator')->get("$string_file.last_name"); ?> :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="merchantLastName"
                                           name="merchantLastName"
                                           placeholder="<?php echo app('translator')->get("$string_file.last_name"); ?>"
                                           value="<?php echo e(Auth::user()->merchantLastName); ?>" required>
                                    <?php if($errors->has('merchantLastName')): ?>
                                        <label class="danger"><?php echo e($errors->first('merchantLastName')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.phone"); ?> :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="merchantPhone"
                                           name="merchantPhone"
                                           placeholder="<?php echo app('translator')->get("$string_file.phone"); ?>"
                                           value="<?php echo e(Auth::user()->merchantPhone); ?>" required>
                                    <?php if($errors->has('merchantPhone')): ?>
                                        <label class="danger"><?php echo e($errors->first('merchantPhone')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="profile_image">
                                        <?php echo app('translator')->get("$string_file.address"); ?> :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="merchantAddress"
                                           name="merchantAddress"
                                           placeholder="<?php echo app('translator')->get("$string_file.address"); ?>"
                                           value="<?php echo e(Auth::user()->merchantAddress); ?>">
                                    <?php if($errors->has('merchantAddress')): ?>
                                        <label class="danger"><?php echo e($errors->first('merchantAddress')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.password"); ?> :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="password" class="form-control" id="password"
                                           name="password"
                                           placeholder="<?php echo app('translator')->get("$string_file.password"); ?>" disabled>
                                    <?php if($errors->has('password')): ?>
                                        <label class="danger"><?php echo e($errors->first('password')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.logo"); ?> :
                                        <span class="danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="business_logo"
                                           name="business_logo"
                                           placeholder="<?php echo app('translator')->get("$string_file.logo"); ?>">
                                    <?php if($errors->has('business_logo')): ?>
                                        <label class="danger"><?php echo e($errors->first('business_logo')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="checkbox-custom checkbox-primary">
                                    <input type="checkbox" value="1" name="edit_password"
                                           id="edit_password" onclick="EditPassword()">
                                    <label for="inputChecked"> <?php echo app('translator')->get("$string_file.edit_password"); ?> </label>
                                </div>
                            </div>
                            <?php if(Auth::user('merchant')->demo != 1): ?>
                             <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.login_background_image"); ?> :
                                        <span class="danger">*</span>
                                    </label>
                                    <?php if(!empty(Auth::user('merchant')->ApplicationTheme->login_background_image)): ?>
                                    <a href="<?php echo e(get_image(Auth::user('merchant')->ApplicationTheme->login_background_image,'login_background')); ?>" target="_blank"><?php echo app('translator')->get("$string_file.view"); ?></a>
                                    <?php endif; ?>

                                    <input type="file" class="form-control" id="business_logo"
                                           name="login_background_image"
                                           placeholder="<?php echo app('translator')->get("$string_file.login_background_image"); ?>">
                                    <br>
                                    <span style="color:red;"><?php echo app('translator')->get("$string_file.login_image_warning"); ?></span>
                                    <?php if($errors->has('login_background_image')): ?>
                                        <label class="danger"><?php echo e($errors->first('login_background_image')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="wb-check-circle"></i><?php echo app('translator')->get("$string_file.save"); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        function EditPassword() {
            if (document.getElementById("edit_password").checked = true) {
                document.getElementById('password').disabled = false;
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/random/edit-profile.blade.php ENDPATH**/ ?>