<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content container-fluid">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('referral-system')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                                <?php echo app('translator')->get("$string_file.referral_system"); ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="panel-body container-fluid">
                    <form method="POST" enctype="multipart/form-data" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="<?php echo e(route('referral-system.store',["id" => $referral_system->id])); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-3">
                                <label><?php echo app('translator')->get("$string_file.country"); ?>
                                </label><br>
                                <b><?php echo e($referral_system->Country->CountryName); ?></b>
                            </div>
                            <div class="col-md-3">
                                <label><?php echo app('translator')->get("$string_file.area"); ?>
                                </label><br>
                                <b><?php echo e($referral_system->CountryArea->CountryAreaName); ?></b>
                            </div>
                            <div class="col-md-3">
                                <label><?php echo app('translator')->get("$string_file.referral_for"); ?>
                                </label><br>
                                <b><?php if($referral_system->application == 1): ?> <?php echo app('translator')->get("$string_file.user"); ?> <?php else: ?> <?php echo app('translator')->get("$string_file.driver"); ?> <?php endif; ?></b>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <?php $__currentLoopData = $referral_system_segments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ref_segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class='col-md-2'>
                                    <div class=''>
                                        <li><label for='segment_id'><?php echo e($ref_segment); ?></label></li>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-3">
                                <label> <?php echo app('translator')->get("$string_file.start_date"); ?>
                                </label><br>
                                <b><?php echo e($referral_system->start_date); ?></b>
                            </div>
                            <input type="hidden" name="start_date" value="<?php echo e(date("Y-m-d")); ?>">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="datepicker"><?php echo app('translator')->get("$string_file.end_date"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="icon wb-calendar"
                                                                              aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" class="form-control customDatePicker1" name="end_date"
                                               id="end_date"
                                               value="<?php echo e(old("end_date",isset($referral_system->end_date) ? $referral_system->end_date : "")); ?>"
                                               placeholder="" autocomplete="off" readonly>
                                        <?php if($errors->has('end_date')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('end_date')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label>
                                    <?php echo app('translator')->get("$string_file.discount_applicable"); ?>
                                </label><br>
                                <b>
                                    <?php switch($referral_system->offer_applicable):
                                        case (1): ?> <?php echo app('translator')->get("$string_file.sender"); ?>
                                            <?php break; ?>
                                        <?php case (2): ?> <?php echo app('translator')->get("$string_file.receiver"); ?>
                                            <?php break; ?>
                                        <?php case (3): ?> <?php echo app('translator')->get("$string_file.both"); ?> (<?php echo app('translator')->get("$string_file.sender"); ?>/<?php echo app('translator')->get("$string_file.receiver"); ?>)
                                            <?php break; ?>
                                    <?php endswitch; ?>
                                </b>
                            </div>
                            <div class="col-md-3">
                                <label><?php echo app('translator')->get("$string_file.offer_type"); ?>
                                </label><br>
                                <b><?php if($referral_system->offer_type == 1): ?> <?php echo app('translator')->get("$string_file.fixed_amount"); ?> <?php else: ?> <?php echo app('translator')->get("$string_file.discount"); ?> <?php endif; ?></b>
                            </div>
                            <div class="col-md-3">
                                <label><?php echo app('translator')->get("$string_file.offer_value"); ?>
                                </label><br>
                                <?php if($referral_system->offer_type == 1): ?>
                                    <b><?php echo e($referral_system->Country->isoCode." ".$referral_system->offer_value); ?></b>
                                <?php else: ?>
                                    <b><?php echo e($referral_system->offer_value." %"); ?></b>
                               <?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <label><?php echo app('translator')->get("$string_file.maximum_offer_amount"); ?>
                                </label><br>
                                <b><?php echo e(!empty($referral_system->maximum_offer_amount) ? $referral_system->maximum_offer_amount : "--"); ?></b>
                            </div>
                            <div class="col-md-3">
                                <label><?php echo app('translator')->get("$string_file.offer_condition"); ?>
                                </label><br>
                                <b><?php echo e(getReferralSystemOfferCondition($string_file)[$referral_system->offer_condition]); ?></b>
                            </div>
                        </div>
                        <?php $additional_data = json_decode($referral_system->offer_condition_data,true); ?>
                        <hr>
                        <?php if($referral_system->offer_condition == 1): ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <label><?php echo app('translator')->get("$string_file.no_of_uses"); ?>
                                    </label><br>
                                    <b><b><?php echo e($additional_data['limit_usage']); ?></b></b>
                                </div>
                                <div class="col-md-3">
                                    <label><?php echo app('translator')->get("$string_file.no_of_days"); ?>
                                    </label><br>
                                    <b><b><?php echo e($additional_data['day_limit']); ?></b></b>
                                </div>
                                <div class="col-md-3">
                                    <label><?php echo app('translator')->get("$string_file.days_count_start"); ?>
                                    </label><br>
                                    <b><?php if($additional_data['day_count'] == 1): ?> <?php echo app('translator')->get("$string_file.after_signup"); ?> <?php else: ?> <?php echo app('translator')->get("$string_file.after_financial_transaction"); ?> <?php endif; ?></b>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if($referral_system->offer_condition == 4): ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <label><?php echo app('translator')->get("$string_file.no_of_drivers"); ?>
                                    </label><br>
                                    <b><?php echo e($additional_data['conditional_no_driver']); ?></b>
                                </div>
                                <div class="col-md-3">
                                    <label>Rule <?php echo app('translator')->get("$string_file.for_driver"); ?>
                                    </label><br>
                                    <b><?php echo e(getReferralSystemDriverCondition($string_file)[$additional_data['conditional_driver_rule']]); ?></b>
                                </div>
                                <div class="col-md-3">
                                    <label><?php echo app('translator')->get("$string_file.no_of_services"); ?>
                                    </label><br>
                                    <b><?php echo e($additional_data['conditional_no_services']); ?></b>
                                </div>
                            </div>
                        <?php endif; ?>
                            <div class="form-actions float-right" style="margin-bottom: 1%">
                                <button type="submit" class="btn btn-primary"><i
                                            class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?> </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/referral_system/edit.blade.php ENDPATH**/ ?>