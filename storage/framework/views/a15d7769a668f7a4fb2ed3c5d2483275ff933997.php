<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(URL::previous()); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="fa fa-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-info-circle" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.ride_details"); ?>
                        #<?php echo e($booking->merchant_booking_id); ?>

                    </h3>
                </header>
                <div class="panel-body">
                    <div id="user-profile">
                        <div class="row">
                            <div class="col-md-4 col-xs-12">
                                <div class="card my-2 shadow">
                                    <div class="">
                                        <img class="card-img-top" width="100%" style="height:22vh"
                                             alt="google image" src="<?php echo $booking->map_image; ?>">
                                    </div>
                                    <div class="card-body ">
                                        <div class="col-md-4 col-sm-4" style="float:left;">
                                            <img height="80" width="80" class="rounded-circle"
                                                 src="<?php if($booking->User->UserProfileImage): ?> <?php echo e(get_image($booking->User->UserProfileImage,'user')); ?> <?php else: ?> <?php echo e(get_image(null,'user')); ?> <?php endif; ?>"
                                                 alt="img">

                                        </div>
                                        <div class="card-text col-md-8 col-sm-8 py-2" style="float:left;">
                                            <?php if(Auth::user()->demo == 1): ?>
                                                <h4 class="user-name"><?php echo e("********".substr($booking->User->UserName, -2)); ?></h4>
                                                <p class="user-job"><?php echo e("********".substr($booking->User->UserPhone, -2)); ?></p>
                                                <p class="user-location"><?php echo e("********".substr($booking->User->email, -2)); ?></p>
                                            <?php else: ?>
                                                <h5 class="user-name"><?php echo e($booking->User->UserName); ?></h5>
                                                <h6 class="user-job"><?php echo e($booking->User->UserPhone); ?></h6>
                                                <h6 class="user-location"><?php echo e($booking->User->email); ?></h6>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="card my-2 shadow bg-white h-280">
                                    <div class="justify-content-center p-3">
                                        <div class="col-md-12 col-xs-12 mt-10"
                                             style="text-align:center;justify-content:center">

                                            <img height="80" width="80" class="rounded-circle"
                                                 src="<?php if($booking->Driver): ?> <?php echo e(get_image($booking->Driver->profile_image,'driver')); ?> <?php else: ?> <?php echo e(get_image(null,'driver')); ?> <?php endif; ?>">

                                        </div>
                                        <div class="overlay-box">
                                            <div class="user-content " style="text-align:center">
                                                <!-- <a href="javascript:void(0)"> -->
                                            <!-- <img src="<?php if($booking->Driver): ?> <?php echo e(asset($booking->Driver->profile_image)); ?> <?php else: ?> <?php echo e(asset("user.png")); ?> <?php endif; ?>" -->
                                                <!-- class="thumb-lg img-circle" alt="img"> -->
                                                <!-- </a> -->
                                                <?php if(!empty($booking->Driver->id)): ?>
                                                    <?php if(Auth::user()->demo == 1): ?>
                                                        <h5 class="user-name mt-5 mb-5"><?php if($booking->Driver): ?> <?php echo e("********".substr($booking->Driver->fullName, -2)); ?> <?php else: ?>  <?php endif; ?></h5>
                                                        <p class="user-job mb-1 "><?php if($booking->Driver): ?> <?php echo e("********".substr($booking->Driver->email, 2)); ?> <?php else: ?>  <?php endif; ?></p>
                                                        <p class="user-location mb-2"><?php if($booking->Driver): ?> <?php echo e("********".substr($booking->Driver->phoneNumber, -2)); ?> <?php else: ?>  <?php endif; ?></p>
                                                    <?php else: ?>
                                                        <h5 class="user-name mt-5 mb-1"><?php if($booking->Driver): ?> <?php echo e($booking->Driver->fullName); ?> <?php else: ?>  <?php endif; ?></h5>
                                                        <p class="user-job mb-1"><?php if($booking->Driver): ?> <?php echo e($booking->Driver->email); ?> <?php else: ?>  <?php endif; ?></p>
                                                        <p class="user-location mb-2"><?php if($booking->Driver): ?> <?php echo e($booking->Driver->phoneNumber); ?> <?php else: ?>  <?php endif; ?></p>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?php echo app('translator')->get("$string_file.not_accepted"); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="clear"></div>
                                        <div class="user-btm-box">
                                            <div class="col-md-4 col-sm-4" style="float:left;">
                                                <a class="avatar img-bordered avatar-100" href="javascript:void(0)">
                                                    <img src="<?php if($booking->VehicleType): ?> <?php echo e(get_image($booking->VehicleType->vehicleTypeImage,'vehicle')); ?><?php endif; ?>"
                                                    /></a>
                                            </div>
                                            <div class="col-md-8 col-sm-8" style="float:left;">
                                                <?php
                                                    $package_name = ($booking->service_type_id == 2) && !empty($booking->service_package_id) ? ' ('.$booking->ServicePackage->PackageName.')' : '';
                                                    $service_text = ($booking->ServiceType) ? $booking->ServiceType->serviceName.$package_name : ($booking->deliveryType ? $booking->deliveryType->name : '---' );
                                                ?>
                                                <h5 class="user-name"><?php echo e($service_text); ?></h5>
                                                <h6 class="user-job"><?php echo e($booking->VehicleType->VehicleTypeName); ?></h6>
                                                <h6 class="user-location"><?php if($booking->DriverVehicle): ?> <?php echo e($booking->DriverVehicle->VehicleMake->VehicleMakeName); ?>

                                                    :<?php echo e($booking->DriverVehicle->VehicleModel->VehicleModelName); ?>

                                                    -<?php echo e($booking->DriverVehicle->vehicle_number); ?> <?php else: ?>  <?php endif; ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-xs-12 mt-20">
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12 mb-5 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="font-weight-500 text-success text-uppercase mb-1">
                                                    <i class="icon fa-map-marker fa-2x text-gray-300"></i>
                                                    <?php echo app('translator')->get("$string_file.pickup_location"); ?></div>
                                                <div class="mb-0"><?php echo e($booking->pickup_location); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 mb-5 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="font-weight-500 text-danger text-uppercase mb-1">
                                                    <i class="icon fa-tint fa-2x text-gray-300"></i>
                                                    <?php echo app('translator')->get("$string_file.drop_location"); ?></div>
                                                <div class="mb-0"><?php echo e($booking->drop_location); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if(isset($booking->BookingDetail) && !empty($booking->BookingDetail->start_location) && !empty($booking->BookingDetail->end_location)): ?>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12 mb-5 py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-500 text-success text-uppercase mb-1">
                                                        <i class="icon fa-map-marker fa-2x text-gray-300"></i>
                                                        <?php echo app('translator')->get("$string_file.start_location"); ?></div>
                                                    <div class="mb-0"><?php echo e($booking->BookingDetail->start_location); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12 mb-5 py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-500 text-danger text-uppercase mb-1">
                                                        <i class="icon fa-tint fa-2x text-gray-300"></i>
                                                        <?php echo app('translator')->get("$string_file.end_location"); ?></div>
                                                    <div class="mb-0"><?php echo e($booking->BookingDetail->end_location); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12 mb-5 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="font-weight-500 text-info text-uppercase mb-1">
                                                    <i class="icon fa-money"></i>
                                                    <?php echo app('translator')->get("$string_file.payment"); ?></div>
                                                <div class="mb-0">
                                                    <?php echo e($booking->PaymentMethod->payment_method); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12 mb-5 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="font-weight-500 text-warning text-uppercase mb-1">
                                                    <i class="icon fa-comments fa-2x text-gray-300"></i>
                                                    <?php echo app('translator')->get("$string_file.current_status"); ?></div>
                                                <div class="mb-0">
                                                    <?php if(!empty($arr_booking_status)): ?>
                                                        <?php echo isset($arr_booking_status[$booking->booking_status]) ? $arr_booking_status[$booking->booking_status] : ""; ?>

                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="font-weight-500 text-success text-uppercase mb-1">
                                                    <i class="icon fa-calendar fa-2x text-gray-300"></i>
                                                    <?php echo app('translator')->get("$string_file.date"); ?></div>
                                                <div class="mb-0">
                                                    <?php echo convertTimeToUSERzone($booking->created_at, $booking->CountryArea->timezone,null,$booking->Merchant); ?>

                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if($booking->booking_status == 1005): ?>
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-500 text-success text-uppercase mb-1">
                                                        <i class="icon fa-road fa-2x text-gray-300"></i>
                                                        <?php echo app('translator')->get("$string_file.final_distance_time"); ?></div>
                                                    <div class="mb-0">
                                                        <?php echo e($booking->travel_distance ." ".$booking->travel_time); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="row">
                                    <?php if($booking->insurnce == 1): ?>
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class=" font-weight-400 text-success text-uppercase mb-1">
                                                        <i class="icon fa-calendar-alt fa-2x text-gray-300"></i>
                                                        <?php echo app('translator')->get("$string_file.insurance"); ?></div>
                                                    <div class="mb-0">
                                                        <?php echo app('translator')->get("$string_file.yes"); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="font-weight-400 text-success text-uppercase mb-1">
                                                    <i class="icon fa-calendar fa-2x text-gray-300"></i> <?php echo app('translator')->get("$string_file.ride_type"); ?>
                                                </div>
                                                <div class="mb-0">
                                                    <?php if($booking->booking_type == 1): ?>
                                                        <?php echo app('translator')->get("$string_file.ride_now"); ?>
                                                    <?php else: ?>
                                                        <?php echo app('translator')->get("$string_file.ride_later"); ?><br>(
                                                        <?php echo date(getDateTimeFormat($booking->Merchant->datetime_format,2),strtotime($booking->later_booking_date)); ?>

                                                        
                                                        <br>
                                                        
                                                        <?php echo e($booking->later_booking_time); ?> )
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if(isset($booking->Merchant->Configuration->no_of_children) && $booking->Merchant->Configuration->no_of_children == 1): ?>
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-400 text-success text-uppercase mb-1">
                                                        <i class="icon fa-calendar fa-2x text-gray-300"></i> <?php echo app('translator')->get("$string_file.no_of_children"); ?>
                                                    </div>
                                                    <div class="mb-0"><?php if($booking->no_of_children > 0): ?> <?php echo e($booking->no_of_children); ?> <?php else: ?>
                                                            0 <?php endif; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($booking->service_type_id == 2 || $booking->service_type_id == 4): ?>
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2" style="float:left;">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-400 text-success text-uppercase mb-1">
                                                        <i class="icon fa-tachometer fa-2x text-gray-300"></i> <?php echo app('translator')->get("$string_file.start_meter_image"); ?>
                                                    </div>
                                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                        <?php if(!empty($booking->BookingDetail->start_meter_image)): ?>
                                                            <a href="<?php echo e(get_image($booking->BookingDetail->start_meter_image,'send_meter_image')); ?>"
                                                               target="_blank"><img width="100" height="100"
                                                                                    style="border-radius: 50%"
                                                                                    src="<?php echo e(get_image($booking->BookingDetail->start_meter_image,'send_meter_image')); ?>"></a>
                                                            <h6><?php echo app('translator')->get("$string_file.start_meter_reading"); ?>
                                                                : <?php echo e($booking->BookingDetail->start_meter_value); ?></h6>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2" style="float:left;">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-400 text-danger text-uppercase mb-1">
                                                        <i class="icon fa-tachometer fa-2x text-gray-300"></i> <?php echo app('translator')->get("$string_file.end_meter_image"); ?>
                                                    </div>
                                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                        <?php if(!empty($booking->BookingDetail->end_meter_image)): ?>
                                                            <a href="<?php echo e(get_image($booking->BookingDetail->end_meter_image,'send_meter_image')); ?>"
                                                               target="_blank"><img width="100" height="100"
                                                                                    style="border-radius: 50%"
                                                                                    src="<?php echo e(get_image($booking->BookingDetail->end_meter_image,'send_meter_image')); ?>"></a>
                                                            <h6><?php echo app('translator')->get("$string_file.end_meter_reading"); ?>
                                                                : <?php echo e($booking->BookingDetail->end_meter_value); ?></h6>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($booking->service_type_id == 4): ?>
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2" style="float:left;">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-400 text-danger text-uppercase mb-1">
                                                        <i class="icon fa-road fa-2x text-gray-300"></i> <?php echo app('translator')->get("$string_file.ride_details"); ?>
                                                    </div>
                                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                        <?php if(isset($booking->return_date) && isset($booking->return_time)): ?>
                                                            <h6><?php echo app('translator')->get("$string_file.round_trip_only"); ?></h6>
                                                        <?php else: ?>
                                                            <h6><?php echo app('translator')->get("$string_file.one_way"); ?></h6>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(isset($booking->family_member_id) && $booking->family_member_id != ''): ?>
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="font-weight-500 text-success text-uppercase mb-1">
                                                        <i class="icon fa-child fa-2x text-gray-300"></i>
                                                        <?php echo app('translator')->get('admin.family_member'); ?></div>
                                                    <?php if(Auth::user()->demo == 1): ?>
                                                        <div class="mb-0 font-weight-400"><?php echo app('translator')->get("$string_file.name"); ?>
                                                            : <?php echo e("********".substr($booking->FamilyMember->name, -2)); ?></div>
                                                        <div class="mb-0 font-weight-400"><?php echo app('translator')->get("$string_file.age"); ?>
                                                            : <?php echo e($booking->FamilyMember->age); ?></div>
                                                        <div class="mb-0"><?php echo app('translator')->get("$string_file.gender"); ?>
                                                            : <?php echo e(($booking->FamilyMember->gender == 1) ? 'Male' : 'Female'); ?></div>
                                                    <?php else: ?>
                                                        <div class="mb-0 font-weight-400"><?php echo app('translator')->get("$string_file.name"); ?>
                                                            : <?php echo e($booking->FamilyMember->name); ?></div>
                                                        <div class="mb-0 font-weight-400"><?php echo app('translator')->get("$string_file.age"); ?>
                                                            : <?php echo e($booking->FamilyMember->age); ?></div>
                                                        <div class="mb-0 font-weight-400"><?php echo app('translator')->get("$string_file.gender"); ?>
                                                            : <?php echo e(($booking->FamilyMember->gender == 1) ? 'Male' : 'Female'); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                        <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2" style="float:left;">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="font-weight-400 text-danger text-uppercase mb-1">
                                                            <i class="icon fa-road fa-2x text-gray-300"></i> <?php echo app('translator')->get("$string_file.note"); ?>:
                                                        </div>
                                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                            <?php if($final_bill_calculation == 1): ?>
                                                                <h6><?php echo app('translator')->get("$string_file.final_equal_actual"); ?></h6>
                                                            <?php else: ?>
                                                                <h6><?php echo app('translator')->get("$string_file.final_equal_estimate"); ?></h6>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php if($final_bill_calculation == 2): ?>
                                            <div class="col-md-6 col-sm-6 mb-5 col-xs-12 py-2" style="float:left;">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="font-weight-400 text-success text-uppercase mb-1">
                                                            <i class="icon fa-list text-gray-300"></i> <?php echo app('translator')->get("$string_file.estimate_distance_time"); ?>:
                                                        </div>
                                                        <div class="h6 mb-0 text-gray-800">
                                                           <?php echo e($booking->estimate_time); ?> & <?php echo e($booking->estimate_distance); ?>

                                                            <br>
                                                            <?php echo app('translator')->get("$string_file.estimate_bill"); ?> : <?php echo e($booking->estimate_bill); ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                </div>
                                <div class="row mt-50">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="dataTable">
                                                <thead>
                                                <tr>
                                                    <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                                                    <th><?php echo app('translator')->get("$string_file.time"); ?></th>
                                                    <th><?php echo app('translator')->get("$string_file.coordinates"); ?></th>
                                                    <th><?php echo app('translator')->get("$string_file.map"); ?></th>
                                                    <th><?php echo app('translator')->get("$string_file.accuracy"); ?></th>
                                                    <th><?php echo app('translator')->get("$string_file.time_difference"); ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php if($booking->BookingDetail): ?>
                                                    <tr>
                                                        <td><?php echo app('translator')->get("$string_file.accepted"); ?></td>
                                                        <td><?php echo e(convertTimeToUSERzone(date("Y-m-d H:i:s",$booking->BookingDetail->accept_timestamp), $booking->CountryArea->timezone,null,$booking->Merchant,3)); ?></td>
                                                        
                                                        <td><?php echo e($booking->BookingDetail->accept_latitude); ?>

                                                            ,<?php echo e($booking->BookingDetail->accept_longitude); ?></td>
                                                        <td><a target="_blank"
                                                               href="https://www.google.com/maps/place/<?php echo e($booking->BookingDetail->accept_latitude); ?>,<?php echo e($booking->BookingDetail->accept_longitude); ?>">
                                                                <button type="button"
                                                                        class="btn btn-primary btn-min-width mr-1 mb-1">
                                                                    Map
                                                                </button>
                                                            </a></td>
                                                        <td><?php echo e($booking->BookingDetail->accuracy_at_accept); ?></td>
                                                        <td><?php echo e(round(abs($booking->booking_timestamp - $booking->BookingDetail->accept_timestamp) / 60, 2)); ?></td>
                                                    </tr>
                                                    <?php if($booking->BookingDetail->arrive_timestamp): ?>
                                                        <tr>
                                                            <td><?php echo app('translator')->get("$string_file.arrived"); ?></td>
                                                            <td><?php echo e(convertTimeToUSERzone(date("Y-m-d H:i:s",$booking->BookingDetail->arrive_timestamp), $booking->CountryArea->timezone,null,$booking->Merchant,3)); ?></td>
                                                            
                                                            <td><?php echo e($booking->BookingDetail->arrive_latitude); ?>

                                                                ,<?php echo e($booking->BookingDetail->arrive_longitude); ?></td>
                                                            <td><a target="_blank"
                                                                   href="https://www.google.com/maps/place/<?php echo e($booking->BookingDetail->arrive_latitude); ?>,<?php echo e($booking->BookingDetail->arrive_longitude); ?>">
                                                                    <button type="button"
                                                                            class="btn btn-primary btn-min-width mr-1 mb-1">
                                                                        Map
                                                                    </button>
                                                                </a></td>
                                                            <td><?php echo e($booking->BookingDetail->accuracy_at_arrive); ?></td>
                                                            <td><?php echo e(round(abs($booking->BookingDetail->arrive_timestamp - $booking->BookingDetail->accept_timestamp) / 60, 2)); ?></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    <?php if($booking->BookingDetail->start_timestamp): ?>
                                                        <tr>
                                                            <td><?php echo app('translator')->get("$string_file.started"); ?></td>
                                                            
                                                            <td><?php echo e(convertTimeToUSERzone(date("Y-m-d H:i:s",$booking->BookingDetail->start_timestamp), $booking->CountryArea->timezone,null,$booking->Merchant,3)); ?></td>
                                                            <td><?php echo e($booking->BookingDetail->start_latitude); ?>

                                                                ,<?php echo e($booking->BookingDetail->start_longitude); ?></td>
                                                            <td><a target="_blank"
                                                                   href="https://www.google.com/maps/place/<?php echo e($booking->BookingDetail->start_latitude); ?>,<?php echo e($booking->BookingDetail->start_longitude); ?>">
                                                                    <button type="button"
                                                                            class="btn btn-primary btn-min-width mr-1 mb-1">
                                                                        Map
                                                                    </button>
                                                                </a></td>
                                                            <td><?php echo e($booking->BookingDetail->accuracy_at_start); ?></td>
                                                            <td><?php echo e(round(abs($booking->BookingDetail->arrive_timestamp - $booking->BookingDetail->start_timestamp) / 60, 2)); ?></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    <?php if($booking->BookingDetail->end_timestamp): ?>
                                                        <tr>
                                                            <td><?php echo app('translator')->get("$string_file.completed"); ?></td>
                                                            
                                                            <td><?php echo e(convertTimeToUSERzone(date("Y-m-d H:i:s",$booking->BookingDetail->end_timestamp), $booking->CountryArea->timezone,null,$booking->Merchant,3)); ?></td>
                                                            <td><?php echo e($booking->BookingDetail->end_latitude); ?>

                                                                ,<?php echo e($booking->BookingDetail->end_longitude); ?></td>
                                                            <td><a target="_blank"
                                                                   href="https://www.google.com/maps/place/<?php echo e($booking->BookingDetail->end_latitude); ?>,<?php echo e($booking->BookingDetail->end_longitude); ?>">
                                                                    <button type="button"
                                                                            class="btn btn-primary btn-min-width mr-1 mb-1">
                                                                        <?php echo app('translator')->get("$string_file.map"); ?>
                                                                    </button>
                                                                </a></td>
                                                            <td><?php echo e($booking->BookingDetail->accuracy_at_end); ?></td>
                                                            <td><?php echo e(round(abs($booking->BookingDetail->end_timestamp - $booking->BookingDetail->start_timestamp) / 60, 2)); ?></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if(!empty($booking->BookingDeliveryDetail) && $booking->BookingDeliveryDetail->count() > 0): ?>
                            <br>
                            <h4 class="form-section" style="color: black"><i
                                        class="fa fa-microchip"></i> <?php echo app('translator')->get("$string_file.delivery_drop_details"); ?>
                                <hr>
                            </h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="dataTable">
                                            <thead>
                                            <tr>
                                                <th width="8%"><?php echo app('translator')->get("$string_file.stop_no"); ?></th>
                                                <th><?php echo app('translator')->get("$string_file.map"); ?></th>
                                                <th width="15%"><?php echo app('translator')->get("$string_file.location"); ?></th>
                                                <th><?php echo app('translator')->get("$string_file.receiver_details"); ?></th>
                                                <th><?php echo app('translator')->get("$string_file.products_images"); ?></th>
                                                <th><?php echo app('translator')->get("$string_file.additional_notes"); ?></th>
                                                <?php if($booking->Merchant->BookingConfiguration->delivery_drop_otp == 1): ?>
                                                    <th><?php echo app('translator')->get("$string_file.otp"); ?></th>
                                                <?php endif; ?>
                                                <th><?php echo app('translator')->get("$string_file.drop_status"); ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $__currentLoopData = $booking->BookingDeliveryDetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking_delivery_detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($booking_delivery_detail->stop_no); ?></td>
                                                    <td><a target="_blank"
                                                           href="https://www.google.com/maps/place/<?php echo e($booking_delivery_detail->drop_latitude); ?>,<?php echo e($booking_delivery_detail->drop_longitude); ?>">
                                                            <button type="button"
                                                                    class="btn btn-primary btn-min-width mr-1 mb-1">
                                                                Map
                                                            </button>
                                                        </a></td>
                                                    <td><?php echo e($booking_delivery_detail->drop_location); ?></td>
                                                    <td><?php echo e("Name : ". ($booking_delivery_detail->receiver_name) ? $booking_delivery_detail->receiver_name : "---"); ?>

                                                        <br><?php echo e("Phone : ".($booking_delivery_detail->receiver_phone) ? $booking_delivery_detail->receiver_phone : "---"); ?>

                                                        <br>
                                                        <?php if($booking_delivery_detail->receiver_image != ''): ?>
                                                            <img height="80" width="80" class="rounded-circle"
                                                                 src="<?php if($booking_delivery_detail->receiver_image): ?> <?php echo e(get_image($booking_delivery_detail->receiver_image,'booking_images')); ?> <?php endif; ?>"
                                                                 alt="img">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($booking_delivery_detail->product_image_one != ''): ?>
                                                            <img height="80" width="80" class="rounded-circle"
                                                                 src="<?php if($booking_delivery_detail->product_image_one): ?> <?php echo e(get_image($booking_delivery_detail->product_image_one,'product_image')); ?> <?php endif; ?>"
                                                                 alt="img">
                                                        <?php endif; ?>
                                                        <?php if($booking_delivery_detail->product_image_two != ''): ?>
                                                            <img height="80" width="80" class="rounded-circle"
                                                                 src="<?php if($booking_delivery_detail->product_image_two): ?> <?php echo e(get_image($booking_delivery_detail->product_image_two,'product_image')); ?> <?php endif; ?>"
                                                                 alt="img">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e(!empty($booking_delivery_detail->additional_notes) ? $booking_delivery_detail->additional_notes : '--'); ?></td>
                                                    <?php if($booking->Merchant->BookingConfiguration->delivery_drop_otp == 1): ?>
                                                        <td><?php echo e($booking_delivery_detail->opt_for_verify); ?></td>
                                                    <?php endif; ?>
                                                    <td><?php echo e(($booking_delivery_detail->drop_status == 1) ? "Delivered" : "Not Delivered"); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/booking/detail.blade.php ENDPATH**/ ?>