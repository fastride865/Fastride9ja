<?php $__env->startSection('content'); ?>
    <?php $tdt_segment_condition = is_merchant_segment_exist(['TAXI','DELIVERY','TOWING']); ?>
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
                    <h3 class="panel-title">
                        <i class=" icon fa-gears" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.general_configuration"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              action="<?php echo e(route('merchant.general_configuration.store')); ?>">
                            <?php echo csrf_field(); ?>
                            <fieldset>
                                <div class="row">

















                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.report_issue_email"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" class="form-control"
                                                   id="report_issue_email"
                                                   name="report_issue_email"
                                                   placeholder="<?php echo app('translator')->get("$string_file.report_issue_email"); ?>"
                                                   value="<?php echo e($configuration->report_issue_email); ?>"
                                                   required>
                                            <?php if($errors->has('report_issue_email')): ?>
                                                <label class="danger"><?php echo e($errors->first('report_issue_email')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                               <?php echo app('translator')->get("$string_file.report_issue_phone"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="report_issue_phone"
                                                   name="report_issue_phone"
                                                   placeholder=""
                                                   value="<?php echo e($configuration->report_issue_phone); ?>"
                                                   required>
                                            <?php if($errors->has('report_issue_phone')): ?>
                                                <label class="danger"><?php echo e($errors->first('report_issue_phone')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.android_user_maintenance_mode"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="android_user_maintenance_mode"
                                                    id="android_user_maintenance_mode" required>
                                                <option value="1"
                                                <?php if(isset($configuration->android_user_maintenance_mode)): ?> <?php echo e($configuration->android_user_maintenance_mode == 1 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.on"); ?></option>
                                                <option value="2"
                                                <?php if(isset($configuration->android_user_maintenance_mode)): ?> <?php echo e($configuration->android_user_maintenance_mode == 2 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.off"); ?></option>
                                            </select>
                                            <?php if($errors->has('android_user_maintenance_mode')): ?>
                                                <label class="danger"><?php echo e($errors->first('android_user_maintenance_mode')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.android_user_app_version"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="android_user_version"
                                                   name="android_user_version"
                                                   placeholder="<?php echo app('translator')->get("$string_file.android_user_app_version"); ?>"
                                                   value="<?php echo e($configuration->android_user_version); ?>"
                                                   required>
                                            <?php if($errors->has('android_user_version')): ?>
                                                <label class="danger"><?php echo e($errors->first('android_user_version')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.android_user_app_mandatory_update"); ?>
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="android_user_mandatory_update"
                                                    id="android_user_mandatory_update" required>
                                                <option value="1"
                                                <?php if(isset($configuration->android_user_mandatory_update)): ?> <?php echo e($configuration->android_user_mandatory_update == 1 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.on"); ?></option>
                                                <option value="2"
                                                <?php if(isset($configuration->android_user_mandatory_update)): ?> <?php echo e($configuration->android_user_mandatory_update == 2 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.off"); ?></option>
                                            </select>
                                            <?php if($errors->has('android_user_mandatory_update')): ?>
                                                <label class="danger"><?php echo e($errors->first('android_user_mandatory_update')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.android_driver_maintenance_mode"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="android_driver_maintenance_mode"
                                                    id="android_driver_maintenance_mode" required>
                                                <option value="1"
                                                <?php if(isset($configuration->android_driver_maintenance_mode)): ?> <?php echo e($configuration->android_driver_maintenance_mode == 1 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.on"); ?></option>
                                                <option value="2"
                                                <?php if(isset($configuration->android_driver_maintenance_mode)): ?> <?php echo e($configuration->android_driver_maintenance_mode == 2 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.off"); ?></option>
                                            </select>
                                            <?php if($errors->has('android_driver_maintenance_mode')): ?>
                                                <label class="danger"><?php echo e($errors->first('android_driver_maintenance_mode')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                               <?php echo app('translator')->get("$string_file.android_driver_app_version"); ?>
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="android_driver_version"
                                                   name="android_driver_version"
                                                   placeholder=""
                                                   value="<?php echo e($configuration->android_driver_version); ?>"
                                                   required>
                                            <?php if($errors->has('android_driver_version')): ?>
                                                <label class="danger"><?php echo e($errors->first('android_driver_version')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.android_driver_app_mandatory_update"); ?>
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="android_driver_mandatory_update"
                                                    id="android_driver_mandatory_update" required>
                                                <option value="1"
                                                <?php if(isset($configuration->android_driver_mandatory_update)): ?> <?php echo e($configuration->android_driver_mandatory_update == 1 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.on"); ?></option>
                                                <option value="2"
                                                <?php if(isset($configuration->android_driver_mandatory_update)): ?> <?php echo e($configuration->android_driver_mandatory_update == 2 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.off"); ?></option>
                                            </select>
                                            <?php if($errors->has('android_driver_mandatory_update')): ?>
                                                <label class="danger"><?php echo e($errors->first('android_driver_mandatory_update')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.ios_user_maintenance_mode"); ?>
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="ios_user_maintenance_mode"
                                                    id="ios_user_maintenance_mode" required>
                                                <option value="1"
                                                <?php if(isset($configuration->ios_user_maintenance_mode)): ?> <?php echo e($configuration->ios_user_maintenance_mode == 1 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.on"); ?></option>
                                                <option value="2"
                                                <?php if(isset($configuration->ios_user_maintenance_mode)): ?> <?php echo e($configuration->ios_user_maintenance_mode == 2 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.off"); ?></option>
                                            </select>
                                            <?php if($errors->has('ios_user_maintenance_mode')): ?>
                                                <label class="danger"><?php echo e($errors->first('ios_user_maintenance_mode')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                 <?php echo app('translator')->get("$string_file.ios_user_app_version"); ?>
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="ios_user_version"
                                                   name="ios_user_version"
                                                   placeholder=""
                                                   value="<?php echo e($configuration->ios_user_version); ?>"
                                                   required>
                                            <?php if($errors->has('ios_user_version')): ?>
                                                <label class="danger"><?php echo e($errors->first('ios_user_version')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.ios_user_app_mandatory_update"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="ios_user_mandatory_update"
                                                    id="ios_user_mandatory_update" required>
                                                <option value="1"
                                                <?php if(isset($configuration->ios_user_mandatory_update)): ?> <?php echo e($configuration->ios_user_mandatory_update == 1 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.on"); ?></option>
                                                <option value="2"
                                                <?php if(isset($configuration->ios_user_mandatory_update)): ?> <?php echo e($configuration->ios_user_mandatory_update == 2 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.off"); ?></option>
                                            </select>
                                            <?php if($errors->has('ios_user_mandatory_update')): ?>
                                                <label class="danger"><?php echo e($errors->first('ios_user_mandatory_update')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.ios_driver_maintenance_mode"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="ios_driver_maintenance_mode"
                                                    id="ios_driver_maintenance_mode" required>
                                                <option value="1"
                                                <?php if(isset($configuration->ios_driver_maintenance_mode)): ?> <?php echo e($configuration->ios_driver_maintenance_mode == 1 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.on"); ?></option>
                                                <option value="2"
                                                <?php if(isset($configuration->ios_driver_maintenance_mode)): ?> <?php echo e($configuration->ios_driver_maintenance_mode == 2 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.off"); ?></option>
                                            </select>
                                            <?php if($errors->has('ios_driver_maintenance_mode')): ?>
                                                <label class="danger"><?php echo e($errors->first('ios_driver_maintenance_mode')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.ios_driver_app_version"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="ios_driver_version"
                                                   name="ios_driver_version"
                                                   placeholder="<?php echo app('translator')->get("$string_file.ios_driver_app_version"); ?>"
                                                   value="<?php echo e($configuration->ios_driver_version); ?>"
                                                   required>
                                            <?php if($errors->has('ios_driver_version')): ?>
                                                <label class="danger"><?php echo e($errors->first('ios_driver_version')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.android_driver_app_mandatory_update"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="ios_driver_mandatory_update"
                                                    id="ios_driver_mandatory_update" required>
                                                <option value="1"
                                                <?php if(isset($configuration->ios_driver_mandatory_update)): ?> <?php echo e($configuration->ios_driver_mandatory_update == 1 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.on"); ?></option>
                                                <option value="2"
                                                <?php if(isset($configuration->ios_driver_mandatory_update)): ?> <?php echo e($configuration->ios_driver_mandatory_update == 2 ? 'selected' : ''); ?> <?php endif; ?>><?php echo app('translator')->get("$string_file.off"); ?></option>
                                            </select>
                                            <?php if($errors->has('ios_driver_mandatory_update')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('ios_driver_mandatory_update')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.admin_application_default_language"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="default_language"
                                                    id="default_language" required>
                                                <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($language->locale); ?>" <?php echo e($configuration->default_language == $language->locale ? 'selected' : ''); ?>><?php echo e($language->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <?php if($errors->has('default_language')): ?>
                                                <label class="danger"><?php echo e($errors->first('default_language')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.user_application_default_language"); ?><span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="user_default_language"
                                                    id="user_default_language" required>
                                                <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($language->locale); ?>" <?php echo e($app_configuration->user_default_language == $language->locale ? 'selected' : ''); ?>><?php echo e($language->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <?php if($errors->has('user_default_language')): ?>
                                                <label class="danger"><?php echo e($errors->first('user_default_language')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                    <?php echo app('translator')->get("$string_file.driver_application_default_language"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="driver_default_language"
                                                    id="driver_default_language" required>
                                                <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($language->locale); ?>" <?php echo e($app_configuration->driver_default_language == $language->locale ? 'selected' : ''); ?>><?php echo e($language->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <?php if($errors->has('driver_default_language')): ?>
                                                <label class="danger"><?php echo e($errors->first('driver_default_language')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if($configuration->user_wallet_status == 1): ?>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="firstName3">
                                                    <?php echo app('translator')->get("$string_file.user_wallet_short_values"); ?>
                                                </label>
                                                <?php $a = json_decode($configuration->user_wallet_amount,true);  ?>
                                                <input type="text" class="form-control"
                                                       id="user_wallet_amount"
                                                       name="user_wallet_amount[]"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_amount"); ?>"
                                                       value="<?php if(array_key_exists(0, $a)): ?> <?php echo e($a[0]['amount']); ?> <?php endif; ?>"
                                                       required>
                                                <?php if($errors->has('user_wallet_amount')): ?>
                                                    <label class="danger"><?php echo e($errors->first('user_wallet_amount')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label style="visibility: hidden" for="firstName3">
                                                    <?php echo app('translator')->get('admin.message144'); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="user_wallet_amount"
                                                       name="user_wallet_amount[]"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_value"); ?> 2"
                                                       value="<?php if(array_key_exists(1, $a)): ?> <?php echo e($a[1]['amount']); ?> <?php endif; ?>"
                                                       required>
                                                <?php if($errors->has('user_wallet_amount')): ?>
                                                    <label class="danger"><?php echo e($errors->first('user_wallet_amount')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label style="visibility: hidden" for="firstName3">
                                                    <?php echo app('translator')->get('admin.message144'); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="user_wallet_amount"
                                                       name="user_wallet_amount[]"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_value"); ?> 3"
                                                       value="<?php if(array_key_exists(2, $a)): ?> <?php echo e($a[2]['amount']); ?> <?php endif; ?>"
                                                       required>
                                                <?php if($errors->has('user_wallet_amount')): ?>
                                                    <label class="danger"><?php echo e($errors->first('user_wallet_amount')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if($configuration->driver_wallet_status == 1): ?>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="firstName3">
                                                    <?php echo app('translator')->get("$string_file.driver_wallet_short_values"); ?>
                                                </label>
                                                <?php $b = json_decode($configuration->driver_wallet_amount,true);  ?>
                                                <input type="text" class="form-control"
                                                       id="driver_wallet_amount"
                                                       name="driver_wallet_amount[]"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_amount"); ?>"
                                                       value="<?php if(array_key_exists(0, $b)): ?> <?php echo e($b[0]['amount']); ?> <?php endif; ?>"
                                                       required>
                                                <?php if($errors->has('driver_wallet_amount')): ?>
                                                    <label class="danger"><?php echo e($errors->first('driver_wallet_amount')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label style="visibility: hidden" for="firstName3">
                                                    <?php echo app('translator')->get('admin.message144'); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="driver_wallet_amount"
                                                       name="driver_wallet_amount[]"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_value"); ?> 2"
                                                       value="<?php if(array_key_exists(1, $b)): ?> <?php echo e($b[1]['amount']); ?> <?php endif; ?>"
                                                       required>
                                                <?php if($errors->has('driver_wallet_amount')): ?>
                                                    <label class="danger"><?php echo e($errors->first('driver_wallet_amount')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label style="visibility: hidden" for="firstName3">
                                                    <?php echo app('translator')->get('admin.message144'); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="driver_wallet_amount"
                                                       name="driver_wallet_amount[]"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_value"); ?> 3"
                                                       value="<?php if(array_key_exists(2, $b)): ?> <?php echo e($b[2]['amount']); ?> <?php endif; ?>"
                                                       required>
                                                <?php if($errors->has('driver_wallet_amount')): ?>
                                                    <label class="danger"><?php echo e($errors->first('driver_wallet_amount')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if($app_configuration->tip_status == 1): ?>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="firstName3">
                                                    <?php echo app('translator')->get("$string_file.user_tip_short_values"); ?>
                                                </label>
                                                <?php $b = !empty($app_configuration->tip_short_amount) ? json_decode($app_configuration->tip_short_amount,true) : [];  ?>
                                                <input type="text" class="form-control"
                                                       id="tip_short_amount"
                                                       name="tip_short_amount[]"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_amount"); ?>"
                                                       value="<?php if(array_key_exists(0, $b)): ?> <?php echo e($b[0]['amount']); ?> <?php endif; ?>"
                                                       required>
                                                <?php if($errors->has('tip_short_amount')): ?>
                                                    <label class="danger"><?php echo e($errors->first('tip_short_amount')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label style="visibility: hidden" for="firstName3">
                                                    <?php echo app('translator')->get('admin.message144'); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="tip_short_amount"
                                                       name="tip_short_amount[]"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_value"); ?> 2"
                                                       value="<?php if(array_key_exists(1, $b)): ?> <?php echo e($b[1]['amount']); ?> <?php endif; ?>"
                                                       required>
                                                <?php if($errors->has('tip_short_amount')): ?>
                                                    <label class="danger"><?php echo e($errors->first('tip_short_amount')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label style="visibility: hidden" for="firstName3">
                                                    <?php echo app('translator')->get('admin.message144'); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="tip_short_amount"
                                                       name="tip_short_amount[]"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_value"); ?> 3"
                                                       value="<?php if(array_key_exists(2, $b)): ?> <?php echo e($b[2]['amount']); ?> <?php endif; ?>"
                                                       required>
                                                <?php if($errors->has('tip_short_amount')): ?>
                                                    <label class="danger"><?php echo e($errors->first('tip_short_amount')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.reminder_expire_doc"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="reminder_expire_doc"
                                                   name="reminder_expire_doc"
                                                   placeholder="<?php echo app('translator')->get("$string_file.reminder_expire_doc"); ?>"
                                                   value="<?php echo e($configuration->reminder_doc_expire); ?>"
                                                   required>
                                            <?php if($errors->has('reminder_expire_doc')): ?>
                                                <label class="danger"><?php echo e($errors->first('reminder_expire_doc')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php if($tdt_segment_condition): ?>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.fare_policy_text"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="fare_policy_text"
                                                   name="fare_policy_text"
                                                   placeholder="<?php echo app('translator')->get("$string_file.fare_policy_text"); ?>"
                                                   value="<?php echo e($configuration->fare_policy_text); ?>"
                                                   required>
                                            <?php if($errors->has('fare_policy_text')): ?>
                                                <label class="danger"><?php echo e($errors->first('fare_policy_text')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.api_version"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="api_version"
                                                   name="api_version"
                                                   placeholder="<?php echo app('translator')->get("$string_file.api_version"); ?>"
                                                   value="<?php echo e(isset($version_management->api_version) ? $version_management->api_version : '0.1'); ?>"
                                                   required>
                                            <?php if($errors->has('api_version')): ?>
                                                <label class="danger"><?php echo e($errors->first('api_version')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if(isset($configuration->twilio_call_masking) && $configuration->twilio_call_masking == 1): ?>
                                    <br>
                                    <h5 class="form-section">
                                        <i class="fa fa-taxi"></i> <?php echo app('translator')->get('admin.twilio_call_masking_configuration'); ?>
                                    </h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="twilio_sid">
                                                    <?php echo app('translator')->get('admin.twilio_sid'); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="twilio_sid"
                                                       name="twilio_sid"
                                                       placeholder="<?php echo app('translator')->get('admin.message168'); ?>"
                                                       value="<?php echo e($configuration->twilio_sid); ?>"
                                                       required>
                                                <?php if($errors->has('twilio_sid')): ?>
                                                    <label class="danger"><?php echo e($errors->first('twilio_sid')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="twilio_service_id">
                                                    <?php echo app('translator')->get('admin.twilio_service_id'); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="twilio_service_id"
                                                       name="twilio_service_id"
                                                       placeholder="<?php echo app('translator')->get('admin.twilio_service_id'); ?>"
                                                       value="<?php echo e($configuration->twilio_service_id); ?>"
                                                       required>
                                                <?php if($errors->has('twilio_service_id')): ?>
                                                    <label class="danger"><?php echo e($errors->first('twilio_service_id')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="twilio_token">
                                                    <?php echo app('translator')->get('admin.twilio_token'); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="twilio_token"
                                                       name="twilio_token"
                                                       placeholder="<?php echo app('translator')->get('admin.twilio_token'); ?>"
                                                       value="<?php echo e($configuration->twilio_token); ?>"
                                                       required>
                                                <?php if($errors->has('twilio_token')): ?>
                                                    <label class="danger"><?php echo e($errors->first('twilio_token')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if(isset($configuration->face_recognition_feature) && $configuration->face_recognition_feature == 1): ?>
                                    <br>
                                    <h5 class="form-section">
                                        <i class="fa fa-taxi"></i> <?php echo app('translator')->get("$string_file.face_recognition_configuration"); ?>
                                    </h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="face_recognition_end_point">
                                                    <?php echo app('translator')->get("$string_file.face_recognition_end_point"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="face_recognition_end_point"
                                                       name="face_recognition_end_point"
                                                       placeholder="<?php echo app('translator')->get("$string_file.face_recognition_end_point"); ?>"
                                                       value="<?php echo e($configuration->face_recognition_end_point); ?>"
                                                       required>
                                                <?php if($errors->has('face_recognition_end_point')): ?>
                                                    <label class="danger"><?php echo e($errors->first('face_recognition_end_point')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="face_recognition_subscription_key">
                                                    <?php echo app('translator')->get("$string_file.face_recognition_subscription_key"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="face_recognition_subscription_key"
                                                       name="face_recognition_subscription_key"
                                                       placeholder="<?php echo app('translator')->get("$string_file.face_recognition_subscription_key"); ?>"
                                                       value="<?php echo e($configuration->face_recognition_subscription_key); ?>"
                                                       required>
                                                <?php if($errors->has('face_recognition_subscription_key')): ?>
                                                    <label class="danger"><?php echo e($errors->first('face_recognition_subscription_key')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="face_recognition_for_user_register">
                                                    <?php echo app('translator')->get("$string_file.face_recognition_for_user_register"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <select class="form-control"
                                                        name="face_recognition_for_user_register"
                                                        id="face_recognition_for_user_register" required>
                                                    <option value="1" <?php echo e($configuration->face_recognition_for_user_register == 1 ? 'selected' : ''); ?>><?php echo app('translator')->get("$string_file.yes"); ?></option>
                                                    <option value="2" <?php echo e($configuration->face_recognition_for_user_register == 2 ? 'selected' : ''); ?>><?php echo app('translator')->get("$string_file.no"); ?></option>
                                                </select>
                                                <?php if($errors->has('face_recognition_for_user_register')): ?>
                                                    <label class="danger"><?php echo e($errors->first('face_recognition_for_user_register')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="face_recognition_for_driver_register">
                                                    <?php echo app('translator')->get("$string_file.face_recognition_for_driver_register"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <select class="form-control"
                                                        name="face_recognition_for_driver_register"
                                                        id="face_recognition_for_driver_register" required>
                                                    <option value="1" <?php echo e($configuration->face_recognition_for_driver_register == 1 ? 'selected' : ''); ?>><?php echo app('translator')->get("$string_file.yes"); ?></option>
                                                    <option value="2" <?php echo e($configuration->face_recognition_for_driver_register == 2 ? 'selected' : ''); ?>><?php echo app('translator')->get("$string_file.no"); ?></option>
                                                </select>
                                                <?php if($errors->has('face_recognition_for_driver_register')): ?>
                                                    <label class="danger"><?php echo e($errors->first('face_recognition_for_driver_register')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="face_recognition_for_driver_online_offline">
                                                    <?php echo app('translator')->get("$string_file.face_recognition_for_driver_online_offline"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <select class="form-control"
                                                        name="face_recognition_for_driver_online_offline"
                                                        id="face_recognition_for_driver_online_offline" required>
                                                    <option value="1" <?php echo e($configuration->face_recognition_for_driver_online_offline == 1 ? 'selected' : ''); ?>><?php echo app('translator')->get("$string_file.yes"); ?></option>
                                                    <option value="2" <?php echo e($configuration->face_recognition_for_driver_online_offline == 2 ? 'selected' : ''); ?>><?php echo app('translator')->get("$string_file.no"); ?></option>
                                                </select>
                                                <?php if($errors->has('face_recognition_for_driver_online_offline')): ?>
                                                    <label class="danger"><?php echo e($errors->first('face_recognition_for_driver_online_offline')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </fieldset>
                            <div class="form-actions right" style="margin-bottom: 3%">
                                <?php if(Auth::user('merchant')->can('edit_configuration')): ?>
                                    <?php if(!$is_demo): ?>
                                    <button type="submit" class="btn btn-primary float-right">
                                        <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?>
                                    </button>
                                    <?php else: ?>
                                        <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/random/generalconfiguration.blade.php ENDPATH**/ ?>