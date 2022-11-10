<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('users.index')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                        <a href="<?php echo e(route('excel.userRides',$user->id)); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                <i class="wb-download" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-info-circle" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.user_details"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <div id="user-profile">
                        <div class="row">
                            <!-- Column -->
                            <div class="col-md-4 col-xs-12">
                                <div class="card shadow">
                                    <div class="card-block text-center">
                                        <img src="<?php echo e(get_image($user->UserProfileImage,'user')); ?>"
                                             class="rounded-circle" width="120" height="120">
                                        <?php if(Auth::user()->demo == 1): ?>
                                            <h5 class="user-name mb-3"><?php echo e("********".substr($user->last_name,-2)); ?></h5>
                                            <p class="user-job mb-3"><?php echo e("********".substr($user->UserPhone,-2)); ?></p>
                                            <p class="user-info mb-3"><?php echo e("********".substr($user->email,-2)); ?></p>
                                        <?php else: ?>
                                            <h5 class="user-name mb-3"><?php echo e($user->first_name." ".$user->last_name); ?></h5>
                                            <p class="user-job mb-3"><?php echo e($user->UserPhone); ?></p>
                                            <p class="user-info mb-3"><?php echo e($user->email); ?></p>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-xs-12 mt-20">
                                <div class="row mb-5">
                                    <div class="col-md-6 col-sm-6 col-xs-12 py-2 ">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="h5 text-uppercase mb-1">
                                                    <i class="icon fa-car fa-2x text-gray-300"></i>
                                                    <?php echo app('translator')->get("$string_file.user_type"); ?>
                                                </div>
                                                <div class="mb-0">
                                                    <?php if($user->user_type == 1): ?>
                                                        <?php echo app('translator')->get("$string_file.corporate_user"); ?>
                                                    <?php else: ?>
                                                        <?php echo app('translator')->get("$string_file.retail"); ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="h5 text-uppercase mb-1">
                                                    <i class="icon fa-tag fa-2x text-gray-300"></i>
                                                    <?php echo app('translator')->get("$string_file.referral_code"); ?>
                                                </div>
                                                <div class="mb-0 text-gray-800"><?php echo e($user->ReferralCode); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="white-box"> -->
                                        <!-- <ul class="book_details"> -->
                                        <!-- <li> -->
                                    <!-- <h4><?php echo app('translator')->get("$string_file.referral_code"); ?></h4> -->
                                    <!-- <p><?php echo e($user->ReferralCode); ?></p> -->
                                        <!-- </li> -->
                                        <!-- </ul> -->
                                        <!-- </div> -->
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="h5 text-uppercase mb-1">
                                                    <i class="icon fa-signing fa-2x text-gray-300"></i>
                                                    <?php echo app('translator')->get("$string_file.signup_type"); ?>
                                                </div>
                                                <div class="mb-0 text-gray-800">
                                                    <?php switch($user->UserSignupType):
                                                        case (1): ?>
                                                        <?php echo app('translator')->get("$string_file.normal"); ?>
                                                        <?php break; ?>
                                                        <?php case (2): ?>
                                                        <?php echo app('translator')->get("$string_file.google"); ?>
                                                        <?php break; ?>
                                                        <?php case (3): ?>
                                                        <?php echo app('translator')->get("$string_file.facebook"); ?>
                                                        <?php break; ?>
                                                    <?php endswitch; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="h5 text-uppercase mb-1">
                                                    <i class="icon fa-mobile fa-2x text-gray-300"></i>
                                                    <?php echo app('translator')->get("$string_file.signup_plateform"); ?>
                                                </div>
                                                <div class="mb-0 text-gray-800">
                                                    <?php switch($user->UserSignupFrom):
                                                        case (1): ?>
                                                        <?php echo app('translator')->get("$string_file.application"); ?>
                                                        <?php break; ?>
                                                        <?php case (2): ?>
                                                        <?php echo app('translator')->get("$string_file.admin"); ?>
                                                        <?php break; ?>
                                                        <?php case (3): ?>
                                                        <?php echo app('translator')->get("$string_file.web"); ?>
                                                        <?php break; ?>
                                                    <?php endswitch; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <div class="col-md-6 col-sm-6 col-xs-12 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="h5 text-uppercase mb-1">
                                                    <i class="icon fa-calendar fa-2x text-gray-300"></i>
                                                    <?php echo app('translator')->get("$string_file.registered_date"); ?>
                                                </div>
                                                <div class="mb-0 text-gray-800">
                                                    <?php if(isset($user->CountryArea->timezone)): ?>
                                                        <?php echo convertTimeToUSERzone($user->created_at, $user->CountryArea->timezone, null, $user->Merchant); ?>

                                                    <?php else: ?>
                                                        <?php echo convertTimeToUSERzone($user->created_at, null, null, $user->Merchant); ?>

                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="h5 text-uppercase mb-1">
                                                    <i class="icon fa-calendar fa-2x text-gray-300"></i>
                                                    <?php echo app('translator')->get("$string_file.updated_at"); ?>
                                                </div>
                                                <div class="mb-0 text-gray-800">
                                                    <?php if(isset($user->CountryArea->timezone)): ?>
                                                        <?php echo convertTimeToUSERzone($user->updated_at, $user->CountryArea->timezone, null, $user->Merchant); ?>

                                                    <?php else: ?>
                                                        <?php echo convertTimeToUSERzone($user->updated_at, null, null, $user->Merchant); ?>

                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="row mb-5">
                                    <?php if($appConfig->gender == 1): ?>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="h5 text-uppercase mb-1">
                                                        <i class="icon fa-sign-in fa-2x text-gray-300"></i>
                                                        <?php echo app('translator')->get("$string_file.gender"); ?>
                                                    </div>
                                                    <div class="mb-0 text-gray-800">
                                                        <?php if($user->user_gender == 1): ?> <?php echo app('translator')->get("$string_file.male"); ?> <?php else: ?> <?php echo app('translator')->get("$string_file.female"); ?>  <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($appConfig->smoker == 1): ?>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="h5 text-uppercase mb-1">
                                                        <i class="icon wi-smoke text-gray-300" area-hidden="true"></i>
                                                        <?php echo app('translator')->get("$string_file.smoke"); ?>
                                                    </div>
                                                    <div class="mb-0 text-gray-800">
                                                    </div>
                                                    <?php if($user->smoker_type == 1): ?>  <?php echo app('translator')->get("$string_file.smoker"); ?> <?php else: ?>  <?php echo app('translator')->get("$string_file.non_smoker"); ?> <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-12 mt-30">
                                <table id="customDataTable"
                                       class="display nowrap table table-hover table-striped w-full" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                        <th><?php echo app('translator')->get("$string_file.ride_type"); ?></th>
                                        <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                                        <th><?php echo app('translator')->get("$string_file.service_details"); ?></th>
                                        <th><?php echo app('translator')->get("$string_file.service_area"); ?> </th>
                                        <th><?php echo app('translator')->get("$string_file.pickup_drop"); ?></th>
                                        <th><?php echo app('translator')->get("$string_file.current_status"); ?></th>
                                        <th><?php echo app('translator')->get("$string_file.payment"); ?></th>
                                        <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $sr = $bookings->firstItem() ?>
                                    <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($sr); ?></td>
                                            <td>
                                                <?php if($booking->booking_type == 1): ?>
                                                    <?php echo app('translator')->get("$string_file.ride_now"); ?>
                                                <?php else: ?>
                                                    <?php echo app('translator')->get("$string_file.ride_later"); ?><br>(
                                                    <?php echo convertTimeToUSERzone($booking->later_booking_date, $booking->CountryArea->timezone,null,$booking->Merchant, 2); ?>

                                                    <br>
                                                    <?php echo e($booking->later_booking_time); ?> )
                                                <?php endif; ?>
                                            </td>
                                            <?php if(Auth::user()->demo == 1): ?>
                                                <td>
                                                         <span class="long_text">
                                                    <?php if($booking->Driver): ?>
                                                                 <?php echo e('********'.substr($booking->Driver->last_name,-2)); ?>

                                                                 <br>
                                                                 <?php echo e('********'.substr($booking->Driver->phoneNumber,-2)); ?>

                                                                 <br>
                                                                 <?php echo e('********'.substr($booking->Driver->email,-2)); ?>

                                                             <?php else: ?>
                                                                 <?php echo app('translator')->get("$string_file.not_assigned_yet"); ?>
                                                             <?php endif; ?>
                                                    </span>
                                                </td>
                                            <?php else: ?>
                                                <td>
                                                         <span class="long_text">
                                                    <?php if($booking->Driver): ?>
                                                                 <?php echo e($booking->Driver->first_name.' '.$booking->Driver->last_name); ?>

                                                                 <br>
                                                                 <?php echo e($booking->Driver->phoneNumber); ?>

                                                                 <br>
                                                                 <?php echo e($booking->Driver->email); ?>

                                                             <?php else: ?>
                                                                 <?php echo app('translator')->get("$string_file.not_assigned_yet"); ?>
                                                             <?php endif; ?>
                                                    </span>
                                                </td>
                                            <?php endif; ?>
                                            <td>
                                                <?php switch($booking->platform):
                                                    case (1): ?>
                                                    <?php echo app('translator')->get("$string_file.application"); ?>
                                                    <?php break; ?>
                                                    <?php case (2): ?>
                                                    <?php echo app('translator')->get("$string_file.admin"); ?>
                                                    <?php break; ?>
                                                    <?php case (3): ?>
                                                    <?php echo app('translator')->get("$string_file.web"); ?>
                                                    <?php break; ?>
                                                <?php endswitch; ?>
                                                <br>
                                                <?php
                                                    $service_text = ($booking->ServiceType) ? $booking->ServiceType->serviceName : $booking->deliveryType->name ;
                                                ?>
                                                <?php echo e($service_text); ?> <br>
                                                <?php echo e($booking->VehicleType->VehicleTypeName); ?>

                                            </td>
                                            <td> <?php echo e($booking->CountryArea->CountryAreaName); ?></td>
                                            <td>
                                                <a title="<?php echo e($booking->pickup_location); ?>"
                                                   href="https://www.google.com/maps/place/<?php echo e($booking->pickup_location); ?>" class="btn btn-icon btn-success ml-20"><i class="icon wb-map"></i></a>
                                                <a title="<?php echo e($booking->drop_location); ?>"
                                                   href="https://www.google.com/maps/place/<?php echo e($booking->drop_location); ?>" class="btn btn-icon btn-danger ml-20"><i class="icon fa-tint"></i></a>
                                            </td>
                                            <td style="text-align: center">
                                                <?php if(!empty($arr_booking_status)): ?>
                                                    <?php echo isset($arr_booking_status[$booking->booking_status]) ? $arr_booking_status[$booking->booking_status] : ""; ?>

                                                    <br>
                                                    <?php echo app('translator')->get("$string_file.at"); ?> <?php echo convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant, 3); ?>

                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo e($booking->PaymentMethod->payment_method); ?>

                                            </td>
                                            <td>
                                                <?php echo convertTimeToUSERzone($booking->created_at, $user->CountryArea->timezone, null, $booking->Merchant); ?>

                                            </td>
                                        </tr>
                                        <?php $sr++ ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                                <div class="pagination1 float-right"><?php echo e($bookings->links()); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/user/show.blade.php ENDPATH**/ ?>