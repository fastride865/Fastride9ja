<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php
                    $config = get_merchant_notification_provider(null,null,null,"full");
                    $firebase_required = isset($config->fire_base) && $config->fire_base == true ? "required" : "";
                    $firebase_required_file = !empty($firebase_required) && empty($config->id) ? "required" : "";
                    $onesignal_required = empty($firebase_required) ? "required" : "";
                    $heading = empty($firebase_required) ? trans("$string_file.onesignal") : trans("$string_file.firebase");
                    $dummy_data = "******";
                ?>
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
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                        <?php echo $heading; ?> <?php echo app('translator')->get("$string_file.configuration"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="<?php echo e(route('merchant.onesignal.submit')); ?>">
                        <?php echo csrf_field(); ?>

                            <?php if(!empty($config->push_notification_provider) && ($config->push_notification_provider == 1 || $config->push_notification_provider == 3)): ?>
                            <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="web_application_key">
                                        <?php echo app('translator')->get("$string_file.web_onesignal_app_key"); ?>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="web_application_key" name="web_application_key"
                                           placeholder=""
                                           value="<?php echo e(!$is_demo ? $onesignal['web_application_key'] : $dummy_data); ?>">
                                    <?php if($errors->has('web_application_key')): ?>
                                        <label class="danger"><?php echo e($errors->first('web_application_key')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="web_rest_key">
                                        <?php echo app('translator')->get("$string_file.web_onesignal_rest_key"); ?>
                                    </label>
                                    <input type="text" class="form-control" id="web_rest_key"
                                           name="web_rest_key"
                                           placeholder=""
                                           value="<?php echo e(!$is_demo ? $onesignal['web_rest_key'] : $dummy_data); ?>">
                                    <?php if($errors->has('web_rest_key')): ?>
                                        <label class="danger"><?php echo e($errors->first('web_rest_key')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.user_application_key"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="user_application_key" name="user_application_key"
                                           placeholder=""
                                           value="<?php echo e(!$is_demo ? $onesignal->user_application_key : $dummy_data); ?>"
                                           <?php echo $onesignal_required; ?>>
                                    <?php if($errors->has('user_application_key')): ?>
                                        <label class="danger"><?php echo e($errors->first('user_application_key')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.user_rest_key"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="user_rest_key"
                                           name="user_rest_key"
                                           placeholder=""
                                           value="<?php echo e(!$is_demo ?  $onesignal->user_rest_key : $dummy_data); ?>" <?php echo $onesignal_required; ?>>
                                    <?php if($errors->has('user_rest_key')): ?>
                                        <label class="danger"><?php echo e($errors->first('user_rest_key')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.user_channel_id"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="user_channel_id"
                                               name="user_channel_id"
                                               placeholder=""
                                               value="<?php echo e(!$is_demo ? $onesignal->user_channel_id : $dummy_data); ?>" <?php echo $onesignal_required; ?>>
                                        <?php if($errors->has('user_channel_id')): ?>
                                            <label class="danger"><?php echo e($errors->first('user_channel_id')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.driver_application_key"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="driver_application_key"
                                           name="driver_application_key"
                                           placeholder=""
                                           value="<?php echo e(!$is_demo ? $onesignal->driver_application_key : $dummy_data); ?>"
                                           <?php echo $onesignal_required; ?>>
                                    <?php if($errors->has('driver_application_key')): ?>
                                        <label class="danger"><?php echo e($errors->first('driver_application_key')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.driver_rest_key"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="driver_rest_key"
                                           name="driver_rest_key"
                                           placeholder=""
                                           value="<?php echo e(!$is_demo ? $onesignal->driver_rest_key : $dummy_data); ?>" <?php echo $onesignal_required; ?>>
                                    <?php if($errors->has('driver_rest_key')): ?>
                                        <label class="danger"><?php echo e($errors->first('driver_rest_key')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.driver_channel_id"); ?><span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="driver_channel_id"
                                               name="driver_channel_id"
                                               placeholder=""
                                               value="<?php echo e(!$is_demo ?  $onesignal->driver_channel_id : $dummy_data); ?>" <?php echo $onesignal_required; ?>>
                                        <?php if($errors->has('driver_channel_id')): ?>
                                            <label class="danger"><?php echo e($errors->first('driver_channel_id')); ?></label>
                                        <?php endif; ?>
                                    </div>
                            </div>

                                <?php if($food_grocery): ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.business_segment_application_key"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="business_segment_application_key"
                                                   name="business_segment_application_key"
                                                   placeholder=""
                                                   value="<?php echo e(!$is_demo ? $onesignal->business_segment_application_key : $dummy_data); ?>"
                                                    >
                                            <?php if($errors->has('business_segment_application_key')): ?>
                                                <label class="danger"><?php echo e($errors->first('business_segment_application_key')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.business_segment_rest_key"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="business_segment_rest_key"
                                                   name="business_segment_rest_key"
                                                   placeholder=""
                                                   value="<?php echo e(!$is_demo ? $onesignal->business_segment_rest_key : $dummy_data); ?>">
                                            <?php if($errors->has('business_segment_rest_key')): ?>
                                                <label class="danger"><?php echo e($errors->first('business_segment_rest_key')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.business_segment_channel_id"); ?><span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="driver_channel_id"
                                               name="business_segment_channel_id"
                                               placeholder=""
                                               value="<?php echo e(!$is_demo ?  $onesignal->business_segment_channel_id : $dummy_data); ?>">
                                        <?php if($errors->has('driver_channel_id')): ?>
                                            <label class="danger"><?php echo e($errors->first('business_segment_channel_id')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($config->push_notification_provider) && ($config->push_notification_provider == 2 || $config->push_notification_provider == 3)): ?>
                            <hr>

                                    <h3 class="panel-title">
                                        <i class=" wb-user-plus" aria-hidden="true"></i>
                                        <?php echo app('translator')->get("$string_file.firebase_configuration"); ?>
                                    </h3>

                            <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.firebase_api_key"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="driver_rest_key"
                                           name="firebase_api_key_android"
                                           placeholder=""
                                           value="<?php echo e(!$is_demo ? $onesignal->firebase_api_key_android : $dummy_data); ?>" <?php echo $firebase_required; ?>>
                                    <?php if($errors->has('firebase_api_key_android')): ?>
                                        <label class="danger"><?php echo e($errors->first('firebase_api_key_android')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.firebase_ios_pem_user"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="document"
                                           name="firebase_ios_pem_user" placeholder="" <?php echo $firebase_required_file; ?>>
                                    <?php if($errors->has('firebase_ios_pem_user')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('firebase_ios_pem_user')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.pem_password_user"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="pem_password_user"
                                           name="pem_password_user"
                                           placeholder=""
                                           value="<?php echo e(!$is_demo ? $onesignal->pem_password_user : $dummy_data); ?>" <?php echo $firebase_required; ?>>
                                    <?php if($errors->has('pem_password_user')): ?>
                                        <label class="danger"><?php echo e($errors->first('pem_password_user')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.firebase_ios_pem_driver"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="document"
                                           name="firebase_ios_pem_driver" placeholder="" <?php echo $firebase_required_file; ?>>
                                    <?php if($errors->has('firebase_ios_pem_driver')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('firebase_ios_pem_driver')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.pem_password_driver"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="pem_password_driver"
                                           name="pem_password_driver"
                                           placeholder=""
                                           value="<?php echo e(!$is_demo ? $onesignal->pem_password_driver : $dummy_data); ?>" <?php echo $firebase_required; ?>>
                                    <?php if($errors->has('pem_password_driver')): ?>
                                        <label class="danger"><?php echo e($errors->first('pem_password_driver')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            <?php if(Auth::user('merchant')->can('edit_onesignal')): ?>
                            <?php if(!$is_demo): ?>
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?>
                                </button>
                            <?php else: ?>
                                <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                            <?php endif; ?>
                        </div>
                            <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/random/onesignal.blade.php ENDPATH**/ ?>