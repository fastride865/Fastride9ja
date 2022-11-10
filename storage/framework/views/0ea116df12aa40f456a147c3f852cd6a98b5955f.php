<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
           <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->edit_text != ""): ?>
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
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.user_details"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <?php if(Auth::user()->demo != 1): ?>
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              action="<?php echo e(route('users.update', $user->id)); ?>">
                            <?php echo e(method_field('PUT')); ?>

                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="firstName3">
                                             <?php echo app('translator')->get("$string_file.first_name"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="first_name"
                                               name="first_name" value="<?php echo e($user->first_name); ?>"
                                               placeholder=" <?php echo app('translator')->get("$string_file.first_name"); ?>" required>
                                        <?php if($errors->has('first_name')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('first_name')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.last_name"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="last_name"
                                               name="last_name" value="<?php echo e($user->last_name); ?>"
                                               placeholder="<?php echo app('translator')->get("$string_file.last_name"); ?>" required>
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
                                        <div class="row">
                                            <input type="text"
                                                   class="form-control col-md-3 ml-15 col-sm-3 col-3" id="isd"
                                                   name="isd" value="<?php echo e(old('isd',isset($user->country) ? $user->Country->phonecode : NULL)); ?>"
                                                   placeholder="<?php echo app('translator')->get("$string_file.isd_code"); ?>" readonly/>
                                            <input type="number" class="form-control col-md-8 col-sm-8 col-8"
                                                   id="user_phone" name="user_phone" value="<?php echo e(old('user_phone',isset($user->country) ?  str_replace($user->Country->phonecode,'',$user->UserPhone) : NULL)); ?>"
                                                   placeholder="" required/>
                                        </div>
                                        
                                        <?php if($errors->has('phonecode')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('phonecode')); ?></label>
                                        <?php endif; ?>
                                        <?php if($errors->has('user_phone')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('user_phone')); ?></label>
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
                                        <input type="email" class="form-control" id="user_email"
                                               name="user_email"
                                               placeholder=""
                                               value="<?php echo e($user->email); ?>" required>
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
                                               placeholder="" disabled>
                                        <?php if($errors->has('password')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('password')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="1" name="edit_password"
                                                   id="edit_password" onclick="EditPassword()">
                                            <?php echo app('translator')->get("$string_file.edit_password"); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <?php if($appConfig->gender == 1): ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="location3"><?php echo app('translator')->get("$string_file.gender"); ?>
                                                :</label>
                                            <select class="form-control" name="user_gender"
                                                    id="user_gender"
                                                    required>
                                                <option value="1"
                                                        <?php if($user->user_gender == 1): ?> selected
                                                        <?php endif; ?>><?php echo app('translator')->get("$string_file.male"); ?>
                                                </option>
                                                <option value="2"
                                                        <?php if($user->user_gender == 2): ?> selected
                                                        <?php endif; ?>><?php echo app('translator')->get("$string_file.female"); ?>
                                                </option>
                                            </select>
                                            <?php if($errors->has('user_gender')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('user_gender')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if($appConfig->smoker == 1): ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="location3"> <?php echo app('translator')->get("$string_file.smoke"); ?>
                                                :</label>

                                            <label class="radio-inline"
                                                   style="margin-left: 5%;margin-right: 10%;margin-top: 1%;">
                                                <input type="radio" value="1"
                                                       checked id="smoker_type"
                                                       name="smoker_type"
                                                       <?php if($user->smoker_type == 1): ?> checked
                                                       <?php endif; ?>
                                                       required> <?php echo app('translator')->get("$string_file.smoker"); ?>
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" value="2" id="smoker_type"
                                                       name="smoker_type"
                                                       <?php if($user->smoker_type == 2): ?> checked
                                                       <?php endif; ?>
                                                       required> <?php echo app('translator')->get("$string_file.non_smoker"); ?>
                                            </label>
                                            <br>
                                            <label class="checkbox-inline" style="margin-left: 5%;margin-top: 1%;">
                                                <input type="checkbox" name="allow_other_smoker"
                                                       id="allow_other_smoker"
                                                       <?php if($user->allow_other_smoker == 1): ?> checked <?php endif; ?>
                                                       value="1">  <?php echo app('translator')->get("$string_file.allow_other_to_smoke"); ?>
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
                                               placeholder="<?php echo app('translator')->get("$string_file.profile_image"); ?>"
                                               onchange="readURL(this)">
                                        <?php if($errors->has('profile')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('profile')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group text-center">
                                        <?php if(!empty($user->UserProfileImage)): ?>
                                            <img id="show_image" style="border-radius: 50%;"
                                                 src="<?php if($user->corporate_id): ?><?php echo e(get_image($user->UserProfileImage,'corporate_user',$user->merchant_id)); ?><?php else: ?><?php echo e(get_image($user->UserProfileImage,'user')); ?><?php endif; ?>" width="150"
                                                 height="150">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions d-flex flex-row-reverse p-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.update"); ?>
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="card bg-danger text-white shadow">
                            <div class="card-body">
                                <div class="large"><?php echo app('translator')->get("$string_file.demo_user_cant_edited"); ?>.</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="examplePositionSidebar" aria-labelledby="examplePositionSidebar"
         role="dialog" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-simple modal-sidebar modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    
                </div>
                <div class="modal-body">
                    <?php if(!empty($info_setting) && $info_setting->edit_text != ""): ?>
                        <?php echo $info_setting->edit_text; ?>

                    <?php else: ?>
                        <p>No information content found...</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        function EditPassword() {
            if (document.getElementById("edit_password").checked = true) {
                document.getElementById('password').disabled = false;
            }
        }

    </script>
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#show_image')
                        .attr('src', e.target.result)
                        .width(200)
                        .height(200);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/user/edit.blade.php ENDPATH**/ ?>