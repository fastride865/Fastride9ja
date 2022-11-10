<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
           <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('driver.index')); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="<?php echo app('translator')->get("$string_file.all_drivers"); ?>
                                        "></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-user" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.driver_profile"); ?>
                        <?php if($driver->signupStep < 8): ?>
                            <span style="color:red; font-size: 16px;"><?php echo app('translator')->get("$string_file.mandatory_document_not_uploaded"); ?></span>
                        <?php endif; ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <div id="user-profile">
                        <div class="col-md-12">
                            <h5><?php echo app('translator')->get("$string_file.personal_details"); ?></h5>
                            <hr>
                            <div class="row">
                                <!-- Column -->
                                <div class="col-md-4 col-xs-12">
                                    <div class="card my-2 shadow  bg-white h-240">
                                        <div class="justify-content-center p-3">
                                            <div class="col-md-12 col-xs-12"
                                                 style="text-align:center;justify-content:center">
                                                <div class="mt-15 mb-15 h-100">
                                                    <img height="100" width="100" class="rounded-circle"
                                                         src="<?php if($driver->profile_image): ?> <?php echo e(get_image($driver->profile_image,'driver')); ?><?php endif; ?>">
                                                </div>
                                            </div>
                                            <div class="overlay-box">
                                                <div class="user-content " style="text-align:center">
                                                    <?php if(Auth::user()->demo == 1): ?>
                                                        <h5 class="user-name mb-3"><?php echo app('translator')->get("$string_file.name"); ?>
                                                            : <?php echo e("********".substr($driver->first_name." ".$driver->last_name, -2)); ?></h5>
                                                        <h6 class="user-job mb-3"> <?php echo app('translator')->get("$string_file.email"); ?>
                                                            : <?php echo e("********".substr($driver->email, -2)); ?></h6>
                                                        <h6 class="user-loaction mb-5"> <?php echo app('translator')->get("$string_file.phone"); ?>
                                                            : <?php echo e("********".substr($driver->phoneNumber, -2)); ?></h6>
                                                    <?php else: ?>
                                                        <h5 class="user-name mt-5 mb-3"><?php echo app('translator')->get("$string_file.name"); ?>
                                                            : <?php echo e($driver->first_name." ".$driver->last_name); ?></h5>
                                                        <h6 class="user-job mb-3"> <?php echo app('translator')->get("$string_file.email"); ?>
                                                            : <?php echo e($driver->email); ?></h6>
                                                        <h6 class="user-location mb-5"> <?php echo app('translator')->get("$string_file.phone"); ?>
                                                            : <?php echo e($driver->phoneNumber); ?></h6>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div class="col-md-8 col-xs-12 mt-20">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4 col-xs-12 py-2 mb-3">
                                            <div class="border-left-success">
                                                <div class="">
                                                    <div class="row no-gutters align-items-center">
                                                        <div class="col mr-2">
                                                            <div class="h6  text-uppercase mb-1"><?php echo app('translator')->get("$string_file.service_area"); ?> </div>
                                                            <div class="h6 mb-0 font-weight-400"
                                                                 style="color:#7c8c9a"><?php if($driver->CountryArea->LanguageSingle): ?> <?php echo e($driver->CountryArea->LanguageSingle->AreaName); ?> <?php else: ?>  <?php echo e($driver->CountryArea->LanguageAny->AreaName); ?> <?php endif; ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if($config->driver_wallet_status == 1): ?>
                                            <div class="col-md-4 col-sm-4 col-xs-12 py-2 mb-3 ">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1"><?php echo app('translator')->get("$string_file.wallet_money"); ?></div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a"> <?php echo e($driver->wallet_money); ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($config->bank_details_enable == 1): ?>
                                            <div class="col-md-4 col-sm-4 col-xs-12 py-2 mb-3">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6  text-uppercase mb-1"><?php echo app('translator')->get("$string_file.account_holder_name"); ?></div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a"><?php echo e($driver->account_holder_name); ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                    <div class="row">
                                        <?php if($config->driver_limit == 1): ?>
                                            <div class="col-md-4 col-sm-4 col-xs-12 py-2 mb-3">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6  text-uppercase mb-1"><?php echo app('translator')->get("$string_file.radius_limit"); ?></div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a">
                                                                    <?php if(isset($driver_config->radius)): ?><?php echo e($driver_config->radius); ?>

                                                                    <a target="_blank"
                                                                       href="https://www.google.com/maps/place/<?php echo e($driver_config->latitude); ?>,<?php echo e($driver_config->longitude); ?>"
                                                                       class="ml-2" title="View Map">
                                                                        <i class="fa fa-map" aria-hidden="true"></i>
                                                                    </a>
                                                                    <?php else: ?> -- <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if(!empty(Auth::user('merchant')->ApplicationConfiguration) && Auth::user('merchant')->ApplicationConfiguration->gender == 1): ?>
                                            <div class="col-md-4 col-sm-4 col-xs-12 py-2 mb-3">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1"><?php echo app('translator')->get("$string_file.gender"); ?></div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a">
                                                                    <?php if($driver->driver_gender == 1): ?> <?php echo app('translator')->get("$string_file.male"); ?> <?php else: ?> <?php echo app('translator')->get("$string_file.female"); ?>  <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if(!empty(Auth::user('merchant')->Configuration) && Auth::user('merchant')->Configuration->paystack_split_payment_enable == 1): ?>
                                            <div class="col-md-4 col-sm-4 col-xs-12 py-2 mb-3">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1"><?php echo app('translator')->get("$string_file.paystack_account"); ?></div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a">
                                                                    <?php echo e($driver->paystack_account_id." (".ucfirst($driver->paystack_account_status).")"); ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($config->bank_details_enable == 1): ?>
                                            <div class="col-md-4 col-sm-4 col-xs-12 py-2 mb-3">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1"><?php echo app('translator')->get("$string_file.bank_name"); ?></div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a"><?php echo e($driver->bank_name); ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                    <div class="row">
                                        <?php if($driver->account_type_id): ?>
                                            <div class="col-md-4 col-sm-4 mb-3 col-xs-12 py-2">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1"><?php echo app('translator')->get("$string_file.account_type"); ?></div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a"><?php echo e($driver->AccountType->name); ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($driver->online_code): ?>
                                            <div class="col-md-4 col-sm-4 mb-3 col-xs-12 py-2">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1"><?php echo app('translator')->get("$string_file.transaction_code"); ?></div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a"><?php echo e($driver->online_code); ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if(!empty(Auth::user('merchant')->ApplicationConfiguration) && Auth::user('merchant')->ApplicationConfiguration->smoker == 1): ?>
                                            <div class="col-md-4 col-sm-4 mb-3 col-xs-12 py-2">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1"> <?php echo app('translator')->get("$string_file.smoke"); ?></div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a">
                                                                    <?php if($driver->DriverRideConfig): ?> <?php if($driver->DriverRideConfig->smoker_type == 1): ?>  <?php echo app('translator')->get("$string_file.smoker"); ?> <?php else: ?>  <?php echo app('translator')->get("$string_file.non_smoker"); ?> <?php endif; ?> <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="row">
                                        <?php if($config->bank_details_enable == 1): ?>
                                            <div class="col-md-4 col-sm-4 mb-3 col-xs-12 py-2">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1"><?php echo app('translator')->get("$string_file.account_number"); ?></div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a"><?php echo e($driver->account_number); ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($driver->driver_address): ?>
                                            <div class="col-md-4 col-sm-4 mb-3 col-xs-12 py-2">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1"><?php echo app('translator')->get("$string_file.address"); ?></div>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a"><?php echo e($driver->driver_address); ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                            <div class="col-md-4 col-sm-6 mb-3 col-xs-12 py-2">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1"><?php echo app('translator')->get("$string_file.address"); ?></div>
                                                                <?php $additionalData = json_decode($driver->driver_additional_data, true); ?>
                                                                <div class="h6 mb-0 font-weight-400"
                                                                     style="color:#7c8c9a">
                                                                    <?php if(!empty($additionalData)): ?>
                                                                        <?php $__currentLoopData = $additionalData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <?php if(Auth::user()->demo == 1): ?>
                                                                                <?php echo e(ucwords( "********".substr($key, -2)) .' : '. ucwords("********".substr($value, -2))); ?>

                                                                                <br>
                                                                            <?php else: ?>
                                                                                <?php echo e(ucwords($key) .' : '. ucwords($value)); ?>

                                                                                <br>
                                                                            <?php endif; ?>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php else: ?>
                                                                        <?php echo app('translator')->get("$string_file.data_not_found"); ?>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php if(isset($driver->subscription_wise_commission) && $driver->subscription_wise_commission != NULL): ?>
                                            <div class="col-md-4 col-sm-6 mb-3 col-xs-12 py-2">
                                                <div class="border-left-success">
                                                    <div class="">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 text-uppercase mb-1"><?php echo app('translator')->get("$string_file.commission_type"); ?></div>
                                                                <?php if($driver->subscription_wise_commission == 2): ?>
                                                                    <div class="h6 mb-0 font-weight-400"
                                                                         style="color:#7c8c9a">
                                                                        <?php echo app('translator')->get("$string_file.commission_based"); ?>
                                                                    </div>
                                                                <?php elseif($driver->subscription_wise_commission == 1): ?>
                                                                    <div class="h6 mb-0 font-weight-400"
                                                                         style="color:#7c8c9a">
                                                                        <?php echo app('translator')->get("$string_file.subscription_based"); ?><br>
                                                                        <?php echo app('translator')->get("$string_file.current_package"); ?>
                                                                        :- <?php echo e(isset($package_name) ? $package_name : '---'); ?>

                                                                    </div>
                                                                <?php else: ?>
                                                                    <?php echo app('translator')->get("$string_file.data_not_found"); ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-20">
                                    <h5><?php echo app('translator')->get("$string_file.personal_document"); ?></h5>
                                    <hr>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable">
                                            <thead>
                                                <tr class="text-center">
                                                    <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                                    <th><?php echo app('translator')->get("$string_file.document_name"); ?></th>
                                                    <th><?php echo app('translator')->get("$string_file.document"); ?> </th>
                                                    <th><?php echo app('translator')->get("$string_file.expire_date"); ?>  </th>
                                                    <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                                                    <th><?php echo app('translator')->get("$string_file.uploaded_at"); ?> </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $doc_sn = 1 ?>
                                                <?php $__currentLoopData = $driver->DriverDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td class="text-center">
                                                            <?php echo e($doc_sn); ?>

                                                        </td>
                                                        <td class="text-center">
                                                            <?php echo e($document->Document->DocumentName); ?>

                                                        </td>
                                                        <td class="text-center">
                                                            <?php $p_doc = get_image($document->document_file,'driver_document'); ?>
                                                            <a target="_blank" href="<?php echo e($p_doc); ?>">
                                                                <img src="<?php echo e($p_doc); ?>" alt="avatar" style="width: 100px;height: 100px;">
                                                            </a>
                                                        </td>
                                                        <td class="text-center"><?php echo convertTimeToUSERzone($document->expire_date, $driver->CountryArea->timezone,null,$driver->Merchant, 2); ?></td>
                                                        <td class="text-center">
                                                            <?php switch($document->document_verification_status):
                                                                case (1): ?>
                                                                <?php echo app('translator')->get("$string_file.pending_for_verification"); ?>
                                                                <?php break; ?>
                                                                <?php case (2): ?>
                                                                <?php echo app('translator')->get("$string_file.verified"); ?>
                                                                <?php break; ?>
                                                                <?php case (3): ?>
                                                                <?php echo app('translator')->get("$string_file.rejected"); ?>
                                                                <?php break; ?>
                                                                <?php case (4): ?>
                                                                <?php echo app('translator')->get("$string_file.expired"); ?>
                                                                <?php break; ?>
                                                            <?php endswitch; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php echo convertTimeToUSERzone($document->updated_at, $driver->CountryArea->timezone,null,$driver->Merchant); ?>

                                                        </td>
                                                    </tr>
                                                    <?php $doc_sn = $doc_sn+1; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php if($driver->segment_group_id == 2 && !empty($arr_segment)): ?>
                                    <div class="col-md-12 mt-20">
                                        <h5><?php echo app('translator')->get("$string_file.segment_services_with_time_slot"); ?></h5>
                                        <hr>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="dataTable">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                                        <th><?php echo app('translator')->get("$string_file.segment"); ?></th>
                                                        <th><?php echo app('translator')->get("$string_file.services"); ?></th>
                                                        <th><?php echo app('translator')->get("$string_file.time_slot"); ?></th>
                                                        <th><?php echo app('translator')->get("$string_file.document"); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $doc_sn = 1 ;  $arr_days = get_days($string_file);?>
                                                    <?php $__currentLoopData = $arr_segment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td class="">
                                                                <?php echo e($doc_sn); ?>

                                                            </td>
                                                            <td class="">
                                                                <?php echo e($segment->Name($driver->merchant_id)); ?>

                                                            </td>
                                                            <td class="">
                                                                <?php $__currentLoopData = $segment->ServiceType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php echo e($service->ServiceName($driver->merchant_id)); ?>,
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </td>
                                                            <td class="">
                                                                <?php $__currentLoopData = $segment->ServiceTimeSlot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day_slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php echo e($arr_days[$day_slot->day] ?? NULL); ?> => <?php echo e(implode(',',array_pluck($day_slot->ServiceTimeSlotDetail,'slot_time_text'))); ?> <br>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </td>
                                                            <td class="">
                                                                <?php if($segment->DriverSegmentDocument->count() > 0): ?>
                                                                <table class="table table-bordered" id="dataTable">
                                                                    <thead>
                                                                    <tr class="text-center">
                                                                        <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                                                                        <th><?php echo app('translator')->get("$string_file.document"); ?></th>
                                                                        <th><?php echo app('translator')->get("$string_file.expire_date"); ?>  </th>
                                                                        <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                                                                        <th><?php echo app('translator')->get("$string_file.uploaded_at"); ?> </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php $doc_sn = 1 ?>
                                                                    <?php $__currentLoopData = $segment->DriverSegmentDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <tr>
                                                                            <td class="text-center">
                                                                                <?php echo e($document->Document->DocumentName); ?>

                                                                            </td>
                                                                            <td class="text-center">
                                                                                <?php $p_doc = get_image($document->document_file,'segment_document'); ?>
                                                                                <a target="_blank" href="<?php echo e($p_doc); ?>">
                                                                                    <img src="<?php echo e($p_doc); ?>" alt="avatar" style="width: 50px;height: 50px;">
                                                                                </a>
                                                                            </td>
                                                                            <td class="text-center"><?php echo convertTimeToUSERzone($document->expire_date, $driver->CountryArea->timezone,null,$driver->Merchant, 2); ?></td>
                                                                            <td class="text-center">
                                                                                <?php switch($document->document_verification_status):
                                                                                    case (1): ?>
                                                                                    <?php echo app('translator')->get("$string_file.pending_for_verification"); ?>
                                                                                    <?php break; ?>
                                                                                    <?php case (2): ?>
                                                                                    <?php echo app('translator')->get("$string_file.verified"); ?>
                                                                                    <?php break; ?>
                                                                                    <?php case (3): ?>
                                                                                    <?php echo app('translator')->get("$string_file.rejected"); ?>
                                                                                    <?php break; ?>
                                                                                    <?php case (4): ?>
                                                                                    <?php echo app('translator')->get("$string_file.expired"); ?>
                                                                                    <?php break; ?>
                                                                                <?php endswitch; ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <?php echo convertTimeToUSERzone($document->updated_at, $driver->CountryArea->timezone,null,$driver->Merchant); ?>

                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </tbody>
                                                                </table>
                                                                <?php endif; ?>
                                                            </td>
                                                            <?php $doc_sn = $doc_sn+1; ?>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <?php if(!empty($vehicle_details)): ?>
                                         <div class="col-md-12 mt-20 mb-10">
                                            <h5><?php echo app('translator')->get("$string_file.vehicle_details"); ?></h5>
                                            <hr>
                                            <div class="row mt-20">
                                                <div class="col-lg-8 mb-30">
                                                    <div class="">
                                                        <span class=""><?php echo app('translator')->get("$string_file.vehicle_type"); ?> </span> : <?php echo e($vehicle_details->VehicleType->VehicleTypeName); ?>  |
                                                        <span class=""><?php echo app('translator')->get("$string_file.vehicle_model"); ?>  </span> : <?php echo e($vehicle_details->VehicleModel->VehicleModelName); ?> |
                                                        <span class=""><?php echo app('translator')->get("$string_file.vehicle_make"); ?>  </span> : <?php echo e($vehicle_details->VehicleMake->VehicleMakeName); ?> <br>

                                                        <span class=""><?php echo app('translator')->get("$string_file.vehicle_number"); ?> </span> : <?php echo e($vehicle_details->vehicle_number); ?> <br><?php if($config->vehicle_model_expire == 1): ?> <?php echo app('translator')->get("$string_file.vehicle_registered_date"); ?> : <?php echo convertTimeToUSERzone($vehicle_details->vehicle_register_date, $driver->CountryArea->timezone,null,$driver->Merchant,2); ?> |  <?php echo app('translator')->get("$string_file.vehicle_expire_date"); ?> : <?php echo convertTimeToUSERzone($vehicle_details->vehicle_expire_date, $driver->CountryArea->timezone,null,$driver->Merchant,2); ?> <?php endif; ?>
                                                        <br>
                                                        <span><?php echo app('translator')->get("$string_file.services"); ?> : <?php echo e(implode(',',array_pluck($vehicle_details->ServiceTypes,'serviceName'))); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-5">
                                                            <h6><?php echo app('translator')->get("$string_file.vehicle_image"); ?> </h6>
                                                            <div class="" style="width: 6.5rem;">
                                                                <div class=" bg-light">
                                                                    <?php $vehicle_image = get_image($vehicle_details->vehicle_image,'vehicle_document'); ?>
                                                                    <a href="<?php echo e($vehicle_image); ?>" target="_blank"><img src="<?php echo e($vehicle_image); ?>" style="width:100%;height:80px;"></a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 col-sm-7">
                                                            <h6><?php echo app('translator')->get("$string_file.vehicle"); ?>  <?php echo app('translator')->get("$string_file.number_plate"); ?>  <?php echo app('translator')->get("$string_file.image"); ?> </h6>
                                                            <div class="" style="width: 6.5rem;">
                                                                <div class=" bg-light">
                                                                    <?php $vehicle_number_plate_image = get_image($vehicle_details->vehicle_number_plate_image,'vehicle_document'); ?>
                                                                    <a href="<?php echo e($vehicle_number_plate_image); ?>" target="_blank"><img src="<?php echo e($vehicle_number_plate_image); ?>" style="width:100%;height:80px;"></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <h5><?php echo app('translator')->get("$string_file.current_vehicle_documents"); ?></h5>
                                            <hr>
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="dataTable">
                                                    <thead>
                                                        <tr>
                                                             <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                                             <th><?php echo app('translator')->get("$string_file.document_name"); ?></th>
                                                             <th><?php echo app('translator')->get("$string_file.document"); ?></th>
                                                             <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                                                             <th><?php echo app('translator')->get("$string_file.expire_date"); ?></th>
                                                             <th><?php echo app('translator')->get("$string_file.uploaded_at"); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                         <?php $sn = 1; ?>
                                                         <?php $__currentLoopData = $vehicle_details->DriverVehicleDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                             <tr>
                                                                 <td><?php echo e($sn); ?></td>
                                                                 <td> <?php echo e($document->Document->documentname); ?></td>
                                                                 <td>
                                                                     <?php $vehicle_file = get_image($document->document,'vehicle_document'); ?>
                                                                     <a href="<?php echo e($vehicle_file); ?>" target="_blank"><img src="<?php echo e($vehicle_file); ?>" style="width:60px;height:60px;border-radius: 10px"></a>
                                                                 </td>
                                                                 <td>
                                                                     <?php switch($document->document_verification_status):
                                                                         case (1): ?>
                                                                         <?php echo app('translator')->get("$string_file.pending_for_verification"); ?>
                                                                         <?php break; ?>
                                                                         <?php case (2): ?>
                                                                         <?php echo app('translator')->get("$string_file.verified"); ?>
                                                                         <?php break; ?>
                                                                         <?php case (3): ?>
                                                                         <?php echo app('translator')->get("$string_file.rejected"); ?>
                                                                         <?php break; ?>
                                                                         <?php case (4): ?>
                                                                         <?php echo app('translator')->get("$string_file.expired"); ?>
                                                                         <?php break; ?>
                                                                     <?php endswitch; ?>
                                                                 </td>
                                                                 <td>
                                                                     <?php echo convertTimeToUSERzone($document->expire_date, $driver->CountryArea->timezone,null,$driver->Merchant,2); ?>

                                                                 </td>
                                                                 <td>
                                                                     <?php echo convertTimeToUSERzone($document->updated_at, $driver->CountryArea->timezone,null,$driver->Merchant); ?>

                                                                 </td>
                                                                 <?php $sn = $sn+1; ?>
                                                             </tr>
                                                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                     </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <?php if($driver->signupStep <= 8 && $driver->reject_driver !=2): ?>
                                <div class="float-right mt-10">
                                    <?php if($driver->signupStep == 8): ?>
                                        <a href="<?php echo e(route('merchant.driver-vehicle-verify',[$driver->id,1])); ?>">
                                            <button class="btn btn-success float-right"><?php echo app('translator')->get("$string_file.approve"); ?></button>
                                        </a>
                                    <?php endif; ?>
                                    <button class="btn btn-danger float-right mr-2"
                                            data-toggle="modal"
                                            data-target="#exampleModalCenter"><?php echo app('translator')->get("$string_file.reject"); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                            <?php if($tempDocUploaded > 0): ?>
                                <div class="row">
                                    <?php if($driver->DriverDocument->where('temp_document_file','!=','')->count()>0): ?>
                                        <div class="col-md-12 mt-20">
                                            <h5><?php echo app('translator')->get("$string_file.temporary_personal_documents"); ?></h5>
                                            <hr>
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="dataTable">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                                            <th><?php echo app('translator')->get("$string_file.document_name"); ?></th>
                                                            <th><?php echo app('translator')->get("$string_file.document"); ?> </th>
                                                            <th><?php echo app('translator')->get("$string_file.expire_date"); ?>  </th>
                                                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                                                            <th><?php echo app('translator')->get("$string_file.uploaded_at"); ?> </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $doc_sn = 1 ?>
                                                        <?php $__currentLoopData = $driver->DriverDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if(!empty($document->temp_document_file)): ?>
                                                                <tr>
                                                                    <td class="text-center">
                                                                        <?php echo e($doc_sn); ?>

                                                                    </td>
                                                                    <td class="text-center">
                                                                        <?php echo e($document->Document->DocumentName); ?>

                                                                    </td>
                                                                    <td class="text-center">
                                                                        <?php $file = get_image($document->temp_document_file,'driver_document'); ?>
                                                                        <a target="_blank"  href="<?php echo e($file); ?>"><img src="<?php echo e($file); ?>" alt="avatar" style="width: 100px;height: 100px;"></a>
                                                                    </td>
                                                                    <td class="text-center"><?php echo convertTimeToUSERzone($document->temp_expire_date, $driver->CountryArea->timezone,null,$driver->Merchant, 2); ?></td>
                                                                    <td class="text-center">
                                                                        <?php switch($document->temp_doc_verification_status):
                                                                            case (1): ?>
                                                                            <?php echo app('translator')->get("$string_file.pending_for_verification"); ?>
                                                                            <?php break; ?>
                                                                            <?php case (2): ?>
                                                                            <?php echo app('translator')->get("$string_file.verified"); ?>
                                                                            <?php break; ?>
                                                                            <?php case (3): ?>
                                                                            <?php echo app('translator')->get("$string_file.rejected"); ?>
                                                                            <?php break; ?>
                                                                            <?php case (4): ?>
                                                                            <?php echo app('translator')->get("$string_file.expired"); ?>
                                                                            <?php break; ?>
                                                                        <?php endswitch; ?>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <?php echo convertTimeToUSERzone($document->updated_at, $driver->CountryArea->timezone,null,$driver->Merchant); ?>

                                                                    </td>
                                                                    <?php $doc_sn = $doc_sn+1; ?>
                                                                </tr>
                                                            <?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($driver->segment_group_id == 2 && !empty($arr_segment)): ?>
                                        <div class="col-md-12 mt-20">
                                            <h5><?php echo app('translator')->get("$string_file.temporary_segment_documents"); ?></h5>
                                            <hr>
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="dataTable">
                                                    <thead>
                                                    <tr class="text-center">
                                                        <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                                        <th><?php echo app('translator')->get("$string_file.segment"); ?></th>
                                                        <th><?php echo app('translator')->get("$string_file.documents"); ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $doc_sn = 1 ;  $arr_days = get_days($string_file);?>
                                                        <?php $__currentLoopData = $arr_segment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <tr class="text-center">
                                                                <td class="">
                                                                    <?php echo e($doc_sn); ?>

                                                                </td>
                                                                <td class="">
                                                                    <?php echo e($segment->Name($driver->merchant_id)); ?>

                                                                </td>
                                                                <td class="">
                                                                    <?php if($segment->DriverSegmentDocument->where('temp_document_file','!=','')->count() > 0): ?>
                                                                        <table class="table table-bordered" id="dataTable">
                                                                            <thead>
                                                                                <tr class="text-center">
                                                                                    <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                                                                                    <th><?php echo app('translator')->get("$string_file.document"); ?></th>
                                                                                    <th><?php echo app('translator')->get("$string_file.expire_date"); ?>  </th>
                                                                                    <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                                                                                    <th><?php echo app('translator')->get("$string_file.uploaded_at"); ?> </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php $doc_sn = 1 ?>
                                                                                <?php $__currentLoopData = $segment->DriverSegmentDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                    <?php if(!empty($document->temp_document_file)): ?>
                                                                                        <tr>
                                                                                            <td class="text-center">
                                                                                                <?php echo e($document->Document->DocumentName); ?>

                                                                                            </td>
                                                                                            <td class="text-center">
                                                                                                <?php $p_doc = get_image($document->document_file,'segment_document'); ?>
                                                                                                <a target="_blank" href="<?php echo e($p_doc); ?>">
                                                                                                    <img src="<?php echo e($p_doc); ?>" alt="avatar" style="width: 50px;height: 50px;">
                                                                                                </a>
                                                                                            </td>
                                                                                            <td class="text-center"><?php echo convertTimeToUSERzone($document->expire_date, $driver->CountryArea->timezone,null,$driver->Merchant, 2); ?></td>
                                                                                            <td class="text-center">
                                                                                                <?php switch($document->temp_doc_verification_status):
                                                                                                    case (1): ?>
                                                                                                    <?php echo app('translator')->get("$string_file.pending_for_verification"); ?>
                                                                                                    <?php break; ?>
                                                                                                    <?php case (2): ?>
                                                                                                    <?php echo app('translator')->get("$string_file.verified"); ?>
                                                                                                    <?php break; ?>
                                                                                                    <?php case (3): ?>
                                                                                                    <?php echo app('translator')->get("$string_file.rejected"); ?>
                                                                                                    <?php break; ?>
                                                                                                <?php endswitch; ?>
                                                                                            </td>
                                                                                            <td class="text-center">
                                                                                                <?php echo convertTimeToUSERzone($document->updated_at, $driver->CountryArea->timezone,null,$driver->Merchant); ?>

                                                                                            </td>
                                                                                        </tr>
                                                                                    <?php endif; ?>
                                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                            </tbody>
                                                                        </table>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <?php $doc_sn = $doc_sn+1; ?>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <?php if(!empty($vehicle_details)): ?>
                                            <?php if($vehicle_details->DriverVehicleDocument->where('temp_document_file','!=','')->count()>0): ?>
                                                <div class="col-md-12 mt-20">
                                                    <h5><?php echo app('translator')->get("$string_file.temporary_vehicle_documents"); ?></h5>
                                                    <hr>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered" id="dataTable">
                                                            <thead>
                                                            <tr class="text-center">
                                                                <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                                                <th><?php echo app('translator')->get("$string_file.document_name"); ?></th>
                                                                <th><?php echo app('translator')->get("$string_file.document"); ?> </th>
                                                                <th><?php echo app('translator')->get("$string_file.expire_date"); ?></th>
                                                                <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                                                                <th><?php echo app('translator')->get("$string_file.uploaded_at"); ?> </th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php $doc_sr = 1 ?>
                                                            <?php $__currentLoopData = $vehicle_details->DriverVehicleDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <?php if(!empty($document->temp_document_file)): ?>
                                                                    <tr>
                                                                        <td class="text-center"><?php echo e($doc_sr); ?></td>
                                                                        <td class="text-center"><?php echo e($document->Document->documentname); ?></td>
                                                                        <td class="text-center">
                                                                            <?php $vehicle_file = get_image($document->temp_document_file,'vehicle_document'); ?>
                                                                            <a href="<?php echo e($vehicle_file); ?>" target="_blank"><img src="<?php echo e($vehicle_file); ?>" style="width:60px;height:60px;border-radius: 10px"></a>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <?php echo convertTimeToUSERzone($document->temp_expire_date, $driver->CountryArea->timezone,null,$driver->Merchant, 2); ?>

                                                                        </td>
                                                                        <td class="text-center">
                                                                            <?php switch($document->temp_doc_verification_status):
                                                                                case (1): ?>
                                                                                <?php echo app('translator')->get("$string_file.pending_for_verification"); ?>
                                                                                <?php break; ?>
                                                                                <?php case (2): ?>
                                                                                <?php echo app('translator')->get("$string_file.verified"); ?>
                                                                                <?php break; ?>
                                                                                <?php case (3): ?>
                                                                                <?php echo app('translator')->get("$string_file.rejected"); ?>
                                                                                <?php break; ?>
                                                                                <?php case (4): ?>
                                                                                <?php echo app('translator')->get("$string_file.expired"); ?>
                                                                                <?php break; ?>
                                                                            <?php endswitch; ?>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <?php echo convertTimeToUSERzone($document->updated_at, $driver->CountryArea->timezone,null,$driver->Merchant); ?>

                                                                        </td>
                                                                        <?php $doc_sr = $doc_sr+1; ?>
                                                                    </tr>
                                                                <?php endif; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <?php if($driver->signupStep == 9 && $driver->reject_driver !=2): ?>
                                    <div class="float-right mt-10">
                                        <a href="<?php echo e(route('merchant.driverTempDocVerify',[$driver->id,1])); ?>">
                                            <button class="btn btn-success float-right"><?php echo app('translator')->get("$string_file.approve"); ?></button>
                                        </a>
                                        <button class="btn btn-danger float-right mr-2" data-toggle="modal"
                                                data-target="#exampleModalCenterTemp"><?php echo app('translator')->get("$string_file.reject"); ?>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-group" action="<?php echo e(route('merchant.driver-vehicle-reject')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle"><?php echo app('translator')->get("$string_file.reject_driver"); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" value="<?php echo e($driver->id); ?>" name="driver_id">
                            <?php if(count($driver->DriverDocument->where('document_verification_status', '=', 1)) > 0): ?>
                                <div class="col-md-12">
                                    <h5><?php echo app('translator')->get("$string_file.personal_document"); ?></h5>
                                </div>
                                <?php $__currentLoopData = $driver->DriverDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($document->document_verification_status == 1): ?>
                                        <div class="col-md-6">
                                            <input type="checkbox" value="<?php echo e($document->id); ?>"
                                                   name="document_id[]"> <?php echo e($document->Document->documentname); ?>

                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            <hr>
                            <?php if($driver->segment_group_id == 2 && !empty($arr_segment)): ?>
                                <div class="col-md-12">
                                    <h5><?php echo app('translator')->get("$string_file.segment_documents"); ?></h5>
                                </div>
                                <?php $__currentLoopData = $arr_segment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $__currentLoopData = $segment->DriverSegmentDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($document->document_verification_status == 1): ?>
                                            <div class="col-md-6">
                                                <input type="checkbox" value="<?php echo e($document->id); ?>"
                                                       name="segment_documents[]"> <?php echo e($segment->Name($driver->merchant_id).' '.$document->Document->documentname); ?>

                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <?php if(!empty($vehicle_details) && $driver->id == $vehicle_details->owner_id): ?>
                                    <div class="col-md-12">
                                        <h5><?php echo app('translator')->get("$string_file.vehicle_document"); ?></h5>
                                    </div>
                                    <input type="hidden" value="<?php echo e($vehicle_details->id); ?>" name="driver_vehicle_id">
                                    <?php $__currentLoopData = $vehicle_details->DriverVehicleDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($document->document_verification_status == 1): ?>
                                            <div class="col-md-6">
                                                <input type="checkbox" value="<?php echo e($document->id); ?>"
                                                       name="vehicle_documents[]"> <?php echo e($document->Document->documentname); ?>

                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <?php echo Form::hidden('request_from','driver_profile'); ?>

                            <div class="col-md-12">
                                <textarea class="form-control" placeholder="<?php echo app('translator')->get("$string_file.comments"); ?>" name="comment" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?php echo app('translator')->get("$string_file.close"); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo app('translator')->get("$string_file.reject"); ?> </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModalCenterTemp" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitleTemp" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-group" action="<?php echo e(route('merchant.driverTempDocReject')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle"><?php echo app('translator')->get("$string_file.reject_driver"); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" value="<?php echo e($driver->id); ?>" name="driver_id">
                            <?php if(count($driver->DriverDocument->where('temp_doc_verification_status', '=', 1)) > 0): ?>
                                <div class="col-md-12">
                                    <h5><?php echo app('translator')->get("$string_file.personal_document"); ?></h5>
                                </div>
                                <?php $__currentLoopData = $driver->DriverDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($document->temp_doc_verification_status == 1): ?>
                                        <div class="col-md-6">
                                            <input type="checkbox" value="<?php echo e($document->id); ?>" name="document_id[]"> <?php echo e($document->Document->documentname); ?>

                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            <hr>
                            <?php if($driver->segment_group_id == 2 && !empty($arr_segment)): ?>
                                <div class="col-md-12">
                                    <h5><?php echo app('translator')->get("$string_file.segment_document"); ?></h5>
                                </div>
                                <?php $__currentLoopData = $arr_segment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $__currentLoopData = $segment->DriverSegmentDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($document->temp_doc_verification_status == 1): ?>
                                            <div class="col-md-6">
                                                <input type="checkbox" value="<?php echo e($document->id); ?>"
                                                       name="segment_documents[]"> <?php echo e($segment->Name($driver->merchant_id).' '.$document->Document->documentname); ?>

                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <?php if(!empty($vehicle_details) && $driver->id == $vehicle_details->owner_id): ?>
                                    <div class="col-md-12">
                                        <h5><?php echo app('translator')->get("$string_file.vehicle_document"); ?></h5>
                                    </div>
                                    <input type="hidden" value="<?php echo e($vehicle_details->id); ?>" name="driver_vehicle_id">
                                    <?php $__currentLoopData = $vehicle_details->DriverVehicleDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($document->temp_doc_verification_status == 1): ?>
                                            <div class="col-md-6">
                                                <input type="checkbox" value="<?php echo e($document->id); ?>"
                                                       name="vehicle_documents[]"> <?php echo e($document->Document->documentname); ?>

                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <?php echo Form::hidden('request_from','driver_profile'); ?>

                            <div class="col-md-12">
                                <textarea class="form-control" placeholder="<?php echo app('translator')->get("$string_file.comments"); ?>" name="comment" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?php echo app('translator')->get("$string_file.close"); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo app('translator')->get("$string_file.reject"); ?> </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/show.blade.php ENDPATH**/ ?>