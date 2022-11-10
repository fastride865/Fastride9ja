<?php $__env->startSection('content'); ?>
    <?php
        $tdt_segment_condition = is_merchant_segment_exist(['TAXI','DELIVERY']);
        $food_grocery = is_merchant_segment_exist(['FOOD','GROCERY']);
      //  $merchant_segment_group = get_merchant_segment_group();
    ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                    <h1 class="panel-title">
                        <i class="icon fa-gears" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.request_configuration"); ?>
                    </h1>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data"
                              action="<?php echo e(route('merchant.booking_configuration.store')); ?>">
                            <?php echo csrf_field(); ?>
                            <?php if(Auth::user()->demo != 1): ?>
                                <h5 class="form-section"><i
                                            class="fa fa-key"></i> <?php echo app('translator')->get("$string_file.google_key_configuration"); ?></h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="android_user_key">
                                                <?php echo app('translator')->get("$string_file.android_user_key"); ?>
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="android_user_key"
                                                   name="android_user_key"
                                                   value="<?php echo e($configuration->android_user_key); ?>"
                                                   required>
                                            <?php if($errors->has('android_user_key')): ?>
                                                <label class="danger"><?php echo e($errors->first('android_user_key')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="android_driver_key">
                                                <?php echo app('translator')->get("$string_file.android_driver_key"); ?>
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="android_driver_key"
                                                   name="android_driver_key"
                                                   value="<?php echo e($configuration->android_driver_key); ?>"
                                                   required>
                                            <?php if($errors->has('android_driver_key')): ?>
                                                <label class="danger"><?php echo e($errors->first('android_driver_key')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="ios_user_key">
                                                <?php echo app('translator')->get("$string_file.ios_user_key"); ?>
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="ios_user_key"
                                                   name="ios_user_key"
                                                   value="<?php echo e($configuration->ios_user_key); ?>"
                                                   required>
                                            <?php if($errors->has('ios_user_key')): ?>
                                                <label class="danger"><?php echo e($errors->first('ios_user_key')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="ios_driver_key">
                                                <?php echo app('translator')->get("$string_file.ios_driver_key"); ?>
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="ios_driver_key"
                                                   name="ios_driver_key"
                                                   value="<?php echo e($configuration->ios_driver_key); ?>"
                                                   required>
                                            <?php if($errors->has('ios_driver_key')): ?>
                                                <label class="danger"><?php echo e($errors->first('ios_driver_key')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="ios_map_load_from"><?php echo app('translator')->get("$string_file.ios_map_load_from"); ?></label>
                                            <select class="form-control" name="ios_map_load_from"
                                                    id="ios_map_load_from"
                                                    required>
                                                <option vlaue=""><?php echo app('translator')->get("$string_file.select"); ?></option>
                                                <option value="1"
                                                        <?php if($configuration->ios_map_load_from == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.apple_map"); ?></option>
                                                <option value="2"
                                                        <?php if($configuration->ios_map_load_from == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.google_map"); ?></option>
                                            </select>
                                            <?php if($errors->has('ios_map_load_from')): ?>
                                                <label class="danger"><?php echo e($errors->first('ios_map_load_from')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="google_key">
                                                <?php echo app('translator')->get("$string_file.google_key_for_api"); ?>
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="google_key"
                                                   name="google_key"
                                                   placeholder="<?php echo app('translator')->get('admin_x.message154'); ?>"
                                                   value="<?php echo e($configuration->google_key); ?>"
                                                   required>
                                            <?php if($errors->has('google_key')): ?>
                                                <label class="danger"><?php echo e($errors->first('google_key')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="google_key_admin">
                                                <?php echo app('translator')->get("$string_file.google_key_for_admin"); ?>
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control"
                                                   id="google_key_admin"
                                                   name="google_key_admin"
                                                   placeholder=""
                                                   value="<?php echo e($configuration->google_key_admin); ?>"
                                                   required>
                                            <?php if($errors->has('google_key_admin')): ?>
                                                <label class="danger"><?php echo e($errors->first('admin_x.message892')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                             <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="slide_button">
                                                <?php echo app('translator')->get("$string_file.slide_button"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="slide_button"
                                                    id="slide_button" required>
                                                <option value="1" <?php echo e($configuration->slide_button == 1 ? 'selected' : ''); ?>><?php echo app('translator')->get("$string_file.yes"); ?></option>
                                                <option value="2" <?php echo e($configuration->slide_button == 2 ? 'selected' : ''); ?>><?php echo app('translator')->get("$string_file.no"); ?></option>
                                            </select>
                                            <?php if($errors->has('slide_button')): ?>
                                                <label class="danger"><?php echo e($errors->first('slide_button')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                            <h5 class="form-section"><i
                                        class="fa fa-taxi"></i> <?php echo app('translator')->get("$string_file.general_configuration"); ?></h5>
                            <hr>
                            <div class="row">
                                <?php if(in_array(1,$merchant_segment_group_config)): ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="driver_request_timeout">
                                                <?php echo app('translator')->get("$string_file.driver_request_time_out"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   id="driver_request_timeout"
                                                   name="driver_request_timeout"
                                                   placeholder="<?php echo app('translator')->get("$string_file.driver_request_time_out"); ?>"
                                                   value="<?php echo e($configuration->driver_request_timeout); ?>"
                                                   required>
                                            <?php if($errors->has('driver_request_timeout')): ?>
                                                <label class="danger"><?php echo e($errors->first('driver_request_timeout')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if($tdt_segment_condition): ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="user_request_timeout">
                                                <?php echo app('translator')->get("$string_file.user_request_time_out"); ?>
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   id="user_request_timeout"
                                                   name="user_request_timeout"
                                                   placeholder="<?php echo app('translator')->get("$string_file.user_request_time_out"); ?>"
                                                   value="<?php echo e($configuration->user_request_timeout); ?>"
                                                   required>
                                            <?php if($errors->has('user_request_timeout')): ?>
                                                <label class="danger"><?php echo e($errors->first('user_request_timeout')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tracking_screen_refresh_timeband">
                                                <?php echo app('translator')->get("$string_file.tracking_screen_time_band"); ?>
                                                <span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   id="tracking_screen_refresh_timeband"
                                                   name="tracking_screen_refresh_timeband"
                                                   placeholder="<?php echo app('translator')->get("$string_file.tracking_screen_time_band"); ?>"
                                                   value="<?php echo e($configuration->tracking_screen_refresh_timeband); ?>"
                                                   required>
                                            <?php if($errors->has('tracking_screen_refresh_timeband')): ?>
                                                <label class="danger"><?php echo e($errors->first('tracking_screen_refresh_timeband')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="drop_location_request"><?php echo app('translator')->get("$string_file.drop_location_request"); ?></label>
                                            <select class="form-control"
                                                    name="drop_location_request"
                                                    id="drop_location_request"
                                                    required>
                                                <option value="1"
                                                        <?php if($configuration->drop_location_request == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.yes"); ?></option>
                                                <option value="2"
                                                        <?php if($configuration->drop_location_request == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.no"); ?></option>
                                            </select>
                                            <?php if($errors->has('drop_location_request')): ?>
                                                <label class="danger"><?php echo e($errors->first('drop_location_request')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="estimate_fare_request"><?php echo app('translator')->get("$string_file.estimate_fare_request"); ?></label>
                                            <select class="form-control"
                                                    name="estimate_fare_request"
                                                    id="estimate_fare_request"
                                                    required>
                                                <option value="1"
                                                        <?php if($configuration->estimate_fare_request == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.yes"); ?></option>
                                                <option value="2"
                                                        <?php if($configuration->estimate_fare_request == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.no"); ?></option>
                                            </select>
                                            <?php if($errors->has('estimate_fare_request')): ?>
                                                <label class="danger"><?php echo e($errors->first('estimate_fare_request')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="number_of_driver_user_map">
                                                <?php echo app('translator')->get("$string_file.no_of_drivers_on_user_map"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   id="number_of_driver_user_map"
                                                   name="number_of_driver_user_map"
                                                   placeholder="<?php echo app('translator')->get('admin_x.message149'); ?>"
                                                   value="<?php echo e($configuration->number_of_driver_user_map); ?>"
                                                   required>
                                            <?php if($errors->has('number_of_driver_user_map')): ?>
                                                <label class="danger"><?php echo e($errors->first('number_of_driver_user_map')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="booking_eta"><?php echo app('translator')->get("$string_file.booking_eta"); ?></label>
                                            <select class="form-control" name="booking_eta"
                                                    id="booking_eta"
                                                    required>
                                                <option value="1"
                                                        <?php if($configuration->booking_eta == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.yes"); ?></option>
                                                <option value="2"
                                                        <?php if($configuration->booking_eta == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.no"); ?></option>
                                            </select>
                                            <?php if($errors->has('booking_eta')): ?>
                                                <label class="danger"><?php echo e($errors->first('booking_eta')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="ride_later_cancel_hour">
                                                <?php echo app('translator')->get("$string_file.time_gap"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   id="partial_accept_hours"
                                                   name="partial_accept_hours"
                                                   placeholder=""
                                                   value="<?php echo e($configuration->partial_accept_hours); ?>"
                                                   required>
                                            <?php if($errors->has('partial_accept_hours')): ?>
                                                <label class="danger"><?php echo e($errors->first('partial_accept_hours')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="partial_accept_before_hours">
                                                <?php echo app('translator')->get("$string_file.partial_accept_before_hours"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   id="partial_accept_before_hours"
                                                   name="partial_accept_before_hours"
                                                   placeholder=""
                                                   value="<?php echo e($configuration->partial_accept_before_hours); ?>"
                                                   required>
                                            <?php if($errors->has('partial_accept_before_hours')): ?>
                                                <label class="danger"><?php echo e($errors->first('partial_accept_before_hours')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize"><?php echo app('translator')->get("$string_file.auto_cancel_expired_rides"); ?></label>
                                            <select class="form-control"
                                                    name="auto_cancel_expired_rides"
                                                    id="auto_cancel_expired_rides"
                                                    required>
                                                <option value="1"
                                                        <?php if($configuration->auto_cancel_expired_rides == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.yes"); ?></option>
                                                <option value="0"
                                                        <?php if($configuration->auto_cancel_expired_rides == 0): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.no"); ?></option>
                                            </select>
                                            <?php if($errors->has('auto_cancel_expired_rides')): ?>
                                                <label class="danger"><?php echo e($errors->first('auto_cancel_expired_rides')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize"><?php echo app('translator')->get("$string_file.ride_later_max_num_days"); ?></label>
                                            <input type="number" class="form-control"
                                                   name="ride_later_max_num_days"
                                                   value="<?php echo e($configuration->ride_later_max_num_days); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize">
                                                <?php echo app('translator')->get("$string_file.ride_later_cancel_hour"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   id="ride_later_cancel_hour"
                                                   name="ride_later_cancel_hour"
                                                   placeholder=""
                                                   value="<?php echo e($configuration->ride_later_cancel_hour); ?>"
                                                   step="any"
                                                   min="0" max="10"
                                                   required>
                                            <?php if($errors->has('ride_later_cancel_hour')): ?>
                                                <label class="danger"><?php echo e($errors->first('ride_later_cancel_hour')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.location_update_timeband"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   id="location_update_timeband"
                                                   name="location_update_timeband"
                                                   placeholder=""
                                                   value="<?php echo e($gen_config->location_update_timeband); ?>"
                                                   required>
                                            <?php if($errors->has('location_update_timeband')): ?>
                                                <label class="danger"><?php echo e($errors->first('location_update_timeband')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="additional_note"><?php echo app('translator')->get("$string_file.additional_notes"); ?> </label>
                                        <select class="form-control" name="additional_note"
                                                id="additional_note"
                                                required>
                                            <option value="1"
                                                    <?php if($configuration->additional_note == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.yes"); ?></option>
                                            <option value="2"
                                                    <?php if($configuration->additional_note == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.no"); ?></option>
                                        </select>
                                        <?php if($errors->has('additional_note')): ?>
                                            <label class="danger"><?php echo e($errors->first('additional_note')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if($merchant->Configuration->ride_later_cancel_in_cancel_hour_enable == 1): ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize"><?php echo app('translator')->get("$string_file.ride_later_cancel_enable_in_cancel_hour"); ?></label>
                                            <select name="ride_later_cancel_enable_in_cancel_hour"
                                                    class="form-control">
                                                <option value="2"><?php echo app('translator')->get("$string_file.disable"); ?></option>
                                                <option value="1" <?php echo e(($configuration->ride_later_cancel_enable_in_cancel_hour == 1) ? 'selected' : ''); ?>>
                                                    <?php echo app('translator')->get("$string_file.enable"); ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize"><?php echo app('translator')->get("$string_file.ride_later_cancel_charge_in_cancel_hour"); ?></label>
                                            <input name="ride_later_cancel_charge_in_cancel_hour"
                                                   value="<?php echo e($configuration->ride_later_cancel_charge_in_cancel_hour); ?>"
                                                   class="form-control"/>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if($configuration->ride_later_payment_types_enable == 1): ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="text-capitalize"><?php echo app('translator')->get("$string_file.ride_later_payment_types"); ?></label>
                                            <select class="form-control select2"
                                                    name="ride_later_payment_types[]" multiple>
                                                <?php $__currentLoopData = $paymentmethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($payment->id); ?>"
                                                            <?php echo e(($configuration->ride_later_payment_types != null && in_array($payment->id , json_decode($configuration->ride_later_payment_types))) ? 'selected' : ''); ?>

                                                    >
                                                        <?php echo e($payment->payment_method); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <?php $a = isset($configuration->driver_ride_radius_request) ? json_decode($configuration->driver_ride_radius_request,true) : [];  ?>
                                <label for="driver_ride_radius_request">
                                    <?php echo app('translator')->get("$string_file.driver_ride_radius_request"); ?><span
                                            class="text-danger">*</span>
                                </label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="number" class="form-control"
                                                   name="driver_ride_radius_request[]"
                                                   value="<?php if(array_key_exists(0, $a)): ?><?php echo e($a[0]); ?><?php endif; ?>"
                                                   placeholder="<?php echo app('translator')->get('admin_x.driver_ride_radius_request'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="number" class="form-control"
                                                   name="driver_ride_radius_request[]"
                                                   value="<?php if(array_key_exists(1, $a)): ?><?php echo e($a[1]); ?><?php endif; ?>"
                                                   placeholder="<?php echo app('translator')->get('admin_x.driver_ride_radius_request'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="number" class="form-control"
                                                   name="driver_ride_radius_request[]"
                                                   value="<?php if(array_key_exists(2, $a)): ?><?php echo e($a[2]); ?><?php endif; ?>"
                                                   placeholder="<?php echo app('translator')->get('admin_x.driver_ride_radius_request'); ?>"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if($configuration->driver_cancel_ride_after_time == 1): ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="driver_cancel_after_time">
                                                <?php echo app('translator')->get("$string_file.driver"); ?> <?php echo app('translator')->get("$string_file.booking"); ?> <?php echo app('translator')->get("$string_file.cancel"); ?> <?php echo app('translator')->get("$string_file.after"); ?> <?php echo app('translator')->get("$string_file.time"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   id="driver_cancel_after_time"
                                                   name="driver_cancel_after_time"
                                                   placeholder=""
                                                   value="<?php echo e(isset($configuration->driver_cancel_after_time) ? $configuration->driver_cancel_after_time :0); ?>"
                                                   required>
                                            <?php if($errors->has('driver_cancel_after_time')): ?>
                                                <label class="danger"><?php echo e($errors->first('driver_cancel_after_time')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <br>
                           <?php if($food_grocery): ?>
                            <h5 class="form-section"><i
                                        class="fa fa-home"></i> <?php echo app('translator')->get("$string_file.restaurant"); ?> / <?php echo app('translator')->get("$string_file.store_configuration"); ?>
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="normal_ride_now_request_driver">
                                            <?php echo app('translator')->get("$string_file.restaurant"); ?> / <?php echo app('translator')->get("$string_file.store_radius_from_user"); ?> (<?php echo app('translator')->get("$string_file.km"); ?>)
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control"
                                               id="store_radius_from_user"
                                               name="store_radius_from_user"
                                               placeholder=""
                                               value="<?php echo e(isset($configuration->store_radius_from_user) ? $configuration->store_radius_from_user :0); ?>"
                                               required>
                                        <?php if($errors->has('normal_ride_now_request_driver')): ?>
                                            <label class="danger"><?php echo e($errors->first('normal_ride_now_request_driver')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if($tdt_segment_condition): ?>
                                <?php if(in_array(1,$service_types) || $tdt_segment_condition): ?>
                                    <br>
                                    <h5 class="form-section"><i
                                                class="fa fa-taxi"></i> <?php echo app('translator')->get("$string_file.ride_allocation_setting"); ?>
                                    </h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="normal_ride_now_request_driver">
                                                    <?php echo app('translator')->get("$string_file.normal_request_driver"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="normal_ride_now_request_driver"
                                                       name="normal_ride_now_request_driver"
                                                       placeholder="<?php echo app('translator')->get('admin_x.message156'); ?>"
                                                       value="<?php echo e($configuration->normal_ride_now_request_driver); ?>"
                                                       required>
                                                <?php if($errors->has('normal_ride_now_request_driver')): ?>
                                                    <label class="danger"><?php echo e($errors->first('normal_ride_now_request_driver')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if(in_array(1,$service_types)): ?>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="normal_ride_now_drop_location">
                                                        <?php echo app('translator')->get("$string_file.normal_ride_now_drop_location"); ?>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-control"
                                                            name="normal_ride_now_drop_location"
                                                            id="normal_ride_now_drop_location" required>
                                                        <option value="1"
                                                                <?php if($configuration->normal_ride_now_drop_location == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.yes"); ?></option>
                                                        <option value="2"
                                                                <?php if($configuration->normal_ride_now_drop_location == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.no"); ?></option>
                                                    </select>
                                                    <?php if($errors->has('normal_ride_now_drop_location')): ?>
                                                        <label class="danger"><?php echo e($errors->first('normal_ride_now_drop_location')); ?></label>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="normal_ride_later_request_type">
                                                    <?php echo app('translator')->get("$string_file.ride_later_request"); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control"
                                                        name="normal_ride_later_request_type"
                                                        id="normal_ride_later_request_type"
                                                        onchange="cronJob(this.value)" required>
                                                    <option value="1"
                                                            <?php if($configuration->normal_ride_later_request_type == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.all_drivers"); ?></option>
                                                    <option value="2"
                                                            <?php if($configuration->normal_ride_later_request_type == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.cron_job"); ?></option>
                                                </select>
                                                <?php if($errors->has('normal_ride_later_request_type')): ?>
                                                    <label class="danger"><?php echo e($errors->first('normal_ride_later_request_type')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="normal_ride_later_booking_hours">
                                                    <?php echo app('translator')->get("$string_file.ride_later_booking_from_current"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="normal_ride_later_booking_hours"
                                                       name="normal_ride_later_booking_hours"
                                                       placeholder=""
                                                       value="<?php echo e($configuration->normal_ride_later_booking_hours); ?>"
                                                       required>
                                                <?php if($errors->has('normal_ride_later_booking_hours')): ?>
                                                    <label class="danger"><?php echo e($errors->first('normal_ride_later_booking_hours')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="firstName3">
                                                    <?php echo app('translator')->get("$string_file.distance_radius_for_ride_later"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="normal_ride_later_radius"
                                                       name="normal_ride_later_radius"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_radius"); ?> ( <?php echo app('translator')->get("$string_file.in_km"); ?> )"
                                                       value="<?php echo e($configuration->normal_ride_later_radius); ?>"
                                                       required>
                                                <?php if($errors->has('normal_ride_later_radius')): ?>
                                                    <label class="danger"><?php echo e($errors->first('normal_ride_later_radius')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <?php if(in_array(1,$service_types)): ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="normal_ride_later_drop_location">
                                                    <?php echo app('translator')->get("$string_file.normal_ride_later_drop_location"); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control"
                                                        name="normal_ride_later_drop_location"
                                                        id="normal_ride_later_drop_location"
                                                        required>
                                                    <option value="1"
                                                            <?php if($configuration->normal_ride_later_drop_location == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.yes"); ?></option>
                                                    <option value="2"
                                                            <?php if($configuration->normal_ride_later_drop_location == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.no"); ?></option>
                                                </select>
                                                <?php if($errors->has('normal_ride_later_drop_location')): ?>
                                                    <label class="danger"><?php echo e($errors->first('normal_ride_later_drop_location')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="normal_ride_later_request_driver">
                                                    <?php echo app('translator')->get("$string_file.ride_later_to_number_of_drivers"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="normal_ride_later_request_driver"
                                                       name="normal_ride_later_request_driver"
                                                       placeholder="<?php echo app('translator')->get("$string_file.no_of_drivers"); ?>"
                                                       value="<?php echo e($configuration->normal_ride_later_request_driver); ?>"
                                                       required>
                                                <?php if($errors->has('normal_ride_later_request_driver')): ?>
                                                    <label class="danger"><?php echo e($errors->first('normal_ride_later_request_driver')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="normal_ride_later_time_before">
                                                    <?php echo app('translator')->get("$string_file.ride_later_start_time_before"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="normal_ride_later_time_before"
                                                       name="normal_ride_later_time_before"
                                                       placeholder=""
                                                       value="<?php echo e($configuration->normal_ride_later_time_before); ?>"
                                                       required>
                                                <?php if($errors->has('normal_ride_later_time_before')): ?>
                                                    <label class="danger"><?php echo e($errors->first('normal_ride_later_time_before')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 custom-hidden"
                                             id="normal_ride_later_cron_hour">
                                            <div class="form-group">
                                                <label for="normal_ride_later_cron_hour">
                                                    <?php echo app('translator')->get("$string_file.cronJob"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="normal_ride_later_cron_hour"
                                                       name="normal_ride_later_cron_hour"
                                                       placeholder=""
                                                       value="<?php echo e($configuration->normal_ride_later_cron_hour); ?>">
                                                <?php if($errors->has('normal_ride_later_cron_hour')): ?>
                                                    <label class="danger"><?php echo e($errors->first('normal_ride_later_cron_hour')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                    </div>
                                <?php endif; ?>
                                <?php if(in_array(2,$service_types)): ?>
                                    <br>
                                    <h5 class="form-section"><i
                                                class="fa fa-taxi"></i> <?php echo app('translator')->get("$string_file.rental_configuration"); ?>
                                    </h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="riderentaldistance">
                                                    <?php echo app('translator')->get("$string_file.rental_distance_radius"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="riderentaldistance"
                                                       name="rental_ride_now_radius"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_radius"); ?> ( <?php echo app('translator')->get("$string_file.in_km"); ?> )"
                                                       value="<?php echo e($configuration->rental_ride_now_radius); ?>"
                                                       required>
                                                <?php if($errors->has('rental_ride_now_radius')): ?>
                                                    <label class="danger"><?php echo e($errors->first('rental_ride_now_radius')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="rental_ride_now_request_driver">
                                                    <?php echo app('translator')->get("$string_file.rental_ride_request_drivers"); ?> <span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="rental_ride_now_request_driver"
                                                       name="rental_ride_now_request_driver"
                                                       placeholder=""
                                                       value="<?php echo e($configuration->rental_ride_now_request_driver); ?>"
                                                       required>
                                                <?php if($errors->has('rental_ride_now_request_driver')): ?>
                                                    <label class="danger"><?php echo e($errors->first('rental_ride_now_request_driver')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="rental_ride_now_drop_location">
                                                    <?php echo app('translator')->get("$string_file.rental_ride_now_drop_location"); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control"
                                                        name="rental_ride_now_drop_location"
                                                        id="rental_ride_now_drop_location" required>
                                                    <option value="1"
                                                            <?php if($configuration->rental_ride_now_drop_location == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.yes"); ?></option>
                                                    <option value="2"
                                                            <?php if($configuration->rental_ride_now_drop_location == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.no"); ?></option>
                                                </select>
                                                <?php if($errors->has('rental_ride_now_drop_location')): ?>
                                                    <label class="danger"><?php echo e($errors->first('rental_ride_now_drop_location')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="rental_ride_later_request_type">
                                                    <?php echo app('translator')->get("$string_file.rental_ride_later_request_type"); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control"
                                                        name="rental_ride_later_request_type"
                                                        id="rental_ride_later_request_type"
                                                        required>
                                                    <option value="1"
                                                            <?php if($configuration->rental_ride_later_request_type == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.all_drivers"); ?></option>
                                                    <option value="2"
                                                            <?php if($configuration->rental_ride_later_request_type == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.cron_job"); ?></option>
                                                </select>
                                                <?php if($errors->has('rental_ride_later_request_type')): ?>
                                                    <label class="danger"><?php echo e($errors->first('rental_ride_later_request_type')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="rental_ride_later_booking_hours">
                                                    <?php echo app('translator')->get("$string_file.rental_ride_later_booking_hours"); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="rental_ride_later_booking_hours"
                                                       name="rental_ride_later_booking_hours"
                                                       placeholder=""
                                                       value="<?php echo e($configuration->rental_ride_later_booking_hours); ?>"
                                                       required>
                                                <?php if($errors->has('rental_ride_later_booking_hours')): ?>
                                                    <label class="danger"><?php echo e($errors->first('rental_ride_later_booking_hours')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="rental_ride_later_radius">
                                                    <?php echo app('translator')->get("$string_file.rental_ride_later_radius"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="rental_ride_later_radius"
                                                       name="rental_ride_later_radius"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_radius"); ?> ( <?php echo app('translator')->get("$string_file.in_km"); ?> )"
                                                       value="<?php echo e($configuration->rental_ride_later_radius); ?>"
                                                       required>
                                                <?php if($errors->has('rental_ride_later_radius')): ?>
                                                    <label class="danger"><?php echo e($errors->first('rental_ride_later_radius')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="rental_ride_later_drop_location">
                                                    <?php echo app('translator')->get("$string_file.rental_ride_later_drop_location"); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control"
                                                        name="rental_ride_later_drop_location"
                                                        id="rental_ride_later_drop_location"
                                                        required>
                                                    <option value="1"
                                                            <?php if($configuration->rental_ride_later_drop_location == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.yes"); ?></option>
                                                    <option value="2"
                                                            <?php if($configuration->rental_ride_later_drop_location == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.no"); ?></option>
                                                </select>
                                                <?php if($errors->has('rental_ride_later_drop_location')): ?>
                                                    <label class="danger"><?php echo e($errors->first('rental_ride_later_drop_location')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="rental_ride_later_request_driver">
                                                    <?php echo app('translator')->get("$string_file.rental_ride_later_request_driver"); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="rental_ride_later_request_driver"
                                                       name="rental_ride_later_request_driver"
                                                       placeholder=""
                                                       value="<?php echo e($configuration->rental_ride_later_request_driver); ?>"
                                                       required>
                                                <?php if($errors->has('rental_ride_later_request_driver')): ?>
                                                    <label class="danger"><?php echo e($errors->first('rental_ride_later_request_driver')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="rental_ride_later_time_before">
                                                    <?php echo app('translator')->get("$string_file.rental_ride_later_time_before"); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="rental_ride_later_time_before"
                                                       name="rental_ride_later_time_before"
                                                       placeholder=""
                                                       value="<?php echo e($configuration->rental_ride_later_time_before); ?>"
                                                       required>
                                                <?php if($errors->has('rental_ride_later_time_before')): ?>
                                                    <label class="danger"><?php echo e($errors->first('rental_ride_later_time_before')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 custom-hidden"
                                             id="rental_ride_later_cron_hour">
                                            <div class="form-group">
                                                <label for="rental_ride_later_cron_hour">
                                                    <?php echo app('translator')->get("$string_file.cronJob"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="rental_ride_later_cron_hour"
                                                       name="rental_ride_later_cron_hour"
                                                       placeholder=""
                                                       value="<?php echo e($configuration->rental_ride_later_cron_hour); ?>"
                                                >
                                                <?php if($errors->has('rental_ride_later_cron_hour')): ?>
                                                    <label class="danger"><?php echo e($errors->first('rental_ride_later_cron_hour')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if(in_array(3,$service_types)): ?>
                                    <br>
                                    <h5 class="form-section"><i
                                                class="fa fa-taxi"></i> <?php echo app('translator')->get('admin_x.transfer_config'); ?>
                                    </h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="ridetransferdistance">
                                                    <?php echo app('translator')->get('admin_x.transfer_distance_radius'); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="ridetransferdistance"
                                                       name="transfer_ride_now_radius"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_radius"); ?> ( <?php echo app('translator')->get("$string_file.in_km"); ?> )"
                                                       value="<?php echo e($configuration->transfer_ride_now_radius); ?>"
                                                       required>
                                                <?php if($errors->has('transfer_ride_now_radius')): ?>
                                                    <label class="danger"><?php echo e($errors->first('transfer_ride_now_radius')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="transfer_ride_now_request_driver">
                                                    <?php echo app('translator')->get('admin_x.transfer_ride_request_drivers'); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="transfer_ride_now_request_driver"
                                                       name="transfer_ride_now_request_driver"
                                                       placeholder="<?php echo app('translator')->get('admin_x.message156'); ?>"
                                                       value="<?php echo e($configuration->transfer_ride_now_request_driver); ?>"
                                                       required>
                                                <?php if($errors->has('transfer_ride_now_request_driver')): ?>
                                                    <label class="danger"><?php echo e($errors->first('transfer_ride_now_request_driver')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="transfer_ride_now_drop_location">
                                                    <?php echo app('translator')->get('admin_x.transfer_ride_now_drop_location'); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control"
                                                        name="transfer_ride_now_drop_location"
                                                        id="transfer_ride_now_drop_location"
                                                        required>
                                                    <option value="1"
                                                            <?php if($configuration->transfer_ride_now_drop_location == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.yes"); ?></option>
                                                    <option value="2"
                                                            <?php if($configuration->transfer_ride_now_drop_location == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.no"); ?></option>
                                                </select>
                                                <?php if($errors->has('transfer_ride_now_drop_location')): ?>
                                                    <label class="danger"><?php echo e($errors->first('transfer_ride_now_drop_location')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="transfer_ride_later_request_type">
                                                    <?php echo app('translator')->get('admin_x.transfer_ride_later_request_type'); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control"
                                                        name="transfer_ride_later_request_type"
                                                        id="transfer_ride_later_request_type"
                                                        required>
                                                    <option value="1"
                                                            <?php if($configuration->transfer_ride_later_request_type == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.all_drivers"); ?></option>
                                                    <option value="2"
                                                            <?php if($configuration->transfer_ride_later_request_type == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.cron_job"); ?></option>
                                                </select>
                                                <?php if($errors->has('transfer_ride_later_request_type')): ?>
                                                    <label class="danger"><?php echo e($errors->first('transfer_ride_later_request_type')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="transfer_ride_later_booking_hours">
                                                    <?php echo app('translator')->get('admin_x.transfer_ride_later_booking_hours'); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="transfer_ride_later_booking_hours"
                                                       name="transfer_ride_later_booking_hours"
                                                       placeholder="<?php echo app('translator')->get('admin_x.message168'); ?>"
                                                       value="<?php echo e($configuration->transfer_ride_later_booking_hours); ?>"
                                                       required>
                                                <?php if($errors->has('transfer_ride_later_booking_hours')): ?>
                                                    <label class="danger"><?php echo e($errors->first('transfer_ride_later_booking_hours')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="transfer_ride_later_radius">
                                                    <?php echo app('translator')->get('admin_x.transfer_ride_later_radius'); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="transfer_ride_later_radius"
                                                       name="transfer_ride_later_radius"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_radius"); ?> ( <?php echo app('translator')->get("$string_file.in_km"); ?> )"
                                                       value="<?php echo e($configuration->transfer_ride_later_radius); ?>"
                                                       required>
                                                <?php if($errors->has('transfer_ride_later_radius')): ?>
                                                    <label class="danger"><?php echo e($errors->first('transfer_ride_later_radius')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="transfer_ride_later_drop_location">
                                                    <?php echo app('translator')->get('admin_x.transfer_ride_later_drop_location'); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control"
                                                        name="transfer_ride_later_drop_location"
                                                        id="transfer_ride_later_drop_location"
                                                        required>
                                                    <option value="1"
                                                            <?php if($configuration->transfer_ride_later_drop_location == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.yes"); ?></option>
                                                    <option value="2"
                                                            <?php if($configuration->transfer_ride_later_drop_location == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.no"); ?></option>
                                                </select>
                                                <?php if($errors->has('transfer_ride_later_drop_location')): ?>
                                                    <label class="danger"><?php echo e($errors->first('transfer_ride_later_drop_location')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="transfer_ride_later_request_driver">
                                                    <?php echo app('translator')->get('admin_x.transfer_ride_later_request_driver'); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="transfer_ride_later_request_driver"
                                                       name="transfer_ride_later_request_driver"
                                                       placeholder="<?php echo app('translator')->get('admin_x.message156'); ?>"
                                                       value="<?php echo e($configuration->transfer_ride_later_request_driver); ?>"
                                                       required>
                                                <?php if($errors->has('transfer_ride_later_request_driver')): ?>
                                                    <label class="danger"><?php echo e($errors->first('transfer_ride_later_request_driver')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="transfer_ride_later_time_before">
                                                    <?php echo app('translator')->get('admin_x.transfer_ride_later_time_before'); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="transfer_ride_later_time_before"
                                                       name="transfer_ride_later_time_before"
                                                       placeholder="<?php echo app('translator')->get('admin_x.message148'); ?>"
                                                       value="<?php echo e($configuration->rental_ride_later_time_before); ?>"
                                                       required>
                                                <?php if($errors->has('transfer_ride_later_time_before')): ?>
                                                    <label class="danger"><?php echo e($errors->first('transfer_ride_later_time_before')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 custom-hidden"
                                             id="transfer_ride_later_cron_hour">
                                            <div class="form-group">
                                                <label for="transfer_ride_later_cron_hour">
                                                    <?php echo app('translator')->get('admin_x.cronJob'); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="transfer_ride_later_cron_hour"
                                                       name="transfer_ride_later_cron_hour"
                                                       placeholder="<?php echo app('translator')->get('admin_x.message148'); ?>"
                                                       value="<?php echo e($configuration->transfer_ride_later_cron_hour); ?>"
                                                >
                                                <?php if($errors->has('transfer_ride_later_cron_hour')): ?>
                                                    <label class="danger"><?php echo e($errors->first('transfer_ride_later_cron_hour')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if(in_array(5,$service_types)): ?>
                                    <br>
                                    <h5 class="form-section"><i
                                                class="fa fa-taxi"></i> <?php echo app('translator')->get("$string_file.pool_configuration"); ?>
                                    </h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="pool_radius">
                                                    <?php echo app('translator')->get("$string_file.pool_ride_request"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="pool_radius"
                                                       name="pool_radius"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_radius"); ?> ( <?php echo app('translator')->get("$string_file.in_km"); ?> )"
                                                       value="<?php echo e($configuration->pool_radius); ?>"
                                                       required>
                                                <?php if($errors->has('pool_radius')): ?>
                                                    <label class="danger"><?php echo e($errors->first('pool_radius')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="pool_drop_radius">
                                                    <?php echo app('translator')->get("$string_file.pool_radius"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="pool_drop_radius"
                                                       name="pool_drop_radius"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_radius"); ?> ( <?php echo app('translator')->get("$string_file.in_km"); ?> )"
                                                       value="<?php echo e($configuration->pool_drop_radius); ?>"
                                                       required>
                                                <?php if($errors->has('pool_drop_radius')): ?>
                                                    <label class="danger"><?php echo e($errors->first('pool_drop_radius')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="pool_now_request_driver">
                                                    <?php echo app('translator')->get("$string_file.pool_request_driver"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="pool_now_request_driver"
                                                       name="pool_now_request_driver"
                                                       placeholder=""
                                                       value="<?php echo e($configuration->pool_now_request_driver); ?>"
                                                       required>
                                                <?php if($errors->has('pool_now_request_driver')): ?>
                                                    <label class="danger"><?php echo e($errors->first('pool_now_request_driver')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="pool_maximum_exceed">
                                                    <?php echo app('translator')->get("$string_file.pool_max_user"); ?><span
                                                            class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="pool_maximum_exceed"
                                                       name="pool_maximum_exceed"
                                                       placeholder=""
                                                       value="<?php echo e($configuration->pool_maximum_exceed); ?>"
                                                       required>
                                                <?php if($errors->has('pool_maximum_exceed')): ?>
                                                    <label class="danger"><?php echo e($errors->first('pool_maximum_exceed')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if(in_array(4,$service_types)): ?>
                                <br>
                                <h5 class="form-section"><i
                                            class="fa fa-taxi"></i> <?php echo app('translator')->get("$string_file.outstation_configuration"); ?>
                                </h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="outstation_request_type">
                                                <?php echo app('translator')->get("$string_file.outstation_request"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <select class="form-control"
                                                    name="outstation_request_type"
                                                    id="outstation_request_type"
                                                    onchange="outstation(obj)" required>
                                                <option value="1"
                                                        <?php if($configuration->outstation_request_type == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.all_drivers"); ?></option>
                                                <option value="2"
                                                        <?php if($configuration->outstation_request_type == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.cron_job"); ?></option>
                                            </select>
                                            <?php if($errors->has('outstation_request_type')): ?>
                                                <label class="danger"><?php echo e($errors->first('outstation_request_type')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="outstation_booking_hours">
                                                <?php echo app('translator')->get("$string_file.outstation_booking_time_from_current"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   id="outstation_booking_hours"
                                                   name="outstation_booking_hours"
                                                   placeholder=""
                                                   value="<?php echo e($configuration->outstation_booking_hours); ?>"
                                                   required>
                                            <?php if($errors->has('outstation_booking_hours')): ?>
                                                <label class="danger"><?php echo e($errors->first('outstation_booking_hours')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="outstation_radius">
                                                <?php echo app('translator')->get("$string_file.outstation_distance_radius"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   id="outstation_radius"
                                                   name="outstation_radius"
                                                   placeholder="<?php echo app('translator')->get("$string_file.enter_radius"); ?> ( <?php echo app('translator')->get("$string_file.in_km"); ?> )"
                                                   value="<?php echo e($configuration->outstation_radius); ?>"
                                                   required>
                                            <?php if($errors->has('outstation_radius')): ?>
                                                <label class="danger"><?php echo e($errors->first('outstation_radius')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="outstation_request_driver">
                                                <?php echo app('translator')->get("$string_file.outstation_request_driver"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   id="outstation_request_driver"
                                                   name="outstation_request_driver"
                                                   placeholder=""
                                                   value="<?php echo e($configuration->outstation_request_driver); ?>"
                                                   required>
                                            <?php if($errors->has('outstation_request_driver')): ?>
                                                <label class="danger"><?php echo e($errors->first('outstation_request_driver')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="outstation_time_before">
                                                <?php echo app('translator')->get("$string_file.outstation_time_before"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   id="outstation_time_before"
                                                   name="outstation_time_before"
                                                   placeholder=""
                                                   value="<?php echo e($configuration->outstation_time_before); ?>"
                                                   required>
                                            <?php if($errors->has('outstation_time_before')): ?>
                                                <label class="danger"><?php echo e($errors->first('outstation_time_before')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 custom-hidden"
                                         id="outstation_ride_later_cron_hour">
                                        <div class="form-group">
                                            <label for="outstation_ride_later_cron_hour">
                                                <?php echo app('translator')->get("$string_file.cronJob"); ?><span
                                                        class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control"
                                                   id="outstation_ride_later_cron_hour"
                                                   name="outstation_ride_later_cron_hour"
                                                   placeholder=""
                                                   value="<?php echo e($configuration->outstation_ride_later_cron_hour); ?>"
                                            >
                                            <?php if($errors->has('outstation_ride_later_cron_hour')): ?>
                                                <label class="danger"><?php echo e($errors->first('outstation_ride_later_cron_hour')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if($configuration->outstation_ride_now_enabled == 1): ?>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="outstation_ride_now_radius">
                                                    <?php echo app('translator')->get("$string_file.outstation_ride_now_radius"); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="ridetransferdistance"
                                                       name="outstation_ride_now_radius"
                                                       placeholder="<?php echo app('translator')->get("$string_file.enter_radius"); ?> ( <?php echo app('translator')->get("$string_file.in_km"); ?> )"
                                                       value="<?php echo e($configuration->outstation_ride_now_radius); ?>"
                                                       required>
                                                <?php if($errors->has('outstation_ride_now_radius')): ?>
                                                    <label class="danger"><?php echo e($errors->first('outstation_ride_now_radius')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="outstation_ride_now_request_driver">
                                                    <?php echo app('translator')->get("$string_file.outstation_ride_now_request_driver"); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control"
                                                       id="outstation_ride_now_request_driver"
                                                       name="outstaion_ride_now_request_driver"
                                                       placeholder=""
                                                       value="<?php echo e($configuration->outstaion_ride_now_request_driver); ?>"
                                                       required>
                                                <?php if($errors->has('outstaion_ride_now_request_driver')): ?>
                                                    <label class="danger"><?php echo e($errors->first('outstaion_ride_now_request_driver')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php endif; ?>
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
<?php $__env->startSection('js'); ?>
    <script>
        function cronJob(obj) {
            $("#loader1").show();
            if (obj == 2) {
                document.getElementById('normal_ride_later_cron_hour').style.display = 'block';
            } else {
                document.getElementById('normal_ride_later_cron_hour').style.display = 'none';
            }
            $("#loader1").hide();
        }

        function rental(obj) {
            $("#loader1").show();
            if (obj == 2) {
                document.getElementById('rental_ride_later_cron_hour').style.display = 'block';
            } else {
                document.getElementById('rental_ride_later_cron_hour').style.display = 'none';
            }
            $("#loader1").hide();
        }

        function outstation(obj) {
            $("#loader1").show();
            if (obj == 2) {
                document.getElementById('outstation_ride_later_cron_hour').style.display = 'block';
            } else {
                document.getElementById('outstation_ride_later_cron_hour').style.display = 'none';
            }
            $("#loader1").hide();
        }

        function transfer(obj) {
            $("#loader1").show();
            if (obj == 2) {
                document.getElementById('transfer_ride_later_cron_hour').style.display = 'block';
            } else {
                document.getElementById('transfer_ride_later_cron_hour').style.display = 'none';
            }
            $("#loader1").hide();
        }
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/random/bookingconfiguration.blade.php ENDPATH**/ ?>